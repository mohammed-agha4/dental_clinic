<x-guest-layout>
    <div class=" flex items-center justify-center bg-gradient-to-br from-blue-50 to-teal-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-xl sm:rounded-lg border-t-4 border-blue-600">
            <!-- Header -->
            <div class="text-center mb-6">
                <div class="flex justify-center">
                    <svg class="w-16 h-16 text-blue-600" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12 2C7.589 2 4 5.589 4 10c0 3.453 2.942 7.5 6 9.75v-2.5c-2.382-1.863-4-5.224-4-7.25 0-3.309 2.691-6 6-6s6 2.691 6 6c0 2.026-1.618 5.387-4 7.25v2.5c3.058-2.25 6-6.297 6-9.75 0-4.411-3.589-8-8-8zm0 14c-1.105 0-2-.895-2-2s.895-2 2-2 2 .895 2 2-.895 2-2 2zm-1-6c-.552 0-1-.448-1-1s.448-1 1-1 1 .448 1 1-.448 1-1 1zm2 0c-.552 0-1-.448-1-1s.448-1 1-1 1 .448 1 1-.448 1-1 1z" />
                    </svg>
                </div>
                <h2 class="mt-3 text-3xl font-bold text-gray-800">DentalSuite Pro</h2>
                <p class="text-gray-600">Professional Clinic Management System</p>
            </div>

            <!-- Authentication Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Email">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" name="password" type="password" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Enter Password">
                </div>

                <div class="flex items-center">
                    <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded ">
                    <label for="remember_me" class="ml-3 text-sm text-gray-700">Remember me</label>
                </div>

                <!-- Login Button -->
                <div>
                    <button type="submit" class="w-full mb-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-md shadow-sm focus:ring-blue-500">
                        Sign In
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
