@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/multipleFiles.css') }}">

    @foreach ($inputs as $input)
        @if ($input['input'] == 'multiple_select')
            <link rel="stylesheet" type="text/css"
                href="{{ asset('admin/app-assets/vendors/css/forms/select/select2.min.css') }}">
        @break
    @endif
@endforeach
@endsection

@section('content')
<!-- // Basic multiple Column Form section start -->
<section id="multiple-column-form">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                {{-- <div class="card-header">
                    <h4 class="card-title">{{ __('admin.view') . ' ' . __('admin.copy') }}</h4>
                </div> --}}
                <div class="card-content">
                    <div class="card-body">
                        <form class="show form-horizontal">

                            <div class="form-body">
                                <div class="row">

                                    @include('admin.shared.inputs.showInputs')

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
<script>
    $('.show input').attr('disabled', true)
    $('.show textarea').attr('disabled', true)
    $('.show select').attr('disabled', true)
</script>

@foreach ($inputs as $input)
    @if ($input['input'] == 'multiple_select')
        {{-- if find one multiple select call scripts --}}
        <script src="{{ asset('admin/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
        <script src="{{ asset('admin/app-assets/js/scripts/forms/select/form-select2.js') }}"></script>


        {{-- if find one multiple loop at inputs and set script for every multiple select --}}
        @foreach ($inputs as $name => $select)
            @if ($select['input'] == 'multiple_select')
                <script>
                    $(document).ready(function() {
                        $('.{{ $name }}-multiple').select2({
                            placeholder: '{{ isset($select['placeholder']) ? $select['placeholder'] : __('admin.choose'). ' ' . $select['text'] }}',
                            dir: "{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}",
                        });
                    });
                </script>
            @endif
        @endforeach

        {{-- stop if find one multiple select --}}
    @break
@endif
@endforeach

{{-- map scripts --}}
@foreach ($inputs as $input)
@if ($input['input'] == 'map')
    @include('admin.shared.inputs.map', [
        'lat' => $item['lat'],
        'lng' => $item['lng'],
        'draggable' => false,
    ])
@break
@endif
@endforeach


{{-- if the input have ckeditor --}}
@foreach ($inputs as $input)
@if (isset($input['ckeditor']) && $input['ckeditor'] === true)
{{-- if find one ckeditor call scripts --}}
<script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>

{{-- if find one ckeditor loop at inputs and set script for every ckeditor --}}
<script>
    $(document).ready(function() {
        @foreach ($inputs as $key => $editor)
            @if (isset($editor['ckeditor']) && $editor['ckeditor'] === true)
                // Initialize CKEditor 5 for {{ $key }}
                ClassicEditor
                    .create(document.querySelector('#{{ $key }}'), {
                        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo'],
                        language: '{{app()->getLocale() === "ar" ? "ar" : "en"}}',
                        isReadOnly: true
                    })
                    .catch(error => {
                        console.error('CKEditor initialization error for {{ $key }}:', error);
                    });

                // Initialize CKEditor 5 for {{ $key }}[ar]
                ClassicEditor
                    .create(document.querySelector('#{{ $key . '[ar]' }}'), {
                        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo'],
                        language: 'ar',
                        isReadOnly: true
                    })
                    .catch(error => {
                        console.error('CKEditor initialization error for {{ $key }}[ar]:', error);
                    });

                // Initialize CKEditor 5 for {{ $key }}[en]
                ClassicEditor
                    .create(document.querySelector('#{{ $key . '[en]' }}'), {
                        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo'],
                        language: 'en',
                        isReadOnly: true
                    })
                    .catch(error => {
                        console.error('CKEditor initialization error for {{ $key }}[en]:', error);
                    });
            @endif
        @endforeach
    });
</script>

{{-- stop if find one ckeditr --}}
@break
@endif
@endforeach
@endsection
