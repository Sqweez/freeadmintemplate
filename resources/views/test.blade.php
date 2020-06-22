<h1>Test view</h1>

<table>
    <tbody>
    @foreach ($products as $product)
        <tr>
            <td>{{ $product->product_name  }}</td>
            <td>{{ $product->manufacturer  }}</td>
            <td></td>
        </tr>
    @endforeach
    </tbody>
</table>
