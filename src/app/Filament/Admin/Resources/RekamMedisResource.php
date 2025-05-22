<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RekamMedisResource\Pages;
use App\Filament\Admin\Resources\RekamMedisResource\RelationManagers;
use App\Models\RekamMedis;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RekamMedisResource extends Resource
{
    protected static ?string $model = RekamMedis::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Rekam Medis';
    protected static ?string $modelLabel = 'Rekam Medis';
    protected static ?string $pluralModelLabel = 'Rekam Medis';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Rekam Medis')
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
                        Forms\Components\Select::make('dokter_id')
                            ->relationship('dokter', 'nama')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nama')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('spesialisasi')
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
                            ->label('Dokter'),
                        Forms\Components\DatePicker::make('tanggal')
                            ->required()
                            ->label('Tanggal Pemeriksaan')
                            ->default(now())
                            ->maxDate(now()),
                        Forms\Components\Textarea::make('diagnosa')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('pengobatan')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pasien.nama')
                    ->searchable()
                    ->sortable()
                    ->label('Pasien'),
                Tables\Columns\TextColumn::make('dokter.nama')
                    ->searchable()
                    ->sortable()
                    ->label('Dokter'),
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
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Dibuat Pada'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Diperbarui Pada'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('pasien')
                    ->relationship('pasien', 'nama')
                    ->searchable()
                    ->preload()
                    ->label('Pasien'),
                Tables\Filters\SelectFilter::make('dokter')
                    ->relationship('dokter', 'nama')
                    ->searchable()
                    ->preload()
                    ->label('Dokter'),
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
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRekamMedis::route('/'),
            'create' => Pages\CreateRekamMedis::route('/create'),
            'edit' => Pages\EditRekamMedis::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()
            ->with(['pasien', 'dokter']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['pasien.nama', 'dokter.nama', 'diagnosa', 'pengobatan'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var RekamMedis $record */
        return [
            'Pasien' => $record->pasien->nama,
            'Dokter' => $record->dokter->nama,
            'Tanggal' => $record->tanggal->format('d F Y'),
        ];
    }
}
