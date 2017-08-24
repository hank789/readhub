<?php

namespace App\Console\Commands;

use App\AppointeddUser;
use App\Traits\CachableCategory;
use App\Traits\CachableUser;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SetDefaultSubscribedCategories extends Command
{
    use CachableUser, CachableCategory;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:setDefaultSubscribedCategories {cid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '设置默认订阅频道';

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
        $id = $this->argument('cid');
        $users = User::all();
        foreach ($users as $user) {
            try {
                $subscriptions = $this->subscriptions($user->id);
                if (in_array($id, $subscriptions)){
                    continue;
                }
                $result = $user->subscriptions()->toggle($id);
            } catch (\Exception $e) {
                continue;
            }

            // subscibed
            if ($result['attached']) {
                $this->updateSubscriptions($user->id, $id, true);

                $this->updateCategorySubscribersCount($id);
            } else {
                // unsubscribed
                $this->updateSubscriptions($user->id, $id, false);

                $this->updateCategorySubscribersCount($id, -1);
            }

        }

    }
}
