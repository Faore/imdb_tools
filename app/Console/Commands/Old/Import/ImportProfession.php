<?php

namespace App\Console\Commands\Old\Import;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
class ImportProfession extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'old_import:profession';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import professions into database';

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
        $file= file("data/IMDB-AWS/Person/data.tsv");
        $row = 0;
        $pendingInserts = [];
        $professions = [];
        foreach ($file as $line){
            $line = trim($line);
            $array = explode("\t", $line);
            if ($row != 0) {
                $array = explode(",", $array[4]);
                foreach ($array as $prof) {
                     $prof = trim(str_replace('"', '', $prof));
                     if($prof == '') {
                         continue;
                     }
                    if (!in_array($prof, $professions)) {
                        $professions[] = $prof;
                        $pendingInserts[] = [
                            'Name' => $prof,
                        ];
                    }
                }
                if($row % 1000 == 0 && $row != 0) {
                    DB::table('Profession')->insert($pendingInserts);
                    $pendingInserts = [];
                    $this->info("Processed $row rows.");
                }
            }
            $row++;
        }
        if(count($pendingInserts) > 0) {
            DB::table('Profession')->insert($pendingInserts);
            $pendingInserts = [];
            $this->info("Processes $row rows.");
        }
    }
}
