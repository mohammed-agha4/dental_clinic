<div
    class=" flex items-center justify-center bg-gradient-to-br from-blue-50 to-teal-50 py-12 px-4 sm:px-6 lg:px-8 "style="width: 80%; margin: 0 auto;">
    <div class="w-full p-4 sm:max-w-md mt-6 px-6 py-8 bg-white shadow-xl sm:rounded-lg border-t-4 border-blue-600"
        style= "width: 50%; margin: 0 auto;">
        <!-- Header -->
        {{-- <div class="text-center mb-6">

            <h2 class="mt-3 text-3xl font-bold text-gray-800">Change Password</h2>
        </div> --}}

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <!-- Current Password -->
            <div class="mt-4">
                <x-input-label for="current_password" :value="__('Current Password')" />
                <x-text-input id="current_password" class="block mt-1 form-control w-full " type="password"
                    name="current_password" required autocomplete="current-password" />

                @error('current_password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <!-- New Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('New Password')" />
                <x-text-input id="password" class="block mt-1 w-full form-control " type="password" name="password"
                    required autocomplete="new-password" />

                @error('password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full form-control" type="password"
                    name="password_confirmation" required autocomplete="new-password" />
                @error('password_confirmation')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div>
                <button type="submit" class="w-50 mb-4 mt-3 py-2  rounded-md shadow-sm"
                    style="background-color: rgb(8, 35, 75); color:aliceblue">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
