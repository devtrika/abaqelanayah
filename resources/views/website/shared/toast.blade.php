{{-- Toast Notification System --}}
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    {{-- Success Toast --}}
    @if(session('success'))
    <div class="toast align-items-center text-white bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    @endif

    {{-- Error Toast --}}
    @if(session('error'))
    <div class="toast align-items-center text-white bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    @endif

    {{-- Info Toast --}}
    @if(session('info'))
    <div class="toast align-items-center text-white bg-info border-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-info-circle me-2"></i>
                {{ session('info') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    @endif

    {{-- Warning Toast --}}
    @if(session('warning'))
    <div class="toast align-items-center text-white bg-warning border-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('warning') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    @endif

    {{-- Validation Errors Toast --}}
    @if($errors->any())
    <div class="toast align-items-center text-white bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="7000">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>{{ __('site.form_errors') }}</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    @endif
</div>

{{-- Toast Auto-hide Script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all toasts
    var toastElList = [].slice.call(document.querySelectorAll('.toast'));
    var toastList = toastElList.map(function(toastEl) {
        return new bootstrap.Toast(toastEl);
    });
    
    // Show all toasts
    toastList.forEach(function(toast) {
        toast.show();
    });
});
</script>

<style>
.toast-container {
    z-index: 9999 !important;
}

.toast {
    min-width: 300px;
    max-width: 500px;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border-radius: 0.5rem;
    direction: rtl;
    text-align: right;
}

.toast-body {
    padding: 1rem;
    font-size: 0.95rem;
}

.toast-body ul {
    padding-right: 1.5rem;
    margin-top: 0.5rem;
}

.toast-body ul li {
    margin-bottom: 0.25rem;
}

.toast .btn-close {
    margin-left: 0.5rem !important;
}
</style>

