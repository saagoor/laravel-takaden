<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <meta
        http-equiv="X-UA-Compatible"
        content="ie=edge"
    >
    <title>Takaden Checkout</title>

</head>

<body>
    <div>
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


</body>

</html>
