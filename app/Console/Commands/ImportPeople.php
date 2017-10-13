<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportPeople extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:people';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports people into the database.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        //dd(DB::table('Person')->get());

        $file_handle = fopen("data/IMDB-AWS/Person/data.tsv", "r");
        $row = 0;
        while (!feof($file_handle)) {
            $line = trim(fgets($file_handle));
            $array = explode("\t", $line);
            if($row != 0) {
                $name = $array[1];
                $birthYear = $array[2];
                $deathYear = $array[3];
                if($birthYear == '\N') {
                    $birthYear = null;
                }
                if($deathYear == '\N') {
                    $deathYear = null;
                }
                DB::table('Person')->insert([
                    'Name' => $name,
                    'BirthDate' => $birthYear,
                    'DeathDate' => $deathYear,
                    'FacebookLikes' => 0
                ]);
                if($row % 100 == 0) {
                    $this->info("Inserted $row rows.");
                }
            }
            $row++;
        }
        fclose($file_handle);
    }
}
