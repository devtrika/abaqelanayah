<div class="imgMontg col-12 text-center">
    <div class="dropBox">
        <div class="textCenter">
            <div class="imagesUploadBlock">
                @if(($allowUpload ?? true))
                    <label class="uploadImg">
                        <span><i class="feather icon-image"></i></span>
                        <input type="file" accept="image/*" name="{{ $inputName ?? 'images[]' }}" class="imageUploader" @if(($multiple ?? true)) multiple @endif>
                    </label>
                @endif

                @if(!empty($existing))
                    @foreach($existing as $media)
                        <div class="uploadedBlock existing" data-media-id="{{ $media->id }}" style="position:relative;display:inline-block;">
                            <img src="{{ $media->getUrl() }}" alt="{{ $alt ?? 'image' }}" style="max-width:150px;max-height:150px;object-fit:cover;" />
                            @if(($allowUpload ?? true))
                                <button class="close" title="Remove"><i class="la la-times"></i></button>
                            @endif
                        </div>
                    @endforeach
                @elseif(!empty($fallback))
                    <div class="uploadedBlock" style="position:relative;display:inline-block;">
                        <img src="{{ $fallback }}" alt="{{ $alt ?? 'image' }}" style="max-width:150px;max-height:150px;object-fit:cover;" />
                    </div>
                @endif
            </div>
        </div>
    </div>
    @if(($allowUpload ?? true))
        <div id="deleted-media-inputs"></div>
    @endif
</div>
