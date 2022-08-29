<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Печать ценника</title>
    <style>

        @font-face {
            font-family: Price;
            src: url("{{ asset('fonts/Eurostile-HeaIta.otf') }}") format('opentype');
            font-display: swap;
        }

        @font-face {
            font-family: Name;
            src: url("{{ asset('fonts/Eurostile-Regular Oblique.otf') }}") format('opentype');
            font-display: swap;
        }

        body {
            margin: 0;
            padding: 0;
        }

        p {
            margin: 0;
            padding: 0;
        }

        .price {
            font-family: Price, sans-serif;
            font-size: 30px;
        }

        .name {
            font-size: 12px;
            font-family: Price, sans-serif;
            text-align: center;
        }

        @media print {
            .pagebreak {
                clear: both;
                page-break-before: always;
                page-break-inside: avoid;
            }

            @page {
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>
<body>
@for($i = 0; $i < $count; $i++)
    <div
        class="pagebreak"
        style="width: 58mm; height: 30mm; float: none; position: relative;">
        <div style="position: absolute; top: 40%; left: 50%; transform: translate(-50%, -50%)">
            @if ($type === 'price')
                <p class="price">{{ $productSku->product_price }}</p>
            @else
                <p class="price">{{ $productSku->kaspi_product_price }}</p>
            @endif
        </div>
        <div style="position: absolute; top: 70%; left: 50%; transform: translate(-50%, -50%); width: 100%;">
            <p class="name">
                {{ $productSku->product_name }}
            </p>
            <p style="font-size: 10px;" class="name">
                @foreach($productSku->attributes as $attribute)
                    {{ $attribute->attribute_name->attribute_name }}: {{ $attribute->attribute_value }}
                @endforeach -
                {{ $productSku->product->attributes->pluck('attribute_value')->join(' ')}}
                ({{ $productSku->manufacturer->manufacturer_name }})
            </p>
            </div>
    </div>

@endfor
</body>
</html>
