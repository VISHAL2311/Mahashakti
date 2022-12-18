//$('#submitData').click(function(){
//    $('.loader-n').css('display','block');
//    setTimeout(function () {
//        $('.loader-n').css('display','none');
//    }, 1000);
//});
function SetBackGround()
{
    $('.loader-n').css('display', 'block');
}
function UnSetBackGround()
{
    $('.loader-n').css('display', 'none');

}
var ValidateSubscribe = function () {
    var handleSubscribe = function () {
        $("#subscription_form").validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block error', // default input error message class
            ignore: [],
            rules: {
                "g-recaptcha-response": {
                    required: true
                },
                email: {
                    required: true,
                    emailFormat: true
                }
            },
            messages: {
                email: {
                    required: "Please enter email address."
                },
                "g-recaptcha-response": {
                    required: "Captcha is required."
                }
            },
            errorPlacement: function (error, element) {
                if (element.attr('id') == 'g-recaptcha-response') {
                    error.insertAfter(element.parent().parent());
                } else {
                    error.insertAfter(element);
                }
            },
            invalidHandler: function (event, validator) { //display error alert on form submit   
                $('.alert-danger', $('#subscription_form')).show();
            },
            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error'); // set error class to the control group
            },
            submitHandler: function (form) {
                var frmData = $(form).serialize();
                // $('#submitform').prop('disabled', true );
                $.ajax({
                    type: "POST",
                    start: SetBackGround(),
                    url: site_url + '/news-letter',
                    data: frmData,
                    dataType: 'json',
                    async: false,
                    success: function (data) {
                        // $('#submitform').prop('disabled', false );
                        // $('.loader-n').css('display','block');
                        UnSetBackGround();
                        for (var key in data) {
                            if (key == 'error') {
                                $('#subscription_form .error').append('<label class="success">' + data[key] + '</label>');
                                setTimeout(function () {
                                    $('.loader-n').css('display', 'block');
                                }, 3000);
                                setTimeout(function () {
                                    $('.loader-n').css('display', 'none');
                                    $('.error').html('');
                                }, 3000);
                            } else {
                                setTimeout(function () {
                                    $('.loader-n').css('display', 'block');
                                }, 3000);
                                $('#subscription_form .error').html('');
                                $('#subscription_form .success').html('');
                                $('#subscription_form .success').append('<label class="success">' + data[key] + '</label>');
                                $("#subscription_form").trigger("reset");
                                setTimeout(function () {
                                    $('.loader-n').css('display', 'none');
                                    $('.success').html('');
                                }, 3000);
                                // $('#subscription_form input[name=email]').val('');
                            }
                        }
                    }
                });
            }
        });
        $('#subscription_form input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#subscription_form').validate().form()) {
                    alert(3)
                }
                return false;
            }
        });
    }
    return {
        //main function to initiate the module
        init: function () {
            handleSubscribe();
        }
    };
}();

$.validator.addMethod("emailFormat", function (value, element) {
    // allow any non-whitespace characters as the host part
    return this.optional(element) || /^[_A-Za-z0-9-]+(\.[_A-Za-z0-9-]+)*@[A-Za-z0-9-]+(\.[A-Za-z0-9-]+)*(\.[A-Za-z]{2,4})$/.test(value);
}, 'Enter valid email format');


ValidateSubscribe.init();
