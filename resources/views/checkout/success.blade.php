<x-app-layout title="Payment Successfull">
    <x-card>
        <h1 class="text-lg font-semibold text-green-500">Congratulations!!!</h1>
        <h2>Your payment has been successfully completed.</h2>
        <x-order-info :order="$order" />
        <a href="{{ route('welcome') }}">&lArr; Back Home</a>
    </x-card>
</x-app-layout>
