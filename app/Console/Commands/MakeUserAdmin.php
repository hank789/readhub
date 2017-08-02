<?php

namespace App\Console\Commands;

use App\AppointeddUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class MakeUserAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:setAdmin {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '设置管理员账户';

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
        $id = $this->argument('id');

        //Make the user an admin
        AppointeddUser::create([
            'user_id'      => $id,
            'appointed_as' => 'administrator',
        ]);
        Cache::forget('general.voten-administrators');
    }
}
