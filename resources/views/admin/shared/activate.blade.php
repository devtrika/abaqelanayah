<script>
    $(document).ready(function(){
        $(document).on('click','.block_user',function(e){
            e.preventDefault();
            var button = $(this);
            $.ajax({
                url: '{{url("admin/clients/block")}}',
                method: 'post',
                data: { id : $(this).data('id')},
                dataType:'json',
                beforeSend: function(){
                    button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').attr('disabled',true);
                },
                success: function(response){
                    Swal.fire({
                                position: 'top-start',
                                type: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 1500,
                                confirmButtonClass: 'btn btn-primary',
                                buttonsStyling: false,
                            })
                    setTimeout(function(){
                        window.location.reload();
                    }, 1000);
                },
                error: function(){
                    button.attr('disabled',false);
                    Swal.fire("خطأ!", "حدث خطأ أثناء تحديث الحالة", "error");
                }
            });

        });

        // Activate/Deactivate user functionality
        $(document).on('click','.activate_user',function(e){
            e.preventDefault();
            var button = $(this);
            $.ajax({
                url: '{{url("admin/clients/activate")}}',
                method: 'post',
                data: { id : $(this).data('id')},
                dataType:'json',
                beforeSend: function(){
                    button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').attr('disabled',true);
                },
                success: function(response){
                    Swal.fire({
                        position: 'top-start',
                        type: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 1500,
                        confirmButtonClass: 'btn btn-primary',
                        buttonsStyling: false,
                    });
                    setTimeout(function(){
                        window.location.reload();
                    }, 1000);
                },
                error: function(){
                    button.attr('disabled',false);
                    Swal.fire("خطأ!", "حدث خطأ أثناء تحديث الحالة", "error");
                }
            });
        });
    });
</script>