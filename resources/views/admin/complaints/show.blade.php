@extends('admin.layout.master')

@section('content')
<section id="complaint-show">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-12">

            <div class="card mt-4">
                <div class="card-header">
                    <h4>{{ __('admin.complaint_details') }}</h4>
                </div>
                <div class="card-body">

                    <p><strong>{{ __('admin.date') }}:</strong>
                        {{ \Carbon\Carbon::parse($complaint->created_at)->format('d/m/Y H:i:s') }}
                    </p>

                    <p><strong>{{ __('admin.name_to_complain') }}:</strong>
                        {{ optional($complaint->user)->name ?? $complaint->name ?? __('admin.visitor') }}
                    </p>

                    <p><strong>{{ __('admin.phone_to_complain') }}:</strong>
                        {{ optional($complaint->user)->phone ?? $complaint->phone ?? __('admin.visitor') }}
                    </p>

                    <p><strong>{{ __('admin.email') }}:</strong>
                        {{ optional($complaint->user)->email ?? $complaint->email ?? __('admin.visitor') }}
                    </p>

                    <p><strong>{{ __('admin.type') }}:</strong>
                        {{ optional($complaint->user)->type ? __('admin.'.optional($complaint->user)->type) : __('admin.visitor') }}
                    </p>

                    <p><strong>{{ __('admin.title') }}:</strong>
                        {{ $complaint->title ?? __('admin.not_available') }}
                    </p>

                    <p><strong>{{ __('admin.message') }}:</strong><br>
                        {!! nl2br(e($complaint->body ?? __('admin.not_available'))) !!}
                    </p>

                    <p><strong>{{ __('admin.type') }}:</strong>
                        {{ __('admin.' . $complaint->type) }}
                    </p>

                    <p><strong>{{ __('admin.mark_as_read') }}:</strong>
                        {!! toggleBooleanView($complaint , route('admin.model.active' , ['model' =>'ContactUs' , 'id' => $complaint->id , 'action' => 'is_read'])) !!}
                    </p>

                </div>
            </div>

            <div class="text-center mt-4">
                {{-- <a href="{{ route('admin.complaints.index') }}" class="btn btn-secondary btn-lg px-4">
                    <i class="feather icon-arrow-left mr-1"></i> {{ __('admin.back_to_list') }}
                </a> --}}
            </div>

        </div>
    </div>
</section>
@endsection
