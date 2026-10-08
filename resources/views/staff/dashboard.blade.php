<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Staff Portal') }}
                </h2>
                <p class="text-xs text-gray-500">Laboratory Class & Attendance Management</p>
            </div>
            <span class="inline-flex items-center gap-2 px-3 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full">
                Faculty / Staff Account
            </span>
        </div>
    </x-slot>

    <!-- MAIN WRAPPER WITH COMPACT PADDING TO FIT ONE SCREEN -->
    <div class="py-3 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4" 
         x-data="{ 
            showManualCheckInModal: false,
            showEditModal: false,
            editData: { id: '', name: '', course: '', station: '', status: '' }
         }">

        <!-- WELCOME STAFF BANNER CARD -->
        <div class="bg-purple-50 border border-purple-200 p-4 rounded-lg shadow-sm flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold text-purple-900">
                    Welcome back, {{ auth()->user()->name ?? 'Faculty' }}! 👋
                </h1>
                <p class="text-purple-700 text-xs mt-0.5 font-medium">
                    Monitor student station check-ins and oversee ongoing laboratory sessions.
                </p>
            </div>
            <div class="hidden sm:block">
                <span class="bg-purple-600 text-white px-3 py-1 rounded-lg text-xs font-semibold shadow-sm">
                    {{ now()->format('l, F j, Y') }}
                </span>
            </div>
        </div>

        <!-- SUCCESS / STATUS ALERT -->
        @if(session('status'))
            <div class="p-3 bg-emerald-100 border border-emerald-400 text-emerald-700 text-xs rounded-lg shadow-sm">
                {{ session('status') }}
            </div>
        @endif

        <!-- QUICK ACTIONS CARD FOR STAFF -->
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <h3 class="text-sm font-bold text-gray-800 mb-2">Class Management Actions</h3>
            <div class="flex flex-wrap gap-3">
                
                <!-- 1. MANUAL CHECK-IN BUTTON -->
                <button type="button" @click="showManualCheckInModal = true" style="background-color: #7c3aed; color: #ffffff;" class="px-3 py-1.5 text-xs font-semibold rounded-lg shadow-sm hover:opacity-90 flex items-center gap-2 cursor-pointer">
                    ✍️ Manual Student Check-In
                </button>

                <!-- 2. REFRESH LOGS -->
                <a href="{{ route('staff.dashboard') }}" style="background-color: #4b5563; color: #ffffff;" class="px-3 py-1.5 text-xs font-semibold rounded-lg shadow-sm hover:opacity-90 flex items-center gap-2 cursor-pointer">
                    🔄 Refresh Class List
                </a>

            </div>

            <!-- MODAL 1: MANUAL CHECK-IN FORM -->
            <div x-show="showManualCheckInModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50" style="display: none;">
                <div class="bg-white p-5 rounded-lg shadow-xl w-full max-w-md border border-gray-200">
                    <h2 class="text-lg font-bold mb-3 text-gray-800">Manual Student Check-In</h2>
                    
                    <form action="{{ route('staff.attendance.store') }}" method="POST" class="space-y-3 text-xs">
                        @csrf
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Student Name</label>
                            <input type="text" name="name" required placeholder="Enter student full name" class="w-full border border-gray-300 p-2 rounded-md focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Student ID / Course</label>
                            <input type="text" name="course" required placeholder="e.g. BSIT 2A / STD-101" class="w-full border border-gray-300 p-2 rounded-md focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Assigned Station / PC</label>
                            <input type="text" name="station" required placeholder="e.g. PC-05" class="w-full border border-gray-300 p-2 rounded-md focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="showManualCheckInModal = false" class="px-3 py-1.5 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                            <button type="submit" style="background-color: #7c3aed; color: #ffffff;" class="px-3 py-1.5 rounded-md font-semibold hover:opacity-90">Check-In Student</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL 2: EDIT RECORD FORM -->
            <div x-show="showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50" style="display: none;">
                <div class="bg-white p-5 rounded-lg shadow-xl w-full max-w-md border border-gray-200">
                    <h2 class="text-lg font-bold mb-3 text-gray-800">Update Student Session</h2>
                    
                    <form :action="'/staff/attendance/' + editData.id" method="POST" class="space-y-3 text-xs">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Student Name</label>
                            <input type="text" name="name" x-model="editData.name" required class="w-full border border-gray-300 p-2 rounded-md focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Station / PC</label>
                            <input type="text" name="station" x-model="editData.station" required class="w-full border border-gray-300 p-2 rounded-md focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Attendance Status</label>
                            <select name="status" x-model="editData.status" class="w-full border border-gray-300 p-2 rounded-md focus:ring-purple-500 focus:border-purple-500">
                                <option value="Active">Active Session</option>
                                <option value="Completed">Completed / Logged Out</option>
                                <option value="Late">Late</option>
                            </select>
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="showEditModal = false" class="px-3 py-1.5 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                            <button type="submit" style="background-color: #7c3aed; color: #ffffff;" class="px-3 py-1.5 rounded-md font-semibold hover:opacity-90">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- LIVE LABORATORY ATTENDANCE TABLE WITH COMPACT SPACING -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="text-base font-bold text-gray-800">Current Class Attendance Logs</h3>
                <p class="text-xs text-gray-500">Real-time terminal updates for this laboratory section</p>
            </div>

            <div class="overflow-x-auto max-h-60">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 font-semibold uppercase border-b border-gray-200">
                            <th class="px-4 py-2">Student Name</th>
                            <th class="px-4 py-2">Course / Year</th>
                            <th class="px-4 py-2">Assigned Station</th>
                            <th class="px-4 py-2">Time In</th>
                            <th class="px-4 py-2 text-center">Status</th>
                            <th class="px-4 py-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($attendances ?? [] as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2.5 font-semibold text-gray-800">
                                    {{ $log->name ?? 'Juan Dela Cruz' }}
                                </td>
                                <td class="px-4 py-2.5 text-gray-600">{{ $log->course ?? 'BSIT' }}</td>
                                <td class="px-4 py-2.5 font-semibold text-gray-700">{{ $log->station ?? 'PC-01' }}</td>
                                <td class="px-4 py-2.5 text-gray-500">{{ $log->created_at ? $log->created_at->format('h:i A') : '08:30 AM' }}</td>
                                <td class="px-4 py-2.5 text-center">
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                        {{ $log->status ?? 'Active' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    <button type="button" 
                                            @click="
                                                editData = {
                                                    id: '{{ $log->id ?? 1 }}',
                                                    name: '{{ $log->name ?? 'Student' }}',
                                                    course: '{{ $log->course ?? 'BSIT' }}',
                                                    station: '{{ $log->station ?? 'PC-01' }}',
                                                    status: '{{ $log->status ?? 'Active' }}'
                                                };
                                                showEditModal = true;
                                            "
                                            style="background-color: #f3e8ff; color: #6b21a8;" 
                                            class="px-2.5 py-1 text-xs font-semibold rounded-md hover:opacity-80 cursor-pointer">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-center text-gray-500">No active student check-ins for this session.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>