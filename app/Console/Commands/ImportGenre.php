<?php

namespace App\Console\Commands;

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

        $file_handle = fopen("data/IMDB-AWS/Movie,Show,Episode/data.tsv", "r");
        $row = 0;
        $genres = [];
        while (!feof($file_handle)) {
            $line = trim(fgets($file_handle));
            $array = explode("\t", $line);
            if ($row != 0) {
                $array = explode(",", $array[8]);
                foreach ($array as $genre) {
                    if (!in_array($genre, $genres)) {
                        $genres[] = $genre;
                        //DB::table('genre')->insert([
                        //    'Name' => $genre,
                        //]);
                    }
                }
                if($row % 100 == 0) {
                    $this->info("Inserted $row rows.");
                }
            }
            $row++;
            fclose($file_handle);
        }
        dd($genres);
    }
}
