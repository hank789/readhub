<?php

namespace App\Http\Controllers;

use App\Traits\CachableCategory;
use App\Traits\CachableSubmission;
use App\Traits\CachableUser;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class ApiController extends Controller
{
    use CachableUser, CachableSubmission, CachableCategory, AuthenticatesUsers;


    public function doRequest(Request $request)
    {
        \Log::info('test',$request->all());
        $uuid = $request->input('uuid');
        if ($uuid) {
            $user = User::where('uuid',$uuid)->first();
            if ($user) {
                $this->guard()->loginUsingId($user->id,true);
            }
        } else {
            return abort(404);
        }
        if (!Auth::check()) {
            return abort(404);
        }

        $api_name = $request->input('api_name');
        switch ($api_name){
            case 'upvote-submission':
                app('App\Http\Controllers\SubmissionVotesController')->upVote($request);
                break;
            case 'downvote-submission':
                app('App\Http\Controllers\SubmissionVotesController')->downVote($request);
                break;
            case 'bookmark-submission':
                app('App\Http\Controllers\BookmarksController')->bookmarkSubmission($request);
                break;
            default:
                break;
        }

        return response('success ', 200);
    }
}
