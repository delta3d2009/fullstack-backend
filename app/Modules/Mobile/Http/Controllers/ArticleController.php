<?php

namespace App\Modules\Mobile\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Post;
use App\Services\Helper;
use DB;

class ArticleController extends Controller
{
	/**
     * 文章主题页
     * @author tangtanglove
	 */
    public function index(Request $request)
    {
        $id      = $request->input('id');
        $name    = $request->input('name');

        if (!empty($id)) {
            $category = Category::where('id', $id)->first();
        } elseif(!empty($name)) {
            $category = Category::where('name', $name)->first();
        }

        if(empty($category)) {
            abort(404, 'Not Found');
        }

        $articles = Post::where('type', 'ARTICLE')
        ->where('category_id', $category->id)
        ->where('status', 1)
        ->orderBy('level', 'desc')
        ->orderBy('id', 'desc')
        ->paginate($category->page_num);

        // 获取文章内图片
        foreach ($articles as $key => $value) {
            $articles[$key]->content_pictures = Helper::getContentPicture($value->content);
        }

        return view('mobile::'.$category->index_tpl,compact('articles','category'));
    }

	/**
     * 文章列表页
     * @author tangtanglove
	 */
    public function lists(Request $request)
    {
        $id      = $request->input('id');
        $name    = $request->input('name');

        if (!empty($id)) {
            $category = Category::where('id', $id)->first();
        } elseif(!empty($name)) {
            $category = Category::where('name', $name)->first();
        }

        if(empty($category)) {
            abort(404, 'Not Found');
        }

        $articles = Post::where('type', 'ARTICLE')
        ->where('category_id', $category->id)
        ->where('status', 1)
        ->orderBy('level', 'desc')
        ->orderBy('id', 'desc')
        ->paginate($category->page_num);

        // 获取文章内图片
        foreach ($articles as $key => $value) {
            $articles[$key]->content_pictures = Helper::getContentPicture($value->content);
        }

        return view('mobile::'.$category->lists_tpl,compact('articles','category'));
    }

	/**
     * 文章详情页
     * @author tangtanglove
	 */
    public function detail(Request $request)
    {
        $id      = $request->input('id');
        $name    = $request->input('name');

        if (!empty($id)) {
            $article = Post::where('id', $id)->where('status', 1)->first();
        } elseif(!empty($name)) {
            $article = Post::where('name', $name)->where('status', 1)->first();
        } else {
            abort(404, 'Not Found');
        }

        $category = Category::where('id', $article->category_id)->first();

        // 浏览量自增
        Post::where('id', $id)->increment('view');

        if (empty($article)) {
            abort(404, 'Not Found');
        }

        // 上一个
        $prev = Post::where('id','<', $id)->where('category_id',$article->category_id)->where('status', 1)->limit(1)->first();
        // 下一个
        $next = Post::where('id','>', $id)->where('category_id',$article->category_id)->where('status', 1)->limit(1)->first();

        return view('mobile::'.$category['detail_tpl'],compact('article','category','prev','next'));
    }
}