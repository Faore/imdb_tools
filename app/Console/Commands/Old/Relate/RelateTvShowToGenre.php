<?php

namespace App\Console\Commands\Old\Relate;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class RelateTvShowToGenre extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'old_relate:tvshow_genre';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attaches genres to tvshows.';

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
        //Title 2, Genres 8
        $file = fopen("data/IMDB-AWS/Movie,Show,Episode/data.tsv", 'r');
        $movies = [];

        while(!feof($file)) {
            $line = explode("\t", trim(fgets($file)));
            try {
                $line[8] = explode(",", trim($line[8]));
            } catch(\Exception $e) {
                continue;
            }
            if($line[1] == 'tvSeries') {
                $movies[] = $line;
            }
        }
        unset($file);
        //Grab all movies in the database:
        $currentSpot = 0;
        $genres = DB::table('genre')->get();
        DB::table('TVShow')->orderBy('id')->chunk(1000, function($dbmovies) use (&$currentSpot, $movies, $genres) {
            $pendingInserts = [];
            foreach($dbmovies as $dbmovie) {
                if($dbmovie->Title != $movies[$currentSpot][2]) {
                    $this->info("$currentSpot Fuck. ($dbmovie->Title, " . $movies[$currentSpot][2] . ")");
                    dd();
                } else {
                    //$this->info("$currentSpot ok.");
                    foreach($movies[$currentSpot][8] as $moviegenre) {
                        foreach($genres as $genre) {
                            if($genre->Name == $moviegenre) {
                                $pendingInserts[] = [
                                    'TVShow_id' => $dbmovie->id,
                                    'Genre_id' => $genre->id
                                ];
                            }
                    }
                    }

                }
                $currentSpot++;
            }
            DB::table('tvshow_has_genre')->insert($pendingInserts);
            $this->info("Processed $currentSpot");
        });
    }
}
