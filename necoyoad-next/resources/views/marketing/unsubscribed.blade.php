{{--
    New Necoyoad — Unsubscribe Confirmation Page
--}}
<x-layouts.storefront>
    <div class="max-w-md mx-auto py-16 text-center">
        <svg class="w-16 h-16 mx-auto text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h1 class="text-2xl font-bold mb-2">Unsubscribed</h1>
        <p class="text-gray-600">You have been successfully unsubscribed from our mailing list.</p>
        <p class="text-gray-500 text-sm mt-4">We're sorry to see you go. You will no longer receive emails from us.</p>
        <a href="{{ route('common.home') }}" class="text-blue-600 hover:underline mt-6 inline-block">Back to Home</a>
    </div>
</x-layouts.storefront>
