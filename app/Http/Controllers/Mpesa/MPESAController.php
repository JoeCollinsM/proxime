<?php

namespace App\Http\Controllers\Mpesa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class MPESAController extends Controller
{
    public function load(){
        return view('payment.mpesa');
        
//         $data = array
//         (
//             'a' => array ( "id" => 20, "name" => "chimpanzee" ),
//             "b" => array ( "id" => 40, "name" => "meeting" ),
//             "c" => array ( "id" => 20, "name" => "dynasty" ),
//             "d" => array ( "id" => 50, "name" => "chocolate" ),
//             "e" => array ( "id" => 10, "name" => "bananas" ),
//             "f" => array ( "id" => 50, "name" => "fantasy" ),
//             "g" => array ( "id" => 50, "name" => "football" )
//         );

//         $result = array();
//         // $result2 = array();
// foreach ($data as $element) {
//     $result[] = $element['id'];
//     // $result2[$element['id']][] = $element;
// }

// //         // var_dump($result);
// //         return Arr::keyBy($result, 'id');
// //         var_dump($result2);
// //         // $keyed = 
        
// // $cars = array("Volvo", "BMW", "Toyota", "BMW", "Toyota");


return $result;
foreach( array_count_values($result) as $key => $val ) {
    if ( $val > 1 ) $result2[] = $key;   //Push the key to the array sice the value is more than 1
}
// return $result2;
// return  array_unique(array_diff_assoc($result,array_unique($result)));
// return Arr::flatten($result['id']);
    }
    
    public function proximeBusinessToClient(){

        $mpesa= new \Safaricom\Mpesa\Mpesa();
        
        $InitiatorName = env('MPESA_B2C_INITIATOR');
        $SecurityCredential = env('MPESA_B2C_PASSWORD');
        $CommandID= "BusinessPayment";
        $Amount = $amount;
        $PartyA = env("MPESA_SHORTCODE");
        $PartyB = $phone;
        $Remarks = $remarks;
        $QueueTimeOutURL= env("MPESA_CALLBACKURL").'/confirmation';
        $ResultURL= env("MPESA_CALLBACKURL").'/confirmation';
        $Occasion = $occasion;

        // $InitiatorName = env('MPESA_B2C_INITIATOR');
        // $SecurityCredential = env('MPESA_B2C_PASSWORD');
        // $CommandID= "BusinessPayment";
        // $Amount = 100;
        // $PartyA = env("MPESA_SHORTCODE");
        // $PartyB = 254707095396;
        // $Remarks = "proxime pay";
        // $QueueTimeOutURL= env("MPESA_CALLBACKURL").'/confirmation';
        // $ResultURL= env("MPESA_CALLBACKURL").'/confirmation';
        // $Occasion = "test";


        $b2cTransaction=$mpesa->b2c($InitiatorName, $SecurityCredential, $CommandID, $Amount, $PartyA,
         $PartyB, $Remarks, $QueueTimeOutURL, $ResultURL, $Occasion);
       
         return $b2cTransaction;

    }

    public function checkTransactionStatus(){
        $mpesa= new \Safaricom\Mpesa\Mpesa();
        $trasactionStatus=$mpesa->transactionStatus($Initiator, $SecurityCredential, $CommandID, $TransactionID, $PartyA, $IdentifierType, $ResultURL, $QueueTimeOutURL, $Remarks, $Occasion);
    }

    public function proximeSTKPush(Request $request){
        
        $BusinessShortCode = env('MPESA_SHORTCODE');
	    $LipaNaMpesaPasskey= env('MPESA_PASSKEY');
        $TransactionType= "CustomerPayBillOnline";
        $Amount = $request->amount;
        $PartyA = $request->phone;
        $PartyB = env('MPESA_SHORTCODE');
        $PhoneNumber = $request->phone;
        $AccountReference = "Proximepay";
	    $CallBackURL= env('MPESA_CALLBACKURL').'/valid';
        $TransactionDesc = "Payment of B"; 
        $Remarks = "Test pay";

        $mpesa= new \Safaricom\Mpesa\Mpesa();

        $stkPushSimulation=$mpesa->STKPushSimulation($BusinessShortCode, $LipaNaMpesaPasskey, $TransactionType, $Amount, $PartyA, $PartyB, $PhoneNumber, $CallBackURL, $AccountReference, $TransactionDesc, $Remarks);

        return $stkPushSimulation;
    }

    public function proximeSTKStatusQuery(Request $request){
            $mpesa= new \Safaricom\Mpesa\Mpesa();
    
            $checkoutRequestID = $request->checkoutrequestid;
            $BusinessShortCode = env('MPESA_SHORTCODE');
            $LipaNaMpesaPasskey = env('MPESA_PASSKEY');
            $timestamp = date('YmdHis');
            $environ = env('MPESA_ENV');
            $password = base64_encode($BusinessShortCode.$LipaNaMpesaPasskey.$timestamp);
    
            $STKPushRequestStatus=$mpesa->STKPushQuery($environ,$checkoutRequestID,$BusinessShortCode,$password,$timestamp);
    
            return $STKPushRequestStatus;
    }

}