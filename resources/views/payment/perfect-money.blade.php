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
<form action="https://perfectmoney.is/api/step1.asp" method="POST" id="payment-form">
    <input type="hidden" name="PAYEE_ACCOUNT" value="{{ $dbMethod->cred1 }}">
    <input type="hidden" name="PAYEE_NAME" value="{{ config('app.name') }}">
    <input type='hidden' name='PAYMENT_ID' value="{{ $payment->track }}">
    <input type="hidden" name="PAYMENT_AMOUNT" value="{{ $payment->gross_amount }}">
    <input type="hidden" name="PAYMENT_UNITS" value="{{ $currency->code }}">
    <input type="hidden" name="STATUS_URL"
           value="{{ route('payment.ipn', ['type' => 'order', 'ref' => $payment->track]) }}">
    <input type="hidden" name="PAYMENT_URL"
           value="{{ route('payment.success', ['type' => 'order', 'ref' => $payment->track]) }}">
    <input type="hidden" name="PAYMENT_URL_METHOD" value="GET">
    <input type="hidden" name="NOPAYMENT_URL"
           value="{{ route('payment.failed', ['type' => 'order', 'ref' => $payment->track]) }}">
    <input type="hidden" name="NOPAYMENT_URL_METHOD" value="GET">
    <input type="hidden" name="SUGGESTED_MEMO" value="{{ $user->name }}">
    <input type="hidden" name="BAGGAGE_FIELDS" value="IDENT"><br>
</form>
<script>
    document.getElementById("payment-form").submit();
</script>
</body>
</html>
