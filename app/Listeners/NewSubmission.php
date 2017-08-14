<?php

namespace App\Listeners;

use App\Events\SubmissionWasCreated;
use App\Traits\CachableCategory;
use App\Traits\CachableSubmission;
use App\Traits\CachableUser;

class NewSubmission
{
    use CachableUser, CachableSubmission, CachableCategory;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param SubmissionWasCreated $event
     *
     * @return void
     */
    public function handle(SubmissionWasCreated $event)
    {
        $this->updateUserSubmissionsCount($event->submission->user_id);

        $this->updateCategorySubmissionsCount($event->submission->category_id);

        $slackFields = [];
        foreach ($event->submission->data as $field=>$value){
            if ($value){
                $slackFields[] = [
                    'title' => $field,
                    'value' => $value
                ];
            }
        }
        slackNotification($event->submission->owner->username,'新文章提交:'.$event->submission->title,$slackFields,config('app.url').'/c/'.$event->submission->category_name.'/'.$event->submission->slug);
    }
}
