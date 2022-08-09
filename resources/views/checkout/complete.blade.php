<x-app-layout>
    <div class="max-w-2xl p-8 mx-auto my-4 space-y-4 bg-blue-100 rounded-lg shadow-sm">
        <h1>Checkout Complete</h1>
        @if ($order)
        <p>Payment Method: {{ $order->payment_method }}</p>
        <p>Total: {{ $order->amount }} {{ $order->currency }}</p>
        @endif
        <p>
            <a href="{{ route('welcome') }}" class="px-4 py-2 bg-blue-400 rounded-md">Go Back</a>
        </p>
    </div>
</x-app-layout>
