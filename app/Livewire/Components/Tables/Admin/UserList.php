<?php

namespace App\Livewire\Components\Tables\Admin;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridColumns;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class UserList extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function header(): array
    {
        return [
            Button::add('new-member')
                ->slot('<i class="bx bxs-plus-circle"></i> ' . __('pages/admin/users/user-list.create'))
                ->class('btn btn-accent')
                ->dispatch('new-user', [])
        ];
    }

    public function datasource(): Builder
    {
        return User::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function addColumns(): PowerGridColumns
    {
        return PowerGrid::columns()
            ->addColumn('id')
            ->addColumn('first_name')
            ->addColumn('last_name')
            ->addColumn('username')
            ->addColumn('email')
            ->addColumn('created_at_formatted', fn(User $model) => Carbon::parse($model->created_at)->format('d.m.Y H:i:s'))
            ->addColumn('updated_at_formatted', fn(User $model) => Carbon::parse($model->updated_at)->format('d.m.Y H:i:s'));
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('First name', 'first_name')
                ->sortable()
                ->searchable(),
            Column::make('Last name', 'last_name')
                ->sortable()
                ->searchable(),
            Column::make('Username', 'username')
                ->sortable()
                ->searchable(),

            Column::make('Email', 'email')
                ->sortable()
                ->searchable(),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),
            Column::make('Updated at', 'updated_at_formatted', 'updated_at')
                ->sortable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->redirect(route('admin-user-edit', ['userId' => $rowId]));
    }

    #[\Livewire\Attributes\On('delete')]
    public function forceDelete($rowId): void
    {
        $this->dispatch('openModal', 'components.modals.admin.user-delete', ['userId' => $rowId]);
    }

    #[\Livewire\Attributes\On('new-user')]
    public function createUser(): void
    {
        $this->redirect(route('admin-user-create'));
    }

    public function actions(User $row): array
    {
        return [
            Button::add('edit')
                ->slot('<i class="bx bxs-edit-alt"></i>')
                ->id()
                ->class('btn btn-primary btn-sm')
                ->dispatch('edit', ['rowId' => $row->id]),
            Button::add('delete')
                ->slot('<i class="bx bxs-trash-alt text-white"></i>')
                ->id()
                ->class('btn btn-error btn-sm')
                ->dispatch('delete', ['rowId' => $row->id]),
        ];
    }

    public function actionRules(): array
    {
        return [
            Rule::button('delete')
                ->when(fn(User $model) => $model->id == auth()->user()->id)
                ->hide(),
        ];
    }
}
