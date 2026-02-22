<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleTag;
use App\Traits\UploadImage;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    use UploadImage;
    public function index()
    {
        $data = Article::latest()->all();
        return view('beckend.products.data', compact('data'));
    }

    public function create()
    {
        return view('beckend.products.create');
    }

    public function edit($id)
    {
        $data['data'] = Article::find($id);
        $data['categories'] = ArticleCategory::all();
        $data['tags'] = \DB::table('article_tags')->where('article_id', $id)->pluck('tag_id')->toArray();
        $data['drugs'] = \DB::table('article_drugs')
            ->join('drugs', 'drugs.id', '=', 'article_drugs.drug_id')
            ->select('drugs.id', 'drugs.name')
            ->where('article_drugs.article_id', $id)->get();
        $data['article_categories'] = \DB::table('articles_categories')->where('article_id', $id)->pluck('category_id')->toArray();

        return view('beckend.products.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        $data = $request->except(['data_id', 'article_category_id', 'article_tag_id', 'drugs', 'user_id']);
        $data['slug'] = \Str::slug($data['name']);
        $data['user_id'] = auth()->user()->id;
        if (request()->hasFile('photo')) {
            $data['photo'] = $this->uploadImage(request()->file('photo'), 'article');
        }
        $article = Article::updateOrCreate(['id' => $request->data_id], $data);
        Article::where('id', $article->id)->update(['slug' => $data['slug'] . '-' . $article->id], $data);
        if ($request->article_tag_id) {
            \DB::table('article_tags')->where('article_id', $request->data_id)->delete();
            foreach ($request->article_tag_id as $tag) {
                \DB::table('article_tags')->insert(['article_id' => $article->id, 'tag_id' => $tag]);
            }
        }
        if ($request->drugs) {
            \DB::table('article_drugs')->where('article_id', $request->data_id)->delete();
            foreach ($request->drugs as $drug) {
                if (is_numeric($drug)) {
                    if (\DB::table('article_drugs')->where('drug_id', $drug)->where('article_id', $article->id)->first()) continue;
                    \DB::table('article_drugs')->insert(['article_id' => $article->id, 'drug_id' => $drug]);
                } else {
                    continue;
                }
            }
        }
        if ($request->user_id) {
            \DB::table('article_users')->where('article_id', $request->data_id)->delete();
            foreach ($request->user_id as $user_id) {
                if (is_numeric($user_id)) {
                    if (\DB::table('article_users')->where('user_id', $user_id)->where('article_id', $article->id)->first()) continue;
                    \DB::table('article_users')->insert(['article_id' => $article->id, 'user_id' => $user_id]);
                } else {
                    continue;
                }
            }
        }
        if ($request->article_category_id) {
            \DB::table('articles_categories')->where('article_id', $request->data_id)->delete();
            foreach ($request->article_category_id as $category_id) {
                \DB::table('articles_categories')->insert(['article_id' => $article->id, 'category_id' => $category_id]);
            }
        }
        return redirect('admin/articles/' . $article->id)->with('success', 'Article created successfully!');
    }
    public function show($id)
    {
        $data['data'] = \DB::table('articles')->where('id', $id)->first();

        $category_ids = \DB::table('articles_categories')->where('article_id', $id)->pluck('category_id')->toArray();
        $data['art_categories'] = \DB::table('article_categories')->whereIn('id', $category_ids)->get();
        $data['tags'] = \DB::table('article_tags')->where('article_id', $id)->pluck('tag_id')->toArray();

        $data['article_categories'] = \DB::table('articles_categories')->where('article_id', $id)->pluck('category_id')->toArray();
        $data['contents'] = \DB::table('article_contents')->where('article_id', $id)->orderBy('order')->get();

        $data['persons'] = \DB::table('article_users as au')
            ->join('persons as p', 'au.user_id', '=', 'p.user_id')
            ->select('p.user_id', 'p.name')
            ->where('au.article_id', $id)->get();

        $data['drugs'] = \DB::table('article_drugs as ad')
            ->join('drugs as d', 'd.id', '=', 'ad.drug_id')
            ->select('d.id', 'd.name')
            ->where('ad.article_id', $id)->get();

        return view('beckend.products.show', $data);
    }


    public function getContentsEdit($id)
    {
        $data['data'] = \DB::table('article_contents')->where('id', $id)->first();
        $data['article'] = \DB::table('articles')->where('id', $data['data']->article_id)->first();

        $category_ids = \DB::table('articles_categories')->where('article_id', $id)->pluck('category_id')->toArray();
        $data['categories'] = \DB::table('article_categories')->whereIn('id', $category_ids)->get();

        return view('beckend.products.content', $data);
    }

    public function postContents(Request $request)
    {
        $values = $request->except(['_token', 'data_id']);

        if ($request->data_id) \DB::table('article_contents')->where('id', $request->data_id)->update($values);
        else \DB::table('article_contents')->insert($values);

        if ($request->data_id) return back()->with('message', 'Article content updated successfully!');
        return back()->with('message', 'Article content created successfully!');
    }

}
