<?php

namespace App\Filament\Admin\Resources;

use App\Domain\Faculty\Models\Faculty;
use App\Filament\Admin\Resources\FacultyResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class FacultyResource extends Resource
{
    protected static ?string $model = Faculty::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Faculty';

    protected static ?string $pluralModelLabel = 'Faculty';

    public static function form(Form $form): Form
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
                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('email')
                        ->required()
                        ->email()
                        ->label('Email')
                        ->maxLength(100),
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
                    ])->columns(2),


                ])->hiddenOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(trans('Name'))
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->orderBy('first_name', $direction);
                    })
                    ->formatStateUsing(function ($record) {
                        return Str::limit($record->first_name . ' ' . $record->last_name, 50);
                    })
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    }),
                Tables\Columns\TextColumn::make('designation')
                    ->label(trans('Designation'))
                    ->formatStateUsing(function ($record) {
                        return empty($record->designation) ? 'N/A' : $record->designation;
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaculties::route('/'),
            'create' => Pages\CreateFaculty::route('/create'),
            'edit' => Pages\EditFaculty::route('/{record}/edit'),
        ];
    }
}
