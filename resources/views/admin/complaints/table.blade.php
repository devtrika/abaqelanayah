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
                <th>{{__('admin.date')}}</th>
                <th>{{__('admin.name_to_complain')}}</th>
                <th>{{__('admin.phone_to_complain')}}</th>
                <th>{{__('admin.email')}}</th>

                <th>{{__('admin.type')}}</th>
                {{-- <th>{{__('admin.title')}}</th> --}}
                {{-- <th>{{__('admin.message')}}</th> --}}
                <th>{{__('admin.type')}}</th>
                <th>{{__('admin.mark_as_read')}}</th>
                     <th>{{ __('admin.control') }}</th>


            </tr>
        </thead>
        <tbody>
            @foreach($complaints as $complaint)
                <tr class="delete_complaint">
                    <td class="text-center">
                        <label class="container-checkbox">
                            <input type="checkbox" class="checkSingle" id="{{$complaint->id}}">
                            <span class="checkmark"></span>
                        </label>
                    </td>
                    <td>{{\Carbon\Carbon::parse($complaint->created_at)->format('d/m/Y H:i:s')}}</td>
                    <td>{{ optional($complaint->user)->name ?? $complaint->name ?? 'زائر' }}</td>
                    <td>{{ optional($complaint->user)->phone ?? $complaint->phone ?? 'زائر' }}</td>
                    <td>{{ optional($complaint->user)->email ?? $complaint->email ?? 'زائر' }}</td>
                    <td>{{ optional($complaint->user)->type ? __('admin.'.optional($complaint->user)->type) : 'زائر' }}</td>
                    {{-- @php
                        $title = $complaint->title ?? '';
                        $titlePreview = $title ? \Illuminate\Support\Str::limit($title, 20) : null;
                    @endphp
                    <td>
                        @if($titlePreview)
                            {{ $titlePreview }}
                            @if(mb_strlen($title) > 40)
                                <a href="{{ route('admin.complaints.show', $complaint->id) }}" class="ml-2">{{ __('admin.more') }}</a>
                            @endif
                        @else
                            <span class="text-muted">{{ __('admin.not_available') }}</span>
                        @endif
                    </td> --}}
                    {{-- @php
                        $body = $complaint->body ?? '';
                        $preview = $body ? \Illuminate\Support\Str::limit($body, 20) : null;
                    @endphp
                    <td>
                        @if($preview)
                            {!! nl2br(e($preview)) !!}
                            @if(mb_strlen($body) > 20)
                                <a href="{{ route('admin.complaints.show', $complaint->id) }}" class="ml-2">{{ __('admin.more') }}</a>
                            @endif
                        @else
                            <span class="text-muted">{{ __('admin.not_available') }}</span>
                        @endif
                    </td> --}}
                    <td>{{__('admin.' .$complaint->type)}}</td>


                    <td class="product-action d-flex align-items-center">
                        {!! toggleBooleanView($complaint , route('admin.model.active' , ['model' =>'ContactUs' , 'id' => $complaint->id , 'action' => 'is_read'])) !!}
                      
                    </td>


                    <td >

                      <a href="{{ route('admin.complaints.show', $complaint->id) }}" class="btn btn-warning btn-sm ml-2">
                            <i class="feather icon-eye"></i> 
                        </a>
                                            </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- table content --}}
    {{-- no data found div --}}
    @if ($complaints->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{asset('admin/app-assets/images/pages/404.png')}}" alt="">
            <span class="mt-2" style="font-family: cairo">{{__('admin.there_are_no_matches_matching')}}</span>
        </div>
    @endif
    {{-- no data found div --}}

</div>
{{-- pagination  links div --}}
@if ($complaints->count() > 0 && $complaints instanceof \Illuminate\Pagination\AbstractPaginator )
    <div class="d-flex justify-content-center mt-3">
        {{$complaints->links()}}
    </div>
@endif
{{-- pagination  links div --}}