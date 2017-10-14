<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
class ImportTVShow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:tvShow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports TV Shows into databbase.';

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
        $pendingInserts = [];

        foreach ($file as $line){
            $line = trim($line);
            $array = explode("\t", $line);
            if ($row != 0) {
                if ($array[1] == 'tvSeries'||$array[1] == 'tvMiniSeries') {
                    $Title = $array[2];
                    $StartYear = $array[5];
                    $EndYear = $array[6];
                    if ($StartYear == '\N') {
                        $StartYear = null;
                    }
                    if ($EndYear == '\N') {
                        $EndYear = null;
                    }
                    $pendingInserts[] = [
                        'Title' => $Title,
                        'StartYear' => $StartYear,
                        'EndYear' => $EndYear,
                    ];
                }
                if($row % 1000 == 0 && $row != 0) {
                    DB::table('TVShow')->insert($pendingInserts);
                    $pendingInserts = [];
                    $this->info("Inserted $row rows.");
                }
            }
            $row++;
        }
        if(count($pendingInserts) > 0) {
            DB::table('TVShow')->insert($pendingInserts);
            $pendingInserts = [];
            $this->info("Inserted $row rows.");
        }
    }
}
