<script>
  $(document).ready(function() {
      $('#region_id').on('change', function() {
          var regionId = $(this).val();
          var citySelect = $('#city_id');

          // Clear cities dropdown
          citySelect.empty().append('<option value="">{{__('admin.select_city')}}</option>');

          if (regionId) {
              // Show loading state
              citySelect.append('<option value="">{{__('admin.loading')}}...</option>');

              // Make AJAX request to get cities
              $.ajax({
                  url: '/api/region/' + regionId + '/cities',
                  type: 'GET',
                  dataType: 'json',
                  success: function(response) {
                      // Clear loading state
                      citySelect.empty().append('<option value="">{{__('admin.select_city')}}</option>');

                      // Populate cities
                      if (response.data && response.data.length > 0) {
                          $.each(response.data, function(index, city) {
                              citySelect.append('<option value="' + city.id + '">' + city.name + '</option>');
                          });
                      }
                  },
                  error: function(xhr, status, error) {
                      console.error('Error loading cities:', error);
                      citySelect.empty().append('<option value="">{{__('admin.select_city')}}</option>');
                      // You can add a toast notification here if needed
                  }
              });
          }
      });
  });
</script>
<script>
  $(document).ready(function() {
      $('#city_id').on('change', function() {
          var cityId = $(this).val();
          var districtSelect = $('#district_id');

          // Clear districts dropdown
          districtSelect.empty().append('<option value="">{{__('admin.select_district')}}</option>');

          if (cityId) {
              // Show loading state
              districtSelect.append('<option value="">{{__('admin.loading')}}...</option>');

              // Make AJAX request to get districts
              $.ajax({
                  url: '/api/city/' + cityId + '/districts',
                  type: 'GET',
                  dataType: 'json',
                  success: function(response) {
                      // Clear loading state
                      districtSelect.empty().append('<option value="">{{__('admin.select_district')}}</option>');

                      // Populate districts
                      if (response.data && response.data.length > 0) {
                          $.each(response.data, function(index, district) {
                              districtSelect.append('<option value="' + district.id + '">' + district.name + '</option>');
                          });
                      }
                  },
                  error: function(xhr, status, error) {
                      console.error('Error loading districts:', error);
                      districtSelect.empty().append('<option value="">{{__('admin.select_district')}}</option>');
                      // You can add a toast notification here if needed
                  }
              });
          }
      });
  });
</script>

        
