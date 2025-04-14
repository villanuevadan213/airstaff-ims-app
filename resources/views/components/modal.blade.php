<div x-data="{ open: false }">
    <!-- Button to trigger modal -->
    <x-button class="bg-green-600 hover:bg-green-500" @click="open = true">Enter Data</x-button>

    <!-- Modal -->
    <div x-show="open" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 backdrop-blur-0"
        @click="open = false">

        <!-- Modal Content -->
        <div @click.stop
            class="bg-white rounded-lg p-10 w-1/2 max-w-lg shadow-xl transform transition-all duration-300 ease-in-out">
            <div class="text-center mb-6">
                <h3 class="text-2xl font-semibold text-gray-800">Audit Data Entry</h3>
                <p class="text-sm text-gray-500">Fill in the details below to submit the audit data.</p>
            </div>

            <form method="POST" action="{{ route('tracking.store') }}" class="flex flex-col gap-4">
                @csrf
                <x-label class="text-lg font-medium text-gray-700">Enter Data:</x-label>
                <textarea
                    class="border-2 border-gray-300 rounded-lg p-4 focus:ring-2 focus:ring-blue-500 focus:outline-none w-full"
                    name="audit_data" id="audit_data" rows="6"></textarea>

                <div class="flex justify-end">
                    <x-button
                        class="bg-green-600 hover:bg-green-500 text-white font-semibold py-2 px-6 rounded-full shadow-lg transform hover:scale-105 transition-all">
                        Submit
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
