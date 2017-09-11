<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Category;
use App\Events\SubmissionWasCreated;
use App\Events\SubmissionWasDeleted;
use App\Filters;
use App\Photo;
use App\PhotoTools;
use App\Submission;
use App\Traits\CachableCategory;
use App\Traits\CachableSubmission;
use App\Traits\CachableUser;
use App\Traits\Submit;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class SubmissionController extends Controller
{
    use Filters, PhotoTools, CachableUser, CachableSubmission, CachableCategory, Submit;

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['getBySlug', 'getById', 'getPhotos', 'show']]);
    }

    /**
     * shows the submission page to guests.
     *
     * @param string $category
     * @param string $slug
     *
     * @return view
     */
    public function show($category, $slug)
    {
        if (Auth::check()) {
            return view('welcome');
        }

        $submission = $this->getSubmissionBySlug($slug);
        $category = $this->getCategoryByName($submission->category_name);
        $category->stats = $this->categoryStats($category->id);
        $submission->category = $category;

        return view('submission.show', compact('submission'));
    }

    /**
     * Stores the submitted submission into database. There are 3 types of submissions:
     * 1.text, 2.link and 3.img. 4.gif Different actions are required for different
     * types. After storing the submission, redirects to the submission page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->isShadowBanned()) {
            return response('很抱歉，您的账户已被锁定，请联系管理员解锁', 500);
        }

        // first make sure user is allowed to submit to this category. (not banned from it)
        if ($this->isUserBanned($user->id, $request->name)) {
            return response('您被禁止向频道 #'.$request->name.' 提交文章。 您可以向频道 #'.$request->name.' 的管理员申诉.', 500);
        }

        if ($this->tooEarlyToCreate()) {
            return response("您提交的频率太快了，请稍后再试", 500);
        }

        if ($request->type == 'link') {
            $this->validate($request, [
                'url'   => 'required|url',
                'title' => 'required|between:7,150',
                'name'  => 'required|exists:categories',
            ]);

            // check if it's in the blocked domains list
            if ($this->isDomainBlocked($request->url, $request->name)) {
                return response("您提交的网站域名被该频道禁止了，请换个网址。", 500);
            }

            //检查url是否重复
            $exist_submission_id = Redis::connection()->hget('voten:submission:url',$request->url);
            if ($exist_submission_id){
                $exist_submission = Submission::find($exist_submission_id);
                if (!$exist_submission) {
                    return response("您提交的网址已经存在", 500);
                }
                $exist_submission_url = '/c/'.$exist_submission->category_name.'/'.$exist_submission->slug;
                return response("您提交的网址已经存在，<a href='$exist_submission_url'>点击查看</a>", 500);
            }
            try {
                //$data = $this->linkSubmission($request);
                $data = [
                    'url'           => $request->url,
                    'title'         => $request->title,
                    'description'   => null,
                    'type'          => 'link',
                    'embed'         => null,
                    'img'           => null,
                    'thumbnail'     => null,
                    'providerName'  => null,
                    'publishedTime' => null,
                    'domain'        => domain($request->url),
                ];
                Redis::connection()->hset('voten:submission:url',$request->url,1);
            } catch (\Exception $e) {
                $data = [
                    'url'           => $request->url,
                    'title'         => $request->title,
                    'description'   => null,
                    'type'          => 'link',
                    'embed'         => null,
                    'img'           => null,
                    'thumbnail'     => null,
                    'providerName'  => null,
                    'publishedTime' => null,
                    'domain'        => domain($request->url),
                ];
            }
        }

        if ($request->type == 'img') {
            $this->validate($request, [
                'title'  => 'required|between:7,150',
                'photos' => 'required',
                'name'   => 'required|exists:categories',
            ]);

            $data = $this->imgSubmission($request);
        }

        if ($request->type == 'gif') {
            $this->validate($request, [
                'title' => 'required|between:7,150',
                'gif'   => 'required|mimes:gif|max:40960',
                'name'  => 'required|exists:categories',
            ]);

            try {
                $data = $this->gifSubmission($request);
            } catch (\Exception $exception) {
                app('sentry')->captureException($exception);

                return response("We couldn't process this GIF, please try another one. ", 500);
            }
        }

        if ($request->type == 'text') {
            $this->validate($request, [
                'title' => 'required|between:7,150',
                'type'  => 'required|in:link,img,text',
                'name'  => 'required|exists:categories',
            ]);

            $data = $this->textSubmission($request);
        }

        $category = Category::where('name', $request->name)->select('id', 'nsfw')->firstOrFail();

        try {
            $submission = Submission::create([
                'title'         => $request->title,
                'slug'          => $this->slug($request->title),
                'type'          => $request->type,
                'category_name' => $request->name,
                'category_id'   => $category->id,
                'nsfw'          => $category->nsfw,
                'rate'          => firstRate(),
                'user_id'       => $user->id,
                'data'          => $data,
            ]);
            if ($request->type == 'link') {
                Redis::connection()->hset('voten:submission:url',$request->url, $submission->id);
            }
            event(new SubmissionWasCreated($submission));
        } catch (\Exception $exception) {
            app('sentry')->captureException($exception);

            return response('系统繁忙，请稍后再试', 500);
        }

        // Update the submission_id field in photos (We just found access to the submission_id)
        if ($request->type == 'img') {
            DB::table('photos')->whereIn('id', $request->input('photos'))->update(['submission_id' => $submission->id]);
        }

        try {
            $this->firstVote($user, $submission->id);
        } catch (\Exception $exception) {
            app('sentry')->captureException($exception);
        }

        return $submission;
    }

    /**
     * Fetches the title from an external URL.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string title
     */
    public function getTitleAPI(Request $request)
    {
        $this->validate($request, [
            'url' => 'required|url',
        ]);

        return $this->getTitle($request->url);
    }

    /**
     * hides the submission so the user won't see it (=== block).
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function hide(Request $request)
    {
        $this->validate($request, [
            'submission_id' => 'required|integer',
        ]);

        $user = Auth::user();

        $user->hides()->attach($request->submission_id);

        // update the cach record for hiddenSubmissions:
        $this->updateHiddenSubmissions($user->id, $request->submission_id);

        return response('文章已经进入到了您的隐藏列表', 200);
    }

    /**
     * Returns the submission.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function getBySlug(Request $request)
    {
        $this->validate($request, [
            'slug' => 'required',
        ]);

        $submission = $this->getSubmissionBySlug($request->slug);

        $category = $this->getCategoryByName($submission->category_name);

        $category->stats = $this->categoryStats($category->id);

        $submission->category = $category;

        return $submission;
    }

    /**
     * Returns the submission (even if it's been soft-deleted).
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function getById(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);

        return $this->getSubmissionById($request->id);
    }

    /**
     * Returns all the uploaded photos for a specific submission.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPhotos(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);

        return Photo::where('submission_id', $request->id)->get();
    }

    /**
     * Destroys the submisison record from the database.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function destroy(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);

        $submission = Submission::findOrFail($request->id);

        abort_unless($this->mustBeOwner($submission), 403);

        event(new SubmissionWasDeleted($submission));
        if ($submission->type == 'link') {
            Redis::connection()->hdel('voten:submission:url',$submission->data['url']);
        }
        $submission->forceDelete();

        return response('Submission was successfully deleted', 200);
    }

    /**
     * Removes the thumbnail.
     *
     * @return response
     */
    public function removeThumbnail(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);

        $submission = $this->getSubmissionById($request->id);

        abort_unless($this->mustBeOwner($submission), 403);

        $submission->update([
            'data' => [
                'url'           => $submission->data['url'],
                'title'         => $submission->data['title'],
                'description'   => $submission->data['description'],
                'type'          => $submission->data['type'],
                'embed'         => $submission->data['embed'],
                'img'           => null,
                'thumbnail'     => null,
                'providerName'  => $submission->data['providerName'],
                'publishedTime' => $submission->data['publishedTime'],
                'domain'        => $submission->data['domain'] ?? domain($submission->data['url']),
            ],
        ]);

        $this->putSubmissionInTheCache($submission);

        return response('thumbnail removed', 200);
    }

    /**
     * Patches the Text Submission.
     *
     * @return reponse
     */
    public function patchTextSubmission(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);

        $submission = Submission::findOrFail($request->id);

        abort_unless($this->mustBeOwner($submission), 403);
        // make sure submission's type is "text" (at the moment submission editing is only available for text submissions)
        abort_unless($submission->type == 'text', 403);

        $submission->update([
            'data' => array_only($request->all(), ['text']),
        ]);

        // so next time it'll fetch the updated copy
        $this->removeSubmissionFromCache($submission);

        return response('Text Submission has been updated. ', 200);
    }

    //推荐文章
    public function recommendSubmission(Request $request){
        $this->validate($request, [
            'submission_id' => 'required|integer',
        ]);
        $submission = Submission::findOrFail($request->submission_id);
        abort_unless($this->mustBeVotenAdministrator(), 403);
        $msg = '推荐成功';
        if ($submission->recommend_status == 0){
            $submission->recommend_status = 1;
            $msg = '推荐成功';
            $submission->save();
        }

        return response($msg, 200);
    }

    /**
     * Whether or the user is breaking the time limit for creating another submission.
     *
     * @return mixed
     */
    protected function tooEarlyToCreate()
    {
        // exclude white-listed users form this checking
        if ($this->mustBeWhitelisted()) {
            return false;
        }

        $submissions_count = Activity::where([
            ['subject_type', 'App\Submission'],
            ['user_id', Auth::user()->id],
            ['name', 'created_submission'],
            ['created_at', '>=', Carbon::now()->subMinute()],
        ])->get()->count();

        return $submissions_count >= 2 ? true : false;
    }
}
