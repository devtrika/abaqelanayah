<div class="tab-pane fade" id="loyalty-points">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">{{ __('admin.loyalty_points') }}</h5>
        </div>
        <div class="card-body">

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="alert alert-info">
                        <strong>{{ __('admin.current_loyalty_points') }}:</strong>
                        {{ $row->loyalty_points ?? 0 }}
                    </div>
                </div>
            </div>

            <h6>{{ __('admin.loyalty_point_transactions') }}</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('admin.amount') }}</th>
                            <th>{{ __('admin.type') }}</th>
                            <th>{{ __('admin.note') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $transactions = \App\Models\Transaction::where('user_id', $row->id)
                                ->whereIn('type', ['loyalty_reward', 'loyalty_spent'])
                                ->orderByDesc('id')
                                ->get();
                        @endphp
                        @foreach($transactions as $transaction)
                            <tr>ُ
                                <td>{{ $transaction->id }}</td>
                                <td>{{ $transaction->amount }}</td>
                                <td>
                                    @if($transaction->type == 'loyalty_reward')
                                        <span class="badge badge-success">{{ __('admin.earned') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ __('admin.spent') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $rawNote = $transaction->note;
                                        $note = '-';
                                        $noteArr = null;

                                        if (is_string($rawNote)) {
                                            $decoded = json_decode($rawNote, true);
                                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                $noteArr = $decoded;
                                            }
                                        } elseif (is_array($rawNote)) {
                                            $noteArr = $rawNote;
                                        }

                                        if (is_array($noteArr)) {
                                            $locale = app()->getLocale();
                                            $note = $noteArr[$locale] ?? $noteArr['ar'] ?? $noteArr['en'] ?? '-';
                                        } else {
                                            $note = $rawNote ?? '-';
                                        }
                                    @endphp
                                    {{ $note }}
                                </td>
                                {{-- date removed per admin request --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
