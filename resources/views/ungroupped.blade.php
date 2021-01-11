<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <title>Несгруппированные товары</title>
</head>
<body>
<div class="container py-3">
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
            <tr>
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
                    <a href="/ungroup/{{ $product['product_id']  }}" class="btn btn-success">
                        Убрать ассортимент
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
