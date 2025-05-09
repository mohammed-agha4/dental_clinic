<x-guest-layout>
    <div class=" flex items-center justify-center bg-gradient-to-br from-blue-50 to-teal-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-xl sm:rounded-lg border-t-4 border-blue-600">
            <!-- Header -->
            <div class="text-center mb-6">
                <div class="flex justify-center">
                    <img src="{{ asset('front/assets/icons/Screenshot_2025-04-29_171414-removebg-preview (1).png') }}" height="150" alt="">
                </div>
                <h2 class="text-gray-600">Professional Clinic Management System</h2>
            </div>

            <!-- Display Session Status -->
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif


            <!-- Authentication Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                        placeholder="Enter Email">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" name="password" type="password" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                        placeholder="Enter Password">
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded ">
                    <label for="remember_me" class="ml-3 text-sm text-gray-700">Remember me</label>
                </div>

                <!-- Login Button -->
                <div>
                    <button type="submit" class="w-full mb-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-md shadow focus:ring-blue-500">
                        Sign In
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
