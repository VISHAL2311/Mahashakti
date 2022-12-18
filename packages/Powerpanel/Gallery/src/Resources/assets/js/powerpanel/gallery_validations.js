/**
 * This method validates FAQs form fields
 * since   2016-12-24
 * author  NetQuick
 */
var Validate = function () {
    var handlePhotoGallery = function () {
        $("#frmGallery").validate({
            errorElement: 'span',
            errorClass: 'help-block',
            ignore: [],
            rules: {
                order: {
                    required: true,
                    minStrict: true,
                    number: true,
                    noSpace: true,
                    xssProtection: true,
                    no_url: true
                },
                img_id: "required",
            },
            messages: {
                title: {required: Lang.get('validation.required', {attribute: Lang.get('template.title')})},
                order: {required: Lang.get('validation.required', {attribute: Lang.get('template.displayorder')})},
                img_id: Lang.get('validation.required', {attribute: Lang.get('template.image')}),
            },
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else if (element.hasClass('select2')) {
                    error.insertAfter(element.next('span'));
                } else {
                    error.insertAfter(element);
                }
            },
            invalidHandler: function (event, validator) {
                var errors = validator.numberOfInvalids();
                if (errors) {
                    $.loader.close(true);
                }
                $('.alert-danger', $('#frmGallery')).show();
            },
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            submitHandler: function (form) {
                $('body').loader(loaderConfig);
                form.submit();
                return false;
            }
        });
        $('#frmGallery input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#frmGallery').validate().form()) {
                    $('#frmGallery').submit();
                }
                return false;
            }
        });
    }
    return {
        init: function () {
            handlePhotoGallery();
        }
    };
}();
jQuery(document).ready(function () {
    Validate.init();
    jQuery.validator.addMethod("noSpace", function (value, element) {
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
jQuery.validator.addMethod("phoneFormat", function (value, element) {
    return this.optional(element) || /((\(\d{3}\) ?)|(\d{3}-))?\d{3}-\d{4}/.test(value);
}, 'Please enter a valid phone number.');
jQuery.validator.addMethod("noSpace", function (value, element) {
    if (value.trim().length <= 0) {
        return false;
    } else {
        return true;
    }
}, "This field is required");
jQuery.validator.addMethod("minStrict", function (value, element) {
    if (value > 0) {
        return true;
    } else {
        return false;
    }
}, 'Display order must be a number higher than zero');
$('input[type=text]').change(function () {
    var input = $(this).val();
    var trim_input = input.trim();
    if (trim_input) {
        $(this).val(trim_input);
        return true;
    }
});

jQuery.validator.addMethod("daterange", function (value, element) {
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

$('.fromButton').click(function () {
    $('#start_date_time').datetimepicker('show');
});
$('.toButton').click(function () {
    $('#end_date_time').datetimepicker('show');
});

$(document).on("change", '#end_date_time', function () {
    $(this).attr('data-newvalue', $(this).val());
});

$('#noexpiry').click(function () {
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
            $("#end_date_time").val('');
        }
    }
});
$(window).on('hashchange', function () {
    if (window.location.hash) {
        var page = window.location.hash.replace('#', '');
        if (page == Number.NaN || page <= 0) {
            return false;
        } else {
            getPosts(page);
        }
    }
});
$(document).ready(function () {


    if (window.location.hash) {
        var page = window.location.hash.replace('#', '');
        if (page == Number.NaN || page <= 0) {
            return false;
        } else {
            getPosts(page);
        }
    }
    $(document).on('click', '.pagination a', function (e) {
        var page = $(this).attr('href').split('page=')[1];
        window.location.hash = page;
        e.preventDefault();
    });
});

function getPosts(page) {
    $.ajax({
        url: '?page=' + page,
        dataType: 'json',
    }).done(function (data) {
        $('.posts').html(data);
        location.hash = page;
    }).fail(function () {
        alert('Posts could not be loaded.');
    });
}

function update_data(id) {
    var title = $('#title_' + id).val();
    $a = title;
    var d_o = $('#display_order_' + id).val();
    $d_o = d_o;
    //if ( $a == "" || $d_o == "" )
    if ($a == "") {
        if ($a == "") {
            alert('Title field is required.');
            var aa = $('.tt1_' + id).val();
        }
    } else {
        var aa = title;
        var title = $('#title_' + id).val();
        var order = $('#display_order_' + id).val();
        var hidden_order = $('#display_order_hidden_' + id).val();
        var imgId = $('.image_' + id).val();
        $('.message_loader').show();
        $.ajax({
            type: "POST",
            cache: true,
            url: window.site_url + '/powerpanel/gallery/update',
            data: {
                'title': title,
                'id': id,
                'order': order,
                'hidden_order': hidden_order,
                'imgId': imgId,
            },
            success: function (data)
            {
                $('.posts_my').html(data);
                $('.message_loader').hide();
                if (window.location.hash) {
                    var page = window.location.hash.replace('#', '');
                    if (page == Number.NaN || page <= 0) {
                        return false;
                    } else {
                        getPosts(page);
                    }
                }
                toastr.success('Record updated successfully.');
            },
            error: function (xhr, ajaxOptions, thrownError) {

            },
            async: true
        });
    }
}
function update_status(id) {
    var status = $('.status_' + id).attr('data-status');
    $.ajax({
        type: "POST",
        cache: true,
        url: window.site_url + '/powerpanel/gallery/update_status',
        data: {
            'id': id,
            'status': status
        },
        success: function (data)
        {
            var response = $.parseJSON(data);
            if (response.publish) {
                $('.status_' + id).attr('data-status', 'Y');
                $('.status_' + id + ' i').attr('class', 'fa fa-eye');
                $('.status_' + id).attr('title', 'Publish');
                toastr.success(response.publish);
            } else {
                $('.status_' + id).attr('data-status', 'N');
                $('.status_' + id + ' i').attr('class', 'fa fa-eye-slash');
                $('.status_' + id).attr('title', 'Unpublish');
                toastr.success(response.unpublish);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
        },
        async: true
    });
}
function remove(id) {
    var response = confirm("Are you sure you want to delete this image?");
    if (response)
    {
         var order = $('#display_order_' + id).val();
        $('.message_loader').show();
        $.ajax({
            type: "POST",
            cache: true,
            url: window.site_url + '/powerpanel/gallery/DeleteRecord',
            data: {
                'ids': id,
                'order': order
            },
            success: function (data) {
                $('.posts_my').html(data);
                $('.message_loader').hide();
                if (window.location.hash) {
                    var page = window.location.hash.replace('#', '');
                    if (page == Number.NaN || page <= 0) {
                        return false;
                    } else {
                        getPosts(page);
                    }
                }
                toastr.success('Record Delete successfully.');
                
            },
            error: function (xhr, ajaxOptions, thrownError) {

            },
            async: true
        });
    } else {
        return false;
    }


}