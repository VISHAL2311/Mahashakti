/**
 * This method show hides default banner fields
 * since   2016-12-22
 * author  NetQuick
 */
var Custom = function() {
    return {
        //main function
        init: function() {
            //initialize here something.            
        },
        checkType: function() {
            var radioValue = $("input[name='banner_type']:checked").val();
            if (radioValue == 'inner_banner') {
                $('#pages').show();
                $('#pagesDescri').hide();
                if ($('#modules').val() != ' ') {
                    $('#records').show();
                }
                $('#DisplayVideo').hide();
                $('#txtshortdesc').hide();
                $('#linkTEXT').hide();
                $('#DisplayLink').hide();
                $('#DisplayOrder').hide();
                $('#HomeBannerSize').hide();
                $('#InnerBannerSize').show();
             
                $('.bannerversion').hide();
                $("input[name='defaultBanner']").removeAttr('disabled');
                $("input[name='defaultBanner']").closest('.form-group').show();
                document.getElementById('videolink').value = "";
                $('#chrDisplayVideo').prop('checked', false);
                $("#VideoLinkTEXT").css("display", "none");
                $("#txtshortdesc").css("display", "block");
                $("#DisplayLink").css("display", "block");
                $("#DisplayOrder").css("display", "none");
                $("#linkTEXT").css("display", "none");
                $('#modules').select2({
                    placeholder: "Select Module",
                    width: '100%',
                    minimumResultsForSearch: 5
                });
                $("#txtTagLine").hide();
                $("#button_title").hide();
            } else if (radioValue == 'home_banner') {
                $('#pagesDescri').show();
                $('#txtTagLine').show();
                $('#pages').hide();
                $('#records').hide();
                $('#txtshortdesc').show();
                $('#linkTEXT').show();
                $('#DisplayLink').show();
                $('#DisplayOrder').show();
                $('#DisplayVideo').show();
                $('#HomeBannerSize').show();
                $('#InnerBannerSize').hide();
                $('.bannerversion').hide();
                Custom.checkVersion();
                $("input[name='defaultBanner']").attr('disabled', true);
                $("input[name='defaultBanner']").closest('.form-group').hide();
                $('#button_title').show();
            }
        },
        checkVersion: function() {
            var radioValue = $("input[name='bannerversion']:checked").val();
            if (radioValue == 'img_banner') {
              
                $('.viduploader').addClass('hide');
            } else {
            
                $('.viduploader').removeClass('hide');
            }
            $('#modules').select2({
                placeholder: "Select Module",
                width: '100%',
                minimumResultsForSearch: 5
            });
        },
        getModuleRecords: function(moduleName, modelName) {
            var ajaxUrl = site_url + '/powerpanel/banners/selectRecords';
            jQuery.ajax({
                type: "POST",
                url: ajaxUrl,
                dataType: 'HTML',
                data: { "module": moduleName, "model": modelName, 'selected': selectedRecord },
                async: false,
                success: function(result) {
                    $('#foritem').html(result).select2({
                        placeholder: "Select Module",
                        width: '100%',
                        minimumResultsForSearch: 5
                    });
                }
            });
        }
    }
}();
/**
 * This method validates banner form fields
 * since   2016-12-24
 * author  NetQuick
 */
