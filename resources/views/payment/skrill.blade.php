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
<form action="https://www.moneybookers.com/app/payment.pl" method="post" id="payment-form">
    <input name="pay_to_email" value="{{ $dbMethod->cred1 }}" type="hidden">
    <input name="transaction_id" value="{{ $payment->track }}" type="hidden">
    <input name="return_url" value="{{ route('payment.success', ['type' => 'order', 'ref' => $payment->track]) }}" type="hidden">
    <input name="return_url_text" value="Return To {{ config('app.name') }}" type="hidden">
    <input name="cancel_url" value="{{ route('payment.failed', ['type' => 'order', 'ref' => $payment->track]) }}" type="hidden">
    <input name="status_url" value="{{ route('payment.ipn', ['type' => 'order', 'ref' => $payment->track]) }}" type="hidden">
    <input name="language" value="EN" type="hidden">
    <input name="amount" value="{{ $payment->gross_amount }}" type="hidden">
    <input name="currency" value="{{ $currency->code }}" type="hidden">
    <input name="detail1_description" value="Payment #{{ $payment->track }}" type="hidden">
    <input name="detail1_text" value="Payment To {{ config('app.name') }}" type="hidden">
    <input name="logo_url" value="{{ config('ui.logo.small') }}" type="hidden">
</form>
<script>
    document.getElementById("payment-form").submit();
</script>
</body>
</html>

