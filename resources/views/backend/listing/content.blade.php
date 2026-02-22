@extends('layouts.app')

@section('style')
    <style type="text/css">
        #map {
            width: 100%;
            height: 95%;
        }
    </style>
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection
@section('script')

    <script src="/ckeditor/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('textarea_editor_article', {
            "height": 200,
            "toolbarGroups": [{"name": "document", "groups": ["mode", "document", "doctools"]}, {
                "name": "clipboard",
                "groups": ["clipboard", "undo"]
            }, {
                "name": "editing",
                "groups": ["find", "selection", "spellchecker"]
            }, {"name": "forms"}, "/", {
                "name": "basicstyles",
                "groups": ["basicstyles", "colors", "cleanup"]
            }, {
                "name": "paragraph",
                "groups": ["list", "indent", "blocks", "align", "bidi"]
            }, {"name": "links"}, {"name": "insert"}, "/", {"name": "styles"}, {"name": "blocks"}, {"name": "colors"}, {"name": "tools"}, {"name": "others"}],
            "filebrowserBrowseUrl": "/laravel-filemanager",
            "filebrowserImageBrowseUrl": "/laravel-filemanager?filter=image",
            "filebrowserFlashBrowseUrl": "/laravel-filemanager?filter=flash",
            "language": "article"
        });
    </script>
@endsection

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4><span class="text-muted">Maqola</span> "{{ $data->name }}"</h4>
                        <a href="/admin/articles/{{ $data->article_id }}" class="btn btn-primary"><i
                                class="uil-left-arrow-from-left"></i> Ortga</a>
                    </div>
                    <div class="card-body">
                        <table
                            class="table table-sm table-hover table-bordered table-striped table-nowrap align-middle">
                            <tr>
                                <td>Kategoriya</td>
                                <td>
                                    @foreach($categories as $category)
                                        {{ $category->name }}@if(!$loop->last),@endif
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <td>Sarlavha</td>
                                <td>{{ $article->name }}</td>
                            </tr>
                            <tr>
                                <td>Sarlavha ostida</td>
                                <td>{{ $article->sub_name }}</td>
                            </tr>
                            <tr>
                                <td>Foto</td>
                                <td><img src="{{ $article->photo }}" style="height: 50px;" alt=""></td>
                            </tr>
                            <tr>
                                <td>Koʻrishlar</td>
                                <td>{{ $article->opened }}</td>
                            </tr>
                            <tr>
                                <td>Taassurotlar</td>
                                <td>{{ $article->viewed }}</td>
                            </tr>
                            <tr>
                                <td>Foydalanuvchi</td>
                                <td>{{ $article->user_id }}</td>
                            </tr>
                            <tr>
                                <td>Til</td>
                                <td>{{ $article->lang }}</td>
                            </tr>
                            <?php
                                \Carbon\Carbon::setLocale('uz_UZ')
                            ?>
                            <tr>
                                <td>Yaratilgan</td>
                                <td>{{ \Carbon\Carbon::parse($article->created_at)->translatedFormat('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <td>Yangilangan</td>
                                <td>{{ \Carbon\Carbon::parse($article->updated_at)->translatedFormat('F d, Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Bo‘lim qo'shish</h4>
                    </div>
                    <div class="card-body">
                        <form action="/admin/articles/contents" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="data_id" value="{{ $data->id }}">
                            <input type="hidden" name="article_id" value="{{ $data->article_id }}">
                            <div class="row">
                                <div class="col-10">
                                    <div class="form-group field-hotels-username required">
                                        <label for="name" class="form-label">Sarlavha</label>
                                        <input type="text" id="name" class="form-control"
                                               name="name" value="{{ $data->name }}" aria-required="true" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group field-hotels-username required">
                                        <label for="order" class="form-label">Tartiblash</label>
                                        <input type="number" id="order" class="form-control"
                                               name="order" value="{{ $data->order }}" aria-required="true" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group field-hotels-username required">
                                        <label for="elm1" class="form-label">Kontent</label>
                                        <textarea id="textarea_editor_article" name="content">{{ $data->content }}</textarea>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">Saqlash</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
