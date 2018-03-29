<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\TopicRequest;
use App\Models\Topic;
use App\Models\User;
use App\Transformers\TopicTransformer;
use Illuminate\Http\Request;

class TopicsController extends Controller
{
    //
    public function index(TopicRequest $request, Topic $topic)
    {
        $query = $topic->query();

        if ($categoryId = $request->category_id) {
            $query->where('category_id', $categoryId);
        }
        //        if ($name = $request->name) {
        //            $query->where('name', 'like', $name);
        //        }
        if ($title = $request->title) {
            $query->where('title', 'like', "%$title%");
        }

        $topics = $query->paginate($request->page_size);

        return $this->response->paginator($topics, new TopicTransformer());
    }

    public function show(Topic $topic)
    {
        return $this->response->item($topic, new TopicTransformer());
    }

    public function store(TopicRequest $request, Topic $topic)
    {
        $topic->fill($request->all());
        $topic->user_id = $this->user()->id;
        $topic->save();

        return $this->response->item($topic, new TopicTransformer())->setStatusCode(201);
    }

    public function update(TopicRequest $request, Topic $topic)
    {
        $this->authorize('update', $topic);
        $topic->update($request->all());

        return $this->response->item($topic, new TopicTransformer());
    }

    public function delete(Topic $topic)
    {
        $this->authorize('update', $topic);

        $topic->delete();

        return $this->response->noContent();
    }

    public function userIndex(User $user, Request $request, Topic $topic)
    {
        $topics = $user->topics()->recent()->paginate(10);

        return $this->response->paginator($topics, new TopicTransformer());
    }

}
