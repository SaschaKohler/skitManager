<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

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
                    $j = 0;
                    //      dd($importData_arr);

                    foreach ($importData_arr as $importData) {

                        $role_id = '3';
                        $manager = '';
                        $title2 = '';
                        $name2 = '';
                        $uident = '';
                        $email1 = '';

                        $name1 = utf8_encode($importData[4]); //Get user names
                        $street = utf8_encode($importData[6]);
                        $title1 = $importData[3];
                        $zip = utf8_encode($importData[8]);
                        $city = utf8_encode($importData[9]);
                        $email = $importData[55];
                        $uuid = $importData[0];
                        if ($importData[10] != '')
                            $phone1 = $importData[10];
                        else $phone1 = 123456789;

                        $phone2 = $importData[12];
                        $phone3 = $importData[82];

                        if ($importData[27] == 'PERSONAL')
                            $role_id = 2;
                        elseif ($importData[27] == 'KUNDE')
                            $role_id = 3;
                        elseif ($importData[27] == 'HÄNDLER')
                            $role_id = 4;

                        if ($importData[3] == 'Firma' || $importData[50] != '') {
                            $uident = $importData[50];
                            $role_id = 3;
                            $email1 = $importData[40];
                            $phone1 = $importData[10];
                            $name2 = utf8_encode($importData[5]);
                            $title2 = $importData[16];
                            $manager = utf8_encode($importData[17]);
                            //    $name1 = utf8_encode($importData[4]);
                        } else if ($importData[3] == 'Gemeinde') {
                            $role_id = 3;
                            $email1 = $importData[40];
                            $phone1 = $importData[10];
                            $title2 = $importData[16];
                            $manager = utf8_encode($importData[17]);
                        }


                        $j++;
                        try {
                            DB::beginTransaction();
                            User::create([
                                'uuid' => $uuid,
                                'street' => $street,
                                'zip' => $zip,
                                'city' => $city,
                                'phone1' => $phone1,
                                'phone2' => $phone2,
                                'phone3' => $phone3,
                                'title1' => $title1,
                                'email' => $email,
                                'email1' => $email1,
                                'role_id' => $role_id,
                                'uident' => $uident,
                                'manager' => $manager,
                                'title2' => $title2,
                                'name1' => $name1,
                                'name2' => $name2
                            ]);
                            DB::commit();
                        } catch (\Exception $e) {
                            DB::rollBack();
                        }
                    }
                    $this->notify('success', 'CSV imported');

                }


                )
        ];
    }
}
