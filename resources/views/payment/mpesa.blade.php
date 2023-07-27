
    @extends('staff.layouts.auth')

<!-- @section('page_title', '{{ $dbMethod->description }}') -->

@section('content')

<div class="mx-auto">
        <div id="c2b_response"></div>
        <form action="" id="payment-form">
            @csrf
            
            <p> Pay to  Proxime Markets for order  </p>
            <p id="mpesainfo"></p>
            <div class="form-group">
                <label for="phone">Phone (without +)</label>
                    <input class="form-control" type="text" max="12" min="10" id="phone" name="phone" value="254707095396" required>
            </div>
            <input id="amount" name="amount" value="100" type="hidden">
            <input name="currency" value="KES" type="hidden">

            <button id="paympesa" type="button" class="btn btn-success">Pay </button>
        </form>
    </div>

    
                      
@endsection

<script>
    document.getElementById("payment-form").submit();
</script>
</body>
</html>

