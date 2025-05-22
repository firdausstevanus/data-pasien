<?php

namespace App\Filament\Admin\Resources\DokterResource\RelationManagers;

use App\Models\Dokter;
use App\Models\Pasien;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RekamMedisRelationManager extends RelationManager
{
    protected static string $relationship = 'rekamMedis';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $title = 'Rekam Medis';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pasien_id')
                    ->relationship('pasien', 'nama')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('tanggal_lahir')
                            ->required()
                            ->maxDate(now()),
                        Forms\Components\Select::make('jenis_kelamin')
                            ->required()
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ]),
                        Forms\Components\Textarea::make('alamat')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('no_telepon')
                            ->required()
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->email()
                            ->unique()
                            ->maxLength(255),
                    ])
                    ->label('Pasien'),
                Forms\Components\DatePicker::make('tanggal')
                    ->required()
                    ->label('Tanggal Pemeriksaan')
                    ->default(now())
                    ->maxDate(now()),
                Forms\Components\Textarea::make('diagnosa')
                    ->required(),
                Forms\Components\Textarea::make('pengobatan')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('pasien.nama')
                    ->searchable()
                    ->sortable()
                    ->label('Pasien'),
                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable()
                    ->label('Tanggal Pemeriksaan'),
                Tables\Columns\TextColumn::make('diagnosa')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('pengobatan')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        
                        return $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('pasien')
                    ->relationship('pasien', 'nama')
                    ->searchable()
                    ->preload()
                    ->label('Pasien'),
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    })
                    ->label('Tanggal Pemeriksaan'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['dokter_id'] = $this->ownerRecord->id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
