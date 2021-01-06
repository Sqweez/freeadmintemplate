<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Чек</title>
</head>
<body>
<div class="check-wrapper" id="print" style="font-family: sans-serif; color: #000; width: 58mm; padding-right: 10mm">
    <div class="check-header"
         style="display: flex;
                align-items: center;
                flex-direction: column;">
        <img src="{{ asset('images/logo-check.png') }}" alt=""
             style="width: 65px;
                    filter: grayscale(1);
                    height: auto;
                    margin-bottom: 10px;">
        <h1
            style="display:block;
                    color: #000;
                    font-family: sans-serif;
                    font-weight: bold;
                    font-style: italic;
                    text-transform: uppercase;
                    font-size: 15px;
                    text-align: center;
                    margin: 0;">
            Сеть магазинов спортивного питания "ironaddicts"
        </h1>
        <div class="check-divider"
             style="margin: 10px 0;
                    width: 58mm;
                    transform: skew(-15deg);
                    display: flex;
                    align-items: center;">
            <div class="divider-one"
                 style="flex: 1;
                        height: 2px;
                        background-color: #000;
                        margin-right: 7px;"></div>
            <div class="divider-two"
                 style="flex: 2;
                            height: 1px;
                            background-color: #000;"></div>
        </div>
    </div>
    <div class="check-body">
        <ol class="products-list" style="padding-left: 10px;">
            @foreach ($report->products as $product)
                <li
                    style="color: #000;
                        font-size: 12px;
                        font-style: italic;
                        text-transform: uppercase;">
                    <span class="product-name">{{ $product['product_name']  }} {{ collect($product['attributes'])->join(' | ') }}</span>
                    <span class="product-footer"
                          style="display: flex;
                             justify-content: space-between;
                             margin-left: 20px;">
                                    <span class="product-count" style="
                                    white-space: nowrap;
                                    overflow: hidden;">
                                        {{ $product['count'] }} х  {{ $product['product_price'] }}..........................................................................................................................................................................................................
                                    </span>
                                     <span
                                         class="product-cost">{{ $product['count'] * $product['product_price']  }}</span>
                                </span>
                </li>
            @endforeach
        </ol>
        @if ($report->discount != 0 || $report->client['id'] != -1)
            <div class="check-divider"
                 style="margin: 10px 0;
                    width: 58mm;
                    transform: skew(-15deg);
                    display: flex;
                    align-items: center;">
                <div class="divider-one"
                     style="flex: 1;
                        height: 2px;
                        background-color: #000;
                        margin-right: 7px;"></div>
                <div class="divider-two"
                     style="flex: 2;
                        height: 1px;
                        background-color: #000;"></div>
            </div>
            @if ($report->client['id'] != -1)
                <div
                    style="font-size: 12px;
                            text-transform: uppercase;
                            font-style: italic;"
                    class="client-info">
                    <div class="client-name"
                         style="display: flex;
                                justify-content: space-between;">
                        <span class="client-label"
                              style="white-space: nowrap;
                                     overflow: hidden;">
                            Ф.И.О......................................................................................................................
                        </span>
                        <span class="name"
                              style="white-space: normal;
                                     margin-left: 5px;">
                            {{ $report->client['client_name'] }}
                    </span>
                    </div>
                    @endif
                    @if($report->discount != 0)
                        <div class="client-discount"
                             style="display: flex;
                        justify-content: space-between;">
                <span class="client-label"
                      style="white-space: nowrap;
                             overflow: hidden;">
                    Скидка......................................................................................................................
                </span>
                            <span class="name"
                                  style="white-space: normal;
                             margin-left: 5px;">
                    {{ $report->discount }}%
                </span>
                        </div>
                    @endif
                </div>
            @endif
    </div>
    <div class="check-divider"
         style="margin: 10px 0;

                width: 58mm;
                transform: skew(-15deg);
                display: flex;
                align-items: center;">
        <div class="divider-one"
             style="flex: 1;
                    height: 2px;
                    background-color: #000;
                    margin-right: 7px;"></div>
        <div class="divider-two"
             style="flex: 2;
                            height: 1px;
                            background-color: #000;"></div>
    </div>
    <div class="check-footer" style="width: 58mm;">
        @if($report->certificate)
        <div class="total"
             style="display: flex;
                    justify-content: space-between;
                    text-transform: uppercase;">
            <span class="footer-label" style="font-size: 12px; white-space: nowrap; overflow: hidden;">
                <span class="label-red" style="font-size: 12px;">Сертификат</span>
                ..........................................................................................................................................................................................................................
            </span>
            <span class="span-total"
                  style="font-size: 12px; white-space: nowrap; margin-left: 5px;">{{ $report->certificate['amount'] }}</span>
        </div>
        @endif
        <div class="total"
             style="display: flex;
                    justify-content: space-between;
                    text-transform: uppercase;">
            <span class="footer-label" style="font-size: 12px; white-space: nowrap; overflow: hidden;">
                <span class="label-red" style="font-size: 12px;">итого</span>
                к оплате..........................................................................................................................................................................................................................
            </span>
            <span class="span-total"
                  style="font-size: 12px; white-space: nowrap; margin-left: 5px;">{{ $report->final_price - ($report->certificate['amount'] ?? 0) }}</span>
        </div>
        <h5 style="text-align: center; text-transform: uppercase; font-style: italic; font-size: 12px; margin-top: 15px;">
            www.iron-addicts.kz</h5>
        <h4 style="text-align: center; text-transform: uppercase; font-size: 15px; margin-top: 10px; margin-bottom: 40px; font-weight: bold;">
            Спасибо за покупку!</h4>
    </div>
    <div class="empty-space"></div>
</div>
<script>
   window.print();

    window.addEventListener('afterprint', (event) => {
        window.close();
    });
</script>
</body>
</html>
