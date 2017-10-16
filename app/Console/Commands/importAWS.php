<?php

namespace App\Console\Commands;

use App\Helper\DataReader;
use Illuminate\Console\Command;

class importAWS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:aws';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merges AWS data with existing IMDB 1000 and 5000 data.';

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
        /*
         * title.basics structure:
         *
         * "headers" => array:9 [
         *    0 => "tconst"
         *    1 => "titleType"
         *    2 => "primaryTitle"
         *    3 => "originalTitle"
         *    4 => "isAdult"
         *    5 => "startYear"
         *    6 => "endYear"
         *    7 => "runtimeMinutes"
         *    8 => "genres"
         * ]
         */

        //Create an associative array of movies and their title_basics ids.
        $title_basics = Datareader::openDataFile(\Config::get('data.AWS.title_basics'));
        $existing_movies = \DB::table('Movie')->get();
        $movies = [];
        $count = 0;
        while(!DataReader::endOfFile($title_basics)) {
            $row = DataReader::getNextRow($title_basics);
            $movie_match = $existing_movies->where('Title', $row['primaryTitle'])->where('Year', $row['startYear']);
            if($movie_match->isNotEmpty()) {
                //Got a movie match.
                $movies[$row['tconst']] = $movie_match->first();
                if($row['isAdult'] != null) {
                    \DB::table('Movie')->where('id', $movie_match->first()->id)->update(['AdultContent' => $row['isAdult']]);
                }
            }
            $count++;
            if($count % 100 == 0) {
                $this->info("Processed $count.");
            }
        }

    }
}
