<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportIMDB1000 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    }
}
