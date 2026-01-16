<script>
    function mpEnsureId(input) {
        if (!input.getAttribute('data-mp-id')) {
            input.setAttribute('data-mp-id', 'mp' + Date.now() + Math.floor(Math.random() * 10000));
        }
        return input.getAttribute('data-mp-id');
    }

    function mpCreateItem(url, mpId, fileKey) {
        var div = document.createElement('div');
        div.className = 'mp-item';
        div.setAttribute('data-mp-id', mpId);
        div.setAttribute('data-file-key', fileKey);
        div.style.position = 'relative';
        div.style.display = 'inline-block';

        var img = document.createElement('img');
        img.src = url;
        img.style.maxWidth = '150px';
        img.style.maxHeight = '150px';
        img.style.objectFit = 'cover';
        div.appendChild(img);

        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'mp-remove';
        btn.style.position = 'absolute';
        btn.style.top = '6px';
        btn.style.right = '6px';
        btn.style.cursor = 'pointer';
        btn.style.background = '#fff';
        btn.style.borderRadius = '50%';
        btn.style.padding = '4px';
        btn.style.boxShadow = '0 0 4px #ccc';
        var i = document.createElement('i');
        i.className = 'feather icon-trash-2';
        i.style.color = '#dc3545';
        i.style.fontSize = '20px';
        btn.appendChild(i);
        div.appendChild(btn);

        return div;
    }

    document.addEventListener('change', function (e) {
        if (e.target && e.target.classList.contains('mp-input')) {
            var input = e.target;
            var mpId = mpEnsureId(input);
            var picker = input.closest('.media-picker');
            var grid = picker.querySelector('.mp-grid');
            Array.prototype.slice.call(grid.querySelectorAll('.mp-item:not(.existing)[data-mp-id="' + mpId + '"]')).forEach(function(el){ el.remove(); });
            var files = Array.prototype.slice.call(input.files || []);
            mpProcessFiles(files, input, grid, mpId, false);
        }
    });

    document.addEventListener('click', function (e) {
        if (e.target && (e.target.classList.contains('mp-remove') || (e.target.parentElement && e.target.parentElement.classList.contains('mp-remove')))) {
            var btn = e.target.classList.contains('mp-remove') ? e.target : e.target.parentElement;
            var item = btn.closest('.mp-item');
            var picker = item.closest('.media-picker');
            var hidden = picker.querySelector('.mp-hidden');

            var mediaId = item.getAttribute('data-media-id');
            if (mediaId) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'deleted_media[]';
                input.value = mediaId;
                hidden.appendChild(input);
                item.remove();
                return;
            }

            var mpId = item.getAttribute('data-mp-id');
            var fileKey = item.getAttribute('data-file-key');
            var inputFile = picker.querySelector('.mp-input[data-mp-id="' + mpId + '"]');
            if (inputFile) {
                var dt = new DataTransfer();
                Array.prototype.forEach.call(inputFile.files || [], function(f) {
                    var k = (f.name + '_' + f.size).replace(/[^a-zA-Z0-9_\-\.]/g, '_');
                    if (k !== fileKey) dt.items.add(f);
                });
                inputFile.files = dt.files;
            }
            item.remove();
        }
    });

    function mpProcessFiles(files, input, grid, mpId, mergeExisting) {
        var dt = new DataTransfer();
        if (mergeExisting) {
            Array.prototype.forEach.call(input.files || [], function(f){ dt.items.add(f); });
        }
        Array.prototype.forEach.call(files || [], function(file) {
            if (file.type && file.type.indexOf('image/') === 0) {
                dt.items.add(file);
                var url = URL.createObjectURL(file);
                var key = (file.name + '_' + file.size).replace(/[^a-zA-Z0-9_\-\.]/g, '_');
                var item = mpCreateItem(url, mpId, key);
                grid.appendChild(item);
            }
        });
        input.files = dt.files;
    }

    document.addEventListener('click', function(e){
        if (e.target && (e.target.classList.contains('mp-dropzone') || (e.target.closest && e.target.closest('.mp-dropzone')))) {
            var dz = e.target.classList.contains('mp-dropzone') ? e.target : e.target.closest('.mp-dropzone');
            var input = dz.querySelector('.mp-input');
            if (input) input.click();
        }
    });

    document.addEventListener('dragenter', function(e){
        var dz = e.target && (e.target.classList.contains('mp-dropzone') ? e.target : (e.target.closest && e.target.closest('.mp-dropzone')));
        if (dz) {
            e.preventDefault();
            dz.style.borderColor = '#0d6efd';
            dz.style.background = 'rgba(13,110,253,0.04)';
        }
    });
    document.addEventListener('dragover', function(e){
        var dz = e.target && (e.target.classList.contains('mp-dropzone') ? e.target : (e.target.closest && e.target.closest('.mp-dropzone')));
        if (dz) {
            e.preventDefault();
            dz.style.borderColor = '#0d6efd';
            dz.style.background = 'rgba(13,110,253,0.04)';
        }
    });
    document.addEventListener('dragleave', function(e){
        var dz = e.target && (e.target.classList.contains('mp-dropzone') ? e.target : (e.target.closest && e.target.closest('.mp-dropzone')));
        if (dz) {
            dz.style.borderColor = '#ced4da';
            dz.style.background = '';
        }
    });
    document.addEventListener('drop', function(e){
        var dz = e.target && (e.target.classList.contains('mp-dropzone') ? e.target : (e.target.closest && e.target.closest('.mp-dropzone')));
        if (dz) {
            e.preventDefault();
            dz.style.borderColor = '#ced4da';
            dz.style.background = '';
            var input = dz.querySelector('.mp-input');
            var picker = dz.closest('.media-picker');
            var grid = picker.querySelector('.mp-grid');
            var mpId = mpEnsureId(input);
            mpProcessFiles(e.dataTransfer.files || [], input, grid, mpId, true);
        }
    });
</script>
