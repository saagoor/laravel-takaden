<x-app-layout title="Welcome">
    <div class="max-w-xl mx-auto text-center">
        <h1 class="text-2xl">Welcome</h1>
        <a class="inline-flex px-6 py-4 text-lg bg-blue-500 rounded-lg" href="{{ route('checkout.index', ['order_id' =>  $order->id]) }}">Continue to Checkout</a>
    </div>
</x-app-layout>
