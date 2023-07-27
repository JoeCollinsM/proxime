@extends('staff.layouts.app')

@section('page_title', 'Service Settings')

@section('content')
     <!-- Breadcrumb-->
     <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Service settings</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Settings</li>
            <li class="breadcrumb-item active">Service Settings</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->

    <div id="app">
        
    
        <form action="{{ route('staff.setting.service.update') }}" method="post">
            @csrf
            @method('PUT')
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="currencylayer_access_key">Currencylayer Access Key <a
                                        href="https://currencylayer.com/" target="_blank">Details</a></label>
                            <input type="text" name="currencylayer_access_key" id="currencylayer_access_key"
                                   class="form-control"
                                   value="{{ get_option('currencylayer_access_key', config('services.currencylayer.access_key')) }}"
                                   required>
                        </div>
                        @if(config('proxime.sms_via') == 'twilio')
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="twilio_auth_token">Twilio Auth Token</label>
                                        <input type="text" name="twilio_auth_token" id="twilio_auth_token"
                                               class="form-control"
                                               value="{{ get_option('twilio_auth_token', config('twilio-notification-channel.auth_token')) }}"
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="twilio_account_sid">Twilio Account SID</label>
                                        <input type="text" name="twilio_account_sid" id="twilio_account_sid"
                                               class="form-control"
                                               value="{{ get_option('twilio_account_sid', config('twilio-notification-channel.account_sid')) }}"
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="twilio_from">Twilio SMS From</label>
                                        <input type="text" name="twilio_from" id="twilio_from" class="form-control"
                                               value="{{ get_option('twilio_from', config('twilio-notification-channel.from')) }}"
                                               required>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="form-group">
                                <label for="sms_api">SMS API (Get Request)</label>
                                <ul class="list-group">
                                    <li class="list-group-item"><code>[to]</code> - Destination Phone Number</li>
                                    <li class="list-group-item"><code>[message]</code> - SMS Message</li>
                                </ul>
                                <input type="text" name="sms_api" id="sms_api" class="form-control"
                                       value="{{ get_option('sms_api', config('services.itech.sms.endpoint')) }}"
                                       required>
                            </div>
                        @endif

                        <div class="form-group">
                            <div class="form-group">
                                <label for="firebase_credentials">firebase_credentials.json contents <span
                                            class="text-info">(FCM Service Credentials exported from firebase <a
                                                href="https://firebase.google.com/docs/admin/setup#initialize-sdk"
                                                target="_blank">Details</a>)</span></label>
                                <textarea id="firebase_credentials" class="form-control" name="firebase_credentials"
                                          required>@php echo $firebase_credentials; @endphp</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary btn-block btn-lg"
                                    type="submit">Update Settings
                            </button>
                        </div>
                    </div>
                </div>
        </form>
    </div>
@endsection
