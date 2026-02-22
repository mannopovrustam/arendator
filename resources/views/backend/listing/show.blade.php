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
    <script src="https://cdn.tiny.cloud/1/5eka86mjs5w9ctulyup42n1x0jtdybaq7mi4ahj2czpwxfby/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.js"></script>

    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>
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

        $(document).ready(function () {
            // Append new options
            var personOptions = [
                @foreach($persons as $person)
                    {id: '{{$person->user_id}}', text: '{{$person->name}}', selected: true} @if(!$loop->last),@endif
                @endforeach
            ];

            var drugOptions = [
                @foreach($drugs as $drug)
                    {id: '{{$drug->id}}', text: '{{$drug->name}}', selected: true} @if(!$loop->last),@endif
                @endforeach
            ];

            $.each(personOptions, function (index, option) {
                $('#user_id').append(new Option(option.text, option.id, option.selected));
            });

            $.each(drugOptions, function (index, option) {
                $('#drugs').append(new Option(option.text, option.id, option.selected));
            });

            // Trigger Select2 update
            $('#user_id option').each(function () {
                $(this).prop('selected', true);
            });

            $('#drugs option').each(function () {
                $(this).prop('selected', true);
            });

            // Trigger change event to update Select2
            $('#user_id').trigger('change');
            $('#drugs').trigger('change');

            $("#user_id").select2({
                tags: true,
                multiple: true,
                tokenSeparators: [',', ' '],
                minimumInputLength: 3,
                minimumResultsForSearch: 10,
                ajax: {
                    url: '/persons/search-persons',
                    dataType: "json",
                    type: "GET",
                    data: function (params) {
                        var queryParameters = {
                            term: params.term
                        }
                        return queryParameters;
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.name + ' (' + moment(item.birthdate).format('MM/DD/YYYY') + ', ' + item.phone + ')',
                                    id: item.user_id
                                }
                            })
                        };
                    }
                }
            });

            $("#drugs").select2({
                tags: true,
                multiple: true,
                tokenSeparators: [',', ' '],
                minimumInputLength: 3,
                minimumResultsForSearch: 10,
                ajax: {
                    url: '/drugs/search-drugs',
                    dataType: "json",
                    type: "GET",
                    data: function (params) {
                        var queryParameters = {
                            term: params.term
                        }
                        return queryParameters;
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.name + ' (' + item.country + ')',
                                    id: item.id
                                }
                            })
                        };
                    }
                }
            });

        });

    </script>

