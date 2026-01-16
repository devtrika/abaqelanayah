@extends('admin.layout.master')
{{-- extra css files --}}
@section('css')
    <link rel="stylesheet" type="text/css"
        href="{{ asset('admin/app-assets/css-rtl/plugins/forms/validation/form-validation.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/extensions/toastr.css') }}">
@endsection
{{-- extra css files --}}

@section('content')
    <!-- // Basic multiple Column Form section start -->
    <form method="POST" action="{{ route('admin.branches.store') }}" class="store form-horizontal" novalidate
        enctype="multipart/form-data">
        <section id="multiple-column-form">
            <div class="row">

                <div class="col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                @csrf

                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="basic-info-tab" data-toggle="tab" href="#basic-info"
                                            aria-controls="basic-info" role="tab" aria-selected="true">
                                            <i class="feather icon-info"></i> {{ __('admin.basic_information') }}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="working-hours-tab" data-toggle="tab" href="#working-hours"
                                            aria-controls="working-hours" role="tab" aria-selected="false">
                                            <i class="feather icon-clock"></i> {{ __('admin.working_hours') }}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="delivery-hours-tab" data-toggle="tab" href="#delivery-hours"
                                            aria-controls="delivery-hours" role="tab" aria-selected="false">
                                            <i class="feather icon-truck"></i> {{ __('admin.delivery_hours') }}
                                        </a>
                                    </li>
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <!-- Basic Information Tab -->
                                    <div class="tab-pane active" id="basic-info" role="tabpanel"
                                        aria-labelledby="basic-info-tab">
                                        <div class="form-body mt-3">
                                            <div class="row">
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="name">{{ __('admin.name') }}</label>
                                                        <div class="controls">
                                                            <input type="text" name="name" value="{{ old('name') }}"
                                                                class="form-control @error('name') is-invalid @enderror"
                                                                placeholder="{{ __('admin.write_the_name') }}" required
                                                                data-validation-required-message="{{ __('admin.this_field_is_required') }}">
                                                            @error('name')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="phone">{{ __('admin.phone_number') }}</label>
                                                        <div class="controls">
                                                            <input type="text" name="phone"
                                                                value="{{ old('phone') }}"
                                                                class="form-control @error('phone') is-invalid @enderror"
                                                                placeholder="{{ __('admin.phone_number') }}" required
                                                                data-validation-required-message="{{ __('admin.this_field_is_required') }}">
                                                            @error('phone')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="email">{{ __('admin.email') }}</label>
                                                        <div class="controls">
                                                            <input type="email" name="email"
                                                                value="{{ old('email') }}"
                                                                class="form-control @error('email') is-invalid @enderror"
                                                                placeholder="{{ __('admin.email') }}" required
                                                                data-validation-required-message="{{ __('admin.this_field_is_required') }}">
                                                            @error('email')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="address">{{ __('admin.address') }}</label>
                                                        <div class="controls">
                                                            <input type="text" name="address"
                                                                value="{{ old('address') }}"
                                                                class="form-control @error('address') is-invalid @enderror"
                                                                placeholder="{{ __('admin.address') }}" required
                                                                data-validation-required-message="{{ __('admin.this_field_is_required') }}">
                                                            @error('address')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>



                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="is_active">{{ __('admin.status') }}</label>
                                                        <div class="controls">
                                                            <select name="status"
                                                                class="form-control @error('status') is-invalid @enderror"
                                                                required>
                                                                <option value="1"
                                                                    {{ old('status', 1) == 1 ? 'selected' : '' }}>
                                                                    {{ __('admin.active') }}</option>
                                                                <option value="0"
                                                                    {{ old('status', 0) == 0 ? 'selected' : '' }}>
                                                                    {{ __('admin.inactive') }}</option>
                                                            </select>
                                                            @error('status')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Delivery Configuration Section -->
                                            <div class="row mt-4">
                                                <div class="col-12">
                                                    <h6 class="mb-3">{{ __('admin.delivery_configuration') }}</h6>
                                                </div>

                                            
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label
                                                            for="expected_duration">{{ __('admin.expected_duration') }}
                                                            ({{ __('admin.minutes') }})</label>
                                                        <div class="controls">
                                                            <input type="number" name="expected_duration"
                                                                value="{{ old('expected_duration') }}"
                                                                class="form-control @error('expected_duration') is-invalid @enderror"
                                                                placeholder="{{ __('admin.expected_duration') }}"
                                                                min="1">
                                                            @error('expected_duration')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                       
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label
                                                            for="last_order_time">{{ __('admin.last_order_time') }}</label>
                                                        <div class="controls">
                                                            <input type="time" name="last_order_time"
                                                                value="{{ old('last_order_time') }}"
                                                                class="form-control @error('last_order_time') is-invalid @enderror">
                                                            @error('last_order_time')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                            
                                           
                                            </div>

                                            <!-- Location and Delivery Area Section -->
                                            <div class="row mt-4">
                                                <div class="col-12">
                                                    <h6 class="mb-3">{{ __('admin.location_and_delivery_area') }}</h6>

                                                    <!-- Location Picker Section -->

                                                    {{-- Branch Location (Lat/Lng) --}}
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label>{{ __('admin.select_location_point_on_map') ?? 'Select Branch Location (Point)' }}</label>
                                                            <div id="location-map"
                                                                style="height: 300px; border: 1px solid #ccc;"></div>
                                                            <input type="hidden" name="latitude" id="latitude" required
                                                                data-validation-required-message="{{ __('admin.this_field_is_required') }}">
                                                            <input type="hidden" name="longitude" id="longitude"
                                                                required
                                                                data-validation-required-message="{{ __('admin.this_field_is_required') }}">
                                                            <small
                                                                class="form-text text-muted">{{ __('admin.location_point_hint') ?? 'Click on the map to select the branch location.' }}</small>
                                                        </div>
                                                    </div>

                                                    {{-- Branch Area (Polygon) --}}
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label>{{ __('admin.select_location_polygon_on_map') ?? 'Draw Branch Area (Polygon)' }}</label>
                                                            <div id="polygon-map"
                                                                style="height: 300px; border: 1px solid #ccc;"></div>
                                                            <input type="hidden" name="polygon" id="polygon" required
                                                                data-validation-required-message="{{ __('admin.this_field_is_required') }}">
                                                            <small
                                                                class="form-text text-muted">{{ __('admin.draw_polygon_hint') ?? 'Draw the branch area on the map.' }}</small>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                    <!-- Working Hours Tab -->
                                    <div class="tab-pane" id="working-hours" role="tabpanel"
                                        aria-labelledby="working-hours-tab">
                                        <div class="form-body mt-3">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h6 class="mb-0">{{ __('admin.working_hours') }}</h6>
                                                        <button type="button" class="btn btn-primary btn-sm"
                                                            id="add-working-hour">
                                                            <i class="feather icon-plus"></i>
                                                            {{ __('admin.add_working_hour') }}
                                                        </button>
                                                    </div>

                                                    <!-- Add Working Hour Form -->
                                                    <div class="card mb-3" id="working-hour-form" style="display: none;">
                                                        <div class="card-header">
                                                            <h6 class="mb-0">{{ __('admin.add_new_working_hour') }}</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label
                                                                            for="working_day">{{ __('admin.day') }}</label>
                                                                        <select class="form-control" id="working_day">
                                                                            <option value="">
                                                                                {{ __('admin.select_day') }}</option>
                                                                            <option value="sunday">
                                                                                {{ __('admin.sunday') }}</option>
                                                                            <option value="monday">
                                                                                {{ __('admin.monday') }}</option>
                                                                            <option value="tuesday">
                                                                                {{ __('admin.tuesday') }}</option>
                                                                            <option value="wednesday">
                                                                                {{ __('admin.wednesday') }}</option>
                                                                            <option value="thursday">
                                                                                {{ __('admin.thursday') }}</option>
                                                                            <option value="friday">
                                                                                {{ __('admin.friday') }}</option>
                                                                            <option value="saturday">
                                                                                {{ __('admin.saturday') }}</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label
                                                                            for="working_start_time">{{ __('admin.start_time') }}</label>
                                                                        <input type="time" class="form-control"
                                                                            id="working_start_time" value="09:00">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label
                                                                            for="working_end_time">{{ __('admin.end_time') }}</label>
                                                                        <input type="time" class="form-control"
                                                                            id="working_end_time" value="22:00">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label>&nbsp;</label>
                                                                        <div class="d-flex">
                                                                            <button type="button"
                                                                                class="btn btn-success btn-sm mr-1"
                                                                                id="save-working-hour">
                                                                                <i class="feather icon-check"></i>
                                                                                {{ __('admin.add') }}
                                                                            </button>
                                                                            <button type="button"
                                                                                class="btn btn-secondary btn-sm"
                                                                                id="cancel-working-hour">
                                                                                <i class="feather icon-x"></i>
                                                                                {{ __('admin.cancel') }}
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Working Hours Table -->
                                                    <div class="table-responsive">
                                                        <table class="table table-striped" id="working-hours-table">
                                                            <thead>
                                                                <tr>
                                                                    <th>{{ __('admin.day') }}</th>
                                                                    <th>{{ __('admin.start_time') }}</th>
                                                                    <th>{{ __('admin.end_time') }}</th>
                                                                    <th>{{ __('admin.actions') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="working-hours-tbody">
                                                                <!-- Working hours will be populated here -->
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <!-- Hidden inputs for form submission -->
                                                    <div id="working-hours-inputs"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delivery Hours Tab -->
                                    <div class="tab-pane" id="delivery-hours" role="tabpanel"
                                        aria-labelledby="delivery-hours-tab">
                                        <div class="form-body mt-3">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h6 class="mb-0">{{ __('admin.delivery_hours') }}</h6>
                                                        <button type="button" class="btn btn-success btn-sm"
                                                            id="add-delivery-hour">
                                                            <i class="feather icon-plus"></i>
                                                            {{ __('admin.add_delivery_hour') }}
                                                        </button>
                                                    </div>

                                                    <!-- Add Delivery Hour Form -->
                                                    <div class="card mb-3" id="delivery-hour-form"
                                                        style="display: none;">
                                                        <div class="card-header">
                                                            <h6 class="mb-0">{{ __('admin.add_new_delivery_hour') }}
                                                            </h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label
                                                                            for="delivery_day">{{ __('admin.day') }}</label>
                                                                        <select class="form-control" id="delivery_day">
                                                                            <option value="">
                                                                                {{ __('admin.select_day') }}</option>
                                                                            <option value="sunday">
                                                                                {{ __('admin.sunday') }}</option>
                                                                            <option value="monday">
                                                                                {{ __('admin.monday') }}</option>
                                                                            <option value="tuesday">
                                                                                {{ __('admin.tuesday') }}</option>
                                                                            <option value="wednesday">
                                                                                {{ __('admin.wednesday') }}</option>
                                                                            <option value="thursday">
                                                                                {{ __('admin.thursday') }}</option>
                                                                            <option value="friday">
                                                                                {{ __('admin.friday') }}</option>
                                                                            <option value="saturday">
                                                                                {{ __('admin.saturday') }}</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label
                                                                            for="delivery_start_time">{{ __('admin.start_time') }}</label>
                                                                        <input type="time" class="form-control"
                                                                            id="delivery_start_time" value="09:00">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label
                                                                            for="delivery_end_time">{{ __('admin.end_time') }}</label>
                                                                        <input type="time" class="form-control"
                                                                            id="delivery_end_time" value="22:00">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label>&nbsp;</label>
                                                                        <div class="d-flex">
                                                                            <button type="button"
                                                                                class="btn btn-success btn-sm mr-1"
                                                                                id="save-delivery-hour">
                                                                                <i class="feather icon-check"></i>
                                                                                {{ __('admin.add') }}
                                                                            </button>
                                                                            <button type="button"
                                                                                class="btn btn-secondary btn-sm"
                                                                                id="cancel-delivery-hour">
                                                                                <i class="feather icon-x"></i>
                                                                                {{ __('admin.cancel') }}
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Delivery Hours Table -->
                                                    <div class="table-responsive">
                                                        <table class="table table-striped" id="delivery-hours-table">
                                                            <thead>
                                                                <tr>
                                                                    <th>{{ __('admin.day') }}</th>
                                                                    <th>{{ __('admin.start_time') }}</th>
                                                                    <th>{{ __('admin.end_time') }}</th>
                                                                    <th>{{ __('admin.actions') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="delivery-hours-tbody">
                                                                <!-- Delivery hours will be populated here -->
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <!-- Hidden inputs for form submission -->
                                                    <div id="delivery-hours-inputs"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 d-flex justify-content-center mt-3">
                                    <button type="submit"
                                        class="btn btn-primary mr-1 mb-1 submit_button">{{ __('admin.add') }}</button>
                                    <a href="{{ url()->previous() }}" type="reset"
                                        class="btn btn-outline-warning mr-1 mb-1">{{ __('admin.back') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </form>
@endsection

@section('js')
    <script src="{{ asset('admin/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js') }}"></script>
    <script src="{{ asset('admin/app-assets/js/scripts/forms/validation/form-validation.js') }}"></script>
    <script src="{{ asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('admin/app-assets/js/scripts/extensions/sweet-alerts.js') }}"></script>
    <script src="{{ asset('admin/app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    {{-- show selected image script --}}
    @include('admin.shared.addImage')
    {{-- show selected image script --}}

    {{-- submit add form script --}}
    @include('admin.shared.submitAddForm')
    {{-- submit add form script --}}

    {{-- Working and Delivery hours toggle functionality --}}
    <script>
        $(document).ready(function() {
            // Working hours toggle functionality
            $('.working-hours-toggle').on('change', function() {
                var dayKey = $(this).attr('id').replace('working_is_working_', '');
                var startTimeInput = $('#working_start_time_' + dayKey);
                var endTimeInput = $('#working_end_time_' + dayKey);

                if ($(this).is(':checked')) {
                    startTimeInput.prop('disabled', false);
                    endTimeInput.prop('disabled', false);
                } else {
                    startTimeInput.prop('disabled', true);
                    endTimeInput.prop('disabled', true);
                }
            });

            // Delivery hours toggle functionality
            $('.delivery-hours-toggle').on('change', function() {
                var dayKey = $(this).attr('id').replace('delivery_is_working_', '');
                var startTimeInput = $('#delivery_start_time_' + dayKey);
                var endTimeInput = $('#delivery_end_time_' + dayKey);

                if ($(this).is(':checked')) {
                    startTimeInput.prop('disabled', false);
                    endTimeInput.prop('disabled', false);
                } else {
                    startTimeInput.prop('disabled', true);
                    endTimeInput.prop('disabled', true);
                }
            });

            // Initialize the state on page load
            $('.working-hours-toggle').trigger('change');
            $('.delivery-hours-toggle').trigger('change');

            // Initialize Google Maps when document is ready
            setTimeout(function() {
                if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
                    initMaps();
                }
            }, 1000);
        });
    </script>

    <!-- Google Maps API -->
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
    <script>
            // Location (Lat/Lng) Map
            let locationMap = L.map('location-map').setView([30.0444, 31.2357], 6);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(locationMap);
            let marker;
            locationMap.on('click', function(e) {
                if (marker) {
                    marker.setLatLng(e.latlng);
                } else {
                    marker = L.marker(e.latlng).addTo(locationMap);
                }
                document.getElementById('latitude').value = e.latlng.lat;
                document.getElementById('longitude').value = e.latlng.lng;
            });
            // Add search control
            L.Control.geocoder({
                    defaultMarkGeocode: false
                })
                .on('markgeocode', function(e) {
                    var latlng = e.geocode.center;
                    locationMap.setView(latlng, 16);
                    if (marker) {
                        marker.setLatLng(latlng);
                    } else {
                        marker = L.marker(latlng).addTo(locationMap);
                    }
                    document.getElementById('latitude').value = latlng.lat;
                    document.getElementById('longitude').value = latlng.lng;
                })
                .addTo(locationMap);

        // Polygon Map
        let polygonMap = L.map('polygon-map').setView([30.0444, 31.2357], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(polygonMap);
        let drawnItems = new L.FeatureGroup();
        polygonMap.addLayer(drawnItems);
        let drawControl = new L.Control.Draw({
            draw: {
                polygon: true,
                marker: false,
                polyline: false,
                rectangle: false,
                circle: false,
                circlemarker: false
            },
            edit: {
                featureGroup: drawnItems
            }
        });
        polygonMap.addControl(drawControl);
        // Add search control for polygon map
        L.Control.geocoder({
                defaultMarkGeocode: false
            })
            .on('markgeocode', function(e) {
                var latlng = e.geocode.center;
                polygonMap.setView(latlng, 16);
            })
            .addTo(polygonMap);
        polygonMap.on(L.Draw.Event.CREATED, function(e) {
            drawnItems.clearLayers();
            let layer = e.layer;
            drawnItems.addLayer(layer);
            let coords = layer.getLatLngs();
            document.getElementById('polygon').value = JSON.stringify(coords);
        });
        polygonMap.on('draw:edited', function(e) {
            let layers = e.layers;
            layers.eachLayer(function(layer) {
                let coords = layer.getLatLngs();
                document.getElementById('polygon').value = JSON.stringify(coords);
            });
        });

        // Working Hours Management
        let workingHours = [];
        let workingHourIndex = 0;

        // Delivery Hours Management
        let deliveryHours = [];
        let deliveryHourIndex = 0;

        // Day names mapping
        const dayNames = {
            'sunday': '{{ __('admin.sunday') }}',
            'monday': '{{ __('admin.monday') }}',
            'tuesday': '{{ __('admin.tuesday') }}',
            'wednesday': '{{ __('admin.wednesday') }}',
            'thursday': '{{ __('admin.thursday') }}',
            'friday': '{{ __('admin.friday') }}',
            'saturday': '{{ __('admin.saturday') }}'
        };

        // Working Hours Functions
        $('#add-working-hour').click(function() {
            $('#working-hour-form').show();
            $(this).hide();
        });

        $('#cancel-working-hour').click(function() {
            $('#working-hour-form').hide();
            $('#add-working-hour').show();
            resetWorkingHourForm();
        });

        $('#save-working-hour').click(function() {
            const day = $('#working_day').val();
            const startTime = $('#working_start_time').val();
            const endTime = $('#working_end_time').val();

            if (!day || !startTime || !endTime) {
                toastr.error('{{ __('admin.please_fill_all_fields') }}');
                return;
            }

            // Check if day already exists
            if (workingHours.find(h => h.day === day)) {
                toastr.error('{{ __('admin.day_already_exists') }}');
                return;
            }

            // Validate time
            if (startTime >= endTime) {
                toastr.error('{{ __('admin.start_time_must_be_before_end_time') }}');
                return;
            }

            const workingHour = {
                id: workingHourIndex++,
                day: day,
                start_time: startTime,
                end_time: endTime
            };

            workingHours.push(workingHour);
            addWorkingHourToTable(workingHour);
            updateWorkingHoursInputs();

            $('#working-hour-form').hide();
            $('#add-working-hour').show();
            resetWorkingHourForm();

            toastr.success('{{ __('admin.working_hour_added_successfully') }}');
        });

        function addWorkingHourToTable(workingHour) {
            const row = `
                <tr data-id="${workingHour.id}">
                    <td>${dayNames[workingHour.day]}</td>
                    <td>${workingHour.start_time}</td>
                    <td>${workingHour.end_time}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeWorkingHour(${workingHour.id})">
                            <i class="feather icon-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#working-hours-tbody').append(row);
        }

        function removeWorkingHour(id) {
            workingHours = workingHours.filter(h => h.id !== id);
            $(`#working-hours-tbody tr[data-id="${id}"]`).remove();
            updateWorkingHoursInputs();
            toastr.success('{{ __('admin.working_hour_removed_successfully') }}');
        }

        function updateWorkingHoursInputs() {
            $('#working-hours-inputs').empty();
            workingHours.forEach((hour, index) => {
                $('#working-hours-inputs').append(`
                    <input type="hidden" name="working_hours[${index}][day]" value="${hour.day}">
                    <input type="hidden" name="working_hours[${index}][start_time]" value="${hour.start_time}">
                    <input type="hidden" name="working_hours[${index}][end_time]" value="${hour.end_time}">
                    <input type="hidden" name="working_hours[${index}][is_working]" value="1">
                `);
            });
        }

        function resetWorkingHourForm() {
            $('#working_day').val('');
            $('#working_start_time').val('09:00');
            $('#working_end_time').val('22:00');
        }

        // Delivery Hours Functions
        $('#add-delivery-hour').click(function() {
            $('#delivery-hour-form').show();
            $(this).hide();
        });

        $('#cancel-delivery-hour').click(function() {
            $('#delivery-hour-form').hide();
            $('#add-delivery-hour').show();
            resetDeliveryHourForm();
        });

        $('#save-delivery-hour').click(function() {
            const day = $('#delivery_day').val();
            const startTime = $('#delivery_start_time').val();
            const endTime = $('#delivery_end_time').val();

            if (!day || !startTime || !endTime) {
                toastr.error('{{ __('admin.please_fill_all_fields') }}');
                return;
            }

            // Check if day already exists
            if (deliveryHours.find(h => h.day === day)) {
                toastr.error('{{ __('admin.day_already_exists') }}');
                return;
            }

            // Validate time
            if (startTime >= endTime) {
                toastr.error('{{ __('admin.start_time_must_be_before_end_time') }}');
                return;
            }

            const deliveryHour = {
                id: deliveryHourIndex++,
                day: day,
                start_time: startTime,
                end_time: endTime
            };

            deliveryHours.push(deliveryHour);
            addDeliveryHourToTable(deliveryHour);
            updateDeliveryHoursInputs();

            $('#delivery-hour-form').hide();
            $('#add-delivery-hour').show();
            resetDeliveryHourForm();

            toastr.success('{{ __('admin.delivery_hour_added_successfully') }}');
        });

        function addDeliveryHourToTable(deliveryHour) {
            const row = `
                <tr data-id="${deliveryHour.id}">
                    <td>${dayNames[deliveryHour.day]}</td>
                    <td>${deliveryHour.start_time}</td>
                    <td>${deliveryHour.end_time}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeDeliveryHour(${deliveryHour.id})">
                            <i class="feather icon-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#delivery-hours-tbody').append(row);
        }

        function removeDeliveryHour(id) {
            deliveryHours = deliveryHours.filter(h => h.id !== id);
            $(`#delivery-hours-tbody tr[data-id="${id}"]`).remove();
            updateDeliveryHoursInputs();
            toastr.success('{{ __('admin.delivery_hour_removed_successfully') }}');
        }

        function updateDeliveryHoursInputs() {
            $('#delivery-hours-inputs').empty();
            deliveryHours.forEach((hour, index) => {
                $('#delivery-hours-inputs').append(`
                    <input type="hidden" name="delivery_hours[${index}][day]" value="${hour.day}">
                    <input type="hidden" name="delivery_hours[${index}][start_time]" value="${hour.start_time}">
                    <input type="hidden" name="delivery_hours[${index}][end_time]" value="${hour.end_time}">
                    <input type="hidden" name="delivery_hours[${index}][is_working]" value="1">
                `);
            });
        }

        function resetDeliveryHourForm() {
            $('#delivery_day').val('');
            $('#delivery_start_time').val('09:00');
            $('#delivery_end_time').val('22:00');
        }
    </script>
@endsection
