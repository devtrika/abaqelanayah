<!-- Map Picker Modal (global) -->
<div class="modal fade" id="mapPickerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header align-items-center justify-content-between">
        <h5 class="modal-title">{{ __('site.choose_location') }}</h5>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-sm btn-outline-primary js-locate-me">
            <i class="fal fa-location"></i>
            {{ __('site.my_current_location') }}
          </button>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
      <div class="modal-body">
        <div id="mapPicker" style="height: 420px;"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="default-btn second-btn" data-bs-dismiss="modal">{{ __('site.close') }}</button>
      </div>
    </div>
  </div>
</div>

