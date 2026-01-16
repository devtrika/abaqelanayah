<div class="position-relative">
    {{-- table loader  --}}
    {{-- <div class="table_loader" >
        {{__('admin.loading')}}
    </div> --}}
    {{-- table loader  --}}
    
    {{-- table content --}}
    <table class="table " id="tab">
        <thead>
            <tr>
                <th>
                    <label class="container-checkbox">
                        <input type="checkbox" value="value1" name="name1" id="checkedAll">
                        <span class="checkmark"></span>
                    </label>
                </th>
                <th>{{__('admin.id_num')}}</th>
                <th>{{__('admin.created_at')}}</th>
                <th>{{__('admin.blog_category')}}</th>
                <th>{{__('admin.image')}}</th>
                <th>{{__('admin.title')}}</th>
                <th>{{__('admin.likes')}}</th>
                <th>{{__('admin.dislikes')}}</th>
                <th>{{__('admin.comments')}}</th>
                <th>{{__('admin.is_active')}}</th>
                <th>{{__('admin.control')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($blogs as $blog)
                <tr class="delete_row">
                    <td class="text-center">
                        <label class="container-checkbox">
                        <input type="checkbox" class="checkSingle" id="{{ $blog->id }}">
                        <span class="checkmark"></span>
                        </label>
                    </td>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $blog->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $blog->category->name }}</td>
                    <td><img src="{{$blog->getfirstMediaUrl('blogs')}}" width="30px" height="30px" alt=""></td>
                    <td>{{ $blog->getTranslations('title')[app()->getLocale()] ?? $blog->getTranslations('title')[array_key_first($blog->getTranslations('title'))] }}</td>
                    <td>{{ $blog->likes_count }}</td>
                    <td>{{ $blog->dislikes_count }}</td>
                    <td>{{ $blog->comments_count }}</td>
                    <td>
                        {!! toggleBooleanView($blog , route('admin.model.active' , ['model' =>'Blog' , 'id' => $blog->id , 'action' => 'is_active'])) !!}
                    </td>
                    
                    <td class="product-action"> 
                        <span class="text-primary"><a href="{{ route('admin.blogs.show', ['id' => $blog->id]) }}" class="btn btn-warning btn-sm"><i class="feather icon-eye"></i> {{ __('admin.show') }}</a></span>
                        <span class="action-edit text-primary"><a href="{{ route('admin.blogs.edit', ['id' => $blog->id]) }}" class="btn btn-primary btn-sm"><i class="feather icon-edit"></i>{{ __('admin.edit') }}</a></span>
                        <span class="delete-row btn btn-danger btn-sm" data-url="{{ url('admin/blogs/' . $blog->id) }}"><i class="feather icon-trash"></i>{{ __('admin.delete') }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- table content --}}
    {{-- no data found div --}}
    @if ($blogs->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{asset('admin/app-assets/images/pages/404.png')}}" alt="">
            <span class="mt-2" style="font-family: cairo">{{__('admin.there_are_no_matches_matching')}}</span>
        </div>
    @endif
    {{-- no data found div --}}

</div>
{{-- pagination  links div --}}
@if ($blogs->count() > 0 && $blogs instanceof \Illuminate\Pagination\AbstractPaginator )
    <div class="d-flex justify-content-center mt-3">
        {{$blogs->links()}}
    </div>
@endif
{{-- pagination  links div --}}

