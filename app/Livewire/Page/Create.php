<?php

namespace App\Livewire\Page;

use App\Models\Page;
use App\Support\PageBlocks;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Create extends Component
{
    use WithPageForm;

    public function mount()
    {
        $this->blocks = PageBlocks::template();
    }

    public function submit()
    {
        abort_if(Gate::denies('page_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->validate();

        $page = Page::create($this->pageAttributes());

        session()->flash('status', trans('cruds.page.saved'));

        return redirect()->route('admin.pages.edit', $page);
    }
}
