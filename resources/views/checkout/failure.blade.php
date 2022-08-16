<x-app-layout title="Payment Failure">
    <x-card>
        <h1 class="font-bold text-red-500">Whooops!!!</h1>
        <h2>Your payment has been failed.</h2>
        <x-order-info :order="$order" />
        <p>
            <a href="{{ $order ? route('checkout.index', ['order_id' => $order]) : route('welcome') }}">&#8610; Retry</a>
        </p>
    </x-card>
</x-app-layout>
