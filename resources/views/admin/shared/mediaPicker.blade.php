<div class="media-picker">
    @php($allow = $allowUpload ?? true)
    @php($multipleInput = $multiple ?? true)
    @php($nameAttr = $inputName ?? 'images[]')
    @php($items = $existing ?? [])

    @if($allow)
    <div class="mp-dropzone" style="border:2px dashed #ced4da;border-radius:8px;min-height:160px;display:flex;flex-direction:column;align-items:center;justify-content:center;margin-bottom:10px;cursor:pointer;padding:10px;">
        <div style="color:#6c757d;">
            <i class="feather icon-upload" style="font-size:22px;margin-right:6px;"></i>
            <span>اسحب الصور هنا أو انقر للرفع</span>
        </div>
        <input type="file" accept="image/*" class="mp-input" name="{{ $nameAttr }}" @if($multipleInput) multiple @endif style="display:none;">
        <div class="mp-grid" style="display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-start;width:100%;margin-top:10px;">
            @foreach($items as $media)
                <div class="mp-item existing" data-media-id="{{ $media->id }}" style="position:relative;display:inline-block;">
                    <img src="{{ $media->getUrl() }}" alt="{{ $alt ?? 'image' }}" style="max-width:150px;max-height:150px;object-fit:cover;" />
                    <button type="button" class="mp-remove" style="position:absolute;top:6px;right:6px;cursor:pointer;background:#fff;border-radius:50%;padding:4px;box-shadow:0 0 4px #ccc;display:flex;align-items:center;justify-content:center;">
                        <i class="feather icon-trash-2" style="color:#dc3545;font-size:20px;"></i>
                    </button>
                </div>
            @endforeach
        </div>
    </div>
    @else
        <div class="mp-grid" style="display:flex;gap:8px;flex-wrap:wrap;justify-content:center;">
            @foreach($items as $media)
                <div class="mp-item existing" data-media-id="{{ $media->id }}" style="position:relative;display:inline-block;">
                    <img src="{{ $media->getUrl() }}" alt="{{ $alt ?? 'image' }}" style="max-width:150px;max-height:150px;object-fit:cover;" />
                </div>
            @endforeach
            @if(empty($items) && !empty($fallback))
                <div class="mp-item" style="position:relative;display:inline-block;">
                    <img src="{{ $fallback }}" alt="{{ $alt ?? 'image' }}" style="max-width:150px;max-height:150px;object-fit:cover;" />
                </div>
            @endif
        </div>
    @endif

    @if($allow)
    <div class="mp-hidden"></div>
    @endif
</div>
