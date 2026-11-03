<?php

namespace App\Livewire\Page;

use App\Models\Page;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Edit extends Component
{
    use WithPageForm;

    public Page $page;

    public function mount(Page $page)
    {
        $this->page            = $page;
        $this->title           = $page->title;
        $this->slug            = $page->slug;
        $this->theme           = $page->theme;
        $this->metaDescription = $page->meta_description;
        $this->inMenu          = $page->in_menu;
        $this->isPublished     = $page->is_published;
        $this->blocks          = $page->blocks;
        $this->slugLocked      = true;
    }

    public function submit()
    {
        abort_if(Gate::denies('page_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->validate();

        $this->page->update($this->pageAttributes());

        session()->flash('status', trans('cruds.page.saved'));

        return redirect()->route('admin.pages.edit', $this->page);
    }

    protected function pageId(): ?int
    {
        return $this->page->id;
    }
}
