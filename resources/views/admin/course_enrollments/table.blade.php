{{-- table content --}}
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
                <th>{{ __('admin.serial_number') }}</th>
                <th>{{ __('admin.enrollment_id') }}</th>
                <th>{{ __('admin.enrollment_datetime') }}</th>
                <th>{{ __('admin.course_name') }}</th>
                <th>{{ __('admin.course') }} ID</th>
                <th>{{ __('admin.course_provider') }}</th>
                <th>{{ __('admin.client_name') }}</th>
                <th>{{ __('admin.mobile_number') }}</th>
                <th>{{ __('admin.amount_paid') }}</th>
                <th>{{ __('admin.payment_method') }}</th>
                <th>{{ __('admin.payment_reference') }}</th>
                <th>{{ __('admin.control') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($enrollments as $index => $enrollment)
                <tr class="delete_row">
                    <td class="text-center">
                        <label class="container-checkbox">
                            <input type="checkbox" class="checkSingle" id="{{ $enrollment->id }}">
                            <span class="checkmark"></span>
                        </label>
                    </td>
                    <td>{{ $enrollments->firstItem() + $index }}</td>
                    <td>{{ $enrollment->id }}</td>
                    <td>{{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d H:i:s') : '-' }}</td>
                    <td>{{ $enrollment->course->name ?? '-' }}</td>
                    <td>{{ $enrollment->course_id }}</td>
                    <td>{{ $enrollment->course->instructor_name ?? '-' }}</td>
                    <td>{{ $enrollment->user->name ?? '-' }}</td>
                    <td>{{ $enrollment->user->phone ?? '-' }}</td>
                    <td>{{ number_format($enrollment->amount_paid, 2) }} {{ __('admin.riyal') }}</td>
                    <td>
                        @switch($enrollment->payment_method_id)
                            @case(1)
                                <span class="badge badge-info">محفظة</span>
                                @break
                            @case(5)
                                <span class="badge badge-warning">تحويل بنكي</span>
                                @break
                            @case(2)
                                <span class="badge badge-primary">بطاقة ائتمان</span>
                                @break
                            @case(3)
                                <span class="badge badge-success">مدى</span>
                                @break
                                    @case(4)
                                <span class="badge badge-dark">Apple Pay</span>
                                @break
                            @default
                                <span class="badge badge-secondary">{{ $enrollment->payment_method }}</span>
                        @endswitch
                    </td>
                    <td>{{ $enrollment->payment_reference ?? '-' }}</td>
                    <td class="product-action">
                        <span class="text-primary">
                            <a href="{{ route('admin.course_enrollments.show', ['id' => $enrollment->id]) }}"
                               class="btn btn-warning btn-sm">
                                <i class="feather icon-eye"></i> {{ __('admin.show') }}
                            </a>
                        </span>

                        {{-- Future: Download Invoice Button --}}
                        {{-- <span class="text-success">
                            <a href="#" class="btn btn-success btn-sm">
                                <i class="feather icon-download"></i> {{ __('admin.download_invoice') }}
                            </a>
                        </span> --}}

                        <span class="delete-row btn btn-danger btn-sm"
                              data-url="{{ url('admin/course-enrollments/' . $enrollment->id) }}">
                            <i class="feather icon-trash"></i>{{ __('admin.delete') }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{-- table content --}}

{{-- no data found div --}}
@if ($enrollments->count() == 0)
    <div class="d-flex flex-column w-100 align-center mt-4">
        <i class="la la-graduation-cap" style="font-size: 100px; color: #ddd;"></i>
        <h4 class="mt-3 text-muted">{{ __('admin.no_data_found') }}</h4>
        <p class="text-muted">لا توجد اشتراكات في الدورات التدريبية</p>
    </div>
@endif
{{-- no data found div --}}

{{-- pagination --}}
<div class="d-flex justify-content-center mt-3">
    {{ $enrollments->appends(request()->query())->links() }}
</div>
{{-- pagination --}}
