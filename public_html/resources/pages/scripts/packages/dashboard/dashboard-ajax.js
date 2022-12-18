$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
    $(".cmsPages").click(function () {
        var cmspage_id = this.id;
        $.ajax({
            url: site_url + '/powerpanel/dashboard/ajax',
            data: {type: 'cms', id: cmspage_id},
            type: "POST",
            dataType: "json",
            success: function (data) {
                var html = '';
                if (data != null && data != '') {
                    html += '<div class="modal-dialog">';
                    html += '<div class="modal-vertical">';
                    html += '<div class="modal-content">';
                    html += '<div class="modal-header">';
                    html += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>';
                    html += '<h3 class="modal-title">' + data.varTitle + '</h3>';
                    html += '</div>';
                    html += '<div class="modal-body">';
                    html += '<p>' + data.varTitle + '</p>';
                    html += '<p>' + data.txtDescription + '</p>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    $('.detailsCmsPage').html(html);
                    $('.detailsCmsPage').modal('show');
                }
            },
            error: function () {
                console.log('error!');
            }
        });
    });
    $(".contactUsLead").click(function ()
    {
        var contactuslead_id = this.id;
        $.ajax({
            url: site_url + '/powerpanel/dashboard/ajax',
            data: {type: 'contactuslead', id: contactuslead_id},
            type: "POST",
            dataType: "json",
            success: function (data) {
                var html = '';
                if (data != null && data != '') {
                    html += '<div class="modal-dialog">';
                    html += '<div class="modal-vertical">';
                    html += '<div class="modal-content">';
                    html += '<div class="modal-header">';
                    html += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>';
                    html += '<h3 class="modal-title">' + data.varName + '</h3>';
                    html += '</div>';
                    html += '<div class="modal-body">';
                    html += '<p><strong>Email:</strong> ' + data.varEmail + '</p>';
                    if (data.varPhoneNo == null || data.varPhoneNo == '') {
                        html += '';
                    } else {
                        html += '<p><strong>Phone:</strong> ' + data.varPhoneNo + '</p>';
                    }
                    if (data.txtUserMessage == null || data.txtUserMessage == '') {
                        html += '';
                    } else {
                        html += '<p><strong>Message:</strong> ' + data.txtUserMessage + '</p>';
                    }
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    $('.detailsContactUsLead').html(html);
                    $('.detailsContactUsLead').modal('show');
                }
            },
            error: function () {
                console.log('error!');
            }
        });
    });
    $(".getaEstimateLead").click(function ()
    {
        var getaestimateleads_id = this.id;
        $.ajax({
            url: site_url + '/powerpanel/dashboard/ajax',
            data: {type: 'getaestimatelead', id: getaestimateleads_id},
            type: "POST",
            dataType: "json",
            success: function (data) {
                var html = '';
                if (data != null && data != '') {
                    html += '<div class="modal-dialog">';
                    html += '<div class="modal-vertical">';
                    html += '<div class="modal-content">';
                    html += '<div class="modal-header">';
                    html += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>';
                    html += '<h3 class="modal-title">' + data.varName + '</h3>';
                    html += '</div>';
                    html += '<div class="modal-body">';
                    html += '<p><strong>Email Address:</strong> ' + data.varEmail + '</p>';
                    if (data.varPhoneNo == null || data.varPhoneNo == '') {
                        html += '';
                    } else {
                        html += '<p><strong>Phone:</strong> ' + data.varPhoneNo + '</p>';
                    }
                    if (data.txtUserMessage == null || data.txtUserMessage == '') {
                        html += '';
                    } else {
                        html += '<p><strong>Message:</strong> ' + data.txtUserMessage + '</p>';
                    }
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    $('.detailsGetaEstimateLead').html(html);
                    $('.detailsGetaEstimateLead').modal('show');
                }
            },
            error: function () {
                console.log('error!');
            }
        });
    });
    $(".serviceInquiryLead").click(function ()
    {
        var serviceinquirylead_id = this.id;
        $.ajax({
            url: site_url + '/powerpanel/dashboard/ajax',
            data: {type: 'serviceinquirylead', id: serviceinquirylead_id},
            type: "POST",
            dataType: "json",
            success: function (data) {
                var html = '';
                if (data != null && data != '') {
                    html += '<div class="modal-dialog">';
                    html += '<div class="modal-vertical">';
                    html += '<div class="modal-content">';
                    html += '<div class="modal-header">';
                    html += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>';
                    html += '<h3 class="modal-title">' + data.varName + '</h3>';
                    html += '</div>';
                    html += '<div class="modal-body">';
                    html += '<p><strong>Email Address:</strong> ' + data.varEmail + '</p>';
                    if (data.varPhoneNo == null || data.varPhoneNo == '') {
                        html += '';
                    } else {
                        html += '<p><strong>Phone:</strong> ' + data.varPhoneNo + '</p>';
                    }
                    if (data.txtUserMessage == null || data.txtUserMessage == '') {
                        html += '';
                    } else {
                        html += '<p><strong>Message:</strong> ' + data.txtUserMessage + '</p>';
                    }
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    $('.detailsServiceInquiryLead').html(html);
                    $('.detailsServiceInquiryLead').modal('show');
                }
            },
            error: function () {
                console.log('error!');
            }
        });
    });
    $(".boatInquiryLead").click(function ()
    {
        var boatinquirylead_id = this.id;
        $.ajax({
            url: site_url + '/powerpanel/dashboard/ajax',
            data: {type: 'boatinquirylead', id: boatinquirylead_id},
            type: "POST",
            dataType: "json",
            success: function (data) {
                var html = '';
                if (data != null && data != '') {
                    html += '<div class="modal-dialog">';
                    html += '<div class="modal-vertical">';
                    html += '<div class="modal-content">';
                    html += '<div class="modal-header">';
                    html += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>';
                    html += '<h3 class="modal-title">' + data.varName + '</h3>';
                    html += '</div>';
                    html += '<div class="modal-body">';
                    html += '<p><strong>Email Address:</strong> ' + data.varEmail + '</p>';
                    if (data.varPhoneNo == null || data.varPhoneNo == '') {
                        html += '';
                    } else {
                        html += '<p><strong>Phone:</strong> ' + data.varPhoneNo + '</p>';
                    }
                    if (data.txtUserMessage == null || data.txtUserMessage == '') {
                        html += '';
                    } else {
                        html += '<p><strong>Message:</strong> ' + data.txtUserMessage + '</p>';
                    }
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    $('.detailsBoatInquiryLead').html(html);
                    $('.detailsBoatInquiryLead').modal('show');
                }
            },
            error: function () {
                console.log('error!');
            }
        });
    });
    $(".dataRemovalLead").click(function ()
    {
        var dataremovallead_id = this.id;
        $.ajax({
            url: site_url + '/powerpanel/dashboard/ajax',
            data: {type: 'dataremovallead', id: dataremovallead_id},
            type: "POST",
            dataType: "json",
            success: function (data) {
                var html = '';
                if (data != null && data != '') {
                    html += '<div class="modal-dialog">';
                    html += '<div class="modal-vertical">';
                    html += '<div class="modal-content">';
                    html += '<div class="modal-header">';
                    html += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>';
                    html += '<h3 class="modal-title">' + data.varName + '</h3>';
                    html += '</div>';
                    html += '<div class="modal-body">';
                    if (data.varReason == null || data.varReason == '') {
                        html += '';
                    } else {
                        html += '<p><strong>Resoan for removal :</strong> ' + data.varReason + '</p>';
                    }
                    if (data.varRequeststatus == null || data.varRequeststatus == '') {
                        html += '';
                    } else {
                        html += '<p><strong>Request status :</strong> ' + data.varRequeststatus + '</p>';
                    }
                    if (data.countmessage == null || data.countmessage == '') {
                        html += '';
                    } else {
                        html += '<p><strong>Record Location :</strong> ' + data.countmessage + '</p>';
                    }
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    $('.detailsDataremovalLead').html(html);
                    $('.detailsDataremovalLead').modal('show');
                }
            },
            error: function () {
                console.log('error!');
            }
        });
    });
    $(".FaqRecord").click(function () {
        var faq_id = this.id;
        $.ajax({
            url: site_url + '/powerpanel/dashboard/ajax',
            data: {type: 'faq', id: faq_id},
            type: "POST",
            dataType: "json",
            success: function (data) {
                var html = '';
                if (data != null && data != '') {
                    html += '<div class="modal-dialog">';
                    html += '<div class="modal-vertical">';
                    html += '<div class="modal-content">';
                    html += '<div class="modal-header">';
                    html += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>';
                    html += '<h3 class="modal-title">FAQ</h3>';
                    html += '</div>';
                    html += '<div class="modal-body">';
                    html += '<p>' + data.question + '</p>';
                    html += '<p>' + data.answer + '</p>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    $('.FAQDetails').html(html);
                    $('.FAQDetails').modal('show');
                }
            },
            error: function () {
                console.log('error!');
            }
        });
    });
    $(".BlogRecord").click(function () {
        var blog_id = this.id;
        $.ajax({
            url: site_url + '/powerpanel/dashboard/ajax',
            data: {type: 'blog', blog_alias: blog_id},
            type: "POST",
            dataType: "json",
            success: function (data) {
                var html = '';
                if (data != null && data != '') {
                    html += '<div class="modal-dialog">';
                    html += '<div class="modal-vertical">';
                    html += '<div class="modal-content">';
                    html += '<div class="modal-header">';
                    html += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>';
                    html += '<h3 class="modal-title">Blog</h3>';
                    html += '</div>';
                    html += '<div class="modal-body">';
                    html += '<p>' + data.title + '</p>';
                    html += '<p>' + data.description + '</p>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    $('.BlogDetails').html(html);
                    $('.BlogDetails').modal('show');
                }
            },
            error: function () {
                console.log('error!');
            }
        });
    });
});
