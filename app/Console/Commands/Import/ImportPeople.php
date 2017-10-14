<?php

namespace App\Console\Commands\Import;

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
        $file = file("data/IMDB-AWS/Person/data.tsv");
        $row = 0;
        $pendingInserts = [];
        foreach ($file as $line) {
            $line = trim($line);
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

                $pendingInserts[] = [
                    'Name' => $name,
                    'BirthDate' => $birthYear,
                    'DeathDate' => $deathYear,
                    'FacebookLikes' => 0
                ];
                
                if($row % 1000 == 0 && $row != 0) {
                    DB::table('Person')->insert($pendingInserts);
                    $pendingInserts = [];
                    $this->info("Inserted $row rows.");
                }
            }
            $row++;
        }
        if(count($pendingInserts) > 0) {
            DB::table('Person')->insert($pendingInserts);
            $pendingInserts = [];
            $this->info("Inserted $row rows.");
        }
    }

}
