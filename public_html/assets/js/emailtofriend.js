function KeycheckOnlyPhonenumber(e) {
    var t = 0;
    t = document.all ? 3 : document.getElementById ? 1 : document.layers ? 2 : 0;
    if (document.all)
        e = window.event;
    var n = "";
    var r = "";
    if (t == 2) {
        if (e.which > 0)
            n = "(" + String.fromCharCode(e.which) + ")";
        r = e.which
    } else {
        if (t == 3) {
            r = window.event ? event.keyCode : e.which
        } else {
            if (e.charCode > 0)
                n = "(" + String.fromCharCode(e.charCode) + ")";
            r = e.charCode
        }
    }
    if (r >= 65 && r <= 90 || r >= 97 && r <= 122 || r >= 33 && r <= 39 || r >= 42 && r <= 42 || r >= 44 && r <= 44 || r >= 46 && r <= 47 || r >= 58 && r <= 64 || r >= 91 && r <= 96 || r >= 123 && r <= 126) {
        return false
    }
    return true
}
var ValidateEmailtoFriend = function () {
    var handleEmailtoFriend = function () {
        $("#emailtofriend_form").validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            ignore: [],
            rules: {
                varEmailName: {
                    required: true,
                    xssProtection: true,
                    no_url: true
                },
                varFrommEmail: {
                    required: true,
                    emailFormat: true
                },
                varFriendName: {
                    required: true,
                    xssProtection: true,
                    no_url: true
                },
                varFriendEmail: {
                    required: true,
                    emailFormat: true
                },
                txtEmailMessage: {
                    xssProtection: true,
                    no_url: true
                },
                "g-recaptcha-response": {
                    required: true
                },
            },
            messages: {
                varEmailName: {
                    required: "Name is required"
                },
                varFrommEmail: {
                    required: "Email is required"
                },
                varFriendName: {
                    required: "Friend's Name is required"
                },
                varFriendEmail: {
                    required: "Friend's Email is required"
                },
                "g-recaptcha-response": {
                    required: "Captcha is required"
                }
            },
            errorPlacement: function (error, element) {
                if (element.attr('name') == 'g-recaptcha-response') {
                    error.insertAfter(element.parent().parent());
                } else {
                    error.insertAfter(element);
                }
            },
            invalidHandler: function (event, validator) { //display error alert on form submit   
                $('.alert-danger', $('#emailtofriend_form')).show();
            },
            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error'); // set error class to the control group
            },
            submitHandler: function (form) {
                $("#emailtofriend_submit").attr("disabled","disabled");
                $.ajax({
                    url: form.action,
                    start: SetBackGround(),
                    type: form.method,
                    dataType: 'JSON',
                    data: $(form).serialize(),
                    success: function (response) {
                        UnSetBackGround();
                        if (response.validatorErrors != null) {
                            $.each(response.validatorErrors, function (key, value) {
                                var errorInput = key;
                                var error = '<span id=' + errorInput + '-error" class="error" style="">' + value + '</span>';
                                $('#' + errorInput + '-error').remove();
                                if (key == "chrSatisfied") {
                                    $(error).insertAfter($('#Satisfied'));
                                } else if (key == "chrCategory") {
                                    $(error).insertAfter($('.feedback_cat'));
                                } else {
                                    $(error).insertAfter($('#' + key + ''));
                                }
                            });
                        } else {
                            alert(response.success);
                            location.reload();
                        }
                    },
                    complete: function () {
                        grecaptcha.reset();
                    }
                });
            }
        });
        $('#emailtofriend_form input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#emailtofriend_form').validate().form()) {
                    $("#emailtofriend_submit").attr("disabled","disabled");
                }
                return false;
            }
        });
    }
    return {
//main function to initiate the module
        init: function () {
            handleEmailtoFriend();
        }
    };
}();
$.validator.addMethod("emailFormat", function (value, element) {
    // allow any non-whitespace characters as the host part
    return this.optional(element) || /^[_A-Za-z0-9-]+(\.[_A-Za-z0-9-]+)*@[A-Za-z0-9-]+(\.[A-Za-z0-9-]+)*(\.[A-Za-z]{2,4})$/.test(value);
}, 'Enter valid email format');
jQuery.validator.addMethod("xssProtection", function (value, element) {
    // allow any non-whitespace characters as the host part
    return this.optional(element) || /<(\w+)((?:\s+\w+(?:\s*=\s*(?:(?:"[^"]*")|(?:'[^']*')|[^>\s]+))?)*)\s*(\/?)>/.test(value) == false ? true : false;
}, 'Enter valid input');
$.validator.addMethod("check_special_char", function (value, element) {
    if (value != '') {
        if (value.match(/^[\x20-\x7E\n]+$/)) {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}, 'Please enter valid input');
$.validator.addMethod('no_url', function (value, element) {
    var re = /^[a-zA-Z0-9\-\.\:\\]+\.(com|org|net|mil|edu|COM|ORG|NET|MIL|EDU)$/;
    var re1 = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
    var trimmed = $.trim(value);
    if (trimmed == '') {
        return true;
    }
    if (trimmed.match(re) == null && re1.test(trimmed) == false) {
        return true;
    }
}, "URL doesn't allowed");
$.validator.addMethod("phonenumber", function (value, element) {
    var numberPattern = /\d+/g;
    var newVal = value.replace(/\D/g);
    if (parseInt(newVal) <= 0) {
        return false;
    } else {
        return true;
    }
}, 'Please enter a valid phone number.');
ValidateEmailtoFriend.init();
function SetBackGround()
{
    $("body").addClass("blur_loader");
    document.getElementById('loader_div').style.display = 'block';
}
function UnSetBackGround()
{
    document.getElementById('loader_div').style.display = 'none';
    $("body").removeClass("blur_loader");
}