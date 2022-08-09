<x-app-layout title="Checkout Details">
    <div
        class="max-w-lg p-8 mx-auto my-4 bg-gray-200 rounded-md shadow-sm"
        x-data="{
            routes: {
                init: '{{ route('checkout.initiate') }}',
                success: '{{ route('checkout.success') }}',
                failure: '{{ route('checkout.failure') }}',
                complete: '{{ route('checkout.complete') }}',
            },
            errors: {},
            loading: false,
            props: {
                paymentProvider: 'cash',
                order: @js($order),
            },
            init(){
                console.log(this.props.order)
            },
            initPayment() {
                this.loading = true;
                this.errors = {};
                axios.post(this.routes.init, {
                    payment_provider: this.props.paymentProvider,
                    order_id: '{{ request('order_id') }}',
                }).then((res) => {
                    if (res.data) {
                        console.log(this.props.paymentProvider, res.data);
                        this.proceedWithProvider(res.data);
                    } else {
                        alert('Whoops! Something went wrong.');
                    }
                }).catch(err => {
                    if (err.response?.status === 422) {
                        this.errors = err.response.data.errors;
                    } else {
                        this.errors = {
                            0: err.message,
                            1: err.response?.data?.message,
                        };
                    }
                    console.log(err.response)
                }).finally(() => this.loading = false);
            },
            proceedWithProvider(responseData) {
                switch (this.props.paymentProvider) {
                    case 'cash':
                        this.proceedWithCash(responseData);
                        break;
                    case 'upay':
                        this.proceedWithUpay(responseData);
                        break;
                    case 'bkash':
                        this.proceedWithBkash(responseData);
                        break;
                    case 'rocket':
                        this.proceedWithRocket(responseData);
                        break;
                    case 'nagad':
                        this.proceedWithNagad(responseData);
                        break;
                }
            },
            proceedWithCash(responseData){
                window.location.href = this.routes.complete;
            },
            proceedWithUpay(responseData) {
                window.location.href = responseData;
            },
            proceedWithBkash(responseData) {

            },
            proceedWithRocket(responseData) {

            },
            proceedWithNagad(responseData) {

            },
        }"
    >
        <form @submit.prevent="initPayment()">
            <div class="space-y-4">
                <h1 class="text-xl font-semibold">Checkout Details</h1>
                <h2 class="text-lg">Total: {{ $order->amount }} {{ $order->currency }}</h2>
                <p>Select Payment Method</p>
                <div class="flex gap-4">
                    <label>
                        <input
                            x-model="props.paymentProvider"
                            type="radio"
                            name="payment_provider"
                            value="cash"
                            checked
                        >
                        <span class="ml-2">Cash</span>
                    </label>
                    <label>
                        <input
                            x-model="props.paymentProvider"
                            type="radio"
                            name="payment_provider"
                            value="bkash"
                        >
                        <span class="ml-2">bKash</span>
                    </label>
                    <label>
                        <input
                            x-model="props.paymentProvider"
                            type="radio"
                            name="payment_provider"
                            value="rocket"
                        >
                        <span class="ml-2">Rocket</span>
                    </label>
                    <label>
                        <input
                            x-model="props.paymentProvider"
                            type="radio"
                            name="payment_provider"
                            value="upay"
                        >
                        <span class="ml-2">Upay</span>
                    </label>
                </div>
                <button
                    type="submit"
                    class="px-4 py-2 transition bg-blue-400 rounded-md hover:bg-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="loading"
                >Continue</button>
                <template x-if="errors && Object.keys(errors).length > 0">
                    <ul class="text-red-500">
                        <template x-for="error in errors">
                            <li x-text="error"></li>
                        </template>
                    </ul>
                </template>
            </div>
        </form>
    </div>
</x-app-layout>
