<?php

namespace App\Filament\Faculty\Pages;

use App\Domain\Faculty\Actions\UpdateFacultyAction;
use App\Domain\Faculty\DataTransferObjects\FacultyData;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

class AccountSettings extends Page
{
    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'filament.pages.faculty.account-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $faculty = auth()->user();

        $this->form->fill([
            'first_name' => $faculty->first_name,
            'last_name' => $faculty->last_name,
            'address' => $faculty->address,
            'mobile' => $faculty->mobile,
            'gender' => $faculty->gender,
            'designation' => $faculty->designation,
        ]);
    }

    public  function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\Group::make([
                        Forms\Components\TextInput::make('first_name')
                            ->label('First Name')
                            ->maxLength(100)
                            ->required(),
                        Forms\Components\TextInput::make('last_name')
                            ->label('Last Name')
                            ->maxLength(100)
                            ->required(),
                    ])->columns(2),
                    Forms\Components\TextInput::make('address')
                        ->label('Address')
                        ->maxLength(100)
                        ->required(),
                    Forms\Components\Group::make([
                        Forms\Components\TextInput::make('mobile')
                            ->label('Phone Number')
                            ->minLength(11)
                            ->maxLength(11),
                        Forms\Components\Select::make('gender')
                            ->required()
                            ->label('Gender')
                            ->options([
                                'Male' => 'Male',
                                'Female' => 'Female',
                            ]),
                    ])->columns(2),
                    Forms\Components\TextInput::make('designation')
                        ->label('Designation')
                        ->maxLength(100),
                ]),
                Forms\Components\Section::make('Change Password')
                    ->schema([
                        Forms\Components\Group::make([
                            Forms\Components\TextInput::make('password')
                                ->required()
                                ->password()
                                ->label('Password')
                                ->maxLength(20)
                                ->minLength(8)
                                ->confirmed(),
                            Forms\Components\TextInput::make('password_confirmation')
                                ->required()
                                ->password()
                                ->label('Confirm Password')
                                ->maxLength(20)
                                ->minLength(8)
                                ->dehydrated(false),
                        ])->columns(2)
                    ])
            ])->statePath('data');
    }

    public function create(): void
    {

        $record = auth()->user();
        $data = $this->form->getState();

        $result = app(UpdateFacultyAction::class)
            ->execute($record, FacultyData::fromArray($data));

        if ($result) {
            Notification::make()
                ->title('Account Updated Successfully')
                ->success()
                ->send();
        }
    }
}
