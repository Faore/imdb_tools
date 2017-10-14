<?php

namespace App\Console\Commands\Import;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
class ImportProfession extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:profession';

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
                    $this->info("Inserted $row rows.");
                }
            }
            $row++;
        }
        if(count($pendingInserts) > 0) {
            DB::table('Profession')->insert($pendingInserts);
            $pendingInserts = [];
            $this->info("Inserted $row rows.");
        }
        dd($professions);
    }
}
