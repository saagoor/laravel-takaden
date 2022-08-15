<x-app-layout title="Checkout Details">
    <div x-data="{
        routes: {
            initiate: '{{ route('takaden.checkout.initiate') }}',
            execute: '{{ route('takaden.checkout.execute') }}',
            success: '{{ route('takaden.checkout.success') }}',
            failure: '{{ route('takaden.checkout.failure') }}',
            cancel: '{{ route('takaden.checkout.cancel') }}',
            complete: '{{ route('checkout.complete') }}',
        },
        errors: {},
        props: {
            paymentProvider: 'cash',
            order: @js($order),
            loading: false,
        },
        init() {
            console.log(this.props.order, this.routes)
        },
        initPayment() {
            this.props.loading = true;
            this.errors = {};
            axios.post(this.routes.initiate + '/' + this.props.paymentProvider, {
                orderable_id: '{{ $order->id }}',
                orderable_type: @js($order::class),
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
            }).finally(() => this.props.loading = false);
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
        proceedWithCash(responseData) {
            window.location.href = this.routes.complete + '?order_id=' + this.props.order.id;
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
    }">
        <x-card>
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
                        class="flex items-center gap-2 px-4 py-2 transition bg-blue-400 rounded-md hover:bg-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="props.loading"
                    >
                        <x-icons.spinner x-show="props.loading" />
                        <span>Continue</span>
                    </button>
                    <template x-if="errors && Object.keys(errors).length > 0">
                        <ul class="text-red-500">
                            <template x-for="error in errors">
                                <li x-text="error"></li>
                            </template>
                        </ul>
                    </template>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
