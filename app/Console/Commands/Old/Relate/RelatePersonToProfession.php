<?php

namespace App\Console\Commands\Old\Relate;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RelatePersonToProfession extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'old_relate:person_profession';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Relates people to professions.';

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
        $file = fopen("data/IMDB-AWS/Person/data.tsv", 'r');
        //Eat the first line
        fgets($file);
        //Grab all people in the database.
        $currentSpot = 0;
        $professions = DB::table('profession')->get();
        $num_professions = count($professions);
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('person')->orderBy('id')->chunk(1000, function($dbpeople) use (&$currentSpot, &$file, $professions, $num_professions) {
            $pendingInserts = [];
            $count_people = count($dbpeople);
            for($i = 0; $i < $count_people; $i++ ) {
                while(true) {
                    $current_person = explode("\t", trim(fgets($file)));
                    try {
                        $current_person[4] = explode(",", trim($current_person[4]));
                    } catch(\Exception $e) {
                        continue;
                    }
                    break;
                }

                if($dbpeople[$i]->Name != $current_person[1]) {
                    $this->info("$currentSpot Fuck. ($dbpeople[$i]->Name, " . $current_person[1] . ")");
                    dd();
                } else {
                    $count_person_professions = count($current_person[4]);
                    for($p = 0; $p < $count_person_professions; $p++) {
                        $person_profession = trim(str_replace('"', '', $current_person[4][$p]));
                        if($person_profession == '') {
                            continue;
                        }

                        for($q = 0; $q < $num_professions; $q++) {
                            if($professions[$q]->Name == $person_profession) {
                                $pendingInserts[] = [
                                    'Person_id' => $dbpeople[$i]->id,
                                    'Profession_id' => $professions[$q]->id
                                ];
                            }
                        }
                    }
                }
                $currentSpot++;
            }
            DB::table('person_has_profession')->insert($pendingInserts);
            $this->info("Processed $currentSpot");
        });
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
