<h1>Test view</h1>


<ul>
    @foreach($reports as $report)
    <li>
        {{ $report['product_name'] }} : {{ $report['quantity']  }}
    </li>
    @endforeach
</ul>
