<script>
    // ADD IMAGE - support multiple files, preserve selected files and allow removing individual files
    // Each input gets a data-uploader-id so we can manage its FileList independently

    function ensureUploaderId(input) {
        if (!$(input).attr('data-uploader-id')) {
            const id = 'u' + Date.now() + Math.floor(Math.random() * 10000);
            $(input).attr('data-uploader-id', id);
        }
        return $(input).attr('data-uploader-id');
    }

    function createPreview(file, uploaderId) {
        const url = URL.createObjectURL(file);
        const safeName = (file.name + '_' + file.size).replace(/[^a-zA-Z0-9_\-\.]/g, '_');
        const $preview = $(
            '<div class="uploadedBlock" data-uploader-id="' + uploaderId + '" data-file-key="' + safeName + '">' +
                '<img src="' + url + '"/>' +
                '<button class="close" title="Remove"><i class="la la-times"></i></button>' +
            '</div>'
        );
        return $preview;
    }

    function handleFiles(input) {
        const files = Array.from(input.files || []);
        const container = $(input).closest('.imagesUploadBlock');
        const uploaderId = ensureUploaderId(input);

        files.forEach(file => {
            const preview = createPreview(file, uploaderId);
            container.append(preview);
        });
    }

    $(document).on('change', '.imageUploader', function (event) {
        handleFiles(this);
    });

    // remove single preview and remove file from the corresponding input FileList
    $(document).on('click', '.uploadedBlock .close', function (e) {
        e.preventDefault();
        const $block = $(this).closest('.uploadedBlock');
        const mediaId = $block.attr('data-media-id');
        if (mediaId) {
            let $container = $block.closest('.imgMontg').find('#deleted-media-inputs');
            if (!$container.length) {
                $container = $('<div id="deleted-media-inputs"></div>');
                $block.closest('form').append($container);
            }
            $('<input type="hidden" name="deleted_media[]" value="' + mediaId + '">').appendTo($container);
            $block.remove();
            return;
        }

        const uploaderId = $block.attr('data-uploader-id');
        const fileKey = $block.attr('data-file-key');
        const $input = $('.imageUploader[data-uploader-id="' + uploaderId + '"]');
        if ($input.length) {
            const input = $input.get(0);
            const dt = new DataTransfer();
            const files = Array.from(input.files || []);
            files.forEach(f => {
                const k = (f.name + '_' + f.size).replace(/[^a-zA-Z0-9_\-\.]/g, '_');
                if (k !== fileKey) {
                    dt.items.add(f);
                }
            });
            input.files = dt.files;
        }
        $block.remove();
    });

    // add another upload input block (ensure name attribute so files are submitted)
    $(document).on('click', '.clickAdd', function (b){
        b.preventDefault();
        const html = '<div class="textCenter"><div class="imagesUploadBlock"><label class="uploadImg"><span><i class="feather icon-image"></i></span><input type="file" accept="image/*" name="images[]" class="imageUploader" multiple></label></div></div>';
        $('.dropBox').append(html);
    });

</script>
