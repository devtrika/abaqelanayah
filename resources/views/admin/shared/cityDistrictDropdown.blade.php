<script>
  $(document).ready(function() {
      var selectDistrictText = "{{ __('admin.select_district') }}";
      var loadingText = "{{ __('admin.loading') }}";

      $('#city_id').on('change', function() {
          var cityId = $(this).val();
          var districtSelect = $('#district_id');

          // Clear districts dropdown
          districtSelect.empty().append('<option value="">' + selectDistrictText + '</option>');

          if (cityId) {
              // Show loading state
              districtSelect.append('<option value="">' + loadingText + '...</option>');

              // Make AJAX request to get districts
              $.ajax({
                  url: '/api/city/' + cityId + '/districts',
                  type: 'GET',
                  dataType: 'json',
                  success: function(response) {
                      // Clear loading state
                      districtSelect.empty().append('<option value="">' + selectDistrictText + '</option>');

                      // Populate districts
                      if (response.data && response.data.length > 0) {
                          $.each(response.data, function(index, district) {
                              districtSelect.append('<option value="' + district.id + '">' + district.name + '</option>');
                          });
                      }
                  },
                  error: function(xhr, status, error) {
                      console.error('Error loading districts:', error);
                      districtSelect.empty().append('<option value="">' + selectDistrictText + '</option>');
                  }
              });
          }
      });
  });
</script>
