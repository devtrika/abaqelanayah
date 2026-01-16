<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\branches\Store;
use App\Http\Requests\Admin\branches\Update;
use App\Models\Branch;
use App\Services\BranchWorkingHoursService;
use App\Traits\Report;

class BranchController extends Controller
{
    protected $branchWorkingHoursService;

    public function __construct(BranchWorkingHoursService $branchWorkingHoursService)
    {
        $this->branchWorkingHoursService = $branchWorkingHoursService;
    }

    public function index($id = null)
    {
        if (request()->ajax()) {
            $branches = Branch::search(request()->searchArray)->paginate(30);
            $html = view('admin.branches.table', compact('branches'))->render();
            return response()->json(['html' => $html]);
        }
        return view('admin.branches.index');
    }

    public function create()
    {
        return view('admin.branches.create');
    }

    public function store(Store $request)
    {
        try {
            $branchData = $request->validated();

            // Create the branch
            $branch = Branch::create($branchData);

            // Store working hours if provided
            if ($request->has('working_hours') && !empty($request->working_hours)) {
                $this->branchWorkingHoursService->storeWorkingHours($branch->id, ['working_hours' => $request->working_hours]);
            }

            // Store delivery hours if provided
            if ($request->has('delivery_hours') && !empty($request->delivery_hours)) {
                $this->branchWorkingHoursService->storeDeliveryHours($branch->id, ['delivery_hours' => $request->delivery_hours]);
            }

            // Assign branch manager if provided
            if ($request->has('managers')) {
                $branch->managers()->sync([$request->managers]);
            }

            // Assign deliveries and notify them
            if ($request->has('deliveries') && !empty($request->deliveries)) {
                $branch->deliveries()->sync($request->deliveries);

                // Notify all assigned delivery persons
                $notificationService = app(\App\Services\Order\OrderNotificationService::class);
                $deliveries = \App\Models\User::whereIn('id', $request->deliveries)
                    ->where('type', 'delivery')
                    ->get();

                foreach ($deliveries as $delivery) {
                    $notificationService->notifyDeliveryOfBranchAssignment($delivery, $branch);
                }
            }

            Report::addToLog('اضافه فرع');
            return response()->json(['url' => route('admin.branches.index')]);
        } catch (\Exception $e) {
            \Log::error('Branch creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create branch'], 500);
        }
    }

    public function edit($id)
    {
        $branch = Branch::with(['workingHours', 'deliveryHours', 'managers', 'deliveries'])->findOrFail($id);
        $managers = \App\Models\Admin::where('role_id', 2)
            ->where(function ($query) use ($id) {
                $query->whereDoesntHave('branches')
                      ->orWhereHas('branches', function ($q) use ($id) {
                          $q->where('branches.id', $id);
                      });
            })->get(); // Managers assigned to this branch or not assigned to any
        $deliveries = \App\Models\User::where('type', 'delivery')
            ->where(function ($query) use ($id) {
                $query->whereDoesntHave('branches')
                      ->orWhereHas('branches', function ($q) use ($id) {
                          $q->where('branches.id', $id);
                      });
            })->get(); // Deliveries assigned to this branch or not assigned to any
        $assignedManagers = $branch->managers;
        $assignedDeliveries = $branch->deliveries;
        return view('admin.branches.edit', [
            'branch' => $branch,
            'managers' => $managers,
            'deliveries' => $deliveries,
            'assignedManagers' => $assignedManagers,
            'assignedDeliveries' => $assignedDeliveries
        ]);
    }

    public function update(Update $request, $id)
    {
        try {
            $branch = Branch::findOrFail($id);
            $branchData = $request->validated();

            // Update the branch
            $branch->update($branchData);

            // Update working hours if provided
            if ($request->has('working_hours')) {
                $this->branchWorkingHoursService->storeWorkingHours($branch->id, ['working_hours' => $request->working_hours]);
            }

            // Update delivery hours if provided
            if ($request->has('delivery_hours')) {
                $this->branchWorkingHoursService->storeDeliveryHours($branch->id, ['delivery_hours' => $request->delivery_hours]);
            }

            // Sync branch manager (single)
            if ($request->has('managers')) {
                $branch->managers()->sync([$request->managers]);
            } else {
                $branch->managers()->detach();
            }

            // Sync branch deliveries and notify new deliveries
            if ($request->has('deliveries')) {
                // Get current delivery IDs
                $currentDeliveryIds = $branch->deliveries()->pluck('users.id')->toArray();
                $newDeliveryIds = $request->deliveries;

                // Find newly added deliveries
                $addedDeliveryIds = array_diff($newDeliveryIds, $currentDeliveryIds);

                // Sync deliveries
                $branch->deliveries()->sync($newDeliveryIds);

                // Notify newly added delivery persons
                if (!empty($addedDeliveryIds)) {
                    $notificationService = app(\App\Services\Order\OrderNotificationService::class);
                    $newDeliveries = \App\Models\User::whereIn('id', $addedDeliveryIds)
                        ->where('type', 'delivery')
                        ->get();

                    foreach ($newDeliveries as $delivery) {
                        $notificationService->notifyDeliveryOfBranchAssignment($delivery, $branch);
                    }
                }
            }

            Report::addToLog('تعديل فرع');
            return response()->json(['url' => route('admin.branches.index')]);
        } catch (\Exception $e) {
            dd($e);
            \Log::error('Branch update failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update branch'], 500);
        }
    }

    public function show($id)
    {
        $branch = Branch::with(['workingHours', 'deliveryHours', 'managers', 'deliveries'])->findOrFail($id);
        return view('admin.branches.show', ['branch' => $branch]);
    }

    public function destroy($id)
    {
        try {
            $branch = Branch::findOrFail($id);
            
            // Delete related working and delivery hours
            $branch->workingHours()->delete();
            $branch->deliveryHours()->delete();
            
            // Delete the branch
            $branch->delete();
            
            Report::addToLog('حذف فرع');
            return response()->json(['id' => $id]);
        } catch (\Exception $e) {
            \Log::error('Branch deletion failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete branch'], 500);
        }
    }

    public function destroyAll(Request $request)
    {
        try {
            $requestIds = json_decode($request->data);
            
            foreach ($requestIds as $id) {
                $ids[] = $id->id;
            }
            
            // Delete related working and delivery hours for all branches
            $branches = Branch::whereIntegerInRaw('id', $ids)->get();
            foreach ($branches as $branch) {
                $branch->workingHours()->delete();
                $branch->deliveryHours()->delete();
            }
            
            // Delete all branches
            if (Branch::whereIntegerInRaw('id', $ids)->delete()) {
                Report::addToLog('حذف العديد من الفروع');
                return response()->json('success');
            } else {
                return response()->json('failed');
            }
        } catch (\Exception $e) {
            \Log::error('Bulk branch deletion failed: ' . $e->getMessage());
            return response()->json('failed');
        }
    }
}