@php
	$cost = $order->cost_details_arabic ?? [];
@endphp

<div class="card mb-3">
	<div class="card-header provider-header d-flex align-items-center justify-content-between">
		<h5 class="card-title mb-0">
			<i class="feather icon-dollar-sign text-primary mr-2"></i>
			{{ __('admin.cost_details') }}
		</h5>
	</div>
	<div class="card-body provider-status-history">
		<table class="table table-bordered mb-0">
			<tbody>
				<tr>
					<th>{{ __('admin.products_total_without_vat') }}</th>
					<td>{{ number_format($cost['products_total_without_vat'] ?? 0, 2) }}</td>
				</tr>
				@if(!empty($cost['discount_code']))
				<tr>
					<th>{{ $cost['discount_code']['label'] ?? __('admin.discount_code') }}</th>
					<td>{{ $cost['discount_code']['code'] }} ({{ number_format($cost['discount_code']['amount'], 2) }})</td>
				</tr>
				@endif
				<tr>
					<th>{{ __('admin.products_total_after_discount') }}</th>
					<td>{{ number_format($cost['products_total_after_discount'] ?? 0, 2) }}</td>
				</tr>
				<tr>
					<th>{{ __('admin.delivery_fee') }}</th>
					<td>{{ number_format($cost['delivery_fee'] ?? 0, 2) }}</td>
				</tr>
				<tr>
					<th>{{ __('admin.total_without_vat') }}</th>
					<td>{{ number_format($cost['total_without_vat'] ?? 0, 2) }}</td>
				</tr>
				<tr>
					<th>{{ __('admin.vat_percent') }}</th>
					<td>{{ $cost['vat_percent'] ?? '15%' }}</td>
				</tr>
				<tr>
					<th>{{ __('admin.vat_amount') }}</th>
					<td>{{ number_format($cost['vat_amount'] ?? 0, 2) }}</td>
				</tr>
					<tr>
					<th>{{ __('admin.coupon_amount') }}</th>
					<td>{{ number_format($cost['coupon_amount'] ?? 0, 2) }}</td>
				</tr>
				<tr>
					<th>{{ __('admin.total_with_vat') }}</th>
					<td>{{ number_format($cost['total_with_vat'] ?? 0, 2) }}</td>
				</tr>
				<tr>
					<th>{{ __('admin.wallet_deduction') }}</th>
					<td>{{ number_format($cost['wallet_deduction'] ?? 0, 2) }}</td>
				</tr>
				<tr>
					<th>{{ __('admin.final_total') }}</th>
					<td><strong>{{ number_format($cost['total'] ?? 0, 2) }}</strong></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
