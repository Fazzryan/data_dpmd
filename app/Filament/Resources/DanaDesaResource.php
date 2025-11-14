<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DanaDesaResource\Pages;
use App\Models\DanaDesa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Http;
use Filament\Tables\Actions\Action;

class DanaDesaResource extends Resource
{
    protected static ?string $model = DanaDesa::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Dana Desa';
    protected static ?string $pluralModelLabel = 'Dana Desa';
    protected static ?string $modelLabel = 'Dana Desa';
    protected static ?string $navigationGroup = 'Data Bantuan';

    public static function form(Form $form): Form
    {
        // Ambil data kecamatan dari API
        $kecamatanResponse = Http::get('https://geoentry.tasikmalayakab.go.id/api/kecamatan');
        $kecamatanOptions = collect($kecamatanResponse->json('data') ?? [])
            ->pluck('nama_kecamatan', 'nama_kecamatan')
            ->toArray();

        return $form->schema([
            Section::make('Data Dana Desa')
                ->description('Lengkapi informasi terkait dana desa.')
                ->schema([
                    Grid::make(2)->schema([
                        // Dropdown Kecamatan
                        Select::make('nama_kecamatan')
                            ->label('Nama Kecamatan')
                            ->options($kecamatanOptions)
                            ->searchable()
                            ->required()
                            ->reactive(),

                        // Dropdown Desa (tergantung kecamatan)
                        Select::make('nama_desa')
                            ->label('Nama Desa')
                            ->options(function (callable $get) {
                                $selectedKecamatan = $get('nama_kecamatan');
                                if (!$selectedKecamatan) return [];

                                $desaResponse = Http::get('https://geoentry.tasikmalayakab.go.id/api/desa');
                                return collect($desaResponse->json('data') ?? [])
                                    ->where('kecamatan', $selectedKecamatan)
                                    ->pluck('nama', 'nama')
                                    ->toArray();
                            })
                            ->searchable()
                            ->required()
                            ->reactive(),
                    ]),

                    Section::make('Rincian Pagu')
                        ->schema([
                            Grid::make(3)->schema([
                                TextInput::make('pagu_blt')->label('Pagu BLT')->numeric()->prefix('Rp')->default(0),
                                TextInput::make('pagu_ketahanan_pangan')->label('Ketahanan Pangan')->numeric()->prefix('Rp')->default(0),
                                TextInput::make('pagu_stunting')->label('Pagu Stunting')->numeric()->prefix('Rp')->default(0),
                            ]),
                            Grid::make(3)->schema([
                                TextInput::make('pagu_proklim')->label('Pagu Proklim')->numeric()->prefix('Rp')->default(0),
                                TextInput::make('pagu_potensi_desa')->label('Pagu Potensi Desa')->numeric()->prefix('Rp')->default(0),
                                TextInput::make('pagu_ti')->label('Pagu TI')->numeric()->prefix('Rp')->default(0),
                            ]),
                            Grid::make(3)->schema([
                                TextInput::make('pagu_padat_karya')->label('Pagu Padat Karya')->numeric()->prefix('Rp')->default(0),
                                TextInput::make('pagu_non_prioritas')->label('Non Prioritas')->numeric()->prefix('Rp')->default(0),
                            ]),
                        ])->collapsible(),

                    Grid::make(3)->schema([
                        Select::make('status_realisasi')
                            ->label('Status Realisasi')
                            ->options([
                                'belum' => 'Belum Realisasi',
                                'proses' => 'Dalam Proses',
                                'selesai' => 'Selesai',
                            ])
                            ->default('belum')
                            ->required(),

                        TextInput::make('tahap')
                            ->label('Tahap')
                            ->maxLength(50)
                            ->placeholder('Misal: Tahap I, Tahap II, dst...'),

                        Select::make('tahun')
                            ->label('Tahun')
                            ->options(collect(range(date('Y') - 5, date('Y') + 1))
                                ->mapWithKeys(fn($year) => [$year => $year]))
                            ->default(date('Y'))
                            ->required(),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kecamatan')->label('Kecamatan')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('nama_desa')->label('Desa')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('tahun')->label('Tahun')->sortable(),
                Tables\Columns\TextColumn::make('status_realisasi')
                    ->label('Realisasi')->badge()
                    ->colors([
                        'warning' => 'belum',
                        'info' => 'proses',
                        'success' => 'selesai',
                    ])
                    ->formatStateUsing(fn($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('tahap')->label('Tahap'),
            ])

            ->headerActions([
                Action::make('importExcel')
                    ->label('Import Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->button()
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('Pilih File Excel')
                            ->required()
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-excel',
                            ]),
                    ])
                    ->action(function (array $data) {
                        $filePath = storage_path('app/public/' . $data['file']);
                        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\DanaDesaImport, $filePath);
                        \Filament\Notifications\Notification::make()
                            ->title('Data berhasil diimport!')
                            ->success()
                            ->send();
                        unlink($filePath);
                    }),
            ])

            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])

            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDanaDesas::route('/'),
            'create' => Pages\CreateDanaDesa::route('/create'),
            'edit' => Pages\EditDanaDesa::route('/{record}/edit'),
        ];
    }
}
