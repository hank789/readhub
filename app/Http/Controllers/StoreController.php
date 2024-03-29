<?php

namespace App\Http\Controllers;

use App\Submission;
use App\Traits\CachableCategory;
use App\Traits\CachableUser;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class StoreController extends Controller
{
    use CachableUser, CachableCategory;

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['sidebarCategories']]);
    }

    /**
     * Returns all the neccessary information for filling the Store. To reduce the number
     * of database requests, we're going to use HTML5's local-storage. Meaning that only
     * users with new browsers or when doing clear-cach a request will be sent here.
     *
     * @return collection
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $recommend_readhub_id = Redis::connection()->get('recommend_readhub_article');
        if ($recommend_readhub_id) {
            $recommend_read = Submission::find($recommend_readhub_id)->toArray();
        } else {
            $recommend_read = Submission::where('recommend_status',2)->orderBy('recommend_sort','desc')->first()->toArray();
        }

        $recommend_read['img_url'] = $recommend_read['data']['img']??'';
        $recommend_read['publish_at'] = date('Y/m/d H:i',strtotime($recommend_read['created_at']));
        $recommend_read['view_url'] = $recommend_read['data']['url'];
        $recommend_read['comment_url'] = '/c/'.($recommend_read['category_id']).'/'.$recommend_read['slug'];

        return collect([
            'submissionUpvotes'           => $this->submissionUpvotes(), // cached
            'submissionDownvotes'         => $this->submissionDownvotes(), // cached
            'commentUpvotes'              => $this->commentUpvotes(), // cached
            'commentDownvotes'            => $this->commentDownvotes(), // cached
            'bookmarkedSubmissions'       => $this->bookmarkedSubmissions(), // cached
            'bookmarkedComments'          => $this->bookmarkedComments(), // cached
            'bookmarkedCategories'        => $this->bookmarkedCategories(), // cached
            'bookmarkedUsers'             => $this->bookmarkedUsers(), // cached
            'subscribedCategories'        => $this->subscribedCategories($request->sidebar_filter),
            'moderatingCategories'        => $this->moderatingCategories(),
            'moderatingCategoriesRecords' => $this->moderatingCategoriesRecords(),
            'blockedUsers'                => $this->blockedUsers(), // cached
            'recommendRead'               => $recommend_read
        ]);
    }

    protected function moderatingCategoriesRecords()
    {
        return DB::table('roles')->where('user_id', Auth::id())->get();
    }

    // Returnes Auth user's moderated categories
    protected function moderatingCategories()
    {
        return Auth::user()->categoryRoles->unique('name');
    }

    // Returns Auth user's (submission) upvote records
    protected function submissionUpvotes()
    {
        return $this->submissionUpvotesIds();
    }

    // Returns Auth user's (submission) downvote records
    protected function submissionDownvotes()
    {
        return $this->submissionDownvotesIds();
    }

    // Returns Auth user's (submission) upvote records
    protected function commentUpvotes()
    {
        return $this->commentUpvotesIds();
    }

    // Returns Auth user's (submission) downvote records
    protected function commentDownvotes()
    {
        return $this->commentDownvotesIds();
    }

    // returns subscriptions of Auth user
    protected function subscribedCategories($filter = 'subscribed-channels')
    {
        if (!Auth::check()) {
            return $this->getDefaultCategoryRecords();
        }

        if ($filter == 'moderating-channels') {
            return Auth::user()->categoryRoles;
        } elseif ($filter == 'bookmarked-channels') {
            return Auth::user()->bookmarkedCategories;
        }

        // $filter == "subscribed channels"
        return Auth::user()->subscriptions;
    }

    /**
     * returns categoeis for sidebar.
     *
     * @return collection
     */
    public function sidebarCategories(Request $request)
    {
        return $this->subscribedCategories($request->sidebar_filter);
    }
}
