<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.approve_deletion_request') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="approveForm">
                    <div class="form-group">
                        <label>{{ __('admin.admin_notes') }} ({{ __('admin.optional') }})</label>
                        <textarea class="form-control" name="admin_notes" rows="3" placeholder="{{ __('admin.enter_admin_notes') }}"></textarea>
                    </div>
                </form>
                <div class="alert alert-warning">
                    <i class="feather icon-alert-triangle"></i>
                    {{ __('admin.approve_deletion_warning') }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('admin.cancel') }}</button>
                <button type="button" class="btn btn-success" id="confirmApprove">{{ __('admin.approve') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.reject_deletion_request') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="rejectForm">
                    <div class="form-group">
                        <label>{{ __('admin.rejection_reason') }} <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="admin_notes" rows="3" placeholder="{{ __('admin.enter_rejection_reason') }}" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('admin.cancel') }}</button>
                <button type="button" class="btn btn-danger" id="confirmReject">{{ __('admin.reject') }}</button>
            </div>
        </div>
    </div>
</div>
