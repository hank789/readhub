<?php

namespace App\Console\Commands;

use App\Events\SubmissionWasDeleted;
use App\Submission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class DelSubmission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'del:submission {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '清除文章url缓存';

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

        $submission = Submission::findOrFail($id);
        event(new SubmissionWasDeleted($submission,true));
        $data = $submission->data;
        Redis::connection()->hdel('voten:submission:url',$data['url']);
        $submission->forceDelete();
    }
}
