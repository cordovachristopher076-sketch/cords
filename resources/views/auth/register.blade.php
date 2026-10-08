<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Account - Laravel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen py-10">

    <div class="w-full max-w-lg p-8 bg-white rounded-lg shadow-md">
        
        <!-- Header -->
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">System Registration</h2>
            <p class="text-sm text-gray-500 mt-1">Automated Laboratory Attendance & Management Portal</p>
        </div>

        <!-- Form Start -->
        <form method="POST" action="{{ route('register.store') }}">
            @csrf

            <!-- Full Name -->
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-medium mb-1">Full Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('name')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium mb-1">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('email')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Select System Role -->
            <div class="mb-4">
                <label for="role" class="block text-gray-700 font-medium mb-1">Select System Role</label>
                <select id="role" name="role" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="admin">Administrator / Custodian</option>
                    <option value="faculty">Faculty / Instructor</option>
                    <option value="student">Student</option>
                </select>
                @error('role')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-medium mb-1">Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('password')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-gray-700 font-medium mb-1">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Register Button -->
            <div class="mb-4">
                <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-lg transition duration-200 cursor-pointer">
                    Register
                </button>
            </div>
        </form>
        <!-- Form End -->

        <!-- Footer Link -->
        <p class="text-center text-sm text-gray-600 mt-4">
            Already have an account? <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">Log in</a>
        </p>

    </div>

</body>
</html>