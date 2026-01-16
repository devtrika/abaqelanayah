@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css') }}">
@endsection

@section('content')
<!-- Branch Details Section -->
<section id="multiple-column-form">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">{{ __('admin.branch_details') }} - {{ $branch->name }}</h4>
                    <div class="card-header-toolbar d-flex align-items-center">
                        <a href="{{ route('admin.branches.index') }}" class="btn btn-primary btn-sm mr-1">
                            <i class="feather icon-arrow-left"></i> {{ __('admin.back') }}
                        </a>
                        <a href="{{ route('admin.branches.edit', $branch->id) }}" class="btn btn-warning btn-sm">
                            <i class="feather icon-edit"></i> {{ __('admin.edit') }}
                        </a>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="basic-info-tab" data-toggle="tab" href="#basic-info" aria-controls="basic-info" role="tab" aria-selected="true">
                                    <i class="feather icon-info"></i> {{ __('admin.basic_information') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="working-hours-tab" data-toggle="tab" href="#working-hours" aria-controls="working-hours" role="tab" aria-selected="false">
                                    <i class="feather icon-clock"></i> {{ __('admin.working_hours') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="delivery-hours-tab" data-toggle="tab" href="#delivery-hours" aria-controls="delivery-hours" role="tab" aria-selected="false">
                                    <i class="feather icon-truck"></i> {{ __('admin.delivery_hours') }}
                                </a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <!-- Basic Information Tab -->
                            <div class="tab-pane active" id="basic-info" aria-labelledby="basic-info-tab" role="tabpanel">
                                <div class="row mt-2">
                                    <div class="col-md-9">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>{{ __('admin.name') }}:</strong></td>
                                                <td>{{ $branch->name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('admin.phone') }}:</strong></td>
                                                <td>{{ $branch->phone ?? __('admin.not_set') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('admin.email') }}:</strong></td>
                                                <td>{{ $branch->email ?? __('admin.not_set') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('admin.description') }}:</strong></td>
                                                <td>{{ $branch->description ?? __('admin.not_set') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('admin.created_at') }}:</strong></td>
                                                <td>{{ $branch->created_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('admin.updated_at') }}:</strong></td>
                                                <td>{{ $branch->updated_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Delivery Configuration Section -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h6 class="mb-3">{{ __('admin.delivery_configuration') }}</h6>
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>{{ __('admin.expected_duration') }}:</strong></td>
                                                <td>{{ $branch->expected_duration ? $branch->expected_duration . ' ' . __('admin.minutes') : __('admin.not_set') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('admin.last_order_time') }}:</strong></td>
                                                <td>{{ $branch->last_order_time ?? __('admin.not_set') }}</td>
                                            </tr>
                                         
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Working Hours Tab -->
                            <div class="tab-pane" id="working-hours" aria-labelledby="working-hours-tab" role="tabpanel">
                                @if($branch->workingHours && $branch->workingHours->count() > 0)
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('admin.day') }}</th>
                                                            <th>{{ __('admin.status') }}</th>
                                                            <th>{{ __('admin.start_time') }}</th>
                                                            <th>{{ __('admin.end_time') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $days = [
                                                                'sunday' => __('admin.sunday'),
                                                                'monday' => __('admin.monday'),
                                                                'tuesday' => __('admin.tuesday'),
                                                                'wednesday' => __('admin.wednesday'),
                                                                'thursday' => __('admin.thursday'),
                                                                'friday' => __('admin.friday'),
                                                                'saturday' => __('admin.saturday')
                                                            ];
                                                        @endphp
                                                        @foreach($days as $dayKey => $dayName)
                                                            @php
                                                                $workingHour = $branch->workingHours->where('day', $dayKey)->first();
                                                            @endphp
                                                            <tr>
                                                                <td><strong>{{ $dayName }}</strong></td>
                                                                <td>
                                                                    @if($workingHour && $workingHour->is_working)
                                                                        <span class="badge badge-success">{{ __('admin.open') }}</span>
                                                                    @else
                                                                        <span class="badge badge-danger">{{ __('admin.closed') }}</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($workingHour && $workingHour->is_working)
                                                                        {{ $workingHour->start_time ?? __('admin.not_set') }}
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($workingHour && $workingHour->is_working)
                                                                        {{ $workingHour->end_time ?? __('admin.not_set') }}
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-info mt-2">
                                        <i class="feather icon-info"></i> {{ __('admin.no_working_hours_found') }}
                                    </div>
                                @endif
                            </div>

                            <!-- Delivery Hours Tab -->
                            <div class="tab-pane" id="delivery-hours" aria-labelledby="delivery-hours-tab" role="tabpanel">
                                @if($branch->deliveryHours && $branch->deliveryHours->count() > 0)
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('admin.day') }}</th>
                                                            <th>{{ __('admin.status') }}</th>
                                                            <th>{{ __('admin.start_time') }}</th>
                                                            <th>{{ __('admin.end_time') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $days = [
                                                                'sunday' => __('admin.sunday'),
                                                                'monday' => __('admin.monday'),
                                                                'tuesday' => __('admin.tuesday'),
                                                                'wednesday' => __('admin.wednesday'),
                                                                'thursday' => __('admin.thursday'),
                                                                'friday' => __('admin.friday'),
                                                                'saturday' => __('admin.saturday')
                                                            ];
                                                        @endphp
                                                        @foreach($days as $dayKey => $dayName)
                                                            @php
                                                                $deliveryHour = $branch->deliveryHours->where('day', $dayKey)->first();
                                                            @endphp
                                                            <tr>
                                                                <td><strong>{{ $dayName }}</strong></td>
                                                                <td>
                                                                    @if($deliveryHour && $deliveryHour->is_working)
                                                                        <span class="badge badge-success">{{ __('admin.open') }}</span>
                                                                    @else
                                                                        <span class="badge badge-danger">{{ __('admin.closed') }}</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($deliveryHour && $deliveryHour->is_working)
                                                                        {{ $deliveryHour->start_time ?? __('admin.not_set') }}
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($deliveryHour && $deliveryHour->is_working)
                                                                        {{ $deliveryHour->end_time ?? __('admin.not_set') }}
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-info mt-2">
                                        <i class="feather icon-info"></i> {{ __('admin.no_delivery_hours_found') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
    <script src="{{asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
@endsection