<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportMovies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:movie';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports movies into the database.';

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

        $file = file("data/IMDB-AWS/Movie,Show,Episode/data.tsv");
        $row = 0;

        foreach ($file as $line){
            $line = trim($line);
            $array = explode("\t", $line);

            if ($row != 0) {
                if ($array[1] == 'movie' || $array[1] == 'short') {
                    $Type = $array[1];
                    $Title = $array[2];
                    $Year = $array[5];
                    $Runtime = $array[7];
                    $AdultContent = $array[4];
                    if ($Runtime == '\N') {
                        $Runtime = null;
                    }

                    DB::table('movie')->insert([
                        'Title' => $Title,
                        'Year' => $Year,
                        'Runtime' => $Runtime,
                        'Type' => $Type,
                        'AdultContent' => $AdultContent,
                    ]);
                }
                if ($row % 100 == 0) {
                    $this->info("Inserted $row rows.");
                }
            }

            $row++;
        }
    }
}
