<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каспи Магазины</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100">
    <div class="max-w-4xl mx-auto py-10 px-4">
        <h1 class="text-3xl font-semibold text-gray-800 mb-6">Kaspi Entities</h1>
        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Наименование</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Компания</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merchant ID</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($entities as $entity)
                    <tr class="@if($loop->even) bg-gray-50 @endif">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $entity->id }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $entity->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $entity->company_name ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $entity->merchant_id ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">Нет доступных магазинов</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
