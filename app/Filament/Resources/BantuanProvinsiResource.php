<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\BantuanProvinsi;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BantuanProvinsiResource\Pages;
use App\Filament\Resources\BantuanProvinsiResource\RelationManagers;

// import excel
use App\Imports\BantuanProvinsiImport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\Action;

class BantuanProvinsiResource extends Resource
{
    protected static ?string $model = BantuanProvinsi::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Bantuan Provinsi';
    protected static ?string $pluralModelLabel = 'Bantuan Provinsi';
    protected static ?string $modelLabel = 'Bantuan Provinsi';
    protected static ?string $navigationGroup = 'Data Bantuan';

    public static function form(Form $form): Form
    {
        // Ambil data kecamatan dari API
        $kecamatanResponse = Http::get('https://geoentry.tasikmalayakab.go.id/api/kecamatan');
        $kecamatanOptions = collect($kecamatanResponse->json('data') ?? [])
            ->pluck('nama_kecamatan', 'nama_kecamatan')
            ->toArray();

        return $form->schema([
            Section::make('Data Bantuan Provinsi')
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

                                if (!$selectedKecamatan) {
                                    return [];
                                }

                                $desaResponse = Http::get('https://geoentry.tasikmalayakab.go.id/api/desa');
                                $desaData = collect($desaResponse->json('data') ?? [])
                                    ->where('kecamatan', $selectedKecamatan)
                                    ->pluck('nama', 'nama')
                                    ->toArray();

                                return $desaData;
                            })
                            ->searchable()
                            ->required()
                            ->reactive(),
                    ]),

                    Grid::make(3)->schema([
                        TextInput::make('tpapd')->label('TPAPD')->numeric()->default(0),
                        TextInput::make('bpd')->label('BPD')->numeric()->default(0),
                        TextInput::make('fisik')->label('Fisik')->numeric()->default(0),
                    ]),

                    Grid::make(2)->schema([
                        TextInput::make('total_banprov')
                            ->label('Total Banprov')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),

                        Select::make('tahun')
                            ->label('Tahun')
                            ->options(collect(range(date('Y') - 5, date('Y') + 1))
                                ->mapWithKeys(fn($y) => [$y => $y]))
                            ->default(date('Y'))
                            ->required(),
                    ]),

                    Grid::make(2)->schema([
                        Toggle::make('lolos_verifikasi')->label('Lolos Verifikasi')->default(false),
                        Toggle::make('sudah_cair')->label('Sudah Cair')->default(false),
                    ]),
                ])
                ->columns(1)
                ->collapsible(),
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_desa')
                    ->label('Desa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nama_kecamatan')
                    ->label('Kecamatan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_banprov')
                    ->label('Total Banprov')
                    ->money('idr', true)
                    ->sortable(),

                TextColumn::make('tahun')
                    ->label('Tahun')
                    ->sortable(),

                TextColumn::make('lolos_verifikasi')
                    ->label('Lolos Verifikasi')
                    ->badge()
                    ->colors([
                        'success' => fn($state) => $state === true,
                        'danger' => fn($state) => $state === false,
                    ])
                    ->formatStateUsing(fn($state) => $state ? 'Ya' : 'Tidak'),

                TextColumn::make('sudah_cair')
                    ->label('Sudah Cair')
                    ->badge()
                    ->colors([
                        'success' => fn($state) => $state === true,
                        'warning' => fn($state) => $state === false,
                    ])
                    ->formatStateUsing(fn($state) => $state ? 'Sudah' : 'Belum'),
            ])
            ->filters([
                SelectFilter::make('tahun')
                    ->options(collect(range(date('Y') - 5, date('Y') + 1))
                        ->mapWithKeys(fn($year) => [$year => $year]))
                    ->label('Tahun'),
            ])
            /**
             * 👉 Bagian ini yang menaruh tombol Import Excel di sebelah tombol Add
             */
            ->headerActions([
                Tables\Actions\Action::make('importExcel')
                    ->label('Import Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->button()
                    ->color('success')
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
                        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\BantuanProvinsiImport, $filePath);
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
            'index' => Pages\ListBantuanProvinsis::route('/'),
            'create' => Pages\CreateBantuanProvinsi::route('/create'),
            'edit' => Pages\EditBantuanProvinsi::route('/{record}/edit'),
        ];
    }
}
