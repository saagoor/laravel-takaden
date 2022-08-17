@push('head')
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="{{ config('takaden.providers.bkash.script_url') }}"></script>
@endpush
<x-app-layout title="Checkout Details">
    <div x-data="{
        getInitiateUrl() {
                return '{{ route('takaden.checkout.initiate') }}' + '/' + this.props.paymentProvider;
            },
            getExecuteUrl() {
                return '{{ route('takaden.checkout.execute') }}' + '/' + this.props.paymentProvider;
            },
            getSuccessUrl() {
                return '{{ route('checkout.success') }}' + '?orderable_id=' + this.props.orderableId + '&orderable_type=' + this.props.orderableType;
            },
            getFailureUrl() {
                return '{{ route('checkout.failure') }}' + '?orderable_id=' + this.props.orderableId + '&orderable_type=' + this.props.orderableType;
            },
            getCompleteUrl() {
                return '{{ route('checkout.complete') }}' + '?orderable_id=' + this.props.orderableId + '&orderable_type=' + this.props.orderableType;
            },
            errors: {},
            props: {
                paymentProvider: 'cash',
                order: @js($order),
                orderableId: @js($order->id),
                orderableType: @js($order::class),
                loading: false,
                initResponse: {},
            },
            init() {
                bKash.init({
                    paymentMode: 'checkout',
                    paymentRequest: {
                        amount: this.props.order.total,
                        intent: 'authorization',
                    },
                    createRequest: () => {
                        this.initPayment()
                    },
                    executeRequestOnAuthorization: () => {
                        this.executePayment({
                            payment_id: this.props.initResponse.paymentID,
                        })
                    },
                });
            },
            handleCheckoutFormSubmit() {
                switch (this.props.paymentProvider) {
                    case 'bkash':
                        document.getElementById('bKash_button').click();
                        break;
                    default:
                        this.initPayment();
                        break;
                }
            },
            initPayment() {
                this.props.loading = true;
                this.errors = {};
                axios.post(this.getInitiateUrl(), {
                    orderable_id: this.props.orderableId,
                    orderable_type: this.props.orderableType,
                }).then((res) => {
                    if (res.data) {
                        this.props.initResponse = res.data;
                        return this.proceedWithProvider(true, res.data);
                    } else {
                        throw new Error('Whoops! Something went wrong.');
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
                    return this.proceedWithProvider(false, err.response);
                }).finally(() => this.props.loading = false);
            },
            executePayment(payload) {
                axios.post(this.getExecuteUrl(), payload)
                    .then(response => { window.location.href = this.getSuccessUrl(); })
                    .catch(err => {
                        if (err.response?.status === 422) {
                            this.errors = err.response.data.errors;
                        } else {
                            this.errors = {
                                0: err.message,
                                1: err.response?.data?.message,
                            };
                        }
                        return this.proceedWithProvider(false, err.response);
                    });
            },
            proceedWithProvider(success, responseData) {
                switch (this.props.paymentProvider) {
                    case 'cash':
                        this.proceedWithCash(success, responseData);
                        break;
                    case 'upay':
                        this.proceedWithUpay(success, responseData);
                        break;
                    case 'bkash':
                        this.proceedWithBkash(success, responseData);
                        break;
                    case 'rocket':
                        this.proceedWithRocket(success, responseData);
                        break;
                    case 'nagad':
                        this.proceedWithNagad(success, responseData);
                        break;
                }
            },
            proceedWithCash(success, responseData) {
                window.location.href = this.getCompleteUrl();
            },
            proceedWithUpay(success, responseData) {
                window.location.href = responseData;
            },
            proceedWithBkash(success, responseData) {
                if (success) {
                    console.log('bkash', responseData);
                    return bKash.create().onSuccess(responseData);
                }
                return bKash.create().onError();
            },
            proceedWithRocket(success, responseData) {

            },
            proceedWithNagad(success, responseData) {

            },
    }">
        <x-card>
            <form @submit.prevent="handleCheckoutFormSubmit()">
                <div>
                    <h1>Checkout Details</h1>
                    <h2>Total: {{ $order->amount }} {{ $order->currency }}</h2>
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

                    <div class="flex mt-4">
                        <button
                            type="submit"
                            class="flex items-center gap-2 px-4 py-2 text-base transition bg-blue-400 rounded-md hover:bg-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="props.loading"
                        >
                            <x-icons.arrow-right x-show="!props.loading" />
                            <x-icons.spinner x-show="props.loading" />
                            <span>Continue</span>
                        </button>

                        <button
                            type="button"
                            id="bKash_button"
                            class="invisible h-0"
                        >Pay with bKash</button>
                    </div>

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
