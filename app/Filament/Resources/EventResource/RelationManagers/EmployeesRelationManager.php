<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Pages\Actions\CreateAction;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Livewire\Features\Placeholder;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';
    protected static ?string $label = 'Employees';
    protected static ?string $recordTitleAttribute = 'name1';


    public static function form(Form $form): Form
    {

        //   return UserResource::form($form);
        return $form
            ->schema([
                Forms\Components\TimePicker::make('start_at')->label('start')
                    ->withoutSeconds()
                    ->reactive(),
                Forms\Components\TimePicker::make('end_at')->label('end')
                    ->withoutSeconds()
                    ->reactive()
                    ->afterStateUpdated(function (Closure $get, $set) {
                        $set('sum', Carbon::parse($get('end_at'))
                            ->diffInSeconds($get('start_at')));
                    }),
                Forms\Components\TimePicker::make('sum')->label('sum')
                    ->withoutSeconds()
                    ->hidden()

            ]);


    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('name1')->label('Name'),
                Tables\Columns\TextColumn::make('start_at')->label('start')
                    ->date('H:i'),
                Tables\Columns\TextColumn::make('end_at')->label('end')
                    ->date('H:i'),
                Tables\Columns\TextColumn::make('sum')->label('sum')
                    ->date('H:i'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect(true)
                    ->recordSelect(function (Select $select, EmployeesRelationManager $livewire) {
                        $event = $livewire->getRelationship()->getParent();
                        $excluded = [... $event->employees->pluck('name1')];
                        $select->options(User::query()
                            ->whereNotIn('name1', $excluded)
                            ->where('role_id', '=', 2)
                            ->pluck('name1', 'id'));
                        return
                            $select->getSearchResultsUsing(function ($search) use ($excluded) {
                                return
                                    User::query()
                                        ->whereNotIn('name1', $excluded)
                                        ->where('role_id', '=', 2)
                                        ->where('name1', 'like', "%{$search}%")
                                        ->pluck('name1', 'id');
                            });
                    })
                    ->form(fn(Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),

                        Forms\Components\TimePicker::make('start_at')->label('start')
                            ->withoutSeconds(),
                        Forms\Components\TimePicker::make('end_at')->label('end')
                            ->withoutSeconds(),
                        Forms\Components\TimePicker::make('sum')->label('sum')
                            ->hidden(),
                    ])->mutateFormDataUsing(function (array $data): array {
                        $data['sum'] = Carbon::parse($data['end_at'])
                            ->diffInSeconds(Carbon::parse($data['start_at']));

                        return $data;
                    })


            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['sum'] = Carbon::parse($data['end_at'])
                            ->diffInSeconds(Carbon::parse($data['start_at']));
                        return $data;
                    })
            ])
            ->bulkActions([
            ]);
    }


}
