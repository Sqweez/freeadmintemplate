<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <title>Несгруппированные товары</title>
    <style>
        .loader,
        .loader:before,
        .loader:after {
            border-radius: 50%;
        }
        .loader {
            color: #ffffff;
            font-size: 11px;
            text-indent: -99999em;
            margin: 55px auto;
            position: relative;
            width: 10em;
            height: 10em;
            box-shadow: inset 0 0 0 1em;
            -webkit-transform: translateZ(0);
            -ms-transform: translateZ(0);
            transform: translateZ(0);
        }
        .loader:before,
        .loader:after {
            position: absolute;
            content: '';
        }
        .loader:before {
            width: 5.2em;
            height: 10.2em;
            background: rgba(0,0,0,.5);
            border-radius: 10.2em 0 0 10.2em;
            top: -0.1em;
            left: -0.1em;
            -webkit-transform-origin: 5.1em 5.1em;
            transform-origin: 5.1em 5.1em;
            -webkit-animation: load2 2s infinite ease 1.5s;
            animation: load2 2s infinite ease 1.5s;
        }
        .loader:after {
            width: 5.2em;
            height: 10.2em;
            background: rgba(0,0,0,.5);
            border-radius: 0 10.2em 10.2em 0;
            top: -0.1em;
            left: 4.9em;
            -webkit-transform-origin: 0.1em 5.1em;
            transform-origin: 0.1em 5.1em;
            -webkit-animation: load2 2s infinite ease;
            animation: load2 2s infinite ease;
        }
        @-webkit-keyframes load2 {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
        @keyframes load2 {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

    </style>
</head>
<body>
<div class="container py-3">
    <div class="loading" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999; background: rgba(0,0,0,.5); display: none; align-items: center; justify-content: center">
        <div class="loader">Loading...</div>
    </div>
    <h1>Несгруппированные товары</h1>
    <table class="table">
        <thead>
        <tr>
            <th>#</th>
            <th>ID</th>
            <th>Название</th>
            <th>Стоимость</th>
            <th>Атрибуты</th>
            <th>Производитель</th>
            <th>Категория</th>
            <th>Группируется по:</th>
            <th>Ссылка на сайт</th>
            <th>Убрать ассортимент</th>
        </tr>
        </thead>
        <tbody>
        @foreach($products as $key => $product)
            <tr id="product-{{ $product['id'] }}">
                <td>{{ $key + 1 }}</td>
                <td>{{ $product['id'] }}</td>
                <td>{{ $product['product_name'] }}</td>
                <td>{{ $product['product_price'] }} тенге</td>
                <td>
                    <ul>
                        @foreach($product['attributes'] as $attribute)
                            <li>
                                {{ $attribute['attribute_name']['attribute_name']}}: {{ $attribute['attribute_value'] }}
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td>{{ $product['manufacturer']  }}</td>
                <td>{{ $product['category']  }}</td>
                <td>{{ $attributes->filter(function ($a) use ($product) {  return $a['id'] === $product['grouping_attribute_id'];})->first()['attribute_name']  }}</td>
                <td>
                    <a href="https://iron-addicts.kz/product/{{$product['product_id']}}" target="_blank">
                        {{ $product['product_name']  }}
                    </a>
                </td>
                <td>
                    <a href="/ungroup/{{ $product['product_id']  }}" class="btn btn-success" data-id="{{ $product['id'] }}" data-product="{{ $product['product_id'] }}">
                        Убрать ассортимент
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<script>
    window.addEventListener('DOMContentLoaded', function () {
        const loading = document.querySelector('.loading');
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(function (btn) {
            btn.addEventListener('click', e => {
                loading.style.display = 'flex';
                e.preventDefault();
                const product_id = e.target.dataset.product;
                const id = e.target.dataset.id;
                fetch('/ungroup/' + product_id).then(() => {
                    document.querySelector('#product-' + id).style.display = 'none';
                    loading.style.display = 'none';
                })
            })
        })
    });
</script>
</body>
</html>
