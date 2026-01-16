@extends('admin.layout.master')

@section('css')
    <style>
        .course-info-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #f8f9fa;
        }
        .stage-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #fff;
        }
        .course-image {
            max-width: 300px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .info-row {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
        }
        .status-badge {
            font-size: 14px;
            padding: 8px 16px;
        }
    </style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">{{ __('admin.course_details') }}</h4>
                <div>
                    <a href="{{ route('admin.courses.edit', $course->id) }}" class="btn btn-primary">
                        <i class="la la-edit"></i> {{ __('admin.edit_course') }}
                    </a>
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">
                        <i class="la la-arrow-left"></i> {{ __('admin.back_to_courses') }}
                    </a>
                </div>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <div class="row">
                        <!-- Course Image -->
                        <div class="col-md-4 text-center">
                            <img src="{{ $course->getfirstMediaUrl('courses') }}" alt="Course Image" class="course-image img-fluid">
                        </div>

                        <!-- Course Information -->
                        <div class="col-md-8">
                            <div class="course-info-card">
                                <div class="row info-row">
                                    <div class="col-md-3 info-label">{{ __('admin.course_name_ar') }}:</div>
                                    <div class="col-md-9">{{ $course->getTranslation('name', 'ar') }}</div>
                                </div>

                                <div class="row info-row">
                                    <div class="col-md-3 info-label">{{ __('admin.course_name_en') }}:</div>
                                    <div class="col-md-9">{{ $course->getTranslation('name', 'en') }}</div>
                                </div>

                                <div class="row info-row">
                                    <div class="col-md-3 info-label">{{ __('admin.instructor_name_ar') }}:</div>
                                    <div class="col-md-9">{{ $course->getTranslation('instructor_name', 'ar') }}</div>
                                </div>

                                <div class="row info-row">
                                    <div class="col-md-3 info-label">{{ __('admin.instructor_name_en') }}:</div>
                                    <div class="col-md-9">{{ $course->getTranslation('instructor_name', 'en') }}</div>
                                </div>

                                <div class="row info-row">
                                    <div class="col-md-3 info-label">{{ __('admin.course_duration') }}:</div>
                                    <div class="col-md-9">{{ $course->duration }} {{ __('admin.hours') }}</div>
                                </div>

                                <div class="row info-row">
                                    <div class="col-md-3 info-label">{{ __('admin.course_price') }}:</div>
                                    <div class="col-md-9">{{ number_format($course->price, 2) }} {{ __('admin.riyal') }}</div>
                                </div>

                                <div class="row info-row">
                                    <div class="col-md-3 info-label">{{ __('admin.course_stages_count') }}:</div>
                                    <div class="col-md-9">
                                        <span class="badge badge-info">{{ $course->stages_count }} {{ __('admin.stage') }}</span>
                                    </div>
                                </div>

                                <div class="row info-row">
                                    <div class="col-md-3 info-label">{{ __('admin.course_status') }}:</div>
                                    <div class="col-md-9">
                                        @if ($course->is_active)
                                            <span class="badge badge-success status-badge">{{ __('admin.course_active') }}</span>
                                        @else
                                            <span class="badge badge-danger status-badge">{{ __('admin.course_inactive') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="row info-row">
                                    <div class="col-md-3 info-label">{{ __('admin.creation_date') }}:</div>
                                    <div class="col-md-9">{{ $course->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Course Description -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>{{ __('admin.course_description') }}</h5>
                            <div class="course-info-card">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>{{ __('admin.course_description_ar') }}:</h6>
                                        <p>{{ $course->getTranslation('description', 'ar') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>{{ __('admin.course_description_en') }}:</h6>
                                        <p>{{ $course->getTranslation('description', 'en') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Course Stages -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>{{ __('admin.course_stages') }}</h5>
                            @if($course->stages->count() > 0)
                                @foreach($course->stages as $index => $stage)
                                    <div class="stage-card">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">{{ __('admin.stage_number') }} {{ $index + 1 }}</h6>
                                            <span class="badge badge-primary">{{ __('admin.stage_number') }} {{ $stage->order }}</span>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-row">
                                                    <span class="info-label">{{ __('admin.stage_title_ar') }}:</span>
                                                    {{ $stage->getTranslation('title', 'ar') }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-row">
                                                    <span class="info-label">{{ __('admin.stage_title_en') }}:</span>
                                                    {{ $stage->getTranslation('title', 'en') }}
                                                </div>
                                            </div>
                                        </div>

                                        @if($stage->video)
                                            <div class="row mt-2">
                                                <div class="col-12">
                                                    <div class="info-row">
                                                        <span class="info-label">{{ __('admin.stage_video') }}:</span>
                                                        <a href="{{ $stage->video }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="la la-play"></i> {{ __('admin.watch_video') }}
                                                        </a>
                                                        @if($stage->video_name)
                                                            <span class="ml-2 text-muted">{{ $stage->video_name }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info">
                                    <i class="la la-info-circle"></i> {{ __('admin.no_stages_yet') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Course Enrollments -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>{{ __('admin.course_enrollments') }}</h5>
                            @if($course->enrollments->count() > 0)
                                <div class="course-info-card">
                                    <!-- Enrollment Statistics -->
                                    <div class="row mb-4">
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 class="text-primary">{{ $course->enrollments->count() }}</h4>
                                                <small class="text-muted">{{ __('admin.total_enrollments') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 class="text-success">{{ $course->enrollments->where('status', 'active')->count() }}</h4>
                                                <small class="text-muted">{{ __('admin.active_enrollments') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 class="text-info">{{ $course->enrollments->where('status', 'completed')->count() }}</h4>
                                                <small class="text-muted">{{ __('admin.completed_enrollments') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 class="text-warning">{{ $course->enrollments->where('payment_status', 'pending')->count() }}</h4>
                                                <small class="text-muted">{{ __('admin.pending_payments') }}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Enrollments Table -->
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ __('admin.student_name') }}</th>
                                                    <th>{{ __('admin.student_email') }}</th>
                                                    <th>{{ __('admin.enrollment_date') }}</th>
                                                    <th>{{ __('admin.enrollment_status') }}</th>
                                                    <th>{{ __('admin.payment_status') }}</th>
                                                    <th>{{ __('admin.payment_method') }}</th>
                                                    <th>{{ __('admin.amount_paid') }}</th>
                                                    <th>{{ __('admin.progress_percentage') }}</th>
                                                    <th>{{ __('admin.completion_date') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($course->enrollments as $index => $enrollment)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>
                                                            <div class="d-flex align-items-center">

                                                                <span>{{ $enrollment->user->name }}</span>
                                                            </div>
                                                        </td>
                                                        <td>{{ $enrollment->user->email }}</td>
                                                        <td>{{ $enrollment->enrolled_at->format('d/m/Y H:i') }}</td>
                                                        <td>
                                                            @switch($enrollment->status)
                                                                @case('active')
                                                                    <span class="badge badge-success">{{ __('admin.enrollment_active') }}</span>
                                                                    @break
                                                                @case('completed')
                                                                    <span class="badge badge-info">{{ __('admin.enrollment_completed') }}</span>
                                                                    @break
                                                                @case('pending_payment')
                                                                    <span class="badge badge-warning">{{ __('admin.enrollment_pending_payment') }}</span>
                                                                    @break
                                                                @case('cancelled')
                                                                    <span class="badge badge-danger">{{ __('admin.enrollment_cancelled') }}</span>
                                                                    @break
                                                                @case('failed')
                                                                    <span class="badge badge-dark">{{ __('admin.enrollment_failed') }}</span>
                                                                    @break
                                                                @default
                                                                    <span class="badge badge-secondary">{{ $enrollment->status }}</span>
                                                            @endswitch
                                                        </td>
                                                        <td>
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
                                                        <td>
                                                            @switch($enrollment->payment_method)
                                                                @case('wallet')
                                                                    <i class="la la-wallet text-primary"></i> {{ __('admin.payment_wallet') }}
                                                                    @break
                                                                @case('credit_card')
                                                                    <i class="la la-credit-card text-info"></i> {{ __('admin.payment_credit_card') }}
                                                                    @break
                                                                @case('mada')
                                                                    <i class="la la-credit-card text-success"></i> {{ __('admin.payment_mada') }}
                                                                    @break
                                                                @case('apple_pay')
                                                                    <i class="la la-apple text-dark"></i> {{ __('admin.payment_apple_pay') }}
                                                                    @break
                                                                @case('bank_transfer')
                                                                    <i class="la la-bank text-warning"></i> {{ __('admin.payment_bank_transfer') }}
                                                                    @break
                                                                @default
                                                                    {{ $enrollment->payment_method }}
                                                            @endswitch
                                                        </td>
                                                        <td>{{ number_format($enrollment->amount_paid, 2) }} {{ __('admin.riyal') }}</td>
                                                        <td>
                                                            <div class="progress" style="height: 20px;">
                                                                <div class="progress-bar bg-primary"
                                                                     role="progressbar"
                                                                     style="width: {{ $enrollment->progress_percentage }}%"
                                                                     aria-valuenow="{{ $enrollment->progress_percentage }}"
                                                                     aria-valuemin="0"
                                                                     aria-valuemax="100">
                                                                    {{ number_format($enrollment->progress_percentage, 1) }}%
                                                                </div>
                                                            </div>
                                                            <small class="text-muted">
                                                                {{ $enrollment->completed_stages_count ?? $enrollment->completedStages()->count() }}/{{ $enrollment->course->stages()->count() }} {{ __('admin.stages') }}
                                                                @if($enrollment->total_time_spent)
                                                                    <br>{{ $enrollment->formatted_time_spent }}
                                                                @endif
                                                            </small>
                                                        </td>
                                                        <td>
                                                            @if($enrollment->completed_at)
                                                                {{ $enrollment->completed_at->format('d/m/Y H:i') }}
                                                            @else
                                                                <span class="text-muted">{{ __('admin.not_completed') }}</span>
                                                            @endif
                                                        </td>

                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="la la-info-circle"></i> {{ __('admin.no_enrollments_yet') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

