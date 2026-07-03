{{--
    New Necoyoad — Customer Registration
--}}
<x-layouts.storefront>
    <div class="max-w-md mx-auto py-12">
        <h1 class="text-2xl font-bold mb-6 text-center">Create Account</h1>
        <form method="POST" action="{{ route('customer.register.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">First Name</label>
                    <input type="text" name="firstname" required class="w-full border rounded-lg p-3">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Last Name</label>
                    <input type="text" name="lastname" required class="w-full border rounded-lg p-3">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" required class="w-full border rounded-lg p-3">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" required class="w-full border rounded-lg p-3">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" required class="w-full border rounded-lg p-3">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700">
                Register
            </button>
        </form>
    </div>
</x-layouts.storefront>
