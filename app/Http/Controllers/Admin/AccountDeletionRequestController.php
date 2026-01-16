<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountDeletionRequest;
use App\Models\Provider;
use App\Models\User;
use App\Notifications\NotifyUser;
use App\Traits\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountDeletionRequestController extends Controller
{
    use Report;

    /**
     * Display account deletion requests
     */
    public function index()
    {
        if (request()->ajax()) {
            $account_deletion_requests = AccountDeletionRequest::with(['user', 'processedBy'])
                ->search(request()->searchArray)
                ->orderBy('created_at', 'desc')
                ->paginate(30);

            $html = view('admin.account-deletion-requests.table', compact('account_deletion_requests'))->render();
            return response()->json(['html' => $html]);
        }

        return view('admin.account-deletion-requests.index');
    }

    /**
     * Show specific deletion request
     */
    public function show($id)
    {
        $request = AccountDeletionRequest::with(['user', 'processedBy'])->findOrFail($id);
        return view('admin.account-deletion-requests.show', compact('request'));
    }

    /**
     * Approve deletion request
     */
    public function approve(Request $request, $id)
    {
        $deletionRequest = AccountDeletionRequest::with('user')->findOrFail($id);

        if (!$deletionRequest->isPending()) {
            return response()->json(['error' => 'Request already processed'], 400);
        }

        if($deletionRequest->user->provider && $deletionRequest->user->provider->providerSubOrders->count() > 0) {
            return response()->json(['error'=> __('admin.cant_delete_provider')], 400);
        }

        DB::beginTransaction();
        try {
            // Update request status
            $deletionRequest->update([
                'status' => 'approved',
                'admin_notes' => $request->admin_notes,
                'processed_by' => auth('admin')->id(),
                'processed_at' => now()
            ]);

            // Send notification to user
            $deletionRequest->user->notify(new NotifyUser([
                'title' => [
                    'ar' => 'تم الموافقة على طلب حذف الحساب',
                    'en' => 'Account Deletion Request Approved'
                ],
                'body' => [
                    'ar' => 'تم الموافقة على طلب حذف حسابك. سيتم حذف الحساب خلال 24 ساعة.',
                    'en' => 'Your account deletion request has been approved. Your account will be deleted within 24 hours.'
                ],
                'type' => 'admin_notify'
            ]));

            // Soft delete the user account
            $user = $deletionRequest->user;
            $user->delete();
            if($user->provider){
                $user->provider->delete();
            }

            DB::commit();
            Report::addToLog("الموافقة على طلب حذف حساب - {$user->name} (ID: {$user->id}) - تم حذف الحساب");

            return response()->json([
                'success' => true,
                'message' => __('admin.deletion_request_approved_successfully')
            ]);

        } catch (\Exception $e) {
            dd($e);
             DB::rollback();
            return response()->json(['error' => 'Failed to approve deletion request'], 500);
        }
    }

    /**
     * Reject deletion request
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000'
        ]);

        $deletionRequest = AccountDeletionRequest::with('user')->findOrFail($id);

        if (!$deletionRequest->isPending()) {
            return response()->json(['error' => 'Request already processed'], 400);
        }

        DB::beginTransaction();
        try {
            // Update request status
            $deletionRequest->update([
                'status' => 'rejected',
                'admin_notes' => $request->admin_notes,
                'processed_by' => auth('admin')->id(),
                'processed_at' => now()
            ]);

            // Send notification to user
            $deletionRequest->user->notify(new NotifyUser([
                'title' => [
                    'ar' => 'تم رفض طلب حذف الحساب',
                    'en' => 'Account Deletion Request Rejected'
                ],
                'body' => [
                    'ar' => 'تم رفض طلب حذف حسابك. السبب: ' . $request->admin_notes,
                    'en' => 'Your account deletion request has been rejected. Reason: ' . $request->admin_notes
                ],
                'type' => 'admin_notify'
            ]));

            DB::commit();
            Report::addToLog('رفض طلب حذف حساب - ' . $deletionRequest->user->name);

            return response()->json([
                'success' => true,
                'message' => __('admin.deletion_request_rejected_successfully')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to reject deletion request'], 500);
        }
    }
}
