<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl sm:text-2xl text-gray-800 leading-tight">
            {{ __('Tracking') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-modal />

            @if(session('success') || session('error'))
                @if(session('success'))
                    <div class="bg-green-500 text-white p-3 sm:p-4 rounded-md text-sm sm:text-base">
                        {{ session('success') }}
                    </div>
                @elseif(session('error'))
                    <div class="bg-red-500 text-white p-3 sm:p-4 rounded-md text-sm sm:text-base">
                        {{ session('error') }}
                    </div>
                @endif
            @endif

            <div class="overflow-x-auto bg-white shadow sm:rounded-lg">
                <table class="min-w-full table-auto text-sm sm:text-base">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left font-medium text-gray-900">Title</th>
                            <th class="px-4 sm:px-6 py-3 text-left font-medium text-gray-900">Product Control Number</th>
                            <th class="px-4 sm:px-6 py-3 text-left font-medium text-gray-900">Basket Number</th>
                            <th class="px-4 sm:px-6 py-3 text-left font-medium text-gray-900">Serial Number</th>
                            <th class="px-4 sm:px-6 py-3 text-left font-medium text-gray-900">Tracking Number</th>
                            <th class="px-4 sm:px-6 py-3 text-left font-medium text-gray-900">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse ($audits as $audit)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">{{ $audit->title }}</td>
                                <td class="px-6 py-4">{{ $audit->product_control_no }}</td>
                                <td class="px-6 py-4">{{ $audit->basket_no }}</td>
                                <td class="px-6 py-4">{{ $audit->serial_no }}</td>
                                <td class="px-6 py-4">{{ $audit->tracking->tracking_no }}</td>
                                <td class="px-6 py-4">{{ $audit->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center px-6 py-4 text-gray-500">No logs available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
