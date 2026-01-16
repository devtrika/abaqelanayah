{{-- City/District Dropdown AJAX Handler for Website --}}
<script>
  $(document).ready(function() {
      var selectDistrictText = "{{ __('site.choose_from_list') }}";
      var loadingText = "{{ __('site.loading') }}";

      var $city = $('#city_id, select[name="city"], select[name="city_id"]');
      var $district = $('#district_id, select[name="district"], select[name="district_id"]');

      function isSelect2Inited($el){ return $el.hasClass('select2-hidden-accessible'); }

      function updateSelect2($el){ if (isSelect2Inited($el)) { $el.trigger('change'); } }

      function loadDistricts(cityId, preselectId){
          if (!cityId) {
              // reset
              $district.prop('disabled', false).empty();
              var ph = new Option(selectDistrictText, '', false, false);
              $(ph).attr('hidden', true);
              $district.append(ph);
              updateSelect2($district);
              return;
          }

          // disable and show loading
          $district.prop('disabled', true).empty();
          $district.append(new Option(loadingText + '...', '', false, false));
          updateSelect2($district);

          $.ajax({
              url: '/api/city/' + cityId + '/districts',
              type: 'GET',
              dataType: 'json',
              success: function(response){
                  $district.empty();
                  var ph = new Option(selectDistrictText, '', false, false);
                  $(ph).attr('hidden', true);
                  $district.append(ph);

                  var list = response && response.data ? response.data : [];
                  if (list.length){
                      $.each(list, function(_, d){
                          var opt = new Option(d.name, d.id, false, false);
                          $district.append(opt);
                      });
                      if (preselectId){
                          $district.val(String(preselectId));
                      }
                  } else {
                      var nd = new Option('{{ __('site.no_districts_available') }}', '', false, false);
                      $(nd).attr('disabled', true);
                      $district.append(nd);
                  }
                  $district.prop('disabled', false);
                  updateSelect2($district);
              },
              error: function(xhr){
                  console.error('Error loading districts:', xhr && xhr.responseText);
                  $district.empty();
                  var ph2 = new Option(selectDistrictText, '', false, false);
                  $(ph2).attr('hidden', true);
                  $district.append(ph2);
                  var er = new Option('{{ __('site.failed_to_load_districts') }}', '', false, false);
                  $(er).attr('disabled', true);
                  $district.append(er);
                  $district.prop('disabled', false);
                  updateSelect2($district);
              }
          });
      }

      // On city change
      $city.on('select2:select change', function(){
          var cityId = $(this).val();
          loadDistricts(cityId, null);
      });

      // Initial auto-load if page has a selected city
      var initCity = $city.val() || $city.data('current-city');
      var initDistrict = $district.val() || $district.data('current-district');
      if (initCity) {
          loadDistricts(initCity, initDistrict);
      }
  });
</script>
