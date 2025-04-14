<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl sm:text-2xl text-gray-900 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Inventory Overview -->
            <div class="bg-white shadow-lg rounded-lg p-4 sm:p-6 border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 12m-9 0a9 9 0 1 0 18 0 9 9 0 1 0-18 0z" />
                    </svg>
                    Inventory Overview
                </h3>
                <p class="text-base sm:text-lg text-gray-600">
                    Total Products: <span class="font-bold text-gray-900 text-lg sm:text-xl">{{ $totalProducts }}</span>
                </p>
            </div>

            <!-- Low Stock Alerts -->
            <div class="bg-white shadow-lg rounded-lg p-4 sm:p-6 border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 text-yellow-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.104 0-2 .896-2 2v4c0 1.104.896 2 2 2s2-.896 2-2V10c0-1.104-.896-2-2-2zm0 6h.01M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2z">
                        </path>
                    </svg>
                    Low Stock Alerts
                </h3>

                @if($lowStock->count())
                    <ul class="list-disc pl-4 sm:pl-6 space-y-2 text-gray-700">
                        @foreach ($lowStock as $product)
                            <li class="flex flex-col sm:flex-row justify-between sm:items-center hover:bg-gray-50 transition-colors p-2 rounded-lg">
                                <div>
                                    <span class="font-semibold text-gray-900">{{ $product->name }}</span>
                                    <span class="text-sm text-gray-600">(SKU: {{ $product->sku }})</span>
                                </div>
                                <span class="text-red-500 font-semibold mt-1 sm:mt-0">{{ $product->quantity }} in stock</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-6 flex justify-center">
                        {{ $lowStock->links() }}
                    </div>
                @else
                    <p class="text-gray-600 text-center py-4">No products are below the reorder level.</p>
                @endif
            </div>

            <!-- Recent Stock Movements -->
            <div class="bg-white shadow-lg rounded-lg p-4 sm:p-6 border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 text-green-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    Recent Stock Movements
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white text-gray-700 table-auto border-collapse">
                        <thead class="bg-gray-100 border-b border-gray-300">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Product Name</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Type</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Quantity</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Reference</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentMovements as $movement)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-sm">{{ $movement->product->name }}</td>
                                    <td class="px-4 py-3 text-sm capitalize">{{ $movement->type }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $movement->quantity }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $movement->reference }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $movement->created_at->format('d-m-Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
