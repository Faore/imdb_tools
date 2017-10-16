<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \App\Helper\DataReader;
use Illuminate\Support\Facades\DB;

class ImportIMDB1000 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:1000';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update movies with info from 1000';

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
        //1000 contains 0:rank 1:Title(R) 2:Genre(R) 3:Description 4:Director(R) 5:Actors 6:Year(R) 7:Runtime (Minutes)(R) 8:Rating 9:Votes(R) 10:Revenue (Millions) 11:Metascore
        $config = \Config::get('data.1000');
        $file = Datareader::openDataFile($config);
        while (!DataReader::endOfFile($file)) {
            $row = DataReader::getNextRow($file);
            $movieId = 0;
            $movieId = DB::table('Movie')->where('Title', '=', $row['Title'])->where('Year', '=', $row['Year'])->where('Rank', '=', null)->first()->id;//find corresponding movie added from 5000 which has not been updated with 1000 data
            if($movieId!=null){
                $this->info("updating movie with id $movieId");
                DB::table('Movie')->where('id', '=', $movieId)->update([
                    'Rank' => $row['Rank'],
                    'Rating'=> $row['Rating'],
                    'Votes'=> $row['Votes'],
                    'Metascore'=>$row['Metascore'],
                    'Revenue'=>$row['Revenue (Millions)'],
                ]);
                break;
            }
        }
    }
}
