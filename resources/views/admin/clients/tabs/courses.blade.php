<div class="tab-pane fade" id="courses">
    @if($row->courseEnrollments->count() > 0)
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">{{ __('admin.courses') }}</h5>
                <div class="header-elements">
                    <span class="badge badge-primary">
                        {{ $row->courseEnrollments->count() }} {{ __('admin.courses') }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div class="contain-table text-center">
                        <table class="table datatable-button-init-basic text-center table-hover">
                            <thead class="thead-light">
                                <tr class="text-center">
                                    <th class="text-center">#</th>
                                    <th class="text-center">{{__('admin.course_name')}}</th>
                                    <th class="text-center">{{__('admin.instructor')}}</th>
                                    <th class="text-center">{{__('admin.enrollment_status')}}</th>
                                    <th class="text-center">{{__('admin.payment_status')}}</th>
                                    <th class="text-center">{{__('admin.progress_percentage')}}</th>
                                    <th class="text-center">{{__('admin.enrollment_date')}}</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @forelse($row->courseEnrollments as $key => $enrollment)
                                    <tr class="delete_row text-center">
                                        <td class="text-center align-middle">{{ $key + 1 }}</td>
                                        <td class="text-center align-middle">{{ $enrollment->course->name ?? 'N/A' }}</td>
                                        <td class="text-center align-middle">{{ $enrollment->course->instructor_name ?? 'N/A' }}</td>
                                        <td class="text-center align-middle">
                                            @switch($enrollment->status)
                                                @case('active')
                                                    <span class="badge badge-success">{{ __('admin.enrollment_active') }}</span>
                                                    @break
                                                @case('completed')
                                                    <span class="badge badge-primary">{{ __('admin.enrollment_completed') }}</span>
                                                    @break
                                                @case('pending_payment')
                                                    <span class="badge badge-warning">{{ __('admin.enrollment_pending_payment') }}</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge badge-danger">{{ __('admin.enrollment_cancelled') }}</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-secondary">{{ $enrollment->status }}</span>
                                            @endswitch
                                        </td>
                                        <td class="text-center align-middle">
                                            @switch($enrollment->payment_status)
                                                @case('paid')
                                                    <span class="badge badge-success">{{ __('admin.payment_paid') }}</span>
                                                    @break
                                                @case('pending')
                                                    <span class="badge badge-warning">{{ __('admin.payment_pending') }}</span>
                                                    @break
                                                @case('failed')
                                                    <span class="badge badge-danger">{{ __('admin.payment_failed') }}</span>
                                                    @break
                                                @case('refunded')
                                                    <span class="badge badge-info">{{ __('admin.payment_refunded') }}</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-secondary">{{ $enrollment->payment_status }}</span>
                                            @endswitch
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: {{ $enrollment->progress_percentage ?? 0 }}%;" 
                                                     aria-valuenow="{{ $enrollment->progress_percentage ?? 0 }}" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    {{ $enrollment->progress_percentage ?? 0 }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">{{ $enrollment->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <div class="empty-state">
                <img src="{{ asset('admin/app-assets/images/pages/404.png') }}"
                     alt="{{ __('admin.no_courses_found') }}"
                     class="img-fluid mb-3"
                     style="max-width: 200px;">
                <h5 class="text-muted mb-2">{{ __('admin.no_courses_found') }}</h5>
                <p class="text-muted" style="font-family: cairo">
                    {{ __('admin.there_are_no_matches_matching') }}
                </p>
            </div>
        </div>
    @endif
</div>
