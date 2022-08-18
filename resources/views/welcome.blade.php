<x-app-layout title="Welcome">
    <x-card>
        <h1>Welcome</h1>
        <a class="inline-flex px-4 py-2 text-lg no-underline bg-blue-500 rounded-lg" href="{{ route('checkout.index', ['order_id' =>  $order->id]) }}">Continue to Checkout</a>
    </x-card>
</x-app-layout>
