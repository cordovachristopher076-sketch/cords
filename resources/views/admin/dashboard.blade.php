<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Welcome Admin') }}
                </h2>
                <p class="text-sm text-gray-500">Laboratory Attendance & System Management</p>
            </div>
            <span class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-semibold rounded-full">
                System Administrator
            </span>
        </div>
    </x-slot>

    <!-- MAIN WRAPPER WITH ALPINE MODAL STATES -->
    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" 
         x-data="{ 
            showScheduleModal: false, 
            showStudentModal: false,
            showEditModal: false,
            editData: { id: '', name: '', course: '', station: '', status: '' }
         }">

        <!-- WELCOME ADMIN BANNER CARD -->
        <div class="bg-indigo-50 border border-indigo-200 p-6 rounded-lg shadow-sm flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-indigo-900">
                    Welcome back, Admin {{ auth()->user()->name ?? 'admin' }}! 👋
                </h1>
                <p class="text-indigo-700 text-sm mt-1 font-medium">
                    Here is an overview of today's laboratory attendance and system activity.
                </p>
            </div>
            <div class="hidden sm:block">
                <span class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm">
                    {{ now()->format('l, F j, Y') }}
                </span>
            </div>
        </div>

        <!-- CURRENT WEATHER -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Current Weather</h3>
                    <p class="text-sm text-gray-500">Live conditions for the configured laboratory location</p>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wide text-sky-700 bg-sky-50 px-3 py-1 rounded-full">Open-Meteo</span>
            </div>

            @if($weather)
                <div class="grid grid-cols-2 md:grid-cols-5 divide-y md:divide-y-0 md:divide-x divide-gray-200">
                    <div class="p-5 col-span-2 md:col-span-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Location</p>
                        <p class="mt-2 text-lg font-bold text-gray-900">{{ $weather['location'] }}</p>
                    </div>
                    <div class="p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Temperature</p>
                        <p class="mt-2 text-lg font-bold text-gray-900">{{ $weather['temperature'] }}&deg;C</p>
                    </div>
                    <div class="p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Condition</p>
                        <p class="mt-2 text-lg font-bold text-gray-900 capitalize">{{ $weather['condition'] }}</p>
                    </div>
                    <div class="p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Humidity</p>
                        <p class="mt-2 text-lg font-bold text-gray-900">{{ $weather['humidity'] }}%</p>
                    </div>
                    <div class="p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Wind Speed</p>
                        <p class="mt-2 text-lg font-bold text-gray-900">{{ $weather['wind_speed'] }} m/s</p>
                    </div>
                </div>
            @else
                <div class="p-6 bg-amber-50 text-amber-800">
                    <p class="font-semibold">Weather information is currently unavailable.</p>
                    <p class="text-sm mt-1">The attendance dashboard is still fully functional. Please try again later.</p>
                </div>
            @endif
        </div>

        <!-- SUCCESS ALERT -->
        @if(session('status'))
            <div class="p-4 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded-lg shadow-sm">
                {{ session('status') }}
            </div>
        @endif

        <!-- QUICK ACTIONS CARD -->
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Quick Actions</h3>
            <div class="flex flex-wrap gap-4">
                
                <!-- 1. CREATE SCHEDULE BUTTON -->
                <button type="button" @click="showScheduleModal = true" style="background-color: #2563eb; color: #ffffff;" class="px-4 py-2 font-semibold rounded-lg shadow-sm hover:opacity-90 flex items-center gap-2 cursor-pointer">
                    + Create Class Schedule
                </button>

                <!-- 2. EXPORT SUMMARY LINK -->
                <a href="{{ route('admin.attendance.export') }}" style="background-color: #059669; color: #ffffff;" class="px-4 py-2 font-semibold rounded-lg shadow-sm hover:opacity-90 flex items-center gap-2 cursor-pointer">
                    📊 Export Attendance Summary
                </a>

                <!-- 3. REGISTER NEW USER BUTTON -->
                <button type="button" @click="showStudentModal = true" style="background-color: #4f46e5; color: #ffffff;" class="px-4 py-2 font-semibold rounded-lg shadow-sm hover:opacity-90 flex items-center gap-2 cursor-pointer">
                    👤 Register New User
                </button>

                <!-- 4. VIEW REGISTERED STUDENTS BUTTON -->
                <a href="{{ route('admin.students.index') }}" style="background-color: #0284c7; color: #ffffff;" class="px-4 py-2 font-semibold rounded-lg shadow-sm hover:opacity-90 flex items-center gap-2 cursor-pointer">
                    📋 View Registered Students
                </a>

            </div>

            <!-- MODAL 1: CREATE CLASS SCHEDULE FORM -->
            <div x-show="showScheduleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50" style="display: none;">
                <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md border border-gray-200">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Create Class Schedule</h2>
                    
                    <form action="#" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subject / Class Name</label>
                            <input type="text" name="subject" required placeholder="e.g. IT 101 - Web Development" class="w-full border border-gray-300 p-2 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Laboratory Room / Station</label>
                            <input type="text" name="room" required placeholder="e.g. Lab 1 / PC-01" class="w-full border border-gray-300 p-2 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                                <input type="time" name="start_time" required class="w-full border border-gray-300 p-2 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                                <input type="time" name="end_time" required class="w-full border border-gray-300 p-2 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="showScheduleModal = false" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                            <button type="submit" style="background-color: #2563eb; color: #ffffff;" class="px-4 py-2 rounded-md font-semibold hover:opacity-90">Save Schedule</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL 2: REGISTER NEW USER FORM (ONLY ADMIN, FACULTY, STUDENT) -->
            <div x-show="showStudentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50" style="display: none;">
                <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md border border-gray-200">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Register New User</h2>
                    
                    <form action="{{ route('admin.students.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="name" required placeholder="e.g. Juan Dela Cruz" class="w-full border border-gray-300 p-2 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="email" required placeholder="e.g. student@example.com" class="w-full border border-gray-300 p-2 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <select name="role" required class="w-full border border-gray-300 p-2 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="" disabled selected>Select Role</option>
                                <option value="admin">Admin</option>
                                <option value="faculty">Faculty</option>
                                <option value="staff">Staff</option>
                                <option value="student">Student</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input type="password" name="password" required class="w-full border border-gray-300 p-2 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                            <input type="password" name="password_confirmation" required class="w-full border border-gray-300 p-2 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="showStudentModal = false" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                            <button type="submit" style="background-color: #4f46e5; color: #ffffff;" class="px-4 py-2 rounded-md font-semibold hover:opacity-90">Save User</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL 3: EDIT ATTENDANCE RECORD -->
            <div x-show="showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50" style="display: none;">
                <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md border border-gray-200">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Edit Attendance Record</h2>
                    
                    <form :action="'/admin/attendance/' + editData.id" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Student Name</label>
                            <input type="text" name="name" x-model="editData.name" required class="w-full border border-gray-300 p-2 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Course & Section</label>
                            <input type="text" name="course" x-model="editData.course" required class="w-full border border-gray-300 p-2 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Station / PC</label>
                            <input type="text" name="station" x-model="editData.station" required class="w-full border border-gray-300 p-2 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" x-model="editData.status" class="w-full border border-gray-300 p-2 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="Active">Active</option>
                                <option value="Completed">Completed</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                            <button type="submit" style="background-color: #2563eb; color: #ffffff;" class="px-4 py-2 rounded-md font-semibold hover:opacity-90">Update Record</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- RECENT ATTENDANCE AUDIT LOGS TABLE -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Recent Attendance Audit Logs</h3>
                <a href="#" style="background-color: #f3f4f6; color: #374151;" class="px-3 py-1 text-sm font-semibold rounded-md border border-gray-300 hover:bg-gray-200">
                    View All
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs font-semibold uppercase border-b border-gray-200">
                            <th class="px-6 py-3">Student Name / ID</th>
                            <th class="px-6 py-3">Course & Section</th>
                            <th class="px-6 py-3">Station</th>
                            <th class="px-6 py-3">Check-In Time</th>
                            <th class="px-6 py-3 text-center">Status</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($attendances ?? [] as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold text-gray-800">
                                    {{ $log->name ?? 'student' }}
                                    <div class="text-xs text-gray-400 font-normal">{{ $log->student_id ?? 'STD-1' }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $log->course ?? 'BSIT' }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-700">{{ $log->station ?? 'PC-01' }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $log->created_at ? $log->created_at->format('h:i A') : '10:41 AM' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                        {{ $log->status ?? 'Active' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center space-x-2">
                                    <button type="button" 
                                            @click="
                                                editData = {
                                                    id: '{{ $log->id ?? 1 }}',
                                                    name: '{{ $log->name ?? 'student' }}',
                                                    course: '{{ $log->course ?? 'BSIT' }}',
                                                    station: '{{ $log->station ?? 'PC-01' }}',
                                                    status: '{{ $log->status ?? 'Active' }}'
                                                };
                                                showEditModal = true;
                                            "
                                            style="background-color: #e0e7ff; color: #3730a3;" 
                                            class="px-3 py-1 text-xs font-semibold rounded-md hover:opacity-80 cursor-pointer">
                                        Edit
                                    </button>

                                    <form action="{{ route('admin.attendance.destroy', $log->id ?? 1) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this log?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background-color: #fee2e2; color: #991b1b;" class="px-3 py-1 text-xs font-semibold rounded-md hover:opacity-80 cursor-pointer">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No attendance records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>