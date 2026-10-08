<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Registered Students List') }}
                </h2>
                <p class="text-sm text-gray-500">Laboratory Attendance & Student Directory</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-700 font-semibold rounded-lg text-sm hover:bg-gray-300">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- REGISTERED STUDENTS TABLE -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">All Registered Students</h3>
                <span class="text-sm text-gray-500 font-medium">
                    Total: {{ $students->total() ?? count($students ?? []) }} Students
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs font-semibold uppercase border-b border-gray-200">
                            <th class="px-6 py-3">Student Name / ID</th>
                            <th class="px-6 py-3">Email Address</th>
                            <th class="px-6 py-3">Course / Section</th>
                            <th class="px-6 py-3">Registered Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($students ?? [] as $student)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold text-gray-800">
                                    {{ $student->name }}
                                    <div class="text-xs text-gray-400 font-normal">{{ $student->student_id ?? 'STD-N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $student->email }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $student->course ?? 'BSIT' }}</td>
                                <td class="px-6 py-4 text-gray-500">
                                    {{ $student->created_at ? $student->created_at->format('M d, Y') : 'N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    No registered students found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            @if(method_exists($students, 'links'))
                <div class="p-4 border-t border-gray-200">
                    {{ $students->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>