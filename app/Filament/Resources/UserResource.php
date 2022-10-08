<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Admin';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Tabs::make('Required Fields')->label('required')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('required')
                            ->schema([
                                Forms\Components\TextInput::make('name1')
                                    ->required()
                                    ->lazy()
                                    ->afterStateUpdated(fn(string $context, $state, callable $set) => $set('search', Str::upper($state)) ),
                                    //->afterStateUpdated(fn(string $context, $state, callable $set) => $context === 'create' ? $set('search', Str::upper($state)) : null),

                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->unique(User::class, 'email', ignoreRecord: true)
                                    ->required(),
                                Forms\Components\TextInput::make('phone1')
                                    ->required(),
                                Forms\Components\TextInput::make('password')
                                    ->password()
                                    ->minLength(8)
                                    ->dehydrateStateUsing(static fn(null|string $state): null|string => filled($state) ? Hash::make($state) : null)
                                    ->required(static fn(Page $livewire): bool => $livewire instanceof CreateUser)
                                    ->dehydrated(static fn(null|string $state): bool => filled($state))
                                    ->label(static fn(Page $livewire): string => $livewire instanceof EditUser ? 'New Password' : 'Password')
                            ]),
                        Forms\Components\Tabs\Tab::make('extended')
                            ->schema([
                                Forms\Components\TextInput::make('street'),
                                Forms\Components\TextInput::make('city'),
                                Forms\Components\TextInput::make('phone2')
                                    ->tel(),
                                Forms\Components\TextInput::make('phone3'),
                                Forms\Components\TextInput::make('phone4'),
                                Forms\Components\TextInput::make('email1'),
                                Forms\Components\DatePicker::make('dob'),


                            ]),
                        Forms\Components\Tabs\Tab::make('bank account')
                            ->schema([
                                Forms\Components\TextInput::make('iban'),
                                Forms\Components\TextInput::make('bic'),


                            ]),

                        Forms\Components\Tabs\Tab::make('manager')
                            ->schema([
                                Forms\Components\Select::make('title2')
                                    ->options([
                                        'Herr' => 'Herr',
                                        'Frau' => 'Frau'
                                    ]),
                                Forms\Components\TextInput::make('manager')
                                    ->label('Account'),

                            ]),
                    ])->columnSpan(['lg' => 2]),

                //
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('search')->columnSpan(['lg' => 1]),
                        Forms\Components\Select::make('role_id')
                            ->options([
                                '1' => 'admin',
                                '2' => 'Employee',
                                '3' => 'Client',
                                '4' => 'Supplier',
                                '5' => 'Dealer',
                                '6' => 'Guest',
                            ])
                        ->default(3)
                    ])->columnSpan(['lg' => 1]),


            ])->columns(3);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('search')
                    ->searchable()
                    ->searchable(isIndividual: true, isGlobal: false),
                Tables\Columns\TextColumn::make('name1')
                    ->searchable()
                    ->searchable(isIndividual: true, isGlobal: false),

                Tables\Columns\TextColumn::make('email')
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('role_id')
                    ->colors([
                        'primary',
                        'success' => '1',
                        'danger' => '2'
                    ])
                    ->toggleable()


            ])
            ->filters([
                //
                Tables\Filters\Filter::make('role_id')->label('role')
                    ->form([
                        Forms\Components\Select::make('role_id')
                            ->options([
                                '1' => 'admin',
                                '2' => 'Employee',
                                '3' => 'Client',
                                '4' => 'Supplier',
                                '5' => 'Dealer',
                                '6' => 'Guest',
                            ])
                    ])->query(function ($query, array $data) {
                        return $query->when($data['role_id'],
                            fn($query) => $query->where('role_id', '=', $data['role_id']));
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //     Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {

        $user = auth()->user();

        if ($user->role_id == 1) {
            return parent::getEloquentQuery()->withoutGlobalScope(SoftDeletingScope::class);
        }

        return parent::getEloquentQuery()
            ->where('role_id','=',2)
            ->withoutGlobalScope(SoftDeletingScope::class);
    }


//    public static function canDeleteAny(): bool
//    {
//     //   return false;
//    }
//
//    public static function canCreate(): bool
//    {
//   //     return true;
//    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
