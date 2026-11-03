<?php

namespace App\Livewire\Page;

use App\Models\Page;
use App\Support\PageBlocks;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;

trait WithPageForm
{
    use WithFileUploads;

    public string $title = '';

    public string $slug = '';

    public string $theme = 'light';

    public ?string $metaDescription = '';

    public bool $inMenu = false;

    public bool $isPublished = false;

    public array $blocks = [];

    public array $uploads = [];

    public bool $slugLocked = false;

    public function render()
    {
        return view('livewire.page.form', [
            'blockTypes' => PageBlocks::types(),
        ]);
    }

    public function updatedTitle(): void
    {
        if (! $this->slugLocked) {
            $this->slug = Str::slug($this->title);
        }
    }

    public function updatedSlug(): void
    {
        $this->slugLocked = true;
        $this->slug       = Str::slug($this->slug);
    }

    /** Aperçu du contenu en cours, sans l'enregistrer ni exiger qu'il soit complet. */
    public function preview(): void
    {
        abort_if(Gate::none(['page_create', 'page_edit']), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->dispatch('page-preview', html: view('site.page', ['page' => new Page($this->pageAttributes())])->render());
    }

    public function addBlock(string $type): void
    {
        abort_unless(array_key_exists($type, PageBlocks::types()), Response::HTTP_NOT_FOUND);

        // « On garde contact ? » reste en fin de page tant que le client ne le déplace pas.
        $position = (end($this->blocks)['type'] ?? null) === 'contact' && $type !== 'contact'
            ? count($this->blocks) - 1
            : count($this->blocks);

        array_splice($this->blocks, $position, 0, [PageBlocks::make($type)]);
        $this->resetValidation();
    }

    public function removeBlock(int $index): void
    {
        unset($this->blocks[$index]);
        $this->blocks = array_values($this->blocks);
        $this->resetValidation();
    }

    public function moveBlock(int $index, int $offset): void
    {
        $this->blocks = $this->moved($this->blocks, $index, $offset);
        $this->resetValidation();
    }

    public function addItem(int $index): void
    {
        $type = $this->blocks[$index]['type'];

        if (count($this->blocks[$index]['items']) < PageBlocks::types()[$type]['items']['max']) {
            $this->blocks[$index]['items'][] = PageBlocks::makeItem($type);
        }
    }

    public function removeItem(int $index, int $item): void
    {
        unset($this->blocks[$index]['items'][$item]);
        $this->blocks[$index]['items'] = array_values($this->blocks[$index]['items']);
        $this->resetValidation();
    }

    public function moveItem(int $index, int $item, int $offset): void
    {
        $this->blocks[$index]['items'] = $this->moved($this->blocks[$index]['items'], $item, $offset);
        $this->resetValidation();
    }

    /** Appelé dès la fin de l'envoi : la clé est le chemin du champ, ex. "blocks-0-items-1-image". */
    public function updatedUploads($file, string $key): void
    {
        abort_if(Gate::none(['page_create', 'page_edit']), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $path = str_replace('-', '.', $key);
        abort_unless(preg_match('/^blocks\.\d+\.(items\.\d+\.)?image$/', $path) && Arr::has($this->all(), $path), Response::HTTP_NOT_FOUND);

        $this->uploads = [];

        $validator = Validator::make(['image' => $file], ['image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096']]);

        if ($validator->fails()) {
            $this->addError($path, $validator->errors()->first('image'));

            return;
        }

        $this->resetErrorBag($path);
        data_set($this->blocks, Str::after($path, 'blocks.'), $file->store(PageBlocks::UPLOAD_DIRECTORY, 'public'));
    }

    protected function rules(): array
    {
        return [
            'title'           => ['required', 'string', 'max:255'],
            'slug'            => [
                'required',
                'string',
                'max:255',
                'regex:/^' . Page::SLUG_PATTERN . '$/',
                Rule::notIn(Page::reservedSlugs()),
                Rule::unique('pages', 'slug')->ignore($this->pageId()),
            ],
            'theme'           => ['required', 'in:light,dark'],
            'metaDescription' => ['nullable', 'string', 'max:255'],
            'inMenu'          => ['boolean'],
            'isPublished'     => ['boolean'],
        ] + PageBlocks::rules($this->blocks);
    }

    protected function messages(): array
    {
        return [
            'slug.not_in' => 'Cette adresse est déjà utilisée par le site.',
            'slug.unique' => 'Une autre page utilise déjà cette adresse.',
            'slug.regex'  => 'L\'adresse ne peut contenir que des lettres minuscules, des chiffres et des tirets.',
            'blocks.required' => 'Ajoutez au moins un bloc à la page.',
            'blocks.min'      => 'Ajoutez au moins un bloc à la page.',
            'blocks.*.button_url.regex' => 'Le lien doit commencer par https://, / , mailto: ou tel:.',
            'blocks.*.items.*.url.regex' => 'Le lien doit commencer par https://, / , mailto: ou tel:.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'title'           => 'titre',
            'slug'            => 'adresse',
            'theme'           => 'apparence',
            'metaDescription' => 'description',
        ] + PageBlocks::attributes($this->blocks);
    }

    protected function pageAttributes(): array
    {
        return [
            'title'            => $this->title,
            'slug'             => $this->slug,
            'theme'            => $this->theme,
            'meta_description' => $this->metaDescription ?: null,
            'in_menu'          => $this->inMenu,
            'is_published'     => $this->isPublished,
            'blocks'           => PageBlocks::clean($this->blocks),
        ];
    }

    protected function pageId(): ?int
    {
        return null;
    }

    private function moved(array $list, int $index, int $offset): array
    {
        $target = $index + $offset;

        if (! isset($list[$index], $list[$target])) {
            return $list;
        }

        [$list[$index], $list[$target]] = [$list[$target], $list[$index]];

        return $list;
    }
}
