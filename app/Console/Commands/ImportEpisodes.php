<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
class ImportEpisodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:episodes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports episodes into database and associates each with parent tvShow.';

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
        $file1 = file("data/IMDB-AWS/Movie,Show,Episode/data.tsv");
        $file2 = file("data/IMDB-AWS/Episodes/data.tsv");
        $row = 0;
        $shows = DB::table('TVShow')->get();
        $pendingInserts = [];

        foreach ($file1 as $line1){
            $line1 = trim($line1);
            $array = explode("\t", $line1);
            if ($row != 0) {
                $this->info("hi $row");
                if ($array[1] == 'tvEpisode') {
                    $Title = $array[2];
                    $Year = $array[5];
                    $AdultContent = $array[4];
                    $Runtime = $array[7];
                    $parent = -1;
                    $season = -1;
                    $episode = -1;
                    if ($Year == '\N') {
                        $Year = null;
                    }
                    if ($Runtime == '\N') {
                        $Runtime = null;
                    }
                    foreach ($file2 as $line2) {
                        $line2 = trim($line2);
                        $array2 = explode("\t", $line2);
                        if ($array[0]==$array2[0]){
                            $this->info("found match for $array[2]");
                            $parent = $array2[1];
                            $season = $array2[2];
                            $episode = $array2[3];
                            foreach ($file1 as $line3) {
                                $line3 = trim($line3);
                                $array3 = explode("\t", $line3);
                                if($parent==$array3[0]){
                                    $this->info("found match for $parent");
                                    $parent = $array3[2];
                                    foreach ($shows as $show) {
                                        if($parent == $show->Title){
                                            $parent = $show->id;
                                            $this->info("found parent for $array[2]");
                                            break;
                                        }
                                    }
                                    break;
                                }
                            }
                            break;
                        }
                    }
                    if($episode=='\N'){
                        $episode = -1;
                    }
                    if($season=='\N'){
                        $season = -1;
                    }
                    if(!is_int($parent)){
                        $parent = -1;
                    }
                    if ($parent != -1 && $episode != -1 && $season != -1 ) {
                        $pendingInserts[] = [
                            'Title' => $Title,
                            'Year' => $Year,
                            'Runtime' => $Runtime,
                            'AdultContent' => $AdultContent,
                            'Season' => $season,
                            'Episode' => $episode,
                            'TVShow_ID' => $parent,
                        ];
                    }

                }
                if($row % 1000 == 0 && $row != 0) {
                    DB::table('Episode')->insert($pendingInserts);
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
