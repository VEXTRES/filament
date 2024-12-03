<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;

class UserController extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithForms;

    public $roles = [];

    public function mount()
    {
        $this->roles = Role::orderBy('name')->pluck('name', 'id');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->headerActions([
                CreateAction::make()
                    ->label('Agregar')
                    ->using(function (array $data): User {
                        $user = User::create([
                            'name' => $data['name'],
                            'email' => $data['email'],
                            'password' => bcrypt($data['password']),
                        ]);
                        if (!empty($data['roles'])) {
                            $user->assignRole((int) $data['roles']);
                        }

                        return $user;
                    })
                    ->form([
                        TextInput::make('name')->rules(['required']),
                        TextInput::make('email')->rules(['required', 'email'])->unique(),
                        Select::make('roles')
                            ->options($this->roles),

                        TextInput::make('password')
                            ->rules(['required', 'confirmed'])
                            ->password()->revealable(),
                        TextInput::make('password_confirmation')
                            ->rules(['required'])
                            ->password()->revealable(),
                    ])
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('User registered')
                            ->body('The user has been created successfully.'),
                    ),

            ])
            ->columns([
                TextColumn::make('name')->label('Nombre')->sortable()->searchable(),
                TextColumn::make('email'),
                TextColumn::make('roles.name'),
            ])
            ->actions([
                EditAction::make()
                    ->using(function (User $record, array $data): User {
                        if (!empty($data['password'])) {
                            $data['password'] = bcrypt($data['password']);
                        } else {
                            unset($data['password']); // No actualizar la contraseña si no se proporciona
                        }

                        $record->update($data);

                        if (!empty($data['roles'])) {
                            $record->syncRoles((int)$data['roles']);
                        }

                        return $record;
                    })
                    ->form([
                        TextInput::make('name')->rules(['required']),
                        TextInput::make('email')->rules(['required', 'email'])->unique(ignoreRecord: true),
                        Select::make('roles')
                            ->options($this->roles),
                        TextInput::make('password')
                            ->password()->revealable()
                            ->rules(['confirmed']),
                        TextInput::make('password_confirmation')
                            ->password()->revealable(),
                    ])->button('Editar'),
                DeleteAction::make(),
            ])
            ->filters([
                SelectFilter::make('Usuarios con Rol')
                    ->relationship('roles', 'name')
                    ->options(
                        $this->roles
                    )
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
