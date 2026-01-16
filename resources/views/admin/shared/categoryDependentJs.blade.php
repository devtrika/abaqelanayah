<script>
    $(document).ready(function () {
        // خزن نسخة من كل الـ subcategories
        var allSubCategories = $('#category_id option[data-parent-id]').clone();

        // خلي الـ subcategories فاضية كبداية
        $('#category_id').empty().append('<option value="">{{ __('admin.select_category') }}</option>');

        // function تعبية الـ subcategories
        function populateSubCategories(parentId, selectedId = null) {
            var subCategorySelect = $('#category_id');
            subCategorySelect.empty().append('<option value="">{{ __('admin.select_category') }}</option>');

            if (parentId) {
                allSubCategories.each(function () {
                    var $opt = $(this).clone();
                    if ($opt.data('parent-id') == parentId) {
                        if (selectedId && $opt.val() == selectedId) {
                            $opt.prop('selected', true);
                        }
                        subCategorySelect.append($opt);
                    }
                });
            }
        }

        // عند تغيير الأب
        $('#parent_category_id').on('change', function () {
            var parentId = $(this).val();
            populateSubCategories(parentId, null);
        });

        // --- خاص بالـ EDIT ---
        // لو في صفحة edit عندك قيمة مختارة
        var currentParentId = $('#parent_category_id').val();
        var currentChildId  = $('#category_id').data('selected'); 
        // ملاحظة: خلي عندك في الـ select الخاص بالـ category_id attribute زي:

        if (currentParentId) {
            populateSubCategories(currentParentId, currentChildId);
        }
    });
</script>
