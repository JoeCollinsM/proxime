
@if(session()->has('alert'))
    <div class="alert alert-warning alert-dismissible">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Alert!</strong> {{ session('alert') }}
    </div>
@endif


<script>
    @if(Session::has('success'))
        Lobibox.notify('success', {
		    pauseDelayOnHover: true,
		    size: 'mini',
		    rounded: true,
		    icon: 'fa fa-check-circle',
		    delayIndicator: false,
            continueDelayOnInactiveTab: false,
		    position: 'top right',
		    msg: "{{ session('success')}}"
		    });
    @endif
  
    @if($errors->any())
        @foreach($errors->all() as $error)
            Lobibox.notify('error', {
                pauseDelayOnHover: true,
                size: 'mini',
                rounded: true,
                delayIndicator: false,
                icon: 'fa fa-times-circle',
                continueDelayOnInactiveTab: false,
                position: 'top right',
                msg: '{{ $error }}'
                });
        @endforeach
    @endif
  
    @if(Session::has('status'))
        Lobibox.notify('info', {
		    pauseDelayOnHover: true,
		    size: 'mini',
		    rounded: true,
		    icon: 'fa fa-info-circle',
		    delayIndicator: false,
            continueDelayOnInactiveTab: false,
		    position: 'top right',
		    msg: "{{ session('status') }}"
		    });
    @endif
  
    @if(Session::has('alert'))
        Lobibox.notify('warning', {
		    pauseDelayOnHover: true,
		    size: 'mini',
		    rounded: true,
		    delayIndicator: false,
		    icon: 'fa fa-exclamation-circle',
            continueDelayOnInactiveTab: false,
		    position: 'top right',
		    msg: "{{session('alert')}}"
		    });
    @endif
  </script>
