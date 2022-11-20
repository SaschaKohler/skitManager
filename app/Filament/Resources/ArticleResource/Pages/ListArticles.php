<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Models\Article;
use App\Models\User;
use App\Models\ZipCode;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListArticles extends ListRecords
{
    protected static string $resource = ArticleResource::class;

    protected int $counter = 0;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('importCSV')
                ->label('import CSV')
                ->form([
                    FileUpload::make('import')

                ])
                ->action(function (array $data) {

                    $filepath = public_path("storage" . "/" . $data['import']);

                    $file = fopen($filepath, "r");

                    $importData_arr = array(); // Read through the file and store the contents as an array
                    $i = 0;
//Read the contents of the uploaded file
                    while (($filedata = fgetcsv($file, 1000, ";")) !== FALSE) {
                        $num = count($filedata);

// Skip first row (Remove below comment if you want to skip the first row)
                        if ($i == 0) {
                            $i++;
                            continue;
                        }
                        for ($c = 0; $c < $num; $c++) {
                            $importData_arr[$i][] = $filedata[$c];
                        }
                        $i++;
                    }
                    fclose($file); //Close after reading
//        LIEFERANT	MATNR	KZ	SUCH	KURZTEXT	ME	EUMATLPR	EUMATEK	EUMATVK1	EUMATVK2	EUMATVK3	ZEIT	LOHNART	EULOHNSEK	EULOHNS1	EULOHNS2	EULOHNS3	ALTLIEF1	ALTLIEF2	ALTMATNR1	ALTMATNR2	CUKENNZ	CUGEWICHT	VERPEINH	MEJEVERP	PREISEINH	RABGR	HWG	WG	EANNR	ERLOESKTO	INLAGER	EUMATVK4	EULOHNS4	KALKMODE	FPREIS	SPREIS	GEAENDERT	USER	ZUSRABATT	PJVPEINH	KATALOG	USTSCHL	BESTELL	BESTELLNR	USESNR	PROABRECH	ISSKONTOF	ISUMSATZF	PEINHEIT	ARTGRUPPE	EBAY	KSTELLE	EUMATVK5	EUMATVK6	EUMATVK7	EUMATVK8	EUMATVK9	EUMATVK10	EULOHNS5	EULOHNS6	EULOHNS7	EULOHNS8	EULOHNS9	EULOHNS10	FARTIKEL	SPREISVON	SPREISBIS	ARTKAT	KRABATTGR	CANLAGER	CANRABATT	ZUSATZ_1	PEMENGE	LAGERBESTAND	LOESCHDATE	TREEKEY
//dd($importData_arr);
                    foreach ($importData_arr as $importData) {
                        //    dd($importData);
                        $uuid = $importData[0] ?? null;
                        $search = utf8_encode($importData[4]) ?? null;
                        $short_text = $importData[5] ?? null;
                        $unit = utf8_encode($importData[6]) ?? null;
                        $lpr = floatval(str_replace(',', '.', $importData[7])) ?? null;
                        $ek = floatval(str_replace(',', '.', $importData[8])) ?? null;
                        $vk1 = floatval(str_replace(',', '.', $importData[9])) ?? null;
                        $vk2 = floatval(str_replace(',', '.', $importData[10])) ?? null;
                        $vk3 = floatval(str_replace(',', '.', $importData[11])) ?? null;


//                        $vk1_perc = floatval(100 - (($ek*10 / $vk1*10) * 100)) ?? null;
//                        $vk2_perc = floatval(100 - (($ek*10 / $vk2*10) * 100)) ?? null;
//                        $vk3_perc = floatval(100 - (($ek*10 / $vk3*10) * 100)) ?? null;

//                        dd($uuid,
//                            $search,
//                            $short_text,
//                            $unit,
//                            $lpr,
//                            $ek,
//                            $vk1,
//                            $vk2,
//                            $vk3,
//                            $vk1_perc,
//                            $vk2_perc,
//                            $vk3_perc,
//                        );
                        try {
                            $article_exist = Article::query()
                                ->where('uuid', '=', $uuid)
                                ->count();
                            if (!$article_exist) {
                                DB::beginTransaction();
                                Article::create([
                                    'uuid' => $uuid,
                                    'search' => $search,
                                    'short_text' => $short_text,
                                    'unit' => $unit,
                                    'lpr' => $lpr,
                                    'ek' => $ek,
                                    'vk1' => $vk1,
                                    'vk2' => $vk2,
                                    'vk3' => $vk3,
//                                    'vk1_perc' => $vk1_perc,
//                                    'vk2_perc' => $vk2_perc,
//                                    'vk3_perc' => $vk3_perc,


                                ]);
                                DB::commit();
                                $this->counter++;
                            }

                        } catch (\Exception $e) {
                            // dd($e);
                            DB::rollBack();
                        }
                    }
                    Notification::make()
                        ->title('Import erfolgreich')
                        ->success()
                        ->body("**{$this->counter}** Datensätze importiert")
                        ->send();
                }
                )
        ];
    }
}
