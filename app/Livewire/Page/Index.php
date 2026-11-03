<?php

namespace App\Livewire\Page;

use App\Livewire\WithSorting;
use App\Models\Page;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, WithSorting;

    public int $perPage;

    public array $orderable;

    public string $search = '';

    public array $paginationOptions;

    protected $queryString = [
        'search' => [
            'except' => '',
        ],
        'sortBy' => [
            'except' => 'updated_at',
        ],
        'sortDirection' => [
            'except' => 'desc',
        ],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->sortBy            = 'updated_at';
        $this->sortDirection     = 'desc';
        $this->perPage           = 25;
        $this->paginationOptions = config('project.pagination.options');
        $this->orderable         = (new Page())->orderable;
    }

    public function render()
    {
        $pages = Page::advancedFilter([
            's'               => $this->search ?: null,
            'order_column'    => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ])->paginate($this->perPage);

        return view('livewire.page.index', compact('pages'));
    }

    public function duplicate(Page $page)
    {
        abort_if(Gate::denies('page_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return redirect()->route('admin.pages.edit', $page->duplicate());
    }

    public function delete(Page $page)
    {
        abort_if(Gate::denies('page_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $page->delete();
    }
}
