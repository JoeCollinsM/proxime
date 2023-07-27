<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $dbMethod->description }}</title>
</head>
<body>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    var options = {
        "key": "{{ $dbMethod->cred1 }}",
        "amount": "{{ $payment->gross_amount*100 }}",
        "currency": "{{ $currency->code }}",
        "name": "{{ config('app.name') }}",
        "description": "Payment #{{ $payment->track }}",
        "image": "{{ config('ui.logo.small') }}",
        "order_id": "{{ $order['id'] }}",
        "callback_url": "{{ route('payment.ipn', ['type' => 'order', 'ref' => $payment->track]) }}",
        "prefill": {
            "name": "{{ optional(optional($payment->order)->user)->name }}",
            "email": "{{ optional(optional($payment->order)->user)->email }}",
            "contact": "{{ optional(optional($payment->order)->user)->phone }}"
        },
        "theme": {
            "color": "{{ config('proxime.app.color.color_primary') }}"
        }
    };
    window.rzp1 = new Razorpay(options);
    window.rzp1.open();
</script>
</body>
</html>
