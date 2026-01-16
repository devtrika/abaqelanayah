<script>
    $(document).ready(function() {
        // Ensure jQuery and required elements are available
        if (typeof $ === 'undefined') {
            console.error('jQuery is not loaded');
            return;
        }

        // Check if table_content_append exists
        if ($('.table_content_append').length === 0) {
            console.warn('table_content_append element not found');
            return;
        }

        // Add a small delay to ensure all DOM elements are fully loaded
        setTimeout(function() {
            initializeTable();
        }, 100);
    });

    // Initialize table with retry mechanism
    function initializeTable(retryCount) {
        // Default parameter fallback for older browsers
        if (typeof retryCount === 'undefined') {
            retryCount = 0;
        }

        var maxRetries = 3;

        console.log('Initializing table, attempt: ' + (retryCount + 1));

        getData({'searchArray' : searchArray()}, function(success) {
            if (!success && retryCount < maxRetries) {
                console.log('Table loading failed, retrying... (' + (retryCount + 1) + '/' + maxRetries + ')');
                setTimeout(function() {
                    initializeTable(retryCount + 1);
                }, 1000 * (retryCount + 1)); // Exponential backoff
            } else if (!success) {
                console.error('Failed to load table after ' + maxRetries + ' attempts');
                showTableError();
            } else {
                console.log('Table loaded successfully');
            }
        });
    }

    // Mobile device detection
    function isMobileDevice() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    }

    // Network connectivity check
    function isOnline() {
        return navigator.onLine !== false;
    }

    function searchArray() {
        var searchArray = {} ;
        $('.search-input').each(function(key, input) {
            searchArray[$(this).attr('name')] = $(this).val()
        });
        return  searchArray
    }

    $(document).on('change', '.search-input', function (e) {
        e.preventDefault();
        getData({'searchArray' : searchArray()} )
    });

    $(document).on('keyup', '.search-input', function (e) {
        e.preventDefault();
        getData({'searchArray' : searchArray()} )
    });

    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        getData({page : $(this).attr('href').split('page=')[1]  , 'searchArray' : searchArray() } )
    });

    // Enhanced getData function with better error handling
    function getData(array, callback) {
        // Check network connectivity
        if (!isOnline()) {
            console.warn('No network connection detected');
            showNetworkError();
            if (callback) callback(false);
            return;
        }

        // Show loading indicator
        showTableLoading();

        var url = "{{$index_route}}";
        console.log('Making AJAX request to:', url, 'with data:', array);

        $.ajax({
            type: "get",
            url: url,
            data: array,
            dataType: "json",
            cache: false,
            timeout: isMobileDevice() ? 25000 : 15000, // Longer timeout for mobile devices
            beforeSend: function(xhr) {
                // Add CSRF token if available
                const token = $('meta[name="csrf-token"]').attr('content');
                if (token) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', token);
                }
            },
            success: function (response) {
                console.log('AJAX Success:', response);
                hideTableLoading();
                if (response && response.html) {
                    $('.table_content_append').html(response.html);
                    if (callback) callback(true);
                } else {
                    console.error('Invalid response received:', response);
                    if (callback) callback(false);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusCode: xhr.status
                });
                hideTableLoading();
                if (callback) callback(false);
            }
        });
    }

    // Show loading indicator
    function showTableLoading() {
        if ($('.table_content_append .table-loading').length === 0) {
            $('.table_content_append').html(`
                <div class="card">
                    <div class="card-body text-center table-loading" style="padding: 3rem;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading...</p>
                    </div>
                </div>
            `);
        }
    }

    // Hide loading indicator
    function hideTableLoading() {
        $('.table_content_append .table-loading').remove();
    }

    // Show error message
    function showTableError() {
        $('.table_content_append').html(`
            <div class="card">
                <div class="card-body text-center" style="padding: 3rem;">
                    <i class="feather icon-alert-triangle text-warning" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Error loading data</h5>
                    <p class="text-muted">There was an error loading the table data. Please try refreshing the page.</p>
                    <button class="btn btn-primary" onclick="initializeTable()">
                        <i class="feather icon-refresh-cw"></i> Retry
                    </button>
                </div>
            </div>
        `);
    }

    // Show network error message
    function showNetworkError() {
        $('.table_content_append').html(`
            <div class="card">
                <div class="card-body text-center" style="padding: 3rem;">
                    <i class="feather icon-wifi-off text-danger" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">No Internet Connection</h5>
                    <p class="text-muted">Please check your internet connection and try again.</p>
                    <button class="btn btn-primary" onclick="initializeTable()">
                        <i class="feather icon-refresh-cw"></i> Try Again
                    </button>
                </div>
            </div>
        `);
    }

    $('.clean-input').on('click' ,function(){
        $(this).siblings('input').val(null);
        $(this).siblings('select').val(null);
        getData({'searchArray' : searchArray()} )
    });

    // Add manual refresh functionality
    $(document).on('click', '.manual-refresh-table', function() {
        initializeTable();
    });

    // Add a refresh button to the table if it doesn't exist
    $(document).ready(function() {
        setTimeout(function() {
            if ($('.table_buttons .manual-refresh-table').length === 0) {
                $('.table_buttons .row > div:first-child').append(`
                    <button type="button" class="btn bg-gradient-info mr-1 mb-1 waves-effect waves-light manual-refresh-table">
                        <i class="feather icon-refresh-cw"></i> Refresh
                    </button>
                `);
            }
        }, 500);
    });
</script>