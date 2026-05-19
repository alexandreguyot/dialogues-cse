## How to install 

Run cp .env.example .env file to copy example file to .env
Then edit your .env file with DB credentials and other settings.
Run composer install command
Run php artisan migrate --seed command.
Run npm install
Notice: seed is important, because it will create the first admin user for you.
Run php artisan key:generate command.
If you have file/photo fields, run php artisan storage:link command.

## Déploiement en production

Le serveur OVH (`51.255.193.209`, user `ubuntu`) héberge plusieurs projets Laravel sous Docker derrière Traefik. Procédure synthétique ci-dessous ; voir le README de [LivePoll](https://github.com/alexandreguyot/livepoll#d%C3%A9ploiement-en-production) pour les détails (bootstrap d'un nouveau projet, etc.).

### 1. Rapide — depuis ton laptop

```bash
git add <fichiers>
git commit -m "type(scope): message clair"   # Conventional Commits
git push origin main

gh workflow run deploy.yml --repo alexandreguyot/dialogues-cse
gh run list --workflow=deploy.yml --repo alexandreguyot/dialogues-cse --limit 1
gh run watch <ID> --repo alexandreguyot/dialogues-cse --exit-status    # ~1m30 – 2 min
```

GitHub Actions SSH ensuite sur le VPS avec une clé restreinte `ForceCommand` qui ne peut faire QUE lancer `docker/deploy.sh`.

### 2. Manuel — directement sur le VPS

```bash
ssh ubuntu@51.255.193.209
cd /srv/dialogues-cse

./docker/deploy.sh              # full deploy
./docker/deploy.sh --no-build   # restart sans rebuild (~10 s)
./docker/deploy.sh --seed       # premier déploiement avec db:seed
```

`deploy.sh` : `git pull --ff-only` → `docker compose build app && build web` → `up -d --remove-orphans` → attente `dialogues-cse-app` healthy → `php artisan migrate --force` → cache config/routes/views/events → `storage:link` → reload PHP-FPM (signal `USR2`) → `docker image prune -f`.

### 3. Vérifs post-déploiement

```bash
ssh ubuntu@51.255.193.209 'docker ps --format "table {{.Names}}\t{{.Status}}"'
ssh ubuntu@51.255.193.209 'docker logs dialogues-cse-web --since 5m 2>&1 | grep -E " 5[0-9]{2} "'
ssh ubuntu@51.255.193.209 'cd /srv/dialogues-cse && docker compose exec -T app tail -50 storage/logs/laravel.log | grep -iE "error|exception"'
ssh ubuntu@51.255.193.209 'docker logs -f dialogues-cse-app'
```

### Recettes courantes

```bash
# Workflow normal
git push origin main && gh workflow run deploy.yml --repo alexandreguyot/dialogues-cse

# Hotfix urgent (saute GitHub Actions)
ssh ubuntu@51.255.193.209 'cd /srv/dialogues-cse && git pull && ./docker/deploy.sh'

# Restart après changement de .env.production
ssh ubuntu@51.255.193.209 'cd /srv/dialogues-cse && ./docker/deploy.sh --no-build'

# Vider les caches Laravel
ssh ubuntu@51.255.193.209 'cd /srv/dialogues-cse && docker compose exec -T app php artisan view:clear'
```
