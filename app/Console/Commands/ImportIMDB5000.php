<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \App\Helper\DataReader;
use Illuminate\Support\Facades\DB;

class ImportIMDB5000 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:5000';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import movie data from movie_metadata.';

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
        $config = \Config::get('data.5000');
        $file = Datareader::openDataFile($config);
        //5000 contains 0:color 1:director_name 2:num_critics_for_reviews 3:duration 4:director_facebook_lkes 5:actor_3_facebook_likes 6:actor_2_name 7:actor_1_facebook_likes
        // 8:gross 9:genres 10:actor_1_name 11:movie_title 12:num_voted_users 13:cast_total_facebook_likes 14:actor_3_name 15:facenumber_in_poster 16:plot_keywords
        // 17:movie_imdb_link 18:num_user_for_reviews 19:language 20:country 21:content_rating 22:budget 23:title_year 24:actor_2_facebook_likes 25:imdb_score 26:aspect_ratio
        // 27:movie_facebook_likes
        while (!DataReader::endOfFile($file)) {
            $row = DataReader::getNextRow($file);
            $director = $row['director_name'];
            $genres = $row['genres'];
            $actor1 = $row['actor_1_name'];
            $actor2 = $row['actor_2_name'];
            $actor3 = $row['actor_3_name'];

            if($row['title_year']==''){
                continue;
            }

            DB::table('Movie')->insert([
                'Title' => $row['movie_title'],
                'Year' => $row['title_year'],
                'GrossProfit' => $row['gross'],
                'Color' => $row['color'] == 'Color' ? 1 : 0,
                'AspectRatio' => $row['aspect_ratio'],
                'FacebookLikes' => $row['movie_facebook_likes'],
                'IMDBScore' => $row['imdb_score'],
                'Runtime' => $row['duration'],
                'Type' => 'Movie',
                'Budget' => $row['budget'],
                'Language' => $row['language'],
                'Country' => $row['country'],
                'ContentRating' => $row['content_rating'],
            ]);
            $movieId = DB::table('Movie')->where('Title', '=', $row['movie_title'])->first()->id;
            $db = DB::table('Person')->where('Name', '=',$director)->get();
            if($director !="") {
                if (count($db) == 0) {
                    DB::table('Person')->insert(['Name' => $director, 'FacebookLikes' => $row['director_facebook_likes']]);
                }
                $personId = DB::table('Person')->where('Name', '=', $director)->first()->id;
                DB::table('person_directs_movie')->insert(['Person_id' => $personId, 'Movie_id' => $movieId]);
            }
            $db = DB::table('Person')->where('Name', '=',$actor1)->get();
            if($actor1 !="") {
                if (count($db) == 0) {
                    DB::table('Person')->insert(['Name' => $actor1, 'FacebookLikes' => $row['actor_1_facebook_likes']]);
                }
                $personId = DB::table('Person')->where('Name', '=', $actor1)->first()->id;
                DB::table('person_acts_for_movie')->insert(['Person_id' => $personId, 'Movie_id' => $movieId]);
            }
            $db = DB::table('Person')->where('Name', '=',$actor2)->get();
            if($actor2 !="") {
                if (count($db) == 0) {
                    DB::table('Person')->insert(['Name' => $actor2, 'FacebookLikes' => $row['actor_2_facebook_likes']]);
                }
                $personId = DB::table('Person')->where('Name', '=', $actor2)->first()->id;
                DB::table('person_acts_for_movie')->insert(['Person_id' => $personId, 'Movie_id' => $movieId]);
            }
            $db = DB::table('Person')->where('Name', '=',$actor3)->get();
            if($actor3 !="") {
                if (count($db) == 0) {
                    DB::table('Person')->insert(['Name' => $actor3, 'FacebookLikes' => $row['actor_3_facebook_likes']]);
                }
                $personId = DB::table('Person')->where('Name', '=', $actor3)->first()->id;
                DB::table('person_acts_for_movie')->insert(['Person_id' => $personId, 'Movie_id' => $movieId]);
            }
        }
    }
}
