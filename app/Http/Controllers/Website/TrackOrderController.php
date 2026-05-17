<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class TrackOrderController extends Controller
{
    public function index()
    {
        return view('website.pages.track-order.index');
    }

    public function track(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
        ]);

        $orderNumber = $request->input('order_number');
        
        // Find the order by its order_number or id (fallback)
        $order = Order::where('order_number', $orderNumber)->orWhere('id', $orderNumber)->first();

        if (!$order) {
            return back()->with('error', __('site.order_not_found'))->withInput();
        }

        return redirect()->route('website.track-order.show', ['orderNumber' => $order->order_number]);
    }

    public function show($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['items.product', 'statusChanges'])
            ->firstOrFail();
        
        return view('website.pages.track-order.show', compact('order'));
    }
}
