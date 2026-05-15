#!/usr/bin/env bash
# =====================================================================
# LivePoll deploy script — runs on the VPS as the `deploy` user.
#
#   ./docker/deploy.sh             # pull, build, up, migrate
#   ./docker/deploy.sh --no-build  # skip image rebuild (env-only restart)
#   ./docker/deploy.sh --seed      # also run db:seed (first deploy only)
# =====================================================================
set -euo pipefail

cd "$(dirname "$0")/.."

ENV_FILE=".env.production"
COMPOSE_FILE="docker-compose.prod.yml"
SKIP_BUILD=0
RUN_SEED=0

for arg in "$@"; do
    case "$arg" in
        --no-build) SKIP_BUILD=1 ;;
        --seed)     RUN_SEED=1   ;;
        *)
            echo "Unknown flag: $arg" >&2
            exit 2
            ;;
    esac
done

if [[ ! -f "$ENV_FILE" ]]; then
    echo "❌ Missing $ENV_FILE — copy .env.production.example and fill secrets." >&2
    exit 1
fi

DC=(docker compose -f "$COMPOSE_FILE" --env-file "$ENV_FILE")

step() { printf "\n\033[1;34m→\033[0m \033[1m%s\033[0m\n" "$*"; }

step "Pulling latest source"
git pull --ff-only

if [[ $SKIP_BUILD -eq 0 ]]; then
    step "Building app image (php-fpm + composer + npm)"
    "${DC[@]}" build app

    step "Building web image (nginx + public/)"
    "${DC[@]}" build web
fi

step "Starting stack (app, web, reverb, queue, scheduler)"
"${DC[@]}" up -d --remove-orphans

step "Waiting for app container to be healthy"
for i in {1..30}; do
    status=$(docker inspect -f '{{.State.Health.Status}}' livepoll-app 2>/dev/null || echo "starting")
    [[ "$status" == "healthy" ]] && break
    sleep 2
done

step "Running database migrations"
"${DC[@]}" exec -T app php artisan migrate --force

if [[ $RUN_SEED -eq 1 ]]; then
    step "Seeding database"
    "${DC[@]}" exec -T app php artisan db:seed --force
fi

step "Caching config / routes / views / events"
"${DC[@]}" exec -T app php artisan optimize:clear
"${DC[@]}" exec -T app php artisan config:cache
"${DC[@]}" exec -T app php artisan route:cache
"${DC[@]}" exec -T app php artisan view:cache
"${DC[@]}" exec -T app php artisan event:cache

step "Ensuring storage symlink"
"${DC[@]}" exec -T app php artisan storage:link || true

step "Reloading php-fpm workers (opcache reset)"
"${DC[@]}" kill -s USR2 app || true

step "Pruning dangling images"
docker image prune -f >/dev/null

printf "\n\033[1;32m✓ Deploy done.\033[0m\n"
"${DC[@]}" ps
