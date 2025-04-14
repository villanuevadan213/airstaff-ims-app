<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl sm:text-2xl text-gray-800 leading-tight">
            {{ __('Inventory') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Add and Search Bar -->
            <div class="flex justify-between items-center mb-6 flex-wrap">
                <!-- Add New Product Button -->
                <a href="{{ route('inventory.create') }}"
                    class="inline-block px-4 sm:px-6 py-2 sm:py-3 bg-green-500 text-white text-sm sm:text-base rounded-md hover:bg-green-600 mb-4 sm:mb-0">
                    Add New Product
                </a>

                <!-- Search Form -->
                <form method="GET" action="{{ route('inventory.index') }}"
                    class="flex items-center space-x-2 w-full sm:w-auto mb-4 sm:mb-0">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Inventory..."
                        class="px-4 py-2 border rounded-l-md w-full sm:w-80">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r-md hover:bg-blue-600">
                        Search
                    </button>
                </form>
            </div>

            <!-- Flash Messages -->
            @if(session('success') || session('error'))
                @if(session('success'))
                    <div class="bg-green-500 text-white p-3 sm:p-4 rounded-md text-sm sm:text-base mb-4">
                        {{ session('success') }}
                    </div>
                @elseif(session('error'))
                    <div class="bg-red-500 text-white p-3 sm:p-4 rounded-md text-sm sm:text-base mb-4">
                        {{ session('error') }}
                    </div>
                @endif
            @endif

            <!-- Table -->
            <div class="overflow-x-auto bg-white shadow sm:rounded-lg">
                <table class="min-w-full table-auto text-sm sm:text-base">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left font-medium text-gray-900">Name</th>
                            <th class="px-4 sm:px-6 py-3 text-left font-medium text-gray-900">SKU</th>
                            <th class="px-4 sm:px-6 py-3 text-left font-medium text-gray-900">Category</th>
                            <th class="px-4 sm:px-6 py-3 text-left font-medium text-gray-900">Quantity</th>
                            <th class="px-4 sm:px-6 py-3 text-left font-medium text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse ($products as $product)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 sm:px-6 py-3 text-gray-900">{{ $product->name }}</td>
                                <td class="px-4 sm:px-6 py-3 text-gray-900">{{ $product->sku }}</td>
                                <td class="px-4 sm:px-6 py-3 text-gray-900">{{ $product->category->name }}</td>
                                <td class="px-4 sm:px-6 py-3 text-gray-900">{{ $product->quantity }}</td>
                                <td class="px-4 sm:px-6 py-3 text-gray-900 space-y-2 sm:space-y-0 sm:space-x-2 flex flex-col sm:flex-row">
                                    <a href="{{ route('inventory.show', $product->id) }}"
                                        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-center">View</a>
                                    <a href="{{ route('inventory.edit', $product->id) }}"
                                        class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 text-center">Edit</a>
                                    <form action="{{ route('inventory.destroy', $product->id) }}" method="POST"
                                        class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 w-full text-center">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center px-6 py-4 text-gray-500">No products available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