@endsection

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4><span class="text-muted">Maqola</span> "{{ $data->name }}"</h4>
                    </div>
                    <div class="card-body">

                        <form id="w0" action="/{{ \Request::segment(1) }}/{{ \Request::segment(2) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="data_id" value="{{ $data->id }}">

                            <div class="row">
                                <div class="d-flex justify-content-between align-items-end">
                                    <h4>{{ Str::headline(Str::singular(\Request::segment(2))) }}</h4>
                                    <a href="/admin/{{ \Request::segment(2) }}/create" class="btn btn-primary"><i class="fa fa-plus-square"></i> Yangi yaratish</a>
                                </div>
                                <div class="col-8">
                                    <div class="form-group field-hotels-username required">
                                        <label for="name" class="form-label">Sarlavhalar</label>
                                        <input type="text" id="name" class="form-control"
                                               name="name" value="{{ $data->name }}" aria-required="true" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="form-group field-hotels-username required">
                                        <label for="sub_name" class="form-label">Sarlavhalar ostida</label>
                                        <input type="text" id="sub_name" class="form-control"
                                               name="sub_name" value="{{ $data->sub_name }}" aria-required="true" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group field-hotels-username required">
                                        <label for="photo" class="form-label">Foto</label>
                                        <input type="file" name="photo" id="photo" class="form-control">
                                        @if($data->photo)
                                            <br>
                                            <img src="{{ $data->photo }}" style="width: 100%;" alt="">
                                        @endif
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="row mt-2">
                                        <div class="col-lg-8">
                                            <div class="form-group field-hotels-username required">
                                                <label for="sub_name" class="form-label">Teglar</label><br>
                                                <select name="article_tag_id[]" id="article_tag_id" class="select2 form-control select2-multiple" required multiple data-placeholder="*** Выбрать ***">
                                                    @foreach(\App\Models\Tag::all() as $d)
                                                        <option value="{{ $d->id }}" @selected(in_array($d->id,$tags))>{{ $d->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group field-hotels-username required">
                                                <label for="status" class="form-label">Holat</label>
                                                <select name="status" id="status" class="select2 form-control select2-multiple" required>
                                                    <option value="0" @selected($data->status == 0)>Deaktiv</option>
                                                    <option value="1" @selected($data->status == 1)>Aktiv</option>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group field-hotels-username required">
                                                <label for="lang" class="form-label">Til</label>
                                                <select name="lang" class="form-select" id="lang">
                                                    @foreach(['uz','oz'] as $d)
                                                        <option value="{{ $d }}" @selected($data->lang == $d)>{{ $d }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group field-hotels-username required">
                                        <label for="sub_name" class="form-label">Dori</label><br>
                                        <select name="drugs[]" class="select2 w-75" id="drugs" multiple></select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group field-hotels-username required">
                                        <label for="user_id" class="form-label">Shaxslar</label><br>
                                        <select name="user_id[]" class="select2 w-75" id="user_id" multiple></select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-10">
                                    <div class="form-group field-hotels-username required">
                                        <label for="article_category_id" class="form-label">Turkum</label>
                                        <select name="article_category_id[]" id="article_category_id" class="select2 form-control select2-multiple" required multiple data-placeholder="*** Выбрать ***">
                                            @foreach(\App\Models\ArticleCategory::all() as $d)
                                                <option value="{{ $d->id }}" @selected(in_array($d->id,$article_categories))>{{ $d->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-10">
                                    <div class="form-group field-hotels-username required">
                                        <label for="date_publication" class="form-label">Nashr etilgan sana</label>
                                        <input type="date" name="date_publication" class="form-control" value="{{ $data->date_publication }}">
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
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Bo'lim qo'shish</h4>
                    </div>
                    <div class="card-body">
                        <form action="/admin/articles/contents" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="article_id" value="{{ $data->id }}">
                            <div class="row">
                                <div class="col-10">
                                    <div class="form-group field-hotels-username required">
                                        <label for="name" class="form-label">Sarlavha</label>
                                        <input type="text" id="name" class="form-control"
                                               name="name" aria-required="true" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group field-hotels-username required">
                                        <label for="order" class="form-label">Tartiblash</label>
                                        <input type="number" id="order" class="form-control"
                                               name="order" aria-required="true" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group field-hotels-username required">
                                        <label for="elm1" class="form-label">Kontent</label>
                                        <textarea id="textarea_editor_article" name="content"></textarea>
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

        <div class="row">
            <div class="col-lg-12">

                    <div class="card">
                        <div class="card-header">
                            <h4>Bo'limlar</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-hover table-bordered table-striped table-nowrap align-middle">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Sarlavha</th>
                                    <th>Tartiblash</th>
                                    <th>Yaratilgan</th>
                                    <th>Yangilangan</th>
                                    <th>Harakatlar</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($contents as $content)
                                    <tr>
                                        <td>{{ $content->id }}</td>
                                        <td>{{ $content->name }}</td>
                                        <td>{{ $content->order }}</td>
                                        <td>{{ \Carbon\Carbon::parse($content->created_at)->format('F d, Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($content->updated_at)->format('F d, Y') }}</td>
                                        <td>
                                            <a href="/admin/articles/contents-edit/{{ $content->id }}"
                                            class="btn btn-sm btn-warning">Tahrirlash</a>
{{--                                            <a href="/admin/articles/contents-delete/{{ $content->id }}" class="btn btn-sm btn-danger">Delete</a>--}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
            </div>
        </div>
    </div>

@endsection
