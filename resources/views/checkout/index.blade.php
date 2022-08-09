<x-app-layout title="Checkout Details">
    <div>
        <h1>Checkout Details</h1>
        <button onclick="payWithUpay(event)">Pay with Upay</button>
    </div>

    <script>
        function payWithUpay(e) {
            e.preventDefault();
            fetch('{{ route('checkout.initiate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        _token: '{{ csrf_token() }}',
                        payment_method: 'upay',
                    })
                })
                .then(async (res) => {
                    const data = await res.text();
                    if (data) {
                        window.location.href = data;
                    } else {
                        alert('Whoops! Something went wrong.');
                    }
                }).catch(err => {
                    alert(err);
                })
        }
    </script>
</x-app-layout>
