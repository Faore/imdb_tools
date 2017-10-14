<?php

namespace App\Console\Commands\Relate;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class RelateMovieToGenre extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'relate:movie_genre';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attaches genres to movies.';

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
            if($line[1] == 'movie' || $line[1] == 'short' || $line[1] == 'tvMovie' || $line[1] == 'video') {
                $movies[] = $line;
            }
        }
        unset($file);
        //Grab all movies in the database:
        $currentSpot = 0;
        $genres = DB::table('genre')->get();
        DB::table('movie')->orderBy('id')->chunk(1000, function($dbmovies) use (&$currentSpot, $movies, $genres) {
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
                                    'Movie_id' => $dbmovie->id,
                                    'Genre_id' => $genre->id
                                ];
                            }
                    }
                    }

                }
                $currentSpot++;
            }
            DB::table('movie_has_genre')->insert($pendingInserts);
            $this->info("Processed $currentSpot");
        });
    }
}
