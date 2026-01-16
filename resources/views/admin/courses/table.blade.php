<div class="position-relative">
    <table class="table" id="tab">
        <thead>
            <tr>
                <th>
                    <label class="container-checkbox">
                        <input type="checkbox" value="value1" name="name1" id="checkedAll">
                        <span class="checkmark"></span>
                    </label>
                </th>
                <th>{{ __('admin.id_num') }}</th>
                <th>{{ __('admin.date') }}</th>
                <th>{{ __('admin.image') }}</th>
                <th>{{ __('admin.course_name') }}</th>
                <th>{{ __('admin.instructor') }}</th>
                <th>{{ __('admin.duration_hours') }}</th>
                <th>{{ __('admin.price') }}</th>
                <th>{{ __('admin.stages_count') }}</th>
                <th>{{ __('admin.status') }}</th>
                <th>{{ __('admin.control') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($courses as $course)
                <tr class="delete_row">
                    <td class="text-center">
                        <label class="container-checkbox">
                            <input type="checkbox" class="checkSingle" id="{{ $course->id }}">
                            <span class="checkmark"></span>
                        </label>
                    </td>
                    <td>{{ $course->id }}</td>

                    <td>{{ $course->created_at->format('d/m/Y h:i A') }}</td>

                    <td>
                        <img src="{{ $course->getfirstMediaUrl('courses') }}" width="50px" height="50px" alt="course image" style="border-radius: 5px;">
                    </td>
                    
                    <td>{{ $course->name }}</td>
                    <td>{{ $course->instructor_name }}</td>
                    <td>{{ $course->duration }}</td>
                    <td>{{ number_format($course->price, 2) }} {{ __('admin.currency') }}</td>
                    <td>
                        <span class="badge badge-info">{{ $course->stages_count }} {{ __('admin.stage') }}</span>
                    </td>
                    <td>
                        {!! toggleBooleanView($course , route('admin.model.active' , ['model' =>'Course' , 'id' => $course->id , 'action' => 'is_active'])) !!}
                    </td>
                    <td class="product-action">
                        <span class="text-primary"><a href="{{ route('admin.courses.show', ['id' => $course->id]) }}" class="btn btn-warning btn-sm"><i class="feather icon-eye"></i> {{ __('admin.show') }}</a></span>
                        <span class="action-edit text-primary"><a href="{{ route('admin.courses.edit', ['id' => $course->id]) }}" class="btn btn-primary btn-sm"><i class="feather icon-edit"></i>{{ __('admin.edit') }}</a></span>
                        <span class="delete-row btn btn-danger btn-sm" data-url="{{ url('admin/courses/' . $course->id) }}"><i class="feather icon-trash"></i>{{ __('admin.delete') }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">
                        <div class="d-flex flex-column w-100 align-center mt-4">
                            <img src="{{asset('admin/app-assets/images/pages/404.png')}}" alt="">
                            <span class="mt-2" style="font-family: cairo">{{ __('admin.there_are_no_matches_matching') }}</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
