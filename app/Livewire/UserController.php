<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Tables\Actions\EditAction;
use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Table;

class UserController extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;



    public function mount() {}

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->headerActions([
                CreateAction::make()->label('Agregar')
                    ->form([
                        TextInput::make('name')->rules(['required']),
                        TextInput::make('email')->rules(['required', 'email'])->unique(),
                        TextInput::make('password')
                            ->rules(['required', 'confirmed'])
                            ->password()->revealable(),
                        TextInput::make('password_confirmation')
                            ->rules(['required'])
                            ->password()->revealable(),
                    ]),
            ])
            ->columns([
                TextColumn::make('name')->label('Nombre')->sortable()->searchable(),
                TextColumn::make('email'),
            ])
            ->actions([
                EditAction::make()
                    ->form([
                        TextInput::make('name')->rules(['required']),
                        TextInput::make('email')->rules(['required', 'email'])->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->password()->revealable()
                            ->rules(['confirmed']),
                        TextInput::make('password_confirmation')
                            ->password()->revealable(),
                    ])->button('Editar'),
                DeleteAction::make(),
            ])
            ->persistSortInSession()
            ->persistSearchInSession()
            ->emptyStateDescription('No hay usuarios')
            ->striped()
            ->paginated([10, 25, 50]);
    }
    public function render()
    {
        return view('livewire.user-controller');
    }
}
