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
        .fqs-title {
            text-align: center;
            color: #bdbdbd;
            font-size: 1rem;
            margin-top: 32px;
            margin-bottom: 12px;
            font-weight: 400;
        }
        .fqs-list {
            width: 315px;
            margin: 0 auto;
        }
        .fqs-card {
            background: #fff;
            border: 1.5px solid #e0e0e0;
            border-radius: 10px;
            margin-bottom: 18px;
            box-sizing: border-box;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .fqs-header {
            display: flex;
            flex-direction: row;
            align-items: center;
            cursor: pointer;
            padding: 16px 14px;
            user-select: none;
        }
        .fqs-icon {
            width: 18px;
            height: 18px;
            border: 1.5px solid #bdbdbd;
            border-radius: 3px;
            margin-#{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 28px;
            margin-top: 4px;
            flex-shrink: 0;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .fqs-arrow {
            display: inline-block;
            transition: transform 0.2s;
            font-size: 1.1em;
            color: #bdbdbd;
        }
        .fqs-header.expanded .fqs-arrow {
            transform: rotate(90deg);
        }
        .fqs-content {
            padding: 0 14px 0 14px;
            display: block;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.7s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.7s cubic-bezier(0.4, 0, 0.2, 1), padding 0.7s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0;
        }
        .fqs-card.expanded .fqs-content {
            max-height: 500px;
            opacity: 1;
            padding: 0 14px 16px 14px;
        }
        .fqs-question {
            font-weight: bold;
            font-size: 1.05rem;
            color: #222;
            flex: 1;
        }
        .fqs-answer {
            font-size: 0.98rem;
            color: #444;
            line-height: 1.9;
            margin-top: 6px;
        }
        @media (max-width: 340px) {
            .fqs-list {
                width: 98vw;
                min-width: 0;
            }
        }
    </style>
</head>
<body>
    <div class="fqs-title">{{ $title }}</div>
    <div class="fqs-list">
        @foreach($fqs as $i => $fqsItem)
            <div class="fqs-card">
                <div class="fqs-header" onclick="toggleFqs(this)">
                    <div class="fqs-icon">
                        <span class="fqs-arrow">&#9654;</span>
                    </div>
                    <div class="fqs-question">{!! $fqsItem->getTranslation('question', app()->getLocale()) !!}</div>
                </div>
                <div class="fqs-content">
                    @if($fqsItem->getTranslation('answer', app()->getLocale()))
                        <div class="fqs-answer">{!! $fqsItem->getTranslation('answer', app()->getLocale()) !!}</div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    <script>
        function toggleFqs(header) {
            const card = header.parentElement;
            const expanded = card.classList.contains('expanded');
            document.querySelectorAll('.fqs-card').forEach(c => {
                c.classList.remove('expanded');
                c.querySelector('.fqs-header').classList.remove('expanded');
            });
            if (!expanded) {
                card.classList.add('expanded');
                header.classList.add('expanded');
            }
        }
    </script>
</body>
</html> 