<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Registered Students') }}
                </h2>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                @if($students->isEmpty())
                    <p class="text-gray-600">No students registered yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-100 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase">
                                    <th class="p-3">Name</th>
                                    <th class="p-3">Email</th>
                                    <th class="p-3">Role</th>
                                    <th class="p-3">Joined</th>
                                    <th class="p-3 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 text-sm">
                                @foreach($students as $student)
                                    <tr>
                                        <td class="p-3 font-medium text-gray-800">{{ $student->name }}</td>
                                        <td class="p-3 text-gray-600">{{ $student->email }}</td>
                                        <td class="p-3">
                                            <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded bg-blue-100 text-blue-800">
                                                {{ $student->role ?? 'student' }}
                                            </span>
                                        </td>
                                        <td class="p-3 text-gray-600">{{ $student->created_at?->format('M d, Y') ?? 'N/A' }}</td>
                                        <td class="p-3 text-center">
                                            <div class="inline-flex items-center space-x-2">
                                                <!-- EDIT BUTTON -->
                                                <button type="button" 
                                                        style="background-color: #d97706; color: #ffffff; display: inline-block; padding: 4px 12px; border-radius: 4px; font-weight: bold; font-size: 12px;"
                                                        onclick="var el = document.getElementById('edit-student-{{ $student->id }}'); el.style.display = (el.style.display === 'none' || el.style.display === '') ? 'table-row' : 'none';">
                                                    Edit
                                                </button>

                                                <!-- DELETE BUTTON -->
                                                <form action="{{ route('admin.students.destroy', $student) }}" method="POST" onsubmit="return confirm('Delete this student?')" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" style="background-color: #dc2626; color: #ffffff; padding: 4px 12px; border-radius: 4px; font-weight: bold; font-size: 12px;">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- EDIT INLINE ROW -->
                                    <tr id="edit-student-{{ $student->id }}" style="display: none; background-color: #f9fafb;">
                                        <td colspan="5" class="p-4">
                                            <form action="{{ route('admin.students.update', $student) }}" method="POST" class="flex flex-col md:flex-row gap-3 items-end">
                                                @csrf
                                                @method('PUT')
                                                <div class="flex-1">
                                                    <label class="block text-xs font-bold text-gray-700 mb-1">Name</label>
                                                    <input type="text" name="name" value="{{ old('name', $student->name) }}" class="w-full border border-gray-300 rounded p-1.5 text-sm" required>
                                                </div>
                                                <div class="flex-1">
                                                    <label class="block text-xs font-bold text-gray-700 mb-1">Email</label>
                                                    <input type="email" name="email" value="{{ old('email', $student->email) }}" class="w-full border border-gray-300 rounded p-1.5 text-sm" required>
                                                </div>
                                                <div class="w-32">
                                                    <label class="block text-xs font-bold text-gray-700 mb-1">Role</label>
                                                    <select name="role" class="w-full border border-gray-300 rounded p-1.5 text-sm">
                                                        <option value="student" {{ $student->role === 'student' ? 'selected' : '' }}>Student</option>
                                                        <option value="faculty" {{ $student->role === 'faculty' ? 'selected' : '' }}>Faculty</option>
                                                        <option value="staff" {{ $student->role === 'staff' ? 'selected' : '' }}>Staff</option>
                                                        <option value="admin" {{ $student->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                    </select>
                                                </div>
                                                <div class="flex gap-2">
                                                    <button type="submit" style="background-color: #2563eb; color: #ffffff; padding: 6px 12px; border-radius: 4px; font-size: 12px;">Save</button>
                                                    <button type="button" onclick="document.getElementById('edit-student-{{ $student->id }}').style.display='none'" style="background-color: #9ca3af; color: #ffffff; padding: 6px 12px; border-radius: 4px; font-size: 12px;">Cancel</button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $students->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>