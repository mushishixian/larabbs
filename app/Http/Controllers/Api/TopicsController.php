<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\TopicRequest;
use App\Models\Topic;
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

}
