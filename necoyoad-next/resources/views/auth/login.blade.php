{{--
    New Necoyoad — Customer Login
--}}
<x-layouts.storefront>
    <div class="max-w-md mx-auto py-12">
        <h1 class="text-2xl font-bold mb-6 text-center">Login</h1>
        <form method="POST" action="{{ route('customer.login.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border rounded-lg p-3 @error('email') border-red-500 @enderror">
                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" required
                       class="w-full border rounded-lg p-3 @error('password') border-red-500 @enderror">
                @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700">
                Login
            </button>
            <p class="text-center text-sm text-gray-500">
                Don't have an account? <a href="{{ route('customer.register') }}" class="text-blue-600">Register</a>
            </p>
        </form>
    </div>
</x-layouts.storefront>
