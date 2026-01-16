@extends('admin.layout.master')
{{-- extra css files --}}
@section('css')
    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/app-assets/css-rtl/plugins/forms/validation/form-validation.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css') }}">

@endsection
{{-- extra css files --}}

@section('content')
<!-- // Basic multiple Column Form section start -->
<section id="multiple-column-form">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                {{-- <div class="card-header">
                    <h4 class="card-title">{{__('admin.add') . ' ' . __('admin.blog')}}</h4>
                </div> --}}
                <div class="card-content">
                    <div class="card-body">
                        <form  method="POST" action="{{route('admin.blogs.store')}}" class="store form-horizontal" enctype="multipart/form-data" novalidate>
                            @csrf
                            <div class="form-body">
                                <div class="row">

                                    <div class="col-12">
                                        <div class="col-12">
                                            <ul class="nav nav-tabs mb-3">
                                                    @foreach (languages() as $lang)
                                                        <li class="nav-item">
                                                            <a class="nav-link @if($loop->first) active @endif"  data-toggle="pill" href="#first_{{$lang}}" aria-expanded="true">{{  __('admin.data') }} {{ $lang }}</a>
                                                        </li>
                                                    @endforeach
                                            </ul>
                                        </div>

                                        <div class="col-12">
                                            <div class="imgMontg col-12 text-center">
                                                <div class="dropBox">
                                                    <div class="textCenter">
                                                        <div class="imagesUploadBlock">
                                                            <label class="uploadImg">
                                                                <span><i class="feather icon-image"></i></span>
                                                                <input type="file" accept="image/*" name="image" class="imageUploader">
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    {{-- Language tabs content --}}
                                       <div class="tab-content">
                                                @foreach (languages() as $lang)
                                                    <div role="tabpanel" class="tab-pane fade @if($loop->first) show active @endif " id="first_{{$lang}}" aria-labelledby="first_{{$lang}}" aria-expanded="true">
                                                        <div class="row">
                                                            <div class="col-md-6 col-12">
                                                                <div class="form-group">
                                                                    <label for="title_{{$lang}}">{{__('admin.blog_title')}} {{ $lang }}</label>
                                                                    <div class="controls">
                                                                        <input type="text" name="title[{{$lang}}]" id="title_{{$lang}}" class="form-control" placeholder="{{__('admin.write') . ' ' . __('admin.blog_title')}} {{ $lang }}" required data-validation-required-message="{{__('admin.this_field_is_required')}}" >
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @if($loop->first)
                                                            <div class="col-md-6 col-12">
                                                                <div class="form-group">
                                                                    <label for="category_id">{{__('admin.blog_category')}}</label>
                                                                    <div class="controls">
                                                                        <select name="category_id" id="category_id" class="form-control" required data-validation-required-message="{{__('admin.this_field_is_required')}}">
                                                                            <option value="">{{__('admin.select_category')}}</option>
                                                                            @foreach($categories as $category)
                                                                                <option value="{{$category->id}}">{{$category->name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endif
                                                        </div>
                                                        @if($loop->first)
                                                        <div class="col-md-6 col-12">
                                                            <div class="form-group">
                                                                <label for="is_active">{{__('admin.status')}}</label>
                                                                <div class="controls">
                                                                    <select name="is_active" id="is_active" class="form-control" required data-validation-required-message="{{__('admin.this_field_is_required')}}">
                                                                        <option value="1" selected>{{__('admin.active')}}</option>
                                                                        <option value="0">{{__('admin.inactive')}}</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endif
                                                        <div class="row">
                                                            <div class="col-md-12 col-12">
                                                                <div class="form-group">
                                                                     <label for="content_{{$lang}}">{{__('admin.blog_content')}} {{ $lang }}</label>
                                                                    <div class="controls">
                                                                        <textarea name="content[{{$lang}}]" id="content_{{$lang}}" class="form-control" placeholder="{{__('admin.write') . ' ' . __('admin.blog_content')}} {{ $lang }}"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                        <div class="col-12 d-flex justify-content-center mt-3">
                                            <button type="submit"
                                                class="btn btn-primary mr-1 mb-1 submit_button">{{ __('admin.add') }}</button>
                                            <a href="{{ url()->previous() }}" type="reset"
                                                class="btn btn-outline-warning mr-1 mb-1">{{ __('admin.back') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('js')
    <script src="{{ asset('admin/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js') }}"></script>
    <script src="{{ asset('admin/app-assets/js/scripts/forms/validation/form-validation.js') }}"></script>
    <script src="{{ asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('admin/app-assets/js/scripts/extensions/sweet-alerts.js') }}"></script>
    <!-- CKEditor 5 JS -->
    <script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>

    {{-- show selected image script --}}
    @include('admin.shared.addImage')
    {{-- show selected image script --}}

    {{-- submit add form script --}}
    @include('admin.shared.submitAddForm')
    {{-- submit add form script --}}

    <script>
        $(document).ready(function() {
            // Store CKEditor instances
            const editorInstances = {};

            // Initialize CKEditor 5 for all content textareas
            @foreach(languages() as $lang)
                ClassicEditor
                    .create(document.querySelector('#content_{{$lang}}'), {
                        toolbar: {
                            items: [
                                'heading', '|',
                                'bold', 'italic', 'underline', 'strikethrough', '|',
                                'fontSize', 'fontColor', 'fontBackgroundColor', '|',
                                'alignment', '|',
                                'numberedList', 'bulletedList', '|',
                                'outdent', 'indent', '|',
                                'link', 'blockQuote', 'insertTable', '|',
                                'imageUpload', 'mediaEmbed', '|',
                                'undo', 'redo', '|',
                                'sourceEditing'
                            ]
                        },
                        language: '{{app()->getLocale() === "ar" ? "ar" : "en"}}',
                        image: {
                            toolbar: [
                                'imageTextAlternative',
                                'imageStyle:inline',
                                'imageStyle:block',
                                'imageStyle:side',
                                'linkImage'
                            ]
                        },
                        table: {
                            contentToolbar: [
                                'tableColumn',
                                'tableRow',
                                'mergeTableCells',
                                'tableCellProperties',
                                'tableProperties'
                            ]
                        },
                        heading: {
                            options: [
                                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                                { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
                            ]
                        },
                        fontSize: {
                            options: [
                                9, 11, 13, 'default', 17, 19, 21, 27, 35
                            ]
                        },
                        fontColor: {
                            colors: [
                                {
                                    color: 'hsl(0, 0%, 0%)',
                                    label: 'Black'
                                },
                                {
                                    color: 'hsl(0, 0%, 30%)',
                                    label: 'Dim grey'
                                },
                                {
                                    color: 'hsl(0, 0%, 60%)',
                                    label: 'Grey'
                                },
                                {
                                    color: 'hsl(0, 0%, 90%)',
                                    label: 'Light grey'
                                },
                                {
                                    color: 'hsl(0, 0%, 100%)',
                                    label: 'White',
                                    hasBorder: true
                                },
                                {
                                    color: 'hsl(0, 75%, 60%)',
                                    label: 'Red'
                                },
                                {
                                    color: 'hsl(30, 75%, 60%)',
                                    label: 'Orange'
                                },
                                {
                                    color: 'hsl(60, 75%, 60%)',
                                    label: 'Yellow'
                                },
                                {
                                    color: 'hsl(90, 75%, 60%)',
                                    label: 'Light green'
                                },
                                {
                                    color: 'hsl(120, 75%, 60%)',
                                    label: 'Green'
                                },
                                {
                                    color: 'hsl(150, 75%, 60%)',
                                    label: 'Aquamarine'
                                },
                                {
                                    color: 'hsl(180, 75%, 60%)',
                                    label: 'Turquoise'
                                },
                                {
                                    color: 'hsl(210, 75%, 60%)',
                                    label: 'Light blue'
                                },
                                {
                                    color: 'hsl(240, 75%, 60%)',
                                    label: 'Blue'
                                },
                                {
                                    color: 'hsl(270, 75%, 60%)',
                                    label: 'Purple'
                                }
                            ]
                        },
                        fontBackgroundColor: {
                            colors: [
                                {
                                    color: 'hsl(0, 0%, 0%)',
                                    label: 'Black'
                                },
                                {
                                    color: 'hsl(0, 0%, 30%)',
                                    label: 'Dim grey'
                                },
                                {
                                    color: 'hsl(0, 0%, 60%)',
                                    label: 'Grey'
                                },
                                {
                                    color: 'hsl(0, 0%, 90%)',
                                    label: 'Light grey'
                                },
                                {
                                    color: 'hsl(0, 0%, 100%)',
                                    label: 'White',
                                    hasBorder: true
                                },
                                {
                                    color: 'hsl(0, 75%, 60%)',
                                    label: 'Red'
                                },
                                {
                                    color: 'hsl(30, 75%, 60%)',
                                    label: 'Orange'
                                },
                                {
                                    color: 'hsl(60, 75%, 60%)',
                                    label: 'Yellow'
                                },
                                {
                                    color: 'hsl(90, 75%, 60%)',
                                    label: 'Light green'
                                },
                                {
                                    color: 'hsl(120, 75%, 60%)',
                                    label: 'Green'
                                },
                                {
                                    color: 'hsl(150, 75%, 60%)',
                                    label: 'Aquamarine'
                                },
                                {
                                    color: 'hsl(180, 75%, 60%)',
                                    label: 'Turquoise'
                                },
                                {
                                    color: 'hsl(210, 75%, 60%)',
                                    label: 'Light blue'
                                },
                                {
                                    color: 'hsl(240, 75%, 60%)',
                                    label: 'Blue'
                                },
                                {
                                    color: 'hsl(270, 75%, 60%)',
                                    label: 'Purple'
                                }
                            ]
                        }
                    })
                    .then(editor => {
                        editorInstances['content_{{$lang}}'] = editor;

                        // Set minimum height
                        editor.editing.view.change(writer => {
                            writer.setStyle('min-height', '300px', editor.editing.view.document.getRoot());
                        });
                    })
                    .catch(error => {
                        console.error('CKEditor initialization error for content_{{$lang}}:', error);
                    });
            @endforeach

            // Custom validation for CKEditor 5
            $('form.store').on('submit', function(e) {
                var isValid = true;

                @foreach(languages() as $lang)
                    if (editorInstances['content_{{$lang}}']) {
                        var content_{{$lang}} = editorInstances['content_{{$lang}}'].getData().trim();
                        if (content_{{$lang}} === '' || content_{{$lang}} === '<p>&nbsp;</p>' || content_{{$lang}} === '<p></p>') {
                            alert('{{__("admin.blog_content")}} {{$lang}} {{__("admin.this_field_is_required")}}');
                            isValid = false;
                            return false;
                        }

                        // Update the textarea with editor content
                        document.querySelector('#content_{{$lang}}').value = content_{{$lang}};
                    }
                @endforeach

                if (!isValid) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    </script>
@endsection
