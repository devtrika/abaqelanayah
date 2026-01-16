<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
            background: #fff;
            margin: 0;
            font-family: 'Tajawal', Arial, sans-serif;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
        }
        .static-page-title {
            text-align: center;
            color: #bdbdbd;
            font-size: 1rem;
            margin-top: 32px;
            margin-bottom: 12px;
            font-weight: 400;
        }
        .static-page-image {
            width: 315px;
            height: 173px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #bdbdbd;
            display: block;
            margin: 0 auto 18px auto;
            background: #fff;
        }
        .static-page-card {
            width: 315px;
            background: #fff;
            border: 1px solid #bdbdbd;
            border-radius: 12px;
            padding: 18px 16px 18px 16px;
            box-sizing: border-box;
            margin-bottom: 32px;
        }
        .static-page-content {
            font-size: 1rem;
            color: #222;
            line-height: 2;
            text-align: justify;
            word-break: break-word;
        }
        @media (max-width: 340px) {
            .static-page-image, .static-page-card {
                width: 98vw;
                min-width: 0;
            }
        }
    </style>
</head>
<body>
    <div class="static-page-title">{{ $title }}</div>
    @php
        $images = [
            'about' => asset('storage/images/about.jpg'),
            'privacy' => asset('storage/images/privacy.jpg'),
            'terms' => asset('storage/images/terms.jpg'),
        ];
        $pageImage = $images[$pageKey] ?? $logo;
    @endphp
    {{-- <img src="{{ $pageImage }}" alt="{{ $pageKey }}" class="static-page-image"> --}}
    <div class="static-page-card">
        <div class="static-page-content">{!! $content !!}</div>
    </div>
</body>
</html> 