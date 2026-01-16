@extends('admin.layout.master')

@section('content')
<section id="multiple-column-form">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <form class="show form-horizontal">
                            <div class="form-body">
                                <div class="row">

                                    {{-- Basic info --}}
                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label>{{ __('admin.transaction_id') }}</label>
                                            <input type="text" value="#{{ $transaction->id }}" class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label>{{ __('admin.user') }}</label>
                                            <input type="text" value="{{ $transaction->user ? $transaction->user->name . ' (' . $transaction->user->email . ')' : __('admin.no_user') }}" class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label>{{ __('admin.amount') }}</label>
                                            <input type="text" value="{{ number_format($transaction->amount,2) }} {{ __('admin.sar') }}" class="form-control" disabled>
                                        </div>
                                    </div>

                                    {{-- Type / Status / Payment --}}
                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label>{{ __('admin.transaction_type') }}</label>
                                            @php
                                                $typeKey = $transaction->type ?? null;
                                                $typeLabel = $typeKey ? (Lang::has('admin.' . $typeKey) ? __('admin.' . $typeKey) : ucfirst(str_replace(['-','_'], ' ', $typeKey))) : '-';
                                            @endphp
                                            <input type="text" value="{{ $typeLabel }}" class="form-control" disabled>
                                        </div>
                                    </div>

                                    {{-- Removed payment method from show page as requested --}}

                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label>{{ __('admin.status') }}</label>
                                            @php
                                                $statusKey = 'admin.' . ($transaction->status ?? '');
                                                if ($transaction->status && Lang::has($statusKey)) {
                                                    $statusLabel = trans($statusKey);
                                                } elseif ($transaction->status) {
                                                    $statusLabel = ucfirst(str_replace('_', ' ', $transaction->status));
                                                } else {
                                                    $statusLabel = '-';
                                                }
                                            @endphp
                                            <input type="text" value="{{ $statusLabel }}" class="form-control" disabled>
                                        </div>
                                    </div>

                                    {{-- References --}}
                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label>{{ __('admin.reference_number') }}</label>
                                            <input type="text" value="{{ $transaction->reference ?? '-' }}" class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label>{{ __('admin.transfer_reference') }}</label>
                                            <input type="text" value="{{ $transaction->transfer_reference ?? $transaction->reference ?? '-' }}" class="form-control" disabled>
                                        </div>
                                    </div>

                                    {{-- Bank details: shown only for wallet withdraw transactions --}}
                                    @if (($transaction->type ?? null) === 'wallet-withdraw')
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label>{{ __('admin.bank_name') }}</label>
                                                <input type="text" value="{{ $transaction->bank_name ?? '-' }}" class="form-control" disabled>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label>{{ __('admin.account_holder_name') }}</label>
                                                <input type="text" value="{{ $transaction->account_holder_name ?? '-' }}" class="form-control" disabled>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label>{{ __('admin.account_number') }}</label>
                                                <input type="text" value="{{ $transaction->account_number ?? '-' }}" class="form-control" disabled>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label>{{ __('admin.iban') }}</label>
                                                <input type="text" value="{{ $transaction->iban ?? '-' }}" class="form-control" disabled>
                                            </div>
                                        </div>
                                    @endif

                              
                                    {{-- Notes (may be array/json) --}}
                                    <div class="col-md-12 col-12">
                                        <div class="form-group">
                                            <label>{{ __('admin.notes') }}</label>
                                            @php
                                                $locale = app()->getLocale();
                                                $note = $transaction->note;
                                                $noteText = '-';

                                                if (is_array($note)) {
                                                    if (isset($note[$locale])) {
                                                        $noteText = is_array($note[$locale])
                                                            ? json_encode($note[$locale], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)
                                                            : $note[$locale];
                                                    } elseif (isset($note['en']) || isset($note['ar'])) {
                                                        $noteText = $note[$locale] ?? ($note['en'] ?? $note['ar']);
                                                    } else {
                                                        $noteText = json_encode($note, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
                                                    }
                                                } elseif (is_string($note)) {
                                                    $decoded = json_decode($note, true);
                                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                        if (isset($decoded[$locale])) {
                                                            $noteText = is_array($decoded[$locale])
                                                                ? json_encode($decoded[$locale], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)
                                                                : $decoded[$locale];
                                                        } elseif (isset($decoded['en']) || isset($decoded['ar'])) {
                                                            $noteText = $decoded[$locale] ?? ($decoded['en'] ?? $decoded['ar']);
                                                        } else {
                                                            $noteText = json_encode($decoded, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
                                                        }
                                                    } else {
                                                        $noteText = $note ?? '-';
                                                    }
                                                } else {
                                                    $noteText = $note ?? '-';
                                                }
                                            @endphp
                                            <textarea class="form-control" rows="4" disabled>{{ $noteText }}</textarea>
                                        </div>
                                    </div>

                                    {{-- Timestamps --}}
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>{{ __('admin.created_at') }}</label>
                                            <input type="text" value="{{ $transaction->created_at->format('Y-m-d H:i:s') }}" class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>{{ __('admin.updated_at') }}</label>
                                            <input type="text" value="{{ $transaction->updated_at->format('Y-m-d H:i:s') }}" class="form-control" disabled>
                                        </div>
                                    </div>


                                    {{-- Buttons --}}
                                    <div class="col-12 d-flex justify-content-center mt-3">
                                        <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-warning mr-1 mb-1">{{ __('admin.back') }}</a>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script>
    $('.show input, .show textarea, .show select').attr('disabled', true);
</script>
@endsection
