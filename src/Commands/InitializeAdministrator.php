<?php

namespace DavidSchneider\LaravelCrewmate\Commands;

use DavidSchneider\LaravelCrewmate\Crewmate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class InitializeAdministrator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crewmate:init {email} {name=admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize first administrator';

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
     * @return int
     */
    public function handle()
    {
        if(Crewmate::all()->count())
        {
            $this->error("Administrator is already intialized");
        }
        else
        {
            $v = Validator::make([
                'email' => $this->argument('email'),
                'name' => $this->argument('name'),
            ],[
                'email' => 'required|string|email',
                'name' => 'required|string',
            ]);
            $password = Str::random();
            $crewmate = new Crewmate($v->validate());
            $crewmate->name = 'admin';
            $crewmate->password = bcrypt($password);
            $crewmate->save();
            $this->info('Crewmate configured, password is: ' . $password);
        }
        return 0;
    }
}
