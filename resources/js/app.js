const { default: axios } = require('axios');

require('./bootstrap');

    
document.getElementById('paympesa').addEventListener('click', (event) => {
    event.preventDefault()

    document.getElementById('pageloader-overlay').style.display = "block";
    document.getElementById('loadertext').innerHTML = "Requesting";

    const requestBody = {
                amount: document.getElementById('amount').value,
                phone: document.getElementById('phone').value
            }
        
            axios.post('payment/mobile/push', requestBody)
            .then((response) => {
                if(response.status === 200){
                    document.getElementById('loadertext').innerHTML = response.data.ResponseDescription

                    var interval;

                    let startTime = new Date().getTime()
                    let stopTime = new Date().getTime() + 25000;

                    const confirmationbody ={
                        checkoutrequestid : response.data.CheckoutRequestID
                    }

                    const callback = async () => {
                        let now = new Date().getTime()
            
                        if(now > stopTime){
                            clearInterval(interval)
                            document.getElementById('pageloader-overlay').style.display = "none";
                            document.getElementById('mpesainfo').innerHTML = "Your reponse has timed out.";
                            
                        }
                        axios.post('payment/mobile/confirm', confirmationbody)
                        .then((confirmationresponse) =>{
                            if(confirmationresponse.status === 200){
                                if(confirmationresponse.data.errorCode){}
                                else if(confirmationresponse.data.ResultCode && confirmationresponse.data.ResultCode == 0) {
                                    clearInterval(interval)
                                    document.getElementById('pageloader-overlay').style.display = "none";
                                    // $('#load1').text("Paid!")
                                    // alert(_res.ResultDesc)
                                    // $('#guestbookingform').submit();
                                } else if(confirmationresponse.data.ResultCode && confirmationresponse.data.ResultCode != 0) {
                                    clearInterval(interval)
                                    document.getElementById('pageloader-overlay').style.display = "none";
                                    document.getElementById('mpesainfo').innerHTML = confirmationresponse.data.ResultDesc;
                                }

                            }
                        })
                    }
                    interval = setInterval(callback, 2000)
                } else {
                    // document.getElementById('loadertext').innerHTML = response.data.
                    document.getElementById('pageloader-overlay').style.display = "none";
                    document.getElementById('mpesainfo').innerHTML = "Error try again!!";
                }
            })
            .catch((error) => {
                console.log(error);
            })
    

});


// document.getElementById('getAccessToken').addEventListener('click', (event) => {
//     event.preventDefault()
//     axios.post('mpesa/get-token', {})
//     .then((response) => {
//         console.log(response.data);
//         document.getElementById('access_token').innerHTML = response.data
//     })
//     .catch((error) => {
//         console.log(error);
//     })
// });


document.getElementById('registerURLS').addEventListener('click', (event) => {
    event.preventDefault()

    axios.post('mpesa/register-urls', {})
    .then((response) => {
        if(response.data.ResponseDescription){
            document.getElementById('response').innerHTML = response.data.ResponseDescription
        } else {
            document.getElementById('response').innerHTML = response.data.errorMessage
        }
        console.log(response.data);
    })
    .catch((error) => {
        console.log(error);
    })

});

  
// document.getElementById('simulate').addEventListener('click', (event) => {
//     event.preventDefault()

//     const requestBody = {
//         amount: document.getElementById('amount').value,
//         account: document.getElementById('account').value
//     }

//     axios.post('mpesa/simulate', requestBody)
//     .then((response) => {
//         if(response.data.ResponseDescription){
//             document.getElementById('c2b_response').innerHTML = response.data.ResponseDescription
//         } else {
//             document.getElementById('c2b_response').innerHTML = response.data.errorMessage
//         }
//     })
//     .catch((error) => {
//         console.log(error);
//     })
// })

// document.getElementById('stkpush').addEventListener('click', (event) => {
//     event.preventDefault()

//     const requestBody = {
//         amount: document.getElementById('amount').value,
//         account: document.getElementById('account').value,
//         phone: document.getElementById('phone').value
//     }

//     axios.post('mpesa/stkpush', requestBody)
//     .then((response) => {
//         if(response.data.ResponseDescription){
//             document.getElementById('c2b_response').innerHTML = response.data.ResponseDescription
//         } else {
//             document.getElementById('c2b_response').innerHTML = response.data.errorMessage
//         }
//     })
//     .catch((error) => {
//         console.log(error);
//     })
// })



// document.getElementById('b2csimulate').addEventListener('click', (event) => {
//     event.preventDefault()

//     const requestBody = {
//         amount: document.getElementById('amount').value,
//         occasion: document.getElementById('occasion').value,
//         remarks: document.getElementById('remarks').value,
//         phone: document.getElementById('phone').value
//     }

//     axios.post('mpesa/simulateb2c', requestBody)
//     .then((response) => {
//         if(response.data.Result){
//             document.getElementById('c2b_response').innerHTML = response.data.Result.ResultDesc
//         } else {
//             document.getElementById('c2b_response').innerHTML = response.data.errorMessage
//         }
//     })
//     .catch((error) => {
//         console.log(error);
//     })
// })

// document.getElementById('status').addEventListener('click', (event) => {
//     event.preventDefault()

//     const requestBody = {
//         transactionid: document.getElementById('transactionid').value
//     }

//     axios.post('check-status', requestBody)
//     .then((response) => {
//         if(response.data.Result){
//             document.getElementById('c2b_response').innerHTML = response.data.Result.ResultDesc
//         } else {
//             document.getElementById('c2b_response').innerHTML = response.data.errorMessage
//         }
//     })
//     .catch((error) => {
//         console.log(error);
//     })
// })
// document.getElementById('reverse').addEventListener('click', (event) => {
//     event.preventDefault()

//     const requestBody = {
//         transactionid: document.getElementById('transactionid').value,
//         amount: document.getElementById('amount').value,
//     }

//     axios.post('reversal', requestBody)
//     .then((response) => {
//         if(response.data){
//             document.getElementById('c2b_response').innerHTML = response.data.ResultDescription
//         } else {
//             document.getElementById('c2b_response').innerHTML = response.data.errorMessage
//         }
//     })
//     .catch((error) => {
//         console.log(error);
//     })
// })
