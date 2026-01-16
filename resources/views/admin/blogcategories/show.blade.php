@extends('admin.layout.master')

@section('content')
<!-- // Basic multiple Column Form section start -->
<section id="multiple-column-form">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{__('admin.blog_category')}}: {{ $blogcategory->name }}</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        @if($blogcategory->blogs->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ __('admin.id') }}</th>
                                            <th>{{ __('admin.title') }}</th>
                                            <th>{{ __('admin.status') }}</th>
                                            <th>{{ __('admin.created_at') }}</th>
                                            <th>{{ __('admin.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($blogcategory->blogs as $blog)
                                            <tr>
                                                <td>{{ $blog->id }}</td>
                                                <td>{{ $blog->title }}</td>
                                                <td>
                                                    @if($blog->is_active)
                                                        <span class="badge badge-success">{{ __('admin.active') }}</span>
                                                    @else
                                                        <span class="badge badge-danger">{{ __('admin.inactive') }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $blog->created_at->format('Y-m-d H:i:s') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.blogs.show', $blog->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fa fa-eye"></i> {{ __('admin.view') }}
                                                    </a>
                                                    <a href="{{ route('admin.blogs.edit', $blog->id) }}" class="btn btn-sm btn-warning">
                                                        <i class="fa fa-edit"></i> {{ __('admin.edit') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                {{ __('admin.no_data_found') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('js')
    <script>
        // No need to disable form elements since we're not using a form anymore
    </script>
@endsection