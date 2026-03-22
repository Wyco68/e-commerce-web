<section>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 text-center mb-8">My Account</h1>

    @if(session('status') === 'profile-updated')
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 text-center">
            {{ __('Profile updated successfully.') }}
        </div>
    @endif

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus
                class="w-full border border-gray-300 dark:border-gray-600 rounded px-4 py-2 text-sm dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                class="w-full border border-gray-300 dark:border-gray-600 rounded px-4 py-2 text-sm dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <p class="text-sm text-yellow-600 dark:text-yellow-400 mt-2">
                    {{ __('Your email address is unverified.') }}
                    <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="inline">
                        @csrf
                        <button type="submit" class="underline hover:text-yellow-700">{{ __('Click here to re-send the verification email.') }}</button>
                    </form>
                </p>
            @endif
        </div>

        <!-- Phone Number -->
        <div>
            <label for="phone_num" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
            <input type="text" id="phone_num" name="phone_num" value="{{ old('phone_num', $user->phone_num) }}" required
                class="w-full border border-gray-300 dark:border-gray-600 rounded px-4 py-2 text-sm dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('phone_num') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Address -->
        <div>
            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
            <input type="text" id="address" name="address" value="{{ old('address', $user->address) }}" required
                class="w-full border border-gray-300 dark:border-gray-600 rounded px-4 py-2 text-sm dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('address') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- New Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Password <span class="text-gray-400 text-xs">(leave blank to keep current)</span></label>
            <input type="password" id="password" name="password"
                class="w-full border border-gray-300 dark:border-gray-600 rounded px-4 py-2 text-sm dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Confirm New Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                class="w-full border border-gray-300 dark:border-gray-600 rounded px-4 py-2 text-sm dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('password_confirmation') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Save Changes Button -->
        <button type="submit" class="w-full bg-gray-700 dark:bg-gray-600 text-white py-2 rounded font-medium hover:bg-gray-800 dark:hover:bg-gray-700 transition">
            {{ __('Save Changes') }}
        </button>
    </form>

    <!-- Log Out Button -->
    <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <button type="submit" class="w-full border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 py-2 rounded font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
            {{ __('Log Out') }}
        </button>
    </form>

    <!-- Delete Account Button -->
    <form method="post" action="{{ route('profile.destroy') }}" class="mt-4" id="delete-form" style="display: none;">
        @csrf
        @method('delete')
        <input type="password" id="password-delete" name="password" required>
    </form>
    
    <button type="button" onclick="showDeleteConfirmation()" class="w-full border border-red-500 text-red-500 py-2 rounded font-medium hover:bg-red-50 dark:hover:bg-red-900/20 transition">
        {{ __('Delete Account') }}
    </button>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-sm mx-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                {{ __('Once deleted, all data will be permanently deleted. Please enter your password to confirm.') }}
            </p>
            <input type="password" id="password-confirm" placeholder="{{ __('Password') }}" 
                class="w-full border border-gray-300 dark:border-gray-600 rounded px-4 py-2 text-sm dark:bg-gray-700 dark:text-gray-200 mb-4">
            <div class="flex gap-3">
                <button type="button" onclick="closeDeleteConfirmation()" class="flex-1 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 py-2 rounded font-medium hover:bg-gray-50 dark:hover:bg-gray-700">
                    {{ __('Cancel') }}
                </button>
                <button type="button" onclick="confirmDelete()" class="flex-1 bg-red-600 text-white py-2 rounded font-medium hover:bg-red-700">
                    {{ __('Delete') }}
                </button>
            </div>
        </div>
    </div>

    <script>
        function showDeleteConfirmation() {
            document.getElementById('delete-modal').style.display = 'flex';
        }

        function closeDeleteConfirmation() {
            document.getElementById('delete-modal').style.display = 'none';
            document.getElementById('password-confirm').value = '';
        }

        function confirmDelete() {
            const password = document.getElementById('password-confirm').value;
            if (!password) {
                alert('{{ __("Please enter your password") }}');
                return;
            }
            document.getElementById('password-delete').value = password;
            document.getElementById('delete-form').submit();
        }
    </script>
</section>
