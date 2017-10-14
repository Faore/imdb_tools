<?php

namespace App\Console\Commands\Import;

use Illuminate\Console\Command;
class ImportGenre extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:genre';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports genres into database.';

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

        $file= file("data/IMDB-AWS/Movie,Show,Episode/data.tsv");
        $row = 0;
        $pendingInserts = [];
        $genres = [];
        foreach ($file as $line){
            $line = trim($line);
            $array = explode("\t", $line);
            if ($row != 0) {
                $array = explode(",", $array[8]);
                foreach ($array as $genre) {
                    if (!in_array($genre, $genres)) {
                        $genres[] = $genre;
                        $pendingInserts[] = [
                            'Name' => $genre
                        ];
                    }
                }
                if($row % 1000 == 0 && $row != 0) {
                    \DB::table('Genre')->insert($pendingInserts);
                    $pendingInserts = [];
                    $this->info("Inserted $row rows.");
                }
            }
            $row++;
        }
        if(count($pendingInserts) > 0) {
            \DB::table('Genre')->insert($pendingInserts);
            $pendingInserts = [];
            $this->info("Inserted $row rows.");
        }
    }
}
