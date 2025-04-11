<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl sm:text-2xl text-gray-800 leading-tight">
            {{ __('Attendance') }}
        </h2>
    </x-slot>

    <div class="container mx-auto mt-6 px-4">
        <!-- Clock Section -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Clock In / Break / Clock Out</h3>

            @php
                $log = $timeLogs->first();
            @endphp

            @if(!$log)
                <!-- Not clocked in -->
                <form method="POST" action="{{ route('clock-in') }}">
                    @csrf
                    <button type="submit"
                        class="bg-green-500 text-white px-6 py-2 rounded-md hover:bg-green-600 transition">
                        Clock In
                    </button>
                </form>
                <p class="mt-4 text-sm text-gray-600">Click "Clock In" to start your work session.</p>

            @elseif($log && !$log->break_in)
                <!-- Show Break In button -->
                <form method="POST" action="{{ route('break-in') }}">
                    @csrf
                    <button type="submit"
                        class="bg-yellow-500 text-white px-6 py-2 rounded-md hover:bg-yellow-600 transition">
                        Break In
                    </button>
                </form>

            @elseif($log && $log->break_in && !$log->break_out)
                <!-- Show Break Out button -->
                <form method="POST" action="{{ route('break-out') }}">
                    @csrf
                    <button type="submit"
                        class="bg-yellow-600 text-white px-6 py-2 rounded-md hover:bg-yellow-700 transition">
                        Break Out
                    </button>
                </form>

            @elseif($log && !$log->clock_out)
                <!-- Show Clock Out button -->
                <form method="POST" action="{{ route('clock-out') }}">
                    @csrf
                    <button type="submit" class="bg-red-500 text-white px-6 py-2 rounded-md hover:bg-red-600 transition">
                        Clock Out
                    </button>
                </form>
                <p class="mt-4 text-sm text-gray-600">You're currently clocked in. Please clock out when you're done.</p>

            @else
                <p class="text-sm text-gray-600">✅ You’ve already clocked in and out today.</p>
            @endif

            @if(session('success'))
                <div class="mt-4 p-3 bg-green-100 border border-green-300 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @elseif(session('error'))
                <div class="mt-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-md">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <!-- Time Logs -->
        <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200 overflow-x-auto">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Your Time Log Records</h3>

            <table class="w-full text-sm text-left text-gray-700 border-collapse">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Clock In</th>
                        <th class="px-6 py-3">Break In</th>
                        <th class="px-6 py-3">Break Out</th>
                        <th class="px-6 py-3">Clock Out</th>
                        <th class="px-6 py-3">Duration</th>
                        <th class="px-6 py-3">Overtime</th>
                        <th class="px-6 py-3">Remarks</th>
                        <th class="px-6 py-3">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($timeLogs as $log)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">{{ $log->clock_in->format('d-m-Y') }}</td>
                            <td class="px-6 py-4">{{ $log->clock_in->format('H:i:s') }}</td>
                            <td class="px-6 py-4">
                                {{ $log->break_in ? $log->break_in->format('H:i:s') : '—' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $log->break_out ? $log->break_out->format('H:i:s') : '—' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $log->clock_out ? $log->clock_out->format('H:i:s') : '—' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($log->clock_out)
                                    {{ $log->clock_in->diff($log->clock_out)->format('%Hh %Im %Ss') }}
                                @else
                                    <span class="text-gray-500 italic">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                {{ $log->overtime ? $log->overtime : '—' }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $remark = '';

                                    if ($log->clock_in) {
                                        $remark = 'Clocked In';

                                        $lateTime = \Carbon\Carbon::parse($log->clock_in->format('Y-m-d') . ' 08:00:00');
                                        if ($log->clock_in->greaterThan($lateTime)) {
                                            $remark .= ' - <strong class="text-red-500">LATE</strong>';
                                        }
                                    }

                                    if ($log->break_in && $log->break_out) {
                                        $breakDuration = $log->break_in->diffInMinutes($log->break_out);
                                        if ($breakDuration > 60) {
                                            $remark .= ' - <strong class="text-red-500">Overbreak</strong>';
                                        } else {
                                            $remark = 'On Break';
                                        }
                                    } elseif ($log->break_in && !$log->break_out) {
                                        $remark = 'On Break';
                                    }

                                    if ($log->clock_in && $log->clock_out) {
                                        $workMinutes = $log->clock_in->diffInMinutes($log->clock_out);
                                        $breakMinutes = ($log->break_in && $log->break_out) ? $log->break_in->diffInMinutes($log->break_out) : 0;
                                        $totalMinutes = $workMinutes - $breakMinutes;

                                        $remark = $totalMinutes > 540 ? 'Overtime' : 'Clocked Out';

                                        $lateTime = \Carbon\Carbon::parse($log->clock_in->format('Y-m-d') . ' 08:00:00');
                                        if ($log->clock_in->greaterThan($lateTime)) {
                                            $remark .= ' - <strong class="text-red-500">LATE</strong>';
                                        }
                                    }
                                @endphp

                                <strong>{!! $remark !!}</strong>
                            </td>
                            <td class="px-6 py-4">
                                @if($log->note)
                                    <!-- If note exists, show 'View Note' button -->
                                    <button class="text-blue-500 hover:underline" onclick="openNoteModal({{ $log->id }}, '{{ $log->note }}')">View Note</button>
                                @else
                                    <!-- If no note, show 'Add Note' button -->
                                    <button class="text-green-500 hover:underline" onclick="openNoteModal({{ $log->id }})">Add Note</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center px-6 py-4 text-gray-500">No logs available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="noteModal" class="hidden fixed inset-0 flex justify-center items-center bg-gray-800 bg-opacity-50">
        <div class="bg-white w-full md:w-1/3 p-6 rounded-lg shadow-lg overflow-hidden">
            <!-- Header with close button -->
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-700">Note</h3>
                <button type="button" class="text-gray-500 hover:text-gray-700" onclick="closeNoteModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Form to add or view the note -->
            <form method="POST" action="{{ route('attendance.add-note') }}">
                @csrf
                <input type="hidden" name="log_id" id="log_id">

                <!-- Textarea for note input, default to read-only if note exists -->
                <textarea id="noteText" name="note" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="4" required placeholder="Enter your note here..."></textarea>

                <!-- Action buttons -->
                <div class="mt-4 flex justify-end space-x-2">
                    <button type="submit" id="saveNoteButton" class="bg-green-500 text-white px-6 py-2 rounded-md hover:bg-green-600 transition-colors">Save Note</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<script>
    function openNoteModal(logId, note = '') {
        document.getElementById('log_id').value = logId;

        const noteTextArea = document.getElementById('noteText');
        const saveButton = document.getElementById('saveNoteButton');

        if (note) {
            // If a note exists, allow editing
            noteTextArea.value = note;
            noteTextArea.readOnly = false;  // Allow editing
            saveButton.style.display = 'inline-block'; // Show the save button
        } else {
            // If no note exists, allow adding a new note
            noteTextArea.value = '';
            noteTextArea.readOnly = false;  // Allow editing
            saveButton.style.display = 'inline-block'; // Show the save button
        }

        document.getElementById('noteModal').classList.remove('hidden');
    }

    function closeNoteModal() {
        document.getElementById('noteModal').classList.add('hidden');
    }
</script>
