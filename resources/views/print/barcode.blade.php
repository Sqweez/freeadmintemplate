<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Штрих-код</title>
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        p {
            margin: 0;
            padding: 0;
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
<div style="float: none!important;">
    @foreach($barcodes as $barcode)
        <div
            class="pagebreak"
            style="width: 58mm; height: 30mm; float: none; position: relative;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%)">
                {!! $barcode['html'] !!}
                <p style="text-align: center; font-family: monospace; font-size: 12px;">{{ $barcode['barcode'] }}</p>
            </div>
        </div>
    @endforeach
</div>
<script>
    window.print();

    window.addEventListener('afterprint', (event) => {
        window.close();
    });
</script>
</body>
</html>
