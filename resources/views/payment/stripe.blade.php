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
<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe = Stripe('{{ $dbMethod->cred1 }}');
    stripe.redirectToCheckout({
        // Make the id field from the Checkout Session creation API response
        // available to this file, so you can provide it as argument here
        // instead of the {{--CHECKOUT_SESSION_ID--}} placeholder.
        sessionId: '{{ $session_id }}'
    }).then(function (result) {
        // If `redirectToCheckout` fails due to a browser or network
        // error, display the localized error message to your customer
        // using `result.error.message`.
        console.log(result)
    });
</script>
</body>
</html>