var Validate = function() {
    var handleBanner = function() {
        $("#frmBanner").validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            ignore: [],
            rules: {
                title: {
                    required: true,
                    noSpace: true,
                    xssProtection: true,
                    no_url: true
                },
                tag_line: {
                    // required: {
                    //     depends: function() {
                    //         return $('#home_banner[value="home_banner"]:checked').length > 0;
                    //     }
                    // },
                    // noSpace: {
                    //     depends: function() {
                    //         return $('#home_banner[value="home_banner"]:checked').length > 0;
                    //     }
                    // }
                },
                // varRotateTime: {
                //     required: true,
                //     noSpace: true,
                //     no_url: true,
                //     xssProtection: true,
                //     number: true,
                // },
                //description: "required",
                display_order: {
                    required: true,
                    minStrict: true,
                    number: true,
                    noSpace: true,
                    xssProtection: true,
                    no_url: true
                },
                img_id: {
                    required:true
                },
                videolink: {
                    required: {
                        depends: function() {
                            if ($('#chrDisplayVideo').prop('checked') == true) {
                                return true;
                            }
                        }
                    },
                    urlvalidate: true,
                },
                video_id: {
                    required: {
                        depends: function() {
                            return $('.versionradio[value="vid_banner"]:checked').length > 0;
                        }
                    }
                },
                modules: {
                    required: {
                        depends: function() {
                            return $('#inner_banner[value="inner_banner"]:checked').length > 0;
                        }
                    },
                    noSpace: {
                        depends: function() {
                            return $('#inner_banner[value="inner_banner"]:checked').length > 0;
                        }
                    }
                },
                foritem: {
                    required: {
                        depends: function() {
                            return $('#inner_banner[value="inner_banner"]:checked').length > 0;
                        }
                    },
                    noSpace: {
                        depends: function() {
                            return $('#inner_banner[value="inner_banner"]:checked').length > 0;
                        }
                    }
                },
                start_date_time: {
                    required: true,
                },
                button_name: {
                    // required: {
                    //     depends: function() {
                    //         if($('#home_banner[value="home_banner"]:checked').length > 0) {
                    //             if ($('#chrDisplayVideo').prop('checked') == false) {
                    //                 // return $('#home_banner[value="home_banner"]:checked').length > 0;
                    //                 return true;
                    //             }
                    //         }                            
                    //     }
                    // },
                    // noSpace: {
                    //     depends: function() {
                    //         if($('#home_banner[value="home_banner"]:checked').length > 0) {
                    //             if ($('#chrDisplayVideo').prop('checked') == false) {
                    //                 // return $('#home_banner[value="home_banner"]:checked').length > 0;
                    //                 return true;
                    //             }
                    //         }
                    //         // return $('#home_banner[value="home_banner"]:checked').length > 0;
                    //     }
                    // },
                    xssProtection: {
                        depends: function() {
                            if($('#home_banner[value="home_banner"]:checked').length > 0) {
                                if ($('#chrDisplayVideo').prop('checked') == false) {
                                    // return $('#home_banner[value="home_banner"]:checked').length > 0;
                                    return true;
                                }
                            }
                            // return $('#home_banner[value="home_banner"]:checked').length > 0;
                        }
                    },
                    no_url: {
                        depends: function() {
                            if($('#home_banner[value="home_banner"]:checked').length > 0) {
                                if ($('#chrDisplayVideo').prop('checked') == false) {
                                    // return $('#home_banner[value="home_banner"]:checked').length > 0;
                                    return true;
                                }
                            }
                            // return $('#home_banner[value="home_banner"]:checked').length > 0;
                        }
                    }
                },
                link: {
                    // required: {
                    //     depends: function() {
                    //         if($('#home_banner[value="home_banner"]:checked').length > 0) {
                    //             if ($('#chrDisplayVideo').prop('checked') == false) {
                    //                 // return $('#home_banner[value="home_banner"]:checked').length > 0;
                    //                 return true;
                    //             }
                    //         }
                    //         // return $('#home_banner[value="home_banner"]:checked').length > 0;
                    //     }
                    // },
                    xssProtection: {
                        depends: function() {
                            if($('#home_banner[value="home_banner"]:checked').length > 0) {
                                if ($('#chrDisplayVideo').prop('checked') == false) {
                                    // return $('#home_banner[value="home_banner"]:checked').length > 0;
                                    return true;
                                }
                            }
                            // return $('#home_banner[value="home_banner"]:checked').length > 0;
                        }
                    },
                    url: {
                        depends: function() {
                            if($('#home_banner[value="home_banner"]:checked').length > 0) {
                                if ($('#chrDisplayVideo').prop('checked') == false) {
                                    // return $('#home_banner[value="home_banner"]:checked').length > 0;
                                    return true;
                                }
                            }
                            // return $('#home_banner[value="home_banner"]:checked').length > 0;
                        }
                    }
                },
                end_date_time: {
                    daterange: true,
                    required: {
                        depends: function() {
                            var isChecked = $('#end_date_time').attr('data-exp');
                            if (isChecked == 0) {
                                return $('input[name=end_date_time]').val().length == 0;
                            }
                        }
                    }
                },
                short_description: {
                    xssProtection: true,
                    no_url: true
                }
            },
            messages: {
                title: {
                    required: Lang.get('validation.required', { attribute: Lang.get('template.title') }),
                },
                tag_line: {
                    required: "Tag line field is required.",
                },
                img_id: Lang.get('validation.required', { attribute: Lang.get('template.banner') }),
                modules: Lang.get('validation.required', { attribute: Lang.get('template.module') }),
                foritem: Lang.get('validation.required', { attribute: Lang.get('template.page') }),
                videolink: {
                    required: "Please enter the url.",
                    urlvalidate: "Please enter Vimeo or Youtube link.",
                },
                // varRotateTime: {
                //     required: "Please enter image rotate time.",
                // },
                button_name: {
                    required: "Button name field is required.",
                },
                link: {
                    required: "Button link field is required.",
                    url: "Please enter valid url.",
                },
                display_order: {
                    required: Lang.get('validation.required', { attribute: Lang.get('template.displayorder') }),
                    minStrict: Lang.get('validation.minStrict', { attribute: Lang.get('template.displayorder') }),
                    number: Lang.get('validation.number', { attribute: Lang.get('template.displayorder') }),
                    noSpace: Lang.get('validation.noSpace', { attribute: Lang.get('template.displayorder') })
                },
                start_date_time: {
                    required: "Start date field is required.",
                },
                end_date_time: {
                    required: "End date field is required.",
                    daterange: 'The end date must be a greater than start date.'
                },
            },
            errorPlacement: function(error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else if (element.hasClass('select2')) {
                    error.insertAfter(element.next('span'));
                } else {
                    error.insertAfter(element);
                }
            },
            invalidHandler: function(event, validator) { //display error alert on form submit
                var errors = validator.numberOfInvalids();
                if (errors) {
                    $.loader.close(true);
                }
                $('.alert-danger', $('#frmBanner')).show();
            },
            highlight: function(element) { // hightlight error inputs
                $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error'); // set error class to the control group
            },
            submitHandler: function(form) { // for demo
                $('body').loader(loaderConfig);
                form.submit();
                $("button[type='submit']").attr('disabled', 'disabled');
                return false;
            }
        });
        $('#frmBanner input').keypress(function(e) {
            if (e.which == 13) {
                if ($('#frmBanner').validate().form()) {
                    $('#frmBanner').submit(); //form validation success, call ajax form submit
                }
                return false;
            }
        });
    }
    return {
        //main function to initiate the module
        init: function() {
            handleBanner();
        }
    };
}();
jQuery(document).ready(function() {
    Custom.init();
    Custom.checkType();
    Custom.checkVersion();
    Validate.init();
    $(document).on('click', '.versionradio', function(e) {
        Custom.checkVersion();
    });
    $(document).on('click', '.banner', function(e) {
        Custom.checkType();
    });
    $('#modules').on("change", function(e) {
        Custom.getModuleRecords($("#modules option:selected").data('module'), $("#modules option:selected").data('model'));
    });
    jQuery.validator.addMethod("noSpace", function(value, element) {
        if (value.trim().length <= 0) {
            return false;
        } else {
            return true;
        }
    }, "This field is required");
    var isChecked = $('#end_date_time').attr('data-exp');
    if (isChecked == 1) {
        $('.expdatelabel').removeClass('no_expiry');
        $('.expiry_lbl').text('Set Expiry');
        $(".expirydate").hide();
        $('#end_date_time').attr('disabled', 'disabled');
    } else {
        $('.expdatelabel').addClass('no_expiry');
        $('.expiry_lbl').text('No Expiry');
        $('#end_date_time').removeAttr('disabled');
    }
});
jQuery.validator.addMethod("urlvalidate", function(value, element) {
    if (value != "") {
        if ((/((http|https):\/\/)?(www\.)?(youtube\.com)(\/)?([a-zA-Z0-9\-\.]+)\/?/.test(value) == true) || (/^(http\:\/\/|https\:\/\/)?(www\.)?(vimeo\.com\/)([0-9]+)$/.test(value) == true)) {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}, '<div class="fl" id="chrVideoTypeY"><?php echo "Please enter a youtube link."; ?></div>');
jQuery.validator.addMethod("phoneFormat", function(value, element) {
    // allow any non-whitespace characters as the host part
    return this.optional(element) || /((\(\d{3}\) ?)|(\d{3}-))?\d{3}-\d{4}/.test(value);
}, 'Please enter a valid phone number.');
jQuery.validator.addMethod("minStrict", function(value, element) {
    // allow any non-whitespace characters as the host part
    if (value > 0) {
        return true;
    } else {
        return false;
    }
}, 'Display order must be a number higher than zero');
jQuery.validator.addMethod("noSpace", function(value, element) {
    if (value.trim().length <= 0) {
        return false;
    } else {
        return true;
    }
}, "This field is required");
$('input[name=title]').change(function() {
    var title = $(this).val();
    var trim_title = title.trim();
    if (trim_title) {
        $(this).val(trim_title);
        return true;
    }
});
$(window).load(function() {
    var radioValue = $("input[name='banner_type']:checked").val();
    if (selectedRecord > 0) {
        $('#modules').trigger('change');
        if (radioValue == 'home_banner') {
            $('#modules').select2({
                placeholder: "Select Module",
                width: '100%',
                minimumResultsForSearch: 5
            });
            $('#records').hide();
        }
    }
});
jQuery.validator.addMethod("daterange", function(value, element) {
    var fromDateTime = $('#start_date_time').val();
    var toDateTime = $("#end_date_time").val();
    var isChecked = $('#end_date_time').attr('data-exp');
    if (isChecked == 0) {
        toDateTime = new Date(toDateTime);
        fromDateTime = new Date(fromDateTime);
        return toDateTime >= fromDateTime && fromDateTime <= toDateTime;
    } else {
        return true;
    }
}, "The end date must be a greater than start date.");
$('.fromButton').click(function() {
    $('#start_date_time').datetimepicker('show');
});
$('.toButton').click(function() {
    $('#end_date_time').datetimepicker('show');
});
$(document).on("change", '#end_date_time', function() {
    $(this).attr('data-newvalue', $(this).val());
});
$('#noexpiry').click(function() {
    var isChecked = $('#end_date_time').attr('data-exp');
    if (isChecked == 0) {
        $('.expdatelabel').removeClass('no_expiry');
        $('.expiry_lbl').text('Set Expiry');
        $('#end_date_time').attr('data-exp', '1');
        $('#end_date_time').attr('disabled', 'disabled');
        $(".expirydate").hide();
        $("#end_date_time").val(null);
        $('#end_date_time').val('');
        $('.expirydate').next('span.help-block').html('');
        $('.expirydate').parent('.form-group').removeClass('has-error');
    } else {
        $('.expdatelabel').addClass('no_expiry');
        $('.expiry_lbl').text('No Expiry');
        $('#end_date_time').attr('data-exp', '0');
        $('#end_date_time').removeAttr('disabled');
        $(".expirydate").show();
        if ($('#end_date_time').attr('data-newvalue').length > 0) {
            $("#end_date_time").val($('#end_date_time').attr('data-newvalue'));
        } else {
            $("#end_date_time").val(oldVal);
        }
    }
});
$('#chrDisplayVideo').click(function() {
    var chrsection = $(this).prop("checked");
    if (chrsection == true) {
        $("#VideoLinkTEXT").css("display", "block");
        $("#txtshortdesc").css("display", "none");
        $("#DisplayLink").css("display", "none");
        // $("#DisplayOrder").css("display", "none");
        $("#linkTEXT").css("display", "none");
        $("#button_title").css("display", "none");
    } else {
        $("#VideoLinkTEXT").css("display", "none");
        $("#txtshortdesc").css("display", "block");
        $("#DisplayLink").css("display", "block");
        // $("#DisplayOrder").css("display", "block");
        $("#linkTEXT").css("display", "block");
        $("#button_title").css("display", "block");
        document.getElementById('videolink').value = "";
    }
});
jQuery(document).ready(function() {
    var chrsection = $('#chrDisplayVideo').prop('checked');
    if (chrsection == true) {
        $("#VideoLinkTEXT").css("display", "block");
        $("#txtshortdesc").css("display", "none");
        $("#DisplayLink").css("display", "none");
        $("#linkTEXT").css("display", "none");
        $("#button_title").css("display", "none");
    } else {
        $("#VideoLinkTEXT").css("display", "none");
        $("#txtshortdesc").css("display", "block");
        $("#DisplayLink").css("display", "block");
        $("#").css("display", "block");
        document.getElementById('videolink').value = "";
    }
});