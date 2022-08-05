<script
    src="https://code.jquery.com/jquery-3.3.1.min.js"
    integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
    crossorigin="anonymous"
></script>
<script src="{{ config('takaden.bkash.script_url') }}"></script>

<button id="bKash_button">Pay with bKash</button>

<script>
    const MERCHANT_BACKEND_CREATE_API_CALLER_URL = '/checkout/initiate';
    const MERCHANT_BACKEND_EXECUTE_API_CALLER_URL = '/checkout/execute';
    const MERCHANT_FRONTEND_PAYMENT_SUCCESSFULL_PAGE_URL = '/checkout/success';
    var paymentID = 12345;
    bKash.init({
        paymentMode: 'checkout',
        paymentRequest: {
            amount: 10,
            intent: 'authorization',
            _token: '{{ csrf_token() }}',
        },
        createRequest: function(request) { //request object is basically the paymentRequest object, automatically pushed by the script in createRequest method
            $.ajax({
                url: MERCHANT_BACKEND_CREATE_API_CALLER_URL,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(request),
                success: function(data) {
                    if (data && data.paymentID != null) {
                        paymentID = data.paymentID;
                        bKash.create().onSuccess(data); //pass the whole response data in bKash.create().onSucess() method as a parameter
                    } else {
                        bKash.create().onError();
                    }
                },
                error: function() {
                    bKash.create().onError();
                }
            });
        },
        executeRequestOnAuthorization: function() {
            $.ajax({
                url: MERCHANT_BACKEND_EXECUTE_API_CALLER_URL,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    _token: '{{ csrf_token() }}',
                    payment_id: paymentID,
                }),
                success: function(data) {
                    if (data) {
                        console.log(data);
                        alert("Payment successfull.");
                        window.location.href = MERCHANT_FRONTEND_PAYMENT_SUCCESSFULL_PAGE_URL;
                    } else {
                        bKash.execute().onError();
                    }
                },
                error: function() {
                    bKash.execute().onError();
                }
            });
        }
    });
</script>
