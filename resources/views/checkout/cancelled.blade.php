<x-app-layout title="Payment Failure">
    <x-card class="space-y-2">
        <h1 class="font-bold text-red-500">Whooops!!!</h1>
        <h2>Your payment has been cancelled.</h2>
        <pre>
            @json($order)
        </pre>
        <p>
            <a
                class="text-blue-500 hover:underline"
                href="{{ route('welcome') }}"
            >&#8610; Retry</a>
        </p>
    </x-card>
</x-app-layout>
