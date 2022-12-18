"use strict";
var acceptfiles = "application/pdf,.doc,.docx,.ppt,.xls,.txt";
var ckOpen = false;
var MediaManager = (function() {
    return {
        open: function(id, recordId = false) {
            if (id != null) {
                $("#data_id").val(id);
                $("#recordId").val(recordId);
                MediaManager.init();
            } else {
                alert("Missing 1 parameter");
                return false;
            }
        },
        openVideoManager: function(id, recordId = false) {
            if (id != null) {
                $("#data_id").val(id);
                $("#videoRecordId").val(recordId);
            } else {
                openAlertDialogForVideo("Missing 1 parameter");
            }
        },
        openDocumentManager: function(id) {
            if (id != null) {
                $("#control_id").val(id);
            } else {
                openAlertDialogForDocument("Missing 1 parameter");
            }
        },
        openAudioManager: function(id) {
            if (id != null) {
                $('#audio_con_id').val(id);
            } else {
                openAlertDialogForAudio('Missing 1 parameter');
            }
        },
        setImageUploadTab: function() {
            $(".file_upload").show();
            $(".user_uploaded").hide();
            $(".image_html").hide();
            $(".trashed_images").hide();
            $(".recent_uploads").hide();
            $(".insert_from_url").hide();
            $(".image_details").hide();
            $(".image_cropper").hide();
            $(".tab_6_3 ul li a").removeClass("active");
            $("#upload_image").addClass("active");

            $('input[name="imageName"]').addClass("hide");
            $.ajax({
                type: "POST",
                cache: true,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                url: site_url + "/powerpanel/media/set_image_html",
                dataType: "html",
                success: function(data) {
                    $(".file_upload").html(data);
                    MediaManager.imageUploadEngine();
                },
                error: function(xhr, ajaxOptions, thrownError) {},
                async: true
            });
        },
        imageUploadEngine: function() {
            var maxfilesexceeded = false;
            var image_id = false;
            var success = false;
            $("#my-dropzone").dropzone({
                acceptedFiles: ".jpg,.jpeg,.png,.gif",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                url: site_url + "/powerpanel/media/upload_image",
                maxFiles: 15, // Number of files at a time
                maxFilesize: 15, //in MB
                clickable: true,
                maxfilesexceeded: function(file) {
                    maxfilesexceeded = true;
                    return false;
                },
                success: function(response) {
                    //App.initSlimScroll('.scroller');
                    var result = JSON.parse(response.xhr.response);
                    image_id = result.success;
                    if (response.status == "success") {
                        if (result.error) {
                            success = false;
                        } else {
                            success = true;
                        }
                    }
                },
                queuecomplete: function(file) {
                    if (success) {

                        if (maxfilesexceeded) {
                            MediaManager.showToaster('error', 'Only 15 images are uploaded others are not uploaded');
                        } else {
                            MediaManager.showToaster('success', 'Images uploaded successfully');
                            MediaManager.setMyUploadTab(window.user_id, image_id);
                        }

                    } else {
                        MediaManager.showToaster('error', "You can't upload this type of file");
                    }
                },
                removedfile: function(file) {

                    var _ref; // Remove file on clicking the 'Remove file' button
                    return (_ref = file.previewElement) != null ?
                        _ref.parentNode.removeChild(file.previewElement) :
                        void 0;
                }
            });
        },
        setInsertImageFromUrlTab: function() {
            $(".file_upload").hide();
            $(".image_html").hide();
            $(".user_uploaded").hide();
            $(".trashed_images").hide();
            $(".recent_uploads").hide();
            var html =
                '<div class="title_section"><h2>Insert from Url</h2></div>\n\
                                              <div class="portlet light">\n\
                                              <div class="row">\n\
                                                                              <div class="col-md-12">\n\
                                                                              <div class="form-group form-md-line-input form-md-floating-label has-info">\n\
                                                                                                              <input type="text" class="form-control input-lg image_url" id="form_control_1">\n\
                                                                                                              <span class="help-block thrownError" style="color:red"></span>\n\
                                                                                                              <label for="form_control_1">Enter Image URL</label>\n\
                                                                               </div>\n\
                                                                                                              <a href="javascript:void(0);" onclick="MediaManager.insertImageFromUrl()" class="btn btn-green-drake">Upload Image</a>\n\
                                              </div>\n\
                                              <br/>\n\
                                              <div class="uploaded_image"></div>\n\
                                              </div>\n\
                                              </div>\n\
                                              </div>';
            $(".insert_from_url").show();
            $(".insert_from_url").html(html);

        },
        setMyUploadTab: function(userid, image_id = false) {
            $(".tab_6_3 ul li a").removeClass("active");
            $("#user_uploaded_image").addClass("active");
            $(".loader").show();
            var image_id_selected = $('input[name="img_id"]').val();
            $.ajax({
                type: "POST",
                cache: true,
                url: site_url + "/powerpanel/media/user_uploaded_image",
                dataType: "json",
                data: {
                    userid: userid,
                    selected: image_id_selected
                },
                success: function(data) {
                    $(".loader").hide();
                    var lastPopedPopover;
                    $(".popovers").popover();
                    // close last displayed popover
                    $(document).on("click.bs.popover.data-api", function(e) {
                        if (lastPopedPopover) {
                            lastPopedPopover.popover("hide");
                        }
                    });
                    $(".file_upload").hide();
                    $(".image_html").hide();
                    $(".insert_from_url").hide();
                    $(".trashed_images").hide();
                    $(".recent_uploads").hide();
                    $(".tab_6_4").show();
                    $(".image_details").hide();
                    $(".image_cropper").hide();

                    $(".user_uploaded").show();
                    $(".user_uploaded").html(data.Image_html);
                    var multiple_selection = $(".multiple-selection").data("multiple");
                    if (multiple_selection == false || multiple_selection == undefined) {
                        $("#note").text("You can insert only one image.");
                    } else {
                        $("#note").text(
                            'Please select the image and click on "Insert Media" button to proceed.'
                        );
                    }
                    if (image_id != false) {
                        var imgIDs = image_id;
                    } else {
                         var data_id = $('#data_id').val();
                        var $parentImgDiv = $('#' + data_id).parents('.fileinput.fileinput-new');
                        var imgIDs = $parentImgDiv.find("input[name^='img_id']").val();
                    }
                    MediaManager.selectImage(imgIDs);
                    $('input[name="imageName"]').removeClass("hide");
                    $('input[name="imageName"]').on('keyup', function() {
                        if ($(this).val().length % 3 == 0) {
                            var imageName = $(this).val();
                            MediaManager.searchByImageName(imageName);
                        }
                    });
                },
                error: function(xhr, ajaxOptions, thrownError) {},
                async: true
            });
        },
        getMoreImages: function(user_id) {
            var track_page = parseInt($("#page").val()) + 1;
            $(".loader").show();
            $.ajax({
                type: "POST",
                cache: true,
                url: site_url + "/powerpanel/media/load_more_images/" + user_id,
                dataType: "json",
                data: {
                    page: track_page,
                    imageName: $('input[name="imageName"]').val()
                },
                success: function(data) {
                    $(".loader").hide();
                    var response = data;
                    $("#append_user_image").append(response.image_box);
                    $("#page").val(track_page);
                    if (response.image_box == "") {
                        $("#load_more_images").css("display", "none");
                        MediaManager.showToaster('error', "No More Images are available");
                    }
                    var imgIDs = $('input[name="img_id"]').val();
                    MediaManager.selectImage(imgIDs);
                },
                error: function(xhr, ajaxOptions, thrownError) {},
                async: true
            });
        },
        searchByImageName: function(imageName = false, image_id = false) {
            $(".loader").show();
            $.ajax({
                type: "POST",
                cache: true,
                url: site_url + "/powerpanel/media/user_uploaded_image",
                dataType: "JSON",
                data: {
                    userid: window.user_id,
                    imageName: imageName
                },
                success: function(data) {
                    //  alert(data.Image_html);
                    $(".loader").hide();
                    var lastPopedPopover;
                    $(".popovers").popover();
                    // close last displayed popover
                    $(document).on("click.bs.popover.data-api", function(e) {
                        if (lastPopedPopover) {
                            lastPopedPopover.popover("hide");
                        }
                    });
                    $(".file_upload").hide();
                    $(".image_html").hide();
                    $(".insert_from_url").hide();
                    $(".trashed_images").hide();
                    $(".recent_uploads").hide();
                    $(".tab_6_4").show();
                    $(".user_uploaded").show();
                    $(".user_uploaded").html(data.Image_html);
                    var multiple_selection = $(".multiple-selection").data("multiple");
                    if (multiple_selection == false || multiple_selection == undefined) {
                        $("#note").text("You can insert only one image.");
                    } else {
                        $("#note").text(
                            'Please select the image and click on "Insert Media" button to proceed.'
                        );
                    }
                    if (image_id != false) {
                        var imgIDs = image_id;
                    } else {
                        var imgIDs = $('input[name="img_id"]').val();
                    }
                    MediaManager.selectImage(imgIDs);
                },
                error: function(xhr, ajaxOptions, thrownError) {},
                async: true
            });
        },
        searchByDocName: function(docName = false, doc_id = false) {
            $(".loader").show();
            $.ajax({
                type: "POST",
                cache: true,
                url: site_url + "/powerpanel/media/user_uploaded_docs",
                dataType: "HTML",
                data: {
                    userid: window.user_id,
                    docName: docName
                },
                success: function(data) {
                    $(".loader").hide();
                    var lastPopedPopover;
                    $(".popovers").popover();
                    // close last displayed popover
                    $(document).on("click.bs.popover.data-api", function(e) {
                        if (lastPopedPopover) {
                            lastPopedPopover.popover("hide");
                        }
                    });
                    $(".docs_upload").hide();
                    $(".docs_html").hide();
                    $(".trashed_docs").hide();
                    $(".tab_6_4").show();
                    $(".user_uploaded_docs").show();
                    $(".user_uploaded_docs").html(data);
                    var multiple_selection = $(".document-multiple-selection").data("multiple");
                    if (multiple_selection == false || multiple_selection == undefined) {
                        $("#note").text("You can insert only one document.");
                    } else {
                        $("#note").text(
                            'Please select the document(s) and click on "Insert Document(s)" button to proceed.'
                        );
                    }
                    if (doc_id != false) {
                        var docIDs = doc_id;
                    } else {
                        var docIDs = $('input[name="doc_id"]').val();
                    }
                    MediaManager.selectDocument(docIDs);
                },
                error: function(xhr, ajaxOptions, thrownError) {},
                async: true
            });
        },
        searchByAudioName: function(AudioName = false, aud_id = false) {
            $('.loader').show();
            $.ajax({
                type: "POST",
                cache: true,
                url: site_url + '/powerpanel/media/user_uploaded_audio',
                dataType: "HTML",
                data: {
                    'userid': window.user_id,
                    'AudioName': AudioName
                },
                success: function(data) {
                    $('.loader').hide();
                    var lastPopedPopover;
                    $('.popovers').popover();
                    // close last displayed popover
                    $(document).on('click.bs.popover.data-api', function(e) {
                        if (lastPopedPopover) {
                            lastPopedPopover.popover('hide');
                        }
                    });
                    $('.auds_upload').hide();
                    $('.auds_html').hide();
                    $('.trashed_auds').hide();
                    $('.tab_6_4').show();
                    $('.user_uploaded_auds').show();
                    $('.user_uploaded_auds').html(data);
                    var multiple_selection = $('.multiple-selection').data('multiple');
                    if (multiple_selection == false || multiple_selection == undefined) {
                        $('#note').text('Please select the audio and click on "Insert Media" button to proceed. You can insert only one audio.');
                    } else {
                        $('#note').text('Please select the audio(s) and click on "Insert Media" button to proceed.');
                    }
                    if (aud_id != false) {
                        var audIDs = aud_id;
                    } else {
                        var audIDs = $('input[name="aud_id"]').val();
                    }
                    MediaManager.selectDocument(audIDs);
                },
                error: function(xhr, ajaxOptions, thrownError) {},
                async: true
            });
        },
        selectImage: function(image_id) {
            if (image_id != null && image_id != "" && image_id != false) 
            {
                var data_id = $("#data_id").val();
                var imgIDArr = [];
                imgIDArr = image_id.toString().split(",");
                if (imgIDArr.length > 1) {
                    //alert(imgIDArr.length);

                    var multiple_selection = $('.'+ data_id).find(".multiple-selection").data("multiple");
                    if (multiple_selection == true) {
                        $.each(imgIDArr, function(index, value) {
                            $("#media_" + value).addClass("img-box-active");
                            $("#media_" + value + " a i").addClass("icon-check icons");
                            var selected_image_count = $(".img-box-active").length;
                            var multiple_selection = $(".multiple-selection").data(
                                "multiple"
                            );
                            if (
                                selected_image_count > 1 &&
                                (multiple_selection == false || multiple_selection == undefined)
                            ) {
                                $("#insert_image").hide();
                                //$("#insert_image").attr("disabled", true);
                            } else {
                                $("#insert_image").show();
                            }
                        });
                    }
                } else {
                    if ($("#media_" + image_id + "").hasClass("img-box-active")) {
                        $("#media_" + image_id).removeClass("img-box-active");
                        $("#media_" + image_id + " a i").removeClass("icon-check icons");
                    } else {
                        $("#media_" + image_id).addClass("img-box-active");
                        $("#media_" + image_id + " a i").addClass("icon-check icons");
                    }
                    var selected_image_count = $(".img-box-active").length;
                    var multiple_selection = $('.'+ data_id).find(".multiple-selection").data("multiple");
                    if (
                        selected_image_count > 1 &&
                        (multiple_selection == undefined || multiple_selection == false)
                    ) {
                        $("#insert_image").hide();
                        $("#insert_image_hide").show();
                        // $("#insert_image").attr("disabled", true);
                    } else {
                        $("#insert_image_hide").hide();
                        $("#insert_image").show();
                    }
                }
            }
        },
        selectVideo: function(video_id) {
            if (video_id != null && video_id != "" && video_id != false) {
                var vidIDArr = [];
                vidIDArr = video_id.toString().split(",");
                if (vidIDArr.length > 1) {
                    var multiple_selection = $(".multiple-selection").data("multiple");
                    if (multiple_selection == true) {
                        $.each(vidIDArr, function(index, value) {
                            $("#video_" + value).addClass("active_video");
                            $("#video_" + value + " a i").addClass("icon-check icons");
                            var selected_video_count = $(".active_video").length;
                            var multiple_selection = $(".multiple-selection").data(
                                "multiple"
                            );
                            if (
                                selected_video_count > 1 &&
                                (multiple_selection == false || multiple_selection == undefined)
                            ) {
                                $("#insert_video").hide();
                            } else {
                                $("#insert_video").show();
                            }
                        });
                    }
                } else {
                    if ($("#video_" + video_id + "").hasClass("active_video")) {
                        $("#video_" + video_id).removeClass("active_video");
                        $("#video_" + video_id + " a i").removeClass("icon-check icons");
                    } else {
                        $("#video_" + video_id).addClass("active_video");
                        $("#video_" + video_id + " a i").addClass("icon-check icons");
                    }
                    var selected_video_count = $(".active_video").length;
                    var multiple_selection = $(".multiple-selection").data("multiple");
                    if (selected_video_count > 1 && (multiple_selection == undefined || multiple_selection == false)) {
                        $("#insert_video").hide();
                        $("#insert_video_hide").show();
                    } else {
                        $("#insert_video").show();
                        $("#insert_video_hide").hide();
                    }
                    // var selected_video_count = $('.active_video').length;
                    // if (selected_video_count > 1) {
                    //          $('.user_uploaded_video #insert_video').hide();
                    // } else {
                    //          $('.user_uploaded_video #insert_video').show();
                    // }
                }
            }
        },
        selectRecentUploadImage: function(image_id = false) {
            if (image_id != null) {
                var imgIDArr = image_id.toString().split(",");
                if (imgIDArr.length > 1) {
                    var multiple_selection = $(".multiple-selection").data("multiple");
                    if (multiple_selection == true) {
                        $.each(imgIDArr, function(index, value) {
                            $("#recent_upload_images #media_" + value).addClass(
                                "img-box-active"
                            );
                            $("#recent_upload_images #media_" + value + " a i").addClass(
                                "icon-check icons"
                            );
                            var selected_image_count = $(
                                "#recent_upload_images .img-box-active"
                            ).length;
                            var multiple_selection = $(".multiple-selection").data(
                                "multiple"
                            );
                            if (
                                selected_image_count > 1 &&
                                (multiple_selection == false || multiple_selection == undefined)
                            ) {
                                $(".recent_uploads #insert_image").hide();
                            } else {
                                $(".recent_uploads #insert_image").show();
                            }
                        });
                    }
                } else {
                    if (
                        $("#recent_upload_images #media_" + image_id + "").hasClass(
                            "img-box-active"
                        )
                    ) {
                        $("#recent_upload_images #media_" + image_id).removeClass(
                            "img-box-active"
                        );
                        $("#recent_upload_images #media_" + image_id + " a i").removeClass(
                            "icon-check icons"
                        );
                    } else {
                        $("#recent_upload_images #media_" + image_id).addClass(
                            "img-box-active"
                        );
                        $("#recent_upload_images #media_" + image_id + " a i").addClass(
                            "icon-check icons"
                        );
                    }
                    var selected_image_count = $("#recent_upload_images .img-box-active").length;
                    var multiple_selection = $(".multiple-selection").data("multiple");
                    if (selected_image_count > 1 && (multiple_selection == false || multiple_selection == undefined)) {
                        $(".recent_uploads #insert_image").hide();
                        $(".recent_uploads #insert_image_hide").show();
                    } else {
                        $(".recent_uploads #insert_image").show();
                        $(".recent_uploads #insert_image_hide").hide();
                    }
                }
            }
        },
        selectDocument: function(doc_id) {
            if (doc_id != null && doc_id != "" && doc_id != false) {
                var docIDArr = [];
                docIDArr = doc_id.toString().split(",");

                if (docIDArr.length > 1) {
                    var multiple_selection = $(".document-multiple-selection").data("multiple");
                    if (multiple_selection == true) {
                        $.each(docIDArr, function(index, value) {
                            $("#document_" + value).addClass("document-box-active");
                            $("#document_" + value + " a i").addClass("icon-check icons");
                            var selected_image_count = $(".document-box-active").length;
                            var multiple_selection = $(".document-multiple-selection").data(
                                "multiple"
                            );
                            if (
                                selected_image_count > 1 &&
                                (multiple_selection == false || multiple_selection == undefined)
                            ) {
                                $("#insert_document").hide();
                            } else {
                                $("#insert_document").show();
                            }
                        });
                    }
                } else {
                    if ($("#document_" + doc_id + "").hasClass("document-box-active")) {
                        $("#document_" + doc_id).removeClass("document-box-active");
                        $("#document_" + doc_id + " a i").removeClass("icon-check icons");
                    } else {
                        $("#document_" + doc_id).addClass("document-box-active");
                        $("#document_" + doc_id + " a i").addClass("icon-check icons");
                    }
                    var selected_image_count = $(".document-box-active").length;
//                    var multiple_selection = $(".document-multiple-selection").data("multiple");
                    var multiple_selection = $('.document_manager').data('multiple');
                    if (ckOpen) {
                        multiple_selection = true;
                    }

//                    if (selected_image_count > 1 && (multiple_selection == undefined || multiple_selection == false)) {
//                        $("#insert_document").hide();
//                        $("#insert_document_hide").show();
//                    } else {
//                        $("#insert_document").show();
//                        $("#insert_document_hide").hide();
//                    }
                    if (selected_image_count > 1 && (multiple_selection == undefined || multiple_selection == false)) {
                    $('#insert_document').addClass('disabled');
                    } else {
                    $('#insert_document').removeClass('disabled');
                    }
                }
            }
        },
        selectAudio: function(aud_id) {
            if (aud_id != null && aud_id != '' && aud_id != false) {
                var audIDArr = [];
                audIDArr = aud_id.toString().split(',');
                if (audIDArr.length > 1) {
                    var multiple_selection = $('.multiple-selection').data('multiple');
                    if (multiple_selection == true) {
                        $.each(audIDArr, function(index, value) {
                            $('#audio_' + value).addClass('audio-box-active');
                            $('#audio_' + value + ' a i').addClass('icon-check icons');
                            var selected_image_count = $('.audio-box-active').length;
                            var multiple_selection = $('.multiple-selection').data('multiple');
                            if (selected_image_count > 1 && (multiple_selection == false || multiple_selection == undefined)) {
                                $('#insert_audio').hide();
                            } else {
                                $('#insert_audio').show();
                            }
                        });
                    }
                } else {
                    if ($('#audio_' + aud_id + '').hasClass('audio-box-active')) {
                        $('#audio_' + aud_id).removeClass('audio-box-active');
                        $('#audio_' + aud_id + ' a i').removeClass('icon-check icons');
                    } else {
                        $('#audio_' + aud_id).addClass('audio-box-active');
                        $('#audio_' + aud_id + ' a i').addClass('icon-check icons');
                    }
                    var selected_image_count = $('.audio-box-active').length;
                    var multiple_selection = $('.multiple-selection').data('multiple');
                    if (selected_image_count > 1 && (multiple_selection == undefined || multiple_selection == false)) {
                        $('#insert_audio').hide();
                    } else {
                        $('#insert_audio').show();
                    }
                }
            }
        },
        removeImage: function(image_id) {
            $(".loader").show();
            var response = confirm("Are you sure you want to delete this image?");
            if (response) {
                $.ajax({
                    type: "POST",
                    cache: true,
                    url: site_url + "/powerpanel/media/remove_image",
                    data: {
                        image_id: image_id
                    },
                    success: function(data) {
                        $(".loader").hide();
                        if (data) {
                            $("#media_" + image_id).remove();
                        }
                        var imgIDs = $('input[name="img_id"]').val();
                        MediaManager.selectImage(imgIDs);
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError);
                    },
                    async: true
                });
            } else {
                return false;
            }
        },
        insertMedia: function () {
             
            if ($('#photoDiv').length > 0){
                var image_id = $('.img-box-active').attr('id');
                    var image = $('.img-box-active img').attr('src');
                    var id = image_id.split('_');
                    var data_id = $('#data_id').val();
                    var recordID = $('#recordId').val();
                    var folder_id = $('.img-box-active').data('folder');

                    $('#' + data_id).val(id[1]);
                    $('.' + data_id + '_img').html('<img src="' + image + '" />');
                    /* for photo-gallery module  */
                    var image_source = $(".img-box-active #media_image_" + id[1]).data('image_big_source');
                    var image_name = $(".img-box-active #media_image_" + id[1]).data('image_title');
                    $('.image_' + recordID).val(id[1]);
                    $('.folder_' + recordID).val(folder_id);
                    $('.photo_gallery_' + recordID).html('<img src="' + image + '" />');
                    $('.image_iframe_' + recordID).attr('href', image_source);
                    $('.image_iframe_' + recordID).attr('title', image_name);
                    $('.image_gallery_change_' + recordID).attr('title', image_name);
                    /* for photo-gallery module */
                    $('#image_url').val(image);
                    $('#' + data_id).parent().find('.help-block').remove();

                    $('#' + data_id).parents('.fileinput.fileinput-new').find('.overflow_layer').css('display', 'block');
                    $('#' + data_id).parents('.fileinput.fileinput-new').find('.overflow_layer .removeimg').show();
 
            }
                  
            if ($('.contains_thumb').hasClass('img-box-active')) {
                var multiple_selection = $('.media_manager.multiple-selection').data('multiple');
//                alert(multiple_selection);
                /*if ($('.media_manager.pgbuilder-img').hasClass('single_image')) {
                 alert();
                 }*/
                if ($('.media_manager.multiple-selection').is('[data-multiple]') && multiple_selection == false) {
                    var imgIds = [];
                    var imgSRC = '';
                    imgSRC += '<div class="multi_image_list"><ul>';
                    $('.img-box-active').each(function (index) {
                        var image_id = $(this).attr('id');
                        var id = image_id.split('_');
                        var imageURL = $(this).find('img').attr('src');
                        imgIds.push(id[1]);
                        imgSRC += '<li id="' + id[1] + '">\n\
													<span>\n\
														<img src="' + imageURL + '" />\n\
														<a href="javascript:;" onclick="MediaManager.removeImageFromGallery(' + id[1] + ');" class="delect_image" data-dismiss="fileinput"><i class="fa fa-times"></i></a>\n\
													</span>\n\
												</li>';
                        $('#image_url').val(imageURL);
                    });
                    imgSRC += '<ul></div>';
                    var data_id = $('#data_id').val();
                    $('#' + data_id).val(imgIds.join(','));
                    $('#' + data_id + '_img').html(imgSRC);
                    $('#' + data_id).parent().find('.help-block').remove();
                } else if (multiple_selection == true) {
                    var imgIds = [];
                    var imgSRC = '';
                    imgSRC += '<div class="multi_image_list"><ul>';
                    $('.img-box-active').each(function (index) {
                        var image_id = $(this).attr('id');
                        var id = image_id.split('_');
                        var imageURL = $(this).find('img').attr('src');
                        imgIds.push(id[1]);
                        imgSRC += '<li id="' + id[1] + '">\n\
												<span>\n\
																<img src="' + imageURL + '" />\n\
																<a href="javascript:;" onclick="MediaManager.removeImageFromGallery(' + id[1] + ');" class="delect_image" data-dismiss="fileinput"><i class="fa fa-times"></i></a>\n\
												</span>\n\
											 </li>';
                        $('#image_url').val(imageURL);
                    });
                    imgSRC += '<ul></div>';
                    var data_id = $('#data_id').val();
                    $('#' + data_id).val(imgIds.join(','));
                    $('#' + data_id + '_img').html(imgSRC);
                    $('#' + data_id).parent().find('.help-block').remove();
                } else {
                    var image_id = $('.img-box-active').attr('id');
                    var image = $('.img-box-active img').attr('src');
                    var id = image_id.split('_');
                    var data_id = $('#data_id').val();
                    var recordID = $('#recordId').val();
                    var folder_id = $('.img-box-active').data('folder');

                    $('#' + data_id).val(id[1]);
                    $('.' + data_id + '_img').html('<img src="' + image + '" />');
                    /* for photo-gallery module  */
                    var image_source = $(".img-box-active #media_image_" + id[1]).data('image_big_source');
                    var image_name = $(".img-box-active #media_image_" + id[1]).data('image_title');
                    $('.image_' + recordID).val(id[1]);
                    $('.folder_' + recordID).val(folder_id);
                    $('.photo_gallery_' + recordID).html('<img src="' + image + '" />');
                    $('.image_iframe_' + recordID).attr('href', image_source);
                    $('.image_iframe_' + recordID).attr('title', image_name);
                    $('.image_gallery_change_' + recordID).attr('title', image_name);
                    /* for photo-gallery module */
                    $('#image_url').val(image);
                    $('#' + data_id).parent().find('.help-block').remove();

                    $('#' + data_id).parents('.fileinput.fileinput-new').find('.overflow_layer').css('display', 'block');
                    $('#' + data_id).parents('.fileinput.fileinput-new').find('.overflow_layer .removeimg').show();
                }
                /* for close portlet */
                var portlet = $('#gallary_component').closest(".portlet");
                if ($('body').hasClass('page-portlet-fullscreen')) {
                    $('body').removeClass('page-portlet-fullscreen');
                }
                if (portlet.hasClass('portlet-fullscreen')) {
                    portlet.removeClass('portlet-fullscreen');
                }
                portlet.find('.portlet-title .fullscreen').tooltip('destroy');
                portlet.find('.portlet-title > .tools > .reload').tooltip('destroy');
                portlet.find('.portlet-title > .tools > .remove').tooltip('destroy');
                portlet.find('.portlet-title > .tools > .config').tooltip('destroy');
                portlet.find('.portlet-title > .tools > .collapse, .portlet > .portlet-title > .tools > .expand').tooltip('destroy');
                portlet.hide();
                $("div").removeClass('modal-backdrop fade in');
                //$('.removeimg').show();
                //$('.image_thumb .overflow_layer').css('display', 'block');
            } else {
                
                openAlertDialogForImage('Please select the image and click on "Insert Media" button to proceed.');
            }
        },
        insertVideo: function() {
            if ($(".contains_thumb").hasClass("active_video")) {
                var multiple_selection = $(".multiple-selection").data("multiple");
                if (multiple_selection == true) {
                    // Multiplease video code
                    var imgIds = [];
                    var vidSRC = "";
                    vidSRC += '<div class="multi_image_list"><ul>';
                    $(".active_video").each(function(index) {
                        var video_id = $(this).attr("id");
                        var video_type = $(".active_video").data("video_type");
                        var video_source = $(".active_video").data("video_source");
                        var id = video_id.split("_");
                        var imageURL = $(this)
                            .find("img")
                            .attr("src");
                        //var video_thumb = $('.active_video img').attr('src');
                        imgIds.push(id[1]);
                        vidSRC +=
                            '<li id="' +
                            id[1] +
                            '">\n\
                    <span>\n\
                      <img src="' +
                            imageURL +
                            '" />\n\
                      <a href="javascript:;" onclick="MediaManager.removeVideoFromVideoManager(' +
                            id[1] +
                            ');" class="delect_image" data-dismiss="fileinput"><i class="fa fa-times"></i></a>\n\
                    </span>\n\
                  </li>';
                        $("#video_url").val(imageURL);
                    });
                    vidSRC += "<ul></div>";
                    var data_id = $("#data_id").val();
                    $("#" + data_id).val(imgIds.join(","));
                    $("#" + data_id + "_vid").html(vidSRC);

                } else {
                    var video_id = $(".active_video").attr("id");
                    var video_type = $(".active_video").data("video_type");
                    var video_source = $(".active_video").data("video_source");
                    var id = video_id.split("_");
                    var data_id = $("#data_id").val();
                    var recordID = $("#videoRecordId").val();
                    $("#" + data_id).val(id[1]);
                    var video_name = $("#" + video_id).data("video_name");
                    $("#video_name").show();
                    $("#video_name").val(video_name);
                   

                    /* for video-gallery module  */
                    var video_thumb = $(".active_video img").attr("src");
                    $("#video_url").val(video_thumb);
                     /*** Single vide **/
                    // $(".video-fileinpu > div").html(
                    //     '<img src="' + video_thumb + '" />'
                    // );
                    $("." + data_id + "_vid").html('<img src="' + video_thumb + '" />');


                    $(".video_" + recordID).val(id[1]);
                    $(".video_gallery_" + recordID).html(
                        '<img src="' + video_thumb + '" />'
                    );
                    if (video_type == "youtube") {
                        var youtubeLink =
                            "http://www.youtube.com/embed/" + video_source + "?autoplay=1";
                        $(".video_iframe_" + recordID).attr("href", youtubeLink);
                    } else {
                        $(".video_iframe_" + recordID).attr("href", video_source);
                    }
                    $(".video_iframe_" + recordID).attr("title", video_name);
                    $(".video_gallery_change_" + recordID).attr("title", video_name);

                    $("#" + data_id).parents(".fileinput.fileinput-new").find(".overflow_layer").css("display", "block");
                    $("#" + data_id).parents(".fileinput.fileinput-new").find(".overflow_layer .removeVideo").show();

                }
                /* for video-gallery module */
                /* for close portlet */
                var portlet = $("#video_component").closest(".portlet");
                if ($("body").hasClass("page-portlet-fullscreen")) {
                    $("body").removeClass("page-portlet-fullscreen");
                }
                if (portlet.hasClass("portlet-fullscreen")) {
                    portlet.removeClass("portlet-fullscreen");
                }
                portlet.find(".portlet-title .fullscreen").tooltip("destroy");
                portlet.find(".portlet-title > .tools > .reload").tooltip("destroy");
                portlet.find(".portlet-title > .tools > .remove").tooltip("destroy");
                portlet.find(".portlet-title > .tools > .config").tooltip("destroy");
                portlet
                    .find(
                        ".portlet-title > .tools > .collapse, .portlet > .portlet-title > .tools > .expand"
                    )
                    .tooltip("destroy");
                portlet.hide();
            } else {
                openAlertDialogForVideo('Please select the video and click on "Insert video(s)" button to proceed.');
            }
        },
//        insertDocument: function() {
//            if ($(".contains_thumb").hasClass("document-box-active")) {
//                var multiple_selection = $(".document-multiple-selection").data("multiple");
//                if (multiple_selection == true) {
//                    var docsIds = [];
//                    var docSRC = "";
//                    docSRC +=
//                        '<div class="multi_image_list" id="multi_document_list"><ul>';
//                    $(".document-box-active").each(function() {
//                        var doc_id = $(this).attr("id");
//                        var id = doc_id.split("_");
//                        var documentURL = $(this)
//                            .find("img")
//                            .attr("src");
//                        docsIds.push(id[1]);
//                        docSRC +=
//                            '<li id="doc_' + id[1] + '">\n\
//                              <span>\n\
//                              <img src="' + documentURL + '" />\n\
//                              <a href="javascript:;" onclick="MediaManager.removeDocumentFromGallery(' + id[1] + ');" class="delect_image" data-dismiss="fileinput"><i class="fa fa-times"></i></a>\n\
//                              </span>\n\
//                              </li>';
//                    });
//                    docSRC += "<ul></div>";
//                    var data_id = $("#control_id").val();
//                    $("#" + data_id).val(docsIds.join(","));
//                    $("#" + data_id + "_documents").html(docSRC);
//                } else {
//                    var doc_id = $(".document-box-active").attr("id");
//                    var image = $(".document-box-active img").attr("src");
//                    var id = doc_id.split("_");
//                    var data_id = $("#control_id").val();
//
//                    var document_name = $("#" + doc_id).data("document_name");
//                    $("#document_name").show();
//                    $("#document_name").val(document_name);
//
//                    $("#" + data_id).val(id[1]);
//                    $("." + data_id + "_documents").html('<img src="' + image + '" />');
//                    $("#" + data_id).parents(".fileinput.fileinput-new").find(".overflow_layer").css("display", "block");
//                }
//                /* for close portlet */
//                var portlet = $("#document_component").closest(".portlet");
//                if ($("body").hasClass("page-portlet-fullscreen")) {
//                    $("body").removeClass("page-portlet-fullscreen");
//                }
//                if (portlet.hasClass("portlet-fullscreen")) {
//                    portlet.removeClass("portlet-fullscreen");
//                }
//                portlet.find(".portlet-title .fullscreen").tooltip("destroy");
//                portlet.find(".portlet-title > .tools > .reload").tooltip("destroy");
//                portlet.find(".portlet-title > .tools > .remove").tooltip("destroy");
//                portlet.find(".portlet-title > .tools > .config").tooltip("destroy");
//                portlet
//                    .find(
//                        ".portlet-title > .tools > .collapse, .portlet > .portlet-title > .tools > .expand"
//                    )
//                    .tooltip("destroy");
//                portlet.hide();
//            } else {
//                openAlertDialogForDocument(
//                    'Please select the document and click on "Insert Document(s)" button to proceed.'
//                );
//            }
//        },
insertDocument: function () {
            if ($('.user_uploaded_docs .contains_thumb').hasClass('document-box-active')) {
                var multiple_selection = $('.document_manager').data('multiple');
                if (ckOpen) {
                    multiple_selection = true;
                }
                if (multiple_selection) {
                    var doc_ids = [];
                    var doc_path = [];
                    var doc_nm = [];
                    var docSRC = '';
                    var data_id = $('#control_id').val();

                    if (data_id == 'Composer_doc') {
                        docSRC += '<div class="multi_image_list"><ul class="dochtml">';
                    } else {
                        docSRC += '<div class="multi_image_list" id="multi_document_list"><ul class="dochtml">';
                    }

                    var alreadySelectedDocIds = $('#' + data_id).val();
                    if (data_id == 'Composer_doc') {
                        var alreadySelectedDocIds = $('#frmSectionOnlyDocument').find('.imgip1').val();
                        if (typeof alreadySelectedDocIds !== "undefined" && alreadySelectedDocIds != "") {
                            var alreadySelectedDocArray = alreadySelectedDocIds.split(',');
                        }
                    } else {
                        if (typeof alreadySelectedDocIds !== "undefined" && alreadySelectedDocIds != "") {
                            var alreadySelectedDocArray = alreadySelectedDocIds.split(',');
                        }
                    }


                    if (alreadySelectedDocIds != "" && !ckOpen) {
                        $.each(alreadySelectedDocArray, function (index, value) {
                            doc_ids.push(value);
                            if (data_id == 'Composer_doc') {
                                docSRC += '<li id="doc_' + value + '">\n\
									<span>\n\
													<img src="' + CDN_PATH + "assets/images/document_icon.png" + '" />\n\
													<a href="javascript:;" onclick="MediaManager.removeDocumentFromComposerBlock(' + value + ');" class="delect_image" data-dismiss="fileinput"><i class="fa fa-times"></i></a>\n\
									</span>\n\
								</li>';
                            } else {
                                var image = $('.document-box-active#document_' + value + ' img').attr('src');
                               if(image != undefined){
                                   var image_url  = image;
                               }else{
                                   var image_url = CDN_PATH + "assets/images/document_icon.png";
                               }
                                docSRC += '<li id="doc_' + value + '">\n\
									<span>\n\
													<img src="' + image_url + '" />\n\
													<a href="javascript:;" onclick="MediaManager.removeDocumentFromGallery(' + value + ');" class="delect_image" data-dismiss="fileinput"><i class="fa fa-times"></i></a>\n\
									</span>\n\
								</li>';
                            }

                        });
                    }

                    $('.user_uploaded_docs .document-box-active').each(function () {
                         var recordID = $('#recordId').val();
                        var doc_id = $(this).attr('id');
                        var id = doc_id.split('_');
                        var documentURL = $(this).data('docurl');
//						var image = $('.document-box-active img').attr('src');
                        var image = $('.document-box-active#' + doc_id + ' img').attr('src');
                        var folder_id = $(this).data('folder');
                        $('.folder_1').val(folder_id);
                        if (data_id == 'Composer_doc') {
                            doc_ids.push(id[1]);
                            doc_path.push(documentURL);
                            doc_nm.push($(this).data('docnm'));
                            if (!ckOpen && $.inArray(id[1], alreadySelectedDocArray) == -1) {
                                docSRC += '<li id="doc_' + id[1] + '">\n\
													<span>\n\
																	<img src="' + CDN_PATH + 'assets/images/document_icon.png' + '" />\n\
																	<a href="javascript:;" onclick="MediaManager.removeDocumentFromComposerBlock(' + id[1] + ');" class="delect_image" data-dismiss="fileinput"><i class="fa fa-times"></i></a>\n\
													</span><label>' + $(this).data('docnm') + '.' + $(this).data('docext') + '</label>\n\
												</li>';
                            }

                        } else {
                            if (ckOpen || (!ckOpen && $.inArray(id[1], alreadySelectedDocArray) == -1)) {
                                doc_ids.push(id[1]);
                                doc_path.push(documentURL);
                                doc_nm.push($(this).data('docnm'));
                                docSRC += '<li id="doc_' + id[1] + '">\n\
												<span>\n\
														<img src="' + image + '" />\n\
														<a href="javascript:;" onclick="MediaManager.removeDocumentFromGallery(' + id[1] + ');" class="delect_image" data-dismiss="fileinput"><i class="fa fa-times"></i></a>\n\
												</span>\n\
								</li>';
                            }
                        }
                    });
                    docSRC += '</ul></div>';

                    if (data_id == 'Composer_doc') {
                        $('#frmSectionOnlyDocument').find('.image_1.item-data.imgip1').val(doc_ids.join(','));
                        //$('.image_1.item-data.imgip1').val(doc_ids.join(','));
                        var DOC_URL = site_url + "/powerpanel/media/ComposerDocData";
                        $.ajax({
                            type: 'POST',
                            url: DOC_URL,
                            data: 'id=' + doc_ids.join(',') + '',
                            success: function (html) {
                                $("#sectionOnlyDocument .dochtml").html(html);
                            }
                        });
                        //$('#frmSectionOnlyDocument #' + data_id + '_documents').html(docSRC);
                    } else {
                        $('#' + data_id).val(doc_ids.join(','));
                    }
                    $('#' + data_id + '_path').val(doc_path.join(','));
                    $('#' + data_id + '_name').val(doc_nm.join(','));
                    if (data_id != 'Composer_doc') {
                        $('#' + data_id + '_documents').html(docSRC);
                    }

                } else {
                    var recordID = $('#recordId').val();
                    var doc_id = $('.user_uploaded_docs .document-box-active').attr('id');
                    var doc_path = $('.user_uploaded_docs .document-box-active').data('docurl');
                    var doc_nm = $('.user_uploaded_docs .document-box-active').data('docnm');
//					var image = $('.document-box-active img').attr('src');
                    var image = $('.user_uploaded_docs .document-box-active#' + doc_id + ' img').attr('src');
                    var id = doc_id.split('_');
                    var data_id = $('#control_id').val();
                     var folder_id = $('.user_uploaded_docs .document-box-active').data('folder');

                    $('#' + data_id).val(id[1]);
                    $('#' + data_id + '_path').val(doc_path);
                    $('#' + data_id + '_name').val(doc_nm);
                    $('.photo_gallery_' + recordID).html('<img src="' + image + '" />');
                    $('#' + data_id).val(id[1]);
                    $('.image_' + recordID).val(id[1]);
                    $('.folder_' + recordID).val(folder_id);

                    var docSRC = '';
                    docSRC += '<div class="multi_image_list" id="multi_document_list"><ul>';
                    docSRC += '<li id="doc_' + id[1] + '">\n\
													<span>\n\
																	<img src="' + image + '" />\n\
																	<a href="javascript:;" onclick="MediaManager.removeDocumentFromGallery(' + id[1] + ');" class="delect_image" data-dismiss="fileinput"><i class="fa fa-times"></i></a>\n\
													</span>\n\
												</li>';
                    docSRC += '<ul></div>';
                    $('#' + data_id + '_documents').html(docSRC);



                }
                /* for close portlet */
                var portlet = $('#document_component').closest(".portlet");
                if ($('body').hasClass('page-portlet-fullscreen')) {
                    $('body').removeClass('page-portlet-fullscreen');
                }
                if (portlet.hasClass('portlet-fullscreen')) {
                    portlet.removeClass('portlet-fullscreen');
                }
                portlet.find('.portlet-title .fullscreen').tooltip('destroy');
                portlet.find('.portlet-title > .tools > .reload').tooltip('destroy');
                if (data_id != 'Composer_doc') {
                    portlet.find('.portlet-title > .tools > .remove').tooltip('destroy');
                }

                portlet.find('.portlet-title > .tools > .config').tooltip('destroy');
                portlet.find('.portlet-title > .tools > .collapse, .portlet > .portlet-title > .tools > .expand').tooltip('destroy');
                portlet.hide();
            } else {
                openAlertDialogForDocument('Please select the document and click on "Insert Document" button to proceed.');
            }
        },
        insertAudio: function() {
            if ($('.contains_thumb').hasClass('audio-box-active')) {
                var multiple_selection = $('.multiple-selection').data('multiple');
                if (multiple_selection == true) {
                    var audsIds = [];
                    var audSRC = '';
                    audSRC += '<div class="multi_image_list" id="multi_audio_list"><ul>';
                    $('.audio-box-active').each(function() {
                        var aud_id = $(this).attr('id');
                        var id = aud_id.split('_');
                        var audioURL = $(this).find('img').attr('src');
                        audsIds.push(id[1]);
                        audSRC += '<li id="aud_' + id[1] + '">\n\<span>\n\
			<img src="' + audioURL + '" />\n\
			<a href="javascript:;" onclick="MediaManager.removeAudioFromGallery(' + id[1] + ');" class="delect_image" data-dismiss="fileinput"><i class="fa fa-times"></i></a>\n\
			</span>\n\
			</li>';
                    });
                    audSRC += '<ul></div>';
                    var data_id = $('#audio_con_id').val();
                    $('#' + data_id).val(audsIds.join(','));
                    $('#' + data_id + '_audios').html(audSRC);
                } else {
                    var audsIds = [];
                    var audSRC = '';
                    audSRC += '<div class="multi_image_list" id="multi_audio_list"><ul>';
                    $('.audio-box-active').each(function() {
                        var aud_id = $(this).attr('id');
                        var id = aud_id.split('_');
                        var audioURL = $(this).find('img').attr('src');
                        audsIds.push(id[1]);
                        audSRC += '<li id="aud_' + id[1] + '">\n\<span>\n\
			<img src="' + audioURL + '" />\n\
			<a href="javascript:;" onclick="MediaManager.removeAudioFromGallery(' + id[1] + ');" class="delect_image" data-dismiss="fileinput"><i class="fa fa-times"></i></a>\n\
			</span>\n\
			</li>';
                    });
                    audSRC += '<ul></div>';
                    var data_id = $('#audio_con_id').val();
                    $('#' + data_id).val(audsIds.join(','));
                    $('#' + data_id + '_audios').html(audSRC);
                }
                /* for close portlet */
                var portlet = $('#audio_component').closest(".portlet");
                if ($('body').hasClass('page-portlet-fullscreen')) {
                    $('body').removeClass('page-portlet-fullscreen');
                }
                if (portlet.hasClass('portlet-fullscreen')) {
                    portlet.removeClass('portlet-fullscreen');
                }
                portlet.find('.portlet-title .fullscreen').tooltip('destroy');
                portlet.find('.portlet-title > .tools > .reload').tooltip('destroy');
                portlet.find('.portlet-title > .tools > .remove').tooltip('destroy');
                portlet.find('.portlet-title > .tools > .config').tooltip('destroy');
                portlet.find('.portlet-title > .tools > .collapse, .portlet > .portlet-title > .tools > .expand').tooltip('destroy');
                portlet.hide();
            } else {
                openAlertDialogForAudio('Please select the audio and click on "Insert Media" button to proceed.');
            }
        },
        setRecentUploadTab: function(userid) {
            $(".loader").show();
            $.ajax({
                type: "POST",
                cache: true,
                url: site_url + "/powerpanel/media/get_recent_uploaded_images",
                data: {
                    user_id: userid
                },
                success: function(data) {
                    $(".loader").hide();
                    $(".file_upload").hide();
                    $(".image_html").hide();
                    $(".insert_from_url").hide();
                    $(".user_uploaded").hide();
                    $(".trashed_images").hide();
                    $(".image_details").hide();
                    $(".image_cropper").hide();
                    $(".recent_uploads").show();
                    $('input[name="imageName"]').addClass("hide");
                    $(".recent_uploads").html(data);
                    var multiple_selection = $(".multiple-selection").data("multiple");
                    if (multiple_selection == false || multiple_selection == undefined) {
                        $("#note").text("You can insert only one image.");
                    } else {
                        $("#note").text(
                            'Please select the image and click on "Insert Media" button to proceed.'
                        );
                    }
                    var imgIDs = $('input[name="img_id"]').val();
                    MediaManager.selectRecentUploadImage(imgIDs);
                },
                error: function(xhr, ajaxOptions, thrownError) {},
                async: true
            });
        },
        setTrashedImageTab: function(userid) {
            $(".loader").show();
            $.ajax({
                type: "POST",
                cache: true,
                url: site_url + "/powerpanel/media/get_trash_images",
                data: {
                    user_id: userid
                },
                success: function(data) {
                    $(".loader").hide();
                    $(".file_upload").hide();
                    $(".image_html").hide();
                    $(".insert_from_url").hide();
                    $(".user_uploaded").hide();
                    $(".recent_uploads").hide();
                    $(".image_details").hide();
                    $(".image_cropper").hide();
                    $(".trashed_images").show();
                    $('input[name="imageName"]').addClass("hide");
                    $(".trashed_images").html(data);
                },
                error: function(xhr, ajaxOptions, thrownError) {},
                async: true
            });
        },
        setTrashedVideoTab: function(userid) {
            $(".loader").show();
            $.ajax({
                type: "POST",
                cache: true,
                url: site_url + "/powerpanel/media/get_trash_videos",
                data: {
                    user_id: userid
                },
                success: function(data) {
                    $(".loader").hide();
                    $(".video_upload").hide();
                    $(".image_html").hide();
                    $(".insert_video_from_url").hide();
                    $(".user_uploaded_video").hide();
                    $(".video_upload").hide();
                    $(".trashed_videos").show();
                    $(".trashed_videos").html(data);
                },
                error: function(xhr, ajaxOptions, thrownError) {},
                async: true
            });
        },
        setTrashedDocumentTab: function(userid) {
            $(".loader").show();
            $.ajax({
                type: "POST",
                cache: true,
                url: site_url + "/powerpanel/media/get_trash_documents",
                data: {
                    user_id: userid
                },
                success: function(data) {
                    $(".loader").hide();
                    $(".docs_upload").hide();
                    $(".user_uploaded_docs").hide();
                    $(".docs_html").hide();
                    $(".trashed_docs").show();
                    $('input[name="docName"]').addClass("hide");
                    $(".trashed_docs").html(data);
                },
                error: function(xhr, ajaxOptions, thrownError) {},
                async: true
            });
        },
        setTrashedAudioTab: function(userid) {
            $('.loader').show();
            $.ajax({
                type: "POST",
                cache: true,
                url: site_url + '/powerpanel/media/get_trash_audios',
                data: {
                    'user_id': userid
                },
                success: function(data) {
                    $('.loader').hide();
                    $('.auds_upload').hide();
                    $('.user_uploaded_auds').hide();
                    $('.auds_html').hide();
                    $('.trashed_auds').show();
                    $('input[name="audName"]').addClass('hide');
                    $('.trashed_auds').html(data);
                },
                error: function(xhr, ajaxOptions, thrownError) {},
                async: true
            });
        },
        insertImageFromUrl: function() {
            $(".loader").show();
            var image_url = $(".image_url").val();
            setTimeout(function() {
                $(".thrownError").text("");
            }, 5000);
            if (image_url.length > 0) {
                $.ajax({
                    type: "POST",
                    cache: true,
                    url: site_url + "/powerpanel/media/insert_image_by_url",
                    data: {
                        url: image_url
                    },
                    success: function(data) {
                        $(".loader").hide();
                        var response = JSON.parse(data);
                        if (response.error) {
                            $(".loader").hide();
                            $(".uploaded_image").html("");
                            $(".thrownError").text(response.error);
                        } else {
                            $(".thrownError").text("");
                            MediaManager.setMyUploadTab(window.user_id, response.image_id);
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        $(".loader").hide();
                    },
                    async: true
                });
            } else {
                $(".loader").hide();
                $(".thrownError").text("Please enter URL.");
                return false;
            }
        },
        removeMultipleImages: function(identity = false, ignore = false) {
            if ($(".contains_thumb").hasClass("img-box-active")) {
                $(".loader").show();
                if (identity == "recent") {
                    var IDs = [];
                    $("#recent_upload_images .img-box-active").each(function() {
                        var class_with_id = $(this)
                            .attr("id")
                            .split("_");
                        IDs.push(class_with_id[1]);
                    });
                } else {
                    var IDs = [];
                    $("#append_user_image .img-box-active").each(function() {
                        var class_with_id = $(this)
                            .attr("id")
                            .split("_");
                        IDs.push(class_with_id[1]);
                    });
                }
                if ($("#append_image .img-box-active").length > 0) {
                    var IDs = [];
                    $("#append_image .img-box-active").each(function() {
                        var class_with_id = $(this)
                            .attr("id")
                            .split("_");
                        IDs.push(class_with_id[1]);
                    });
                }
                if (IDs) {
                    $.ajax({
                        type: "POST",
                        cache: true,
                        url: site_url + "/powerpanel/media/remove_multiple_image",
                        data: {
                            idArr: IDs,
                            identity: identity
                        },
                        success: function(data) {
                            $(".loader").hide();
                            if (identity == "recent") {
                                MediaManager.setRecentUploadTab(window.user_id);
                            } else if (identity == "trash") {
                                MediaManager.setTrashedImageTab(window.user_id);
                            } else {
                                MediaManager.setMyUploadTab(window.user_id);
                            }
                            $.each(IDs, function(index, value) {
                                if ($.inArray(value, IDs) != "-1") {
                                    if (".multi_image_list #" + value) {
                                        $(".multi_image_list ul li#" + value).remove();
                                    }
                                }

                                var imgIDs = $('input[name="img_id"]').val();
                                if (imgIDs != undefined) {
                                    if (imgIDs.indexOf(",") != -1) {
                                        var commma = true;
                                    }
                                    if (commma) {
                                        imgIDs = imgIDs.split(",");
                                    }

                                    var filterdValue = $.grep(imgIDs, function(val) {
                                        return val != value;
                                    });
                                    $('input[name="img_id"]').val(filterdValue.toString());
                                }
                            });
                            MediaManager.showToaster('success', 'Images removed successfully');

                        },
                        error: function(xhr, ajaxOptions, thrownError) {},
                        async: true
                    });
                }
            }
        },
        checkImageInuse: function (type = false) {
            if (type == 'recent') {
                $('#deleteMediaImage .remove_multiple_images').val('recent');
                var IDs = [];
                $('#recent_upload_images .img-box-active').each(function () {
                    var class_with_id = $(this).attr('id').split('_');
                    IDs.push(class_with_id[1]);
                });
            } else {
                $('#deleteMediaImage .remove_multiple_images').val(null);
                var IDs = [];
                $('#append_user_image .img-box-active').each(function () {
                    var class_with_id = $(this).attr('id').split('_');
                    IDs.push(class_with_id[1]);
                });
            }
            if (IDs) {
                $.ajax({
                    type: "POST",
                    cache: true,
                    url: site_url + '/powerpanel/media/check-img-inuse',
                    data: {
                        'idArr': IDs
                    },
                    dataType: 'JSON',
                    success: function (data) {
                        if (data.message) {
                            // $('#imgInUse').modal('show');
                            // $('#imgInUse #imgInUseMessage').text(data.message);
                            toastr.options = {
                                "closeButton": true,
                                "debug": false,
                                "positionClass": "toast-top-right",
                                "onclick": null,
                                "showDuration": "1000",
                                "hideDuration": "2000",
                                "timeOut": "6000",
                                "extendedTimeOut": "1000",
                                "showEasing": "swing",
                                "hideEasing": "linear",
                                "showMethod": "fadeIn",
                                "hideMethod": "fadeOut"
                            }
                            toastr.warning(data.message);
                            var ignoreArr = [];
                            if (type == 'recent') {
                                $.each(data.usedImg, function (i, item) {
                                    $('#recent_upload_images #media_' + item.intFkImgId).addClass('img_assigned');
                                    var flag = $('<span class="flag_assigned">Assigned</span>');
                                    $('#recent_upload_images #media_' + item.intFkImgId + ' .flag_assigned').remove();
                                    $('#recent_upload_images #media_' + item.intFkImgId + ' .thumbnail').append(flag);
                                    ignoreArr.push(item.intFkImgId);
                                });
                            } else {
                                $.each(data.usedImg, function (i, item) {
                                    $('#append_user_image #media_' + item.intFkImgId).addClass('img_assigned');
                                    var flag = $('<span class="flag_assigned">Assigned</span>');
                                    $('#append_user_image #media_' + item.intFkImgId + ' .flag_assigned').remove();
                                    $('#append_user_image #media_' + item.intFkImgId + ' .thumbnail').append(flag);
                                    ignoreArr.push(item.intFkImgId);
                                });
                            }
                            // MediaManager.removeMultipleImages(false, ignoreArr);
                        } else {
                            $('#deleteMediaImage').modal('show');
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {},
                    async: true
                });
        }
        },
        checkDocumentInuse: function(type = false) {
            if (type == "recent") {
                $("#deleteMediaDocument .remove_multiple_document").val("recent");
                var IDs = [];
                $("#recent_upload_images .document-box-active").each(function() {
                    var class_with_id = $(this)
                        .attr("id")
                        .split("_");
                    IDs.push(class_with_id[1]);
                });
            } else {
                $("#deleteMediaDocument .remove_multiple_document").val(null);
                var IDs = [];
                $("#append_user_image .document-box-active").each(function() {
                    var class_with_id = $(this)
                        .attr("id")
                        .split("_");
                    IDs.push(class_with_id[1]);
                });
            }
            if (IDs) {
                $.ajax({
                    type: "POST",
                    cache: true,
                    url: site_url + "/powerpanel/media/check-document-inuse",
                    data: {
                        idArr: IDs
                    },
                    dataType: "JSON",
                    success: function(data) {
                        if (data.message) {

                            $("#permanentDeleteMediaDocumentDELETE .recordList").html(data.message);
                            $("#permanentDeleteMediaDocumentDELETE").modal("show");
                            var ignoreArr = [];
                            if (type == "recent") {
                                $.each(data.usedDocument, function(i, item) {
                                    $(
                                        "#append_user_image #document_" + item.intFkDocumentId
                                    ).addClass("img_assigned");
                                    var flag = $('<span class="flag_assigned">Assigned</span>');
                                    $(
                                        "#append_user_image #document_" +
                                        item.intFkDocumentId +
                                        " .flag_assigned"
                                    ).remove();
                                    $(
                                        "#append_user_image #document_" +
                                        item.intFkDocumentId +
                                        " .thumbnail"
                                    ).append(flag);
                                    ignoreArr.push(item.intFkDocumentId);
                                });
                            } else {
                                $.each(data.usedDocument, function(i, item) {
                                    $(
                                        "#append_user_image #document_" + item.intFkDocumentId
                                    ).addClass("img_assigned");
                                    var flag = $('<span class="flag_assigned">Assigned</span>');
                                    $(
                                        "#append_user_image #document_" +
                                        item.intFkDocumentId +
                                        " .flag_assigned"
                                    ).remove();
                                    $(
                                        "#append_user_image #document_" +
                                        item.intFkDocumentId +
                                        " .thumbnail"
                                    ).append(flag);
                                    ignoreArr.push(item.intFkDocumentId);
                                });
                            }
                            // MediaManager.removeMultipleImages(false, ignoreArr);
                        } else {
                            $("#deleteMediaDocument").modal("show");
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {},
                    async: true
                });
            }
        },
        checkAudioInuse: function(type = false) {
            if (type == 'recent') {
                $('#deleteMediaAudio .remove_multiple_audio').val('recent');
                var IDs = [];
                $('#recent_upload_images .audio-box-active').each(function() {
                    var class_with_id = $(this).attr('id').split('_');
                    IDs.push(class_with_id[1]);
                });
            } else {
                $('#deleteMediaAudio .remove_multiple_audio').val(null);
                var IDs = [];
                $('#append_user_image .audio-box-active').each(function() {
                    var class_with_id = $(this).attr('id').split('_');
                    IDs.push(class_with_id[1]);
                });
            }
            if (IDs) {
                $.ajax({
                    type: "POST",
                    cache: true,
                    url: site_url + '/powerpanel/media/check-audio-inuse',
                    data: {
                        'idArr': IDs
                    },
                    dataType: 'JSON',
                    success: function(data) {
                        if (data.message) {
                            // $('#documentInUse').modal('show');
                            // $('#documentInUse #documentInUseMessage').text(data.message);
                            toastr.options = {
                                "closeButton": true,
                                "debug": false,
                                "positionClass": "toast-top-right",
                                "onclick": null,
                                "showDuration": "1000",
                                "hideDuration": "2000",
                                "timeOut": "6000",
                                "extendedTimeOut": "1000",
                                "showEasing": "swing",
                                "hideEasing": "linear",
                                "showMethod": "fadeIn",
                                "hideMethod": "fadeOut"
                            }
                            toastr.warning(data.message);
                            var ignoreArr = [];
                            if (type == 'recent') {
                                $.each(data.usedAudio, function(i, item) {
                                    $('#append_user_image #audio_' + item.intFkAudioId).addClass('img_assigned');
                                    var flag = $('<span class="flag_assigned">Assigned</span>');
                                    $('#append_user_image #audio_' + item.intFkAudioId + ' .flag_assigned').remove();
                                    $('#append_user_image #audio_' + item.intFkAudioId + ' .thumbnail').append(flag);
                                    ignoreArr.push(item.intFkAudioId);
                                });
                            } else {
                                $.each(data.usedAudio, function(i, item) {
                                    $('#append_user_image #audio_' + item.intFkAudioId).addClass('img_assigned');
                                    var flag = $('<span class="flag_assigned">Assigned</span>');
                                    $('#append_user_image #audio_' + item.intFkAudioId + ' .flag_assigned').remove();
                                    $('#append_user_image #audio_' + item.intFkAudioId + ' .thumbnail').append(flag);
                                    ignoreArr.push(item.intFkAudioId);
                                });
                            }
                            // MediaManager.removeMultipleImages(false, ignoreArr);
                        } else {
                            $('#deleteMediaAudio').modal('show');
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {},
                    async: true
                });
            }
        },
        checkVideoInuse: function(type = false) {
            if (type == "recent") {
                $("#permanentDeleteMediaVideo .remove_multiple_videos_permanently").val(
                    "recent"
                );
                var IDs = [];
                $("#recent_upload_images .active_video").each(function() {
                    var class_with_id = $(this)
                        .attr("id")
                        .split("_");
                    IDs.push(class_with_id[1]);
                });
            } else {
                $("#permanentDeleteMediaVideo .remove_multiple_videos_permanently").val(
                    null
                );
                var IDs = [];
                $("#append_user_image .active_video").each(function() {
                    var class_with_id = $(this)
                        .attr("id")
                        .split("_");
                    IDs.push(class_with_id[1]);
                });
            }
            if (IDs) {
                $.ajax({
                    type: "POST",
                    cache: true,
                    url: site_url + "/powerpanel/media/check-video-inuse",
                    data: {
                        idArr: IDs
                    },
                    dataType: "JSON",
                    success: function(data) {
                        if (data.message) {
                            $("#permanentDeleteMediaVideoDELETE .recordList").html(
                                data.message
                            );
                            $("#permanentDeleteMediaVideoDELETE").modal("show");
                            var ignoreArr = [];
                            if (type == "recent") {
                                $.each(data.usedVideo, function(i, item) {
                                    $(
                                        "#recent_upload_images #video_" + item.intFkVideoId
                                    ).addClass("img_assigned");
                                    var flag = $('<span class="flag_assigned">Assigned</span>');
                                    $(
                                        "#recent_upload_images #video_" +
                                        item.intFkVideoId +
                                        " .flag_assigned"
                                    ).remove();
                                    $(
                                        "#recent_upload_images #video_" +
                                        item.intFkVideoId +
                                        " .thumbnail"
                                    ).append(flag);
                                    ignoreArr.push(item.intFkVideoId);
                                });
                            } else {
                                $.each(data.usedVideo, function(i, item) {
                                    $("#append_user_image #video_" + item.intFkVideoId).addClass(
                                        "img_assigned"
                                    );
                                    var flag = $('<span class="flag_assigned">Assigned</span>');
                                    $(
                                        "#append_user_image #video_" +
                                        item.intFkVideoId +
                                        " .flag_assigned"
                                    ).remove();
                                    $(
                                        "#append_user_image #video_" +
                                        item.intFkVideoId +
                                        " .thumbnail"
                                    ).append(flag);
                                    ignoreArr.push(item.intFkVideoId);
                                });
                            }
                        } else {
                            $("#deleteMediaVideo").modal("show");
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {},
                    async: true
                });
            }
        },
        removeMultipleDocuments: function(identity = false) {
            if ($(".contains_thumb").hasClass("document-box-active")) {
                $(".loader").show();
                var IDs = [];
                $(".document-box-active").each(function() {
                    var class_with_id = $(this)
                        .attr("id")
                        .split("_");
                    $("#document_" + class_with_id[1]).remove();
                    IDs.push(class_with_id[1]);
                });
                if (IDs) {
                    $.ajax({
                        type: "POST",
                        cache: true,
                        url: site_url + "/powerpanel/media/remove_multiple_documents",
                        data: {
                            idArr: IDs,
                            identity: identity
                        },
                        success: function(data) {
                            $(".loader").hide();
                            if (identity == "recent") {
                                //recent_uploads(window.user_id);
                            } else if (identity == "trash") {
                                MediaManager.setTrashedDocumentTab(window.user_id);
                            } else {
                                MediaManager.setDocumentListTab(window.user_id);
                            }
                            $.each(IDs, function(index, value) {
                                if ($.inArray(value, IDs) != "-1") {
                                    if (".multi_image_list #" + value) {
                                        $(".multi_image_list ul li#doc_" + value).remove();
                                    }
                                }
                                // var docIDs = $('input[name="doc_id"]')
                                //     .val()
                                //     .split(",");
                                // var filterdValue = $.grep(docIDs, function(val) {
                                //     return val != value;
                                // });
                                // $('input[name="doc_id"]').val(filterdValue.toString());
                            });

                            MediaManager.showToaster('success', "Document(s) are removed successfully");

                        },
                        error: function(xhr, ajaxOptions, thrownError) {},
                        async: true
                    });
                }
            }
        },
        removeMultipleAudios: function(identity = false) {
            if ($('.contains_thumb').hasClass('audio-box-active')) {
                $('.loader').show();
                var IDs = [];
                $('.audio-box-active').each(function() {
                    var class_with_id = $(this).attr('id').split('_');
                    $('#audio_' + class_with_id[1]).remove();
                    IDs.push(class_with_id[1]);
                });
                if (IDs) {
                    $.ajax({
                        type: "POST",
                        cache: true,
                        url: site_url + '/powerpanel/media/remove_multiple_audios',
                        data: {
                            'idArr': IDs,
                            'identity': identity
                        },
                        success: function(data) {
                            $('.loader').hide();
                            if (identity == "recent") {
                                //recent_uploads(window.user_id);
                            } else {
                                MediaManager.setAudioListTab(window.user_id);
                            }
                            toastr.options = {
                                "closeButton": true,
                                "debug": false,
                                "positionClass": "toast-top-right",
                                "onclick": null,
                                "showDuration": "1000",
                                "hideDuration": "1000",
                                "timeOut": "5000",
                                "extendedTimeOut": "1000",
                                "showEasing": "swing",
                                "hideEasing": "linear",
                                "showMethod": "fadeIn",
                                "hideMethod": "fadeOut"
                            }
                            toastr.success("Audio(s) are removed successfully.");
                        },
                        error: function(xhr, ajaxOptions, thrownError) {},
                        async: true
                    });
                }
            }
        },
        setMyUploadVideoTab: function() {
            $.ajax({
                type: "POST",
                cache: true,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                url: site_url + "/powerpanel/media/set_video_html",
                dataType: "html",
                success: function(data) {
                    $(".tab_6_3 ul li a").removeClass("active");
                    $("#upload_video").addClass("active");
                    $(".insert_video_from_url").hide();
                    $(".video_upload").show();
                    $(".video_upload").html(data);
                    var maxfilesexceeded = false;
                    var image_id = false;
                    var success = false;
                    $("#my-dropzone").dropzone({
                        acceptedFiles: "video/*",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                        },
                        url: site_url + "/powerpanel/media/upload_video",
                        maxFiles: 15, // Number of files at a time
                        maxFilesize: 15, //in MB
                        clickable: true,
                        maxfilesexceeded: function(file) {
                            maxfilesexceeded = true;
                        },
                        success: function(response) {
                            image_id = response.xhr.response;
                            if (response.status == "success") {
                                success = true;
                            }
                        },
                        queuecomplete: function(file) {
                            if (success) {
                                if (maxfilesexceeded) {

                                    MediaManager.showToaster('error', "Only 15 videos are uploaded others are not uploaded");

                                } else {

                                    MediaManager.showToaster('success', "Videos are successfully uploaded");

                                    MediaManager.setMyVideosTab(window.user_id);
                                }
                            }
                        },
                        removedfile: function(file) {
                            var _ref; // Remove file on clicking the 'Remove file' button
                            return (_ref = file.previewElement) != null ?
                                _ref.parentNode.removeChild(file.previewElement) :
                                void 0;
                        }
                    });
                },
                error: function(xhr, ajaxOptions, thrownError) {},
                async: true
            });
        },
        setMyVideosTab: function(userid, video_id = false) {
            $(".tab_6_3 ul li a").removeClass("active");
            $("#user_uploaded_video").addClass("active");
            $(".loader").show();
            $.ajax({
                type: "POST",
                cache: true,
                url: site_url + "/powerpanel/media/user_uploaded_video",
                dataType: "html",
                data: {
                    userid: userid
                },
                success: function(data) {
                    $(".loader").hide();
                    var lastPopedPopover;
                    $(".popovers").popover();
                    // close last displayed popover
                    $(document).on("click.bs.popover.data-api", function(e) {
                        if (lastPopedPopover) {
                            lastPopedPopover.popover("hide");
                        }
                    });
                    $(".video_upload").hide();
                    $(".insert_video_from_url").hide();
                    $(".insert_vimeo_video_from_url").hide();
                    $(".user_uploaded_video").show();
                    $(".user_uploaded_video").html(data);
                    $(document).ready(function() {
                        $(".fancybox").fancybox({
                            openEffect: "fade",
                            closeEffect: "fade",
                            prevEffect: "fade",
                            nextEffect: "fade",
                            closebtn: true,
                            autoWidth: true,
                            autoHeight: true,
                            autoResize: true,
                            resize: "Auto",
                            autoCenter: true,
                            autoScale: true,
                            helpers: {
                                overlay: {
                                    locked: false,
                                    closeClick: false
                                }
                            }
                        });
                    });
                    var multiple_selection = $(".multiple-selection").data("multiple");
                    if (multiple_selection == false || multiple_selection == undefined) {
                        $("#note").text("You can insert only one video.");
                    } else {
                        $("#note").text(
                            'Please select the video and click on "Insert Video(s)" button to proceed.'
                        );
                    }
                    if (video_id != false) {
                        var videoIDs = video_id;
                    } else {
                        var videoIDs = $('input[name="video_id"]').val();
                    }
                    MediaManager.selectVideo(videoIDs);
                    // $('input[name="imageName"]').removeClass('hide')
                    // $('input[name="imageName"]').on('keyup',function() {
                    //          var imageName = $(this).val();
                    //          MediaManager.searchByImageName(imageName);
                    // });
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    $(".loader").hide();
                },
                async: true
            });
        },
        setVideoFromUrlTab: function() {
            $(".video_upload").hide();
            $(".user_uploaded_video").hide();
            $(".insert_vimeo_video_from_url").hide();
            var html =
                '<div class="title_section"><h2>Insert video from youtube url</h2></div>\n\
                  <div class="portlet light">\n\
                  <div class="row">\n\
                  <div class="col-md-12">\n\
                  <div class="form-group form-md-line-input form-md-floating-label has-info">\n\
                      <input type="text" class="form-control input-lg video_url" id="form_control_1">\n\
                      <label for="form_control_1">Enter youtube video url</label>\n\
                      <span class="thrownError" style="color:red;position: absolute;"></span>\n\
                   </div>\n\
                      <a href="javascript:void(0);" onclick="MediaManager.insertVideoFromUrl()" class="btn btn-green-drake">Upload YouTube Video</a>\n\
                      <a href="javascript:void(0);" onclick="MediaManager.setMyVideosTab(' +
                window.user_id +
                ')" class="btn btn-green-drake">Go to Video Gallery</a><br/>\n\
                      <p></p>\n\
                  </div>\n\
                   <div class="row">\n\
                    <div class="col-md-12">\n\
                    <div class="uploaded_image"></div>\n\
                   </div>\n\
                  </div>\n\
                  </div>\n\
                  </div>\n\
                  </div>';
            $(".insert_video_from_url").show();
            $(".insert_video_from_url").html(html);
        },
        insertVideoFromUrl: function() {
            $(".loader").show();
            var video_url = $(".video_url").val();
            setTimeout(function() {
                $(".thrownError").text("");
            }, 5000);
            if (video_url.length > 0) {
                $.ajax({
                    type: "POST",
                    cache: true,
                    url: site_url + "/powerpanel/media/insert_video_by_url",
                    data: {
                        url: video_url
                    },
                    success: function(data) {
                        $(".loader").hide();
                        var response = JSON.parse(data);
                        if (response.error) {
                            $(".loader").hide();
                            $(".uploaded_image").html("");
                            $(".thrownError").text(response.error);
                        } else {
                            $(".thrownError").text("");
                            $(".uploaded_image").html(response.html);
                            $(document).ready(function() {
                                $(".fancybox").fancybox({
                                    openEffect: "fade",
                                    closeEffect: "fade",
                                    prevEffect: "fade",
                                    nextEffect: "fade",
                                    closebtn: true,
                                    autoWidth: true,
                                    autoHeight: true,
                                    autoResize: true,
                                    resize: "Auto",
                                    autoCenter: true,
                                    autoScale: true,
                                    helpers: {
                                        overlay: {
                                            locked: false,
                                            closeClick: false
                                        }
                                    }
                                });
                            });
                            MediaManager.setMyVideosTab(window.user_id, response.video_id);
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        $(".loader").hide();
                    },
                    async: true
                });
            } else {
                $(".loader").hide();
                $(".thrownError").text("Please enter YouTube video URL.");
                return false;
            }
        },
        /** Start Vimeo video URL through get the URL */
        setVimeoVideoFromUrlTab: function() {
            $(".video_upload").hide();
            $(".user_uploaded_video").hide();
            $(".insert_video_from_url").hide();
            var html =
                '<div class="title_section"><h2>Insert video from vimeo url</h2></div>\n\
                  <div class="portlet light">\n\
                  <div class="row">\n\
                  <div class="col-md-12">\n\
                  <div class="form-group form-md-line-input form-md-floating-label has-info">\n\
                      <input type="text" class="form-control input-lg vimeo_video_url" id="form_control_1">\n\
                      <label for="form_control_1">Enter vimeo video url</label>\n\
                      <span class="thrownError" style="color:red; position: absolute;"></span>\n\
                   </div>\n\
                      <a href="javascript:void(0);" onclick="MediaManager.insertVimeoVideoFromUrl()" class="btn btn-green-drake">Upload Vimeo Video</a>\n\
                      <a href="javascript:void(0);" onclick="MediaManager.setMyVideosTab(' +
                window.user_id +
                ')" class="btn btn-green-drake">Go to Video Gallery</a><br/>\n\
                      <p></p>\n\
                  </div>\n\
                   <div class="row">\n\
                    <div class="col-md-12">\n\
                    <div class="uploaded_image"></div>\n\
                   </div>\n\
                  </div>\n\
                  </div>\n\
                  </div>\n\
                  </div>';
            $(".insert_vimeo_video_from_url").show();
            $(".insert_vimeo_video_from_url").html(html);
        },
        insertVimeoVideoFromUrl: function() {
            $(".loader").show();
            var video_url = $(".vimeo_video_url").val();
            setTimeout(function() {
                $(".thrownError").text("");
            }, 5000);
            if (video_url.length > 0) {
                $.ajax({
                    type: "POST",
                    cache: true,
                    url: site_url + "/powerpanel/media/insert_vimeo_video_by_url",
                    data: {
                        url: video_url
                    },
                    success: function(data) {
                        $(".loader").hide();
                        var response = JSON.parse(data);
                        if (response.error) {
                            $(".loader").hide();
                            $(".uploaded_image").html("");
                            $(".thrownError").text(response.error);
                        } else {
                            $(".thrownError").text("");
                            $(".uploaded_image").html(response.html);
                            $(document).ready(function() {
                                $(".fancybox").fancybox({
                                    openEffect: "fade",
                                    closeEffect: "fade",
                                    prevEffect: "fade",
                                    nextEffect: "fade",
                                    closebtn: true,
                                    autoWidth: true,
                                    autoHeight: true,
                                    autoResize: true,
                                    resize: "Auto",
                                    autoCenter: true,
                                    autoScale: true,
                                    helpers: {
                                        overlay: {
                                            locked: false,
                                            closeClick: false
                                        }
                                    }
                                });
                            });
                            MediaManager.setMyVideosTab(window.user_id, response.video_id);
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        $(".loader").hide();
                    },
                    async: true
                });
            } else {
                $(".loader").hide();
                $(".thrownError").text("Please enter Vimeo video URL.");
                return false;
            }
        },
        /** End vimeo video code here **/
        removeMultipleVideos: function(identity = false) {
            if ($(".contains_thumb").hasClass("active_video")) {
                $(".loader").show();
                var IDs = [];
                $(".active_video").each(function() {
                    var class_with_id = $(this)
                        .attr("id")
                        .split("_");
                    $("#video_" + class_with_id[1]).remove();
                    IDs.push(class_with_id[1]);
                });
                if (IDs) {
                    $.ajax({
                        type: "POST",
                        cache: true,
                        url: site_url + "/powerpanel/media/remove_multiple_videos",
                        data: {
                            idArr: IDs,
                            identity: identity
                        },
                        success: function(data) {
                            $(".loader").hide();
                            if (identity == "recent") {
                                //recent_uploads(window.user_id);
                            } else if (identity == "trash") {
                                MediaManager.setTrashedVideoTab(window.user_id);
                            } else {
                                MediaManager.setMyVideosTab(window.user_id);
                            }
                            /* Each record for get totla rcord id and remove match value.also html code removed vidoe_id value  */
                            $.each(IDs, function(index, value) {
                                if ($.inArray(value, IDs) != "-1") {
                                    if (".multi_image_list #" + value) {
                                        $(".multi_image_list ul li#" + value).remove();
                                    }
                                }
                                var vidIDs = $('input[name="video_id"]')
                                    .val()
                                    .split(",");
                                var filterdValue = $.grep(vidIDs, function(val) {
                                    return val != value;
                                });
                                $('input[name="video_id"]').val(filterdValue.toString());
                            });
                            /* End code here */

                            MediaManager.showToaster('success', "Videos are removed successfully");

                            // MediaManager.setMyVideosTab(window.user_id);
                        },
                        error: function(xhr, ajaxOptions, thrownError) {},
                        async: true
                    });
                }
            }
        },
        restoreMultipleImages: function() {
            if ($(".contains_thumb").hasClass("img-box-active")) {
                $(".loader").show();
                var IDs = [];
                $(".img-box-active").each(function() {
                    var class_with_id = $(this)
                        .attr("id")
                        .split("_");
                    IDs.push(class_with_id[1]);
                });
                if (IDs) {
                    $.ajax({
                        type: "POST",
                        cache: true,
                        url: site_url + "/powerpanel/media/restore_multiple_image",
                        data: {
                            idArr: IDs
                        },
                        success: function(data) {
                            $(".loader").hide();
                            MediaManager.showToaster('success', "Images are restored successfully");
                        },
                        error: function(xhr, ajaxOptions, thrownError) {},
                        async: true
                    });
                }
            }
        },
        restoreMultipleVideos: function() {
            if ($(".contains_thumb").hasClass("active_video")) {
                $(".loader").show();
                var IDs = [];
                $("#append_user_image .active_video").each(function() {
                    var class_with_id = $(this)
                        .attr("id")
                        .split("_");
                    IDs.push(class_with_id[1]);
                });
                if (IDs) {
                    $.ajax({
                        type: "POST",
                        cache: true,
                        url: site_url + "/powerpanel/media/restore-multiple-videos",
                        data: {
                            idArr: IDs
                        },
                        success: function(data) {
                            $(".loader").hide();

                            MediaManager.showToaster('success', "Videos are restored successfully");

                        },
                        error: function(xhr, ajaxOptions, thrownError) {},
                        async: true
                    });
                }
            }
        },
        restoreMultipleDocument: function() {
            if ($(".contains_thumb").hasClass("document-box-active")) {
                $(".loader").show();
                var IDs = [];
                $(".document-box-active").each(function() {
                    var class_with_id = $(this)
                        .attr("id")
                        .split("_");
                    IDs.push(class_with_id[1]);
                });
                if (IDs) {
                    $.ajax({
                        type: "POST",
                        cache: true,
                        url: site_url + "/powerpanel/media/restore-multiple-document",
                        data: {
                            idArr: IDs
                        },
                        success: function(data) {
                            $(".loader").hide();
                            MediaManager.showToaster('success', "Documents are restored successfully");

                        },
                        error: function(xhr, ajaxOptions, thrownError) {},
                        async: true
                    });
                }
            }
        },
        restoreMultipleAudio: function() {
            if ($('.contains_thumb').hasClass('audio-box-active')) {
                $('.loader').show();
                var IDs = [];
                $('.audio-box-active').each(function() {
                    var class_with_id = $(this).attr('id').split('_');
                    IDs.push(class_with_id[1]);
                });
                if (IDs) {
                    $.ajax({
                        type: "POST",
                        cache: true,
                        url: site_url + '/powerpanel/media/restore-multiple-audio',
                        data: {
                            'idArr': IDs
                        },
                        success: function(data) {
                            $('.loader').hide();
                            toastr.options = {
                                "closeButton": true,
                                "debug": false,
                                "positionClass": "toast-top-right",
                                "onclick": null,
                                "showDuration": "1000",
                                "hideDuration": "1000",
                                "timeOut": "5000",
                                "extendedTimeOut": "1000",
                                "showEasing": "swing",
                                "hideEasing": "linear",
                                "showMethod": "fadeIn",
                                "hideMethod": "fadeOut"
                            }
                            MediaManager.setTrashedAudioTab(window.user_id);
                            toastr.success("Documents are restored successfully.");
                        },
                        error: function(xhr, ajaxOptions, thrownError) {},
                        async: true
                    });
                }
            }
        },
        openRestoreConfirmBox: function(mediaType = "Image") {
            if (mediaType == "Image") {
                if ($(".contains_thumb").hasClass("img-box-active")) {
                    $("#restoreConfirmBox").modal("show");
                } else {
                    openAlertDialogForImage(
                        "Please select at least one image to proceed."
                    );
                    return false;
                }
            }
            if (mediaType == "Video") {
                if ($(".contains_thumb").hasClass("active_video")) {
                    $("#restoreVideoConfirmBox").modal("show");
                } else {
                    openAlertDialogForImage(
                        "Please select at least one video to proceed."
                    );
                    return false;
                }
            }
            if (mediaType == "Document") {
                if ($(".contains_thumb").hasClass("document-box-active")) {
                    $("#restoreDocumentConfirmBox").modal("show");
                } else {
                    openAlertDialogForImage(
                        "Please select at least one document to proceed."
                    );
                    return false;
                }
            }
            if (mediaType == "Audio") {
                if ($(".contains_thumb").hasClass("audio-box-active")) {
                    $("#restoreAudioConfirmBox").modal("show");
                } else {
                    openAlertDialogForImage(
                        "Please select at least one audio to proceed."
                    );
                    return false;
                }
            }
        },
        openConfirmBox: function(
            mediaType = "Image",
            permanentlyDelete = false,
            type = false
        ) {
            if (mediaType == "video") {
                if ($(".contains_thumb").hasClass("active_video")) {
                    if (permanentlyDelete) {
                        $("#permanentDeleteMediaVideo").modal("show");
                    } else {
                        MediaManager.checkVideoInuse(type);
                    }
                } else {
                    openAlertDialogForVideo(
                        "Please select at least one video to proceed."
                    );
                    return false;
                }
            } else if (mediaType == "document") {
                if ($(".contains_thumb").hasClass("document-box-active")) {
                    if (permanentlyDelete) {
                        $("#permanentDeleteMediaDocument").modal("show");
                    } else {
                        MediaManager.checkDocumentInuse(type);
                    }
                } else {
                    openAlertDialogForDocument(
                        "Please select at least one document to proceed."
                    );
                    return false;
                }
            }else if (mediaType == 'audio') {
                if ($('.contains_thumb').hasClass('audio-box-active')) {
                    if (permanentlyDelete) {
                        $('#permanentDeleteMediaAudio').modal('show');
                    } else {
                        MediaManager.checkAudioInuse(type);
                    }
                } else {
                    openAlertDialogForAudio('Please select at least one audio to proceed.');
                    return false;
                }
            } else {
                if ($(".contains_thumb").hasClass("img-box-active")) {
                    if (permanentlyDelete) {
                        $("#permanentDeleteMediaImage").modal("show");
                    } else {
                        // $('#permanentDeleteMediaImageDELETE').modal('show');
                        MediaManager.checkImageInuse(type);
                    }
                } else {
                    openAlertDialogForImage(
                        "Please select at least one image to proceed."
                    );
                    return false;
                }
            }
        },
        removeImageFromGallery: function(imageIds = false) {

            var imgIDs = $('input[name="img_id"]').val().split(",");
            var filterdValue = $.grep(imgIDs, function(value) {
                return value != imageIds;
            });
            $('input[name="img_id"]').val(filterdValue.toString());
            $("#" + imageIds).remove();
            if ($(".multi_image_list ul li").length <= 0) {
                $(".multi_image_list").remove();
            }
        },
        removeVideoFromVideoManager: function(videoIds = false) {

            var vidIDs = $('input[name="video_id"]').val().split(",");
            var filterdValue = $.grep(vidIDs, function(value) {
                return value != videoIds;
            });

            $('input[name="video_id"]').val(filterdValue.toString());
            $(".video_list").find("#" + videoIds).remove();

            if ($(".video_list .multi_image_list ul li").length <= 0) {
                $(".video_list .multi_image_list").remove();
            }
        },
        removeDocumentFromGallery: function(docsId = false) {

            var docIDs = $('input[name="doc_id"]').val().split(",");

            var filterdValue = $.grep(docIDs, function(value) {
                return value != docsId;
            });

            $('input[name="doc_id"]').val(filterdValue.toString());
            $("#doc_" + docsId).remove();
            if ($("#multi_document_list ul li").length <= 0) {
                $("#multi_document_list").remove();
            }

        },
        removeDocumentFromComposerBlock: function (docsId = false) {
            var data_id = $('#control_id').val();
            var docIDs = $('#frmSectionOnlyDocument').find('.imgip1').val().split(',');
            var filterdValue = $.grep(docIDs, function (value) {
                return value != docsId;
            });
            $('#frmSectionOnlyDocument').find('.imgip1').val(filterdValue.toString());
            $('#frmSectionOnlyDocument #doc_' + docsId).remove();
            if ($('#frmSectionOnlyDocument #' + data_id + '_documents ' + '#multi_document_list ul li').length <= 0) {
                $('#frmSectionOnlyDocument #' + data_id + '_documents ' + '#multi_document_list').remove();
            }
        },
         removeAudioFromGallery: function(audsId = false) {
            var audIds = $('input[name="aud_id"]').val().split(',');
            var filterdValue = $.grep(audIds, function(value) {
                return value != audsId;
            });
            $('input[name="aud_id"]').val(filterdValue.toString());
            $('#aud_' + audsId).remove();
            if ($('#multi_audio_list ul li').length <= 0) {
                $('#multi_audio_list').remove();
            }
        },
        // backToPreTab: function(userid = false) {
        //   var recentORmyUpload = $(".tab_6_3 ul li a.active").attr("id");
        //   if(recentORmyUpload=="recent"){
        //     MediaManager.setRecentUploadTab(1);
        //   }else{
        //     MediaManager.setMyUploadTab(1);
        //   }
        // },
        backToPreTab: function (userid = false, folderid = false) {
            var recentORmyUpload = $(".tab_6_3 ul li a.active").attr("id");
            if (recentORmyUpload == "recent") {
                MediaManager.setRecentUploadTab(1);
            } else {
                if (folderid != 0) {
                    MediaManager.setFolderUploadTab(1, '', folderid);
                } else {
                    MediaManager.setMyUploadTab(1);
                }

        }
        },
        GetUpdateDocumentName: function (id) {
            var response = confirm("Are you sure you want to update title?");
            if (response) {
                var gettitle = $('#documentname_' + id).val();

                $.ajax({
                    type: "POST",
                    cache: true,
                    url: site_url + '/powerpanel/media/updateDocTitle',
                    data: {
                        'id': id,
                        'gettitle': gettitle
                    },
                    success: function (data) {
                        $('.loader').hide();
                        if (data) {
                            alert("Title Successfully Updated.");

                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(thrownError);
                    },
                    async: true
                });
            } else {
                return false;
            }
        },
        GetUpdateAudioName: function (id) {
            var response = confirm("Are you sure you want to update title?");
            if (response) {
                var gettitle = $('#audioname_' + id).val();

                $.ajax({
                    type: "POST",
                    cache: true,
                    url: site_url + '/powerpanel/media/updateAudioTitle',
                    data: {
                        'id': id,
                        'gettitle': gettitle
                    },
                    success: function (data) {
                        $('.loader').hide();
                        if (data) {
                            alert("Title Successfully Updated.");
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(thrownError);
                    },
                    async: true
                });
            } else {
                return false;
            }
        },
        setFolderUploadTab: function (userid, image_id = false, folder_id = false) {

            $('.tab_6_3 ul li a').removeClass('active');
            $('#folder_uploaded_image').addClass('active');
            $('.loader').show();
            $.ajax({
                type: "POST",
                cache: true,
                url: site_url + '/powerpanel/media/folder_uploaded_image',
                dataType: "json",
                data: {
                    'userid': userid
                },
                success: function (data) {
                    $('.loader').hide();
                    var lastPopedPopover;
                    $('.popovers').popover();
                    // close last displayed popover
                    $(document).on('click.bs.popover.data-api', function (e) {
                        if (lastPopedPopover) {
                            lastPopedPopover.popover('hide');
                        }
                    });
                    $('.file_upload').hide();
                    $('.image_html').hide();
                    $('.insert_from_url').hide();
                    $('.trashed_images').hide();
                    $('.recent_uploads').hide();
                    $('.tab_6_4').show();
                    $('.user_uploaded').show();
                    $('.user_uploaded').html(data.Image_html);
                    var multiple_selection = $('.multiple-selection').data('multiple');
                    if (multiple_selection == false || multiple_selection == undefined) {
                        $('#note').text('Please select the image and click on "Insert Media" button to proceed. You can insert only one image.');
                    } else {
                        $('#note').text('Please select the image and click on "Insert Media" button to proceed.');
                    }
                    if (image_id != false) {
                        var imgIDs = image_id;
                    } else {
                        var data_id = $('#data_id').val();
                        var $parentImgDiv = $('#' + data_id).parents('.fileinput.fileinput-new');
                        var imgIDs = $parentImgDiv.find("input[name^='img_id']").val();
                        //var imgIDs = $('input[name="img_id"]').val();
                    }
                    if (folder_id != false) {
                        FolderImages(folder_id, imgIDs);
                    }
                    MediaManager.selectImage(imgIDs);
                    $('input[name="imageName"]').removeClass('hide')
                    $('input[name="imageName"]').keyup(function () {
                        if (($(this).val().length % 3) == 0) {
                            var imageName = $(this).val();
                            MediaManager.searchByImageName(imageName);
                            $('#page').val(1);
                        }
                    });

                    if (sidebargallery) {
                        $(".title_section #insert_image").hide();
                        $(".title_section #delete_image").hide();
                        $("#gallary_component .user_uploaded #note").hide();
                          $('input[name="imageName"]').addClass('hide');
                    } else {
                        $("#gallary_component .user_uploaded #note").show();
                        $(".title_section #insert_image").show();
                        $(".title_section #delete_image").hide();
                          $('input[name="imageName"]').addClass('hide');
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {},
                async: true
            });
        },
        setDocumentUploadTab: function() {
            $(".docs_upload").show();
            $(".user_uploaded_docs").hide();
            $(".docs_html").hide();
            $(".trashed_docs").hide();
            $(".recent_uploads_docs").hide();
            $('input[name="docName"]').addClass("hide");
            $.ajax({
                type: "POST",
                cache: true,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                url: site_url + "/powerpanel/media/set_document_uploader",
                dataType: "html",
                success: function(data) {
                    $(".tab_6_3 ul li a").removeClass("active");
                    $("#upload_document").addClass("active");
                    $(".docs_upload").html(data);
                    var maxfilesexceeded = false;
                    var docs_id = false;
                    var success = false;
                    $("#my-dropzone-document").dropzone({
                        acceptedFiles: acceptfiles,
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                        },
                        url: site_url + "/powerpanel/media/upload_documents",
                        maxFiles: 15, // Number of files at a time
                        maxFilesize: 15, //in MB
                        clickable: true,
                        maxfilesexceeded: function(file) {
                            maxfilesexceeded = true;
                            return false;
                        },
                        success: function(response) {
                            docs_id = response.xhr.response;
                            
                            if (response.status == "success") {
                                success = true;
                            }
                        },
                        queuecomplete: function(file) {
                            if (success) {
                                if (maxfilesexceeded) {
                                    MediaManager.showToaster('error', "Only 15 document(s) are uploaded others are not uploaded");
                                } else {
                                    MediaManager.showToaster('success', "Document(s) are successfully uploaded");
                                    // MediaManager.setDocumentListTab(window.user_id, docs_id);
                                    if (docs_id == "folder") {
                                        MediaManager.setFolderDocumentListTab(window.user_id, docs_id);
                                    } else {
                                        MediaManager.setDocumentListTab(window.user_id, docs_id);
                                    }
                                }
                            }
                        },
                        removedfile: function(file) {
                            var _ref; // Remove file on clicking the 'Remove file' button
                            return (_ref = file.previewElement) != null ?
                                _ref.parentNode.removeChild(file.previewElement) :
                                void 0;
                        }
                    });
                },
                error: function(xhr, ajaxOptions, thrownError) {},
                async: true
            });
        },
        setAudioUploadTab: function() {
            $('.auds_upload').show();
            $('.user_uploaded_auds').hide();
            $('.auds_html').hide();
            $('.trashed_auds').hide();
            $('.recent_uploads_auds').hide();
            $('input[name="audName"]').addClass('hide');
            $.ajax({
                type: "POST",
                cache: true,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: site_url + '/powerpanel/media/set_audio_uploader',
                dataType: "html",
                success: function(data) {
                    $('.auds_upload').html(data);
                    var maxfilesexceeded = false;
                    var auds_id = false;
                    var success = false;
                    $("#my-dropzone-audio").dropzone({
                        acceptedFiles: "audio/*",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: site_url + '/powerpanel/media/upload_audios',
                        maxFiles: 15, // Number of files at a time
                        maxFilesize: 20, //in MB
                        clickable: true,
                        maxfilesexceeded: function(file) {
                            maxfilesexceeded = true;
                        },
                        success: function(response) {
                            auds_id = response.xhr.response;
                            if (response.status == "success") {
                                success = true;
                            }
                        },
                        queuecomplete: function(file) {
                            if (success) {
                                if (maxfilesexceeded) {
                                    toastr.options = {
                                        "closeButton": true,
                                        "debug": false,
                                        "positionClass": "toast-top-right",
                                        "onclick": null,
                                        "showDuration": "1000",
                                        "hideDuration": "1000",
                                        "timeOut": "5000",
                                        "extendedTimeOut": "1000",
                                        "showEasing": "swing",
                                        "hideEasing": "linear",
                                        "showMethod": "fadeIn",
                                        "hideMethod": "fadeOut"
                                    }
                                    toastr.error("Only 15 document(s) are uploaded others are not uploaded");
                                } else {
                                    toastr.options = {
                                        "closeButton": true,
                                        "debug": false,
                                        "positionClass": "toast-top-right",
                                        "onclick": null,
                                        "showDuration": "1000",
                                        "hideDuration": "1000",
                                        "timeOut": "5000",
                                        "extendedTimeOut": "1000",
                                        "showEasing": "swing",
                                        "hideEasing": "linear",
                                        "showMethod": "fadeIn",
                                        "hideMethod": "fadeOut"
                                    }
                                    toastr.success("Document(s) are successfully uploaded.");
                                    MediaManager.setAudioListTab(window.user_id, auds_id);
                                }
                            }
                        },
                        removedfile: function(file) {
                            var _ref; // Remove file on clicking the 'Remove file' button
                            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                        },
                    });
                },
                error: function(xhr, ajaxOptions, thrownError) {},
                async: true
            });
        },
        setDocumentListTab: function(userid, doc_id = false) {
            
            $(".loader").show();
            $.ajax({
                type: "POST",
                cache: true,
                url: site_url + "/powerpanel/media/user_uploaded_docs",
                dataType: "html",
                data: {
                    userid: userid
                },
                success: function(data) {
                   
                    $(".loader").hide();
                    var lastPopedPopover;
                    $(".popovers").popover();
                    // close last displayed popover
                    $(document).on("click.bs.popover.data-api", function(e) {
                        if (lastPopedPopover) {
                            lastPopedPopover.popover("hide");
                        }
                    });
                    $(".docs_upload").hide();
                    $(".docs_html").hide();
                    $(".trashed_docs").hide();
                    $(".tab_6_4").show();
                    $(".user_uploaded_docs").show();
                    $(".user_uploaded_docs").html(data);
                     $(".tab_6_3 ul li a").removeClass("active");
                    $("#user_uploaded_docs").addClass("active");
                    var multiple_selection = $(".document-multiple-selection").data("multiple");
                    if (multiple_selection == false || multiple_selection == undefined) {
                        $("#note").text("You can insert only one document.");
                    } else {
                        $("#note").text(
                            'Please select the document(s) and click on "Insert Document(s)" button to proceed.'
                        );
                    }
                    if (doc_id != false) {
                        var docIDs = doc_id;
                    } else {
                        var docIDs = $('input[name="doc_id"]').val();
                    }
                    MediaManager.selectDocument(docIDs);
                    $('input[name="docName"]').removeClass("hide");
                    $('input[name="docName"]').on('keyup', function() {
                        if ($(this).val().length % 3 == 0) {
                            var docName = $(this).val();
                            MediaManager.searchByDocName(docName);
                        }
                    });
                },
                error: function(xhr, ajaxOptions, thrownError) {},
                async: true
            });
        },
        setFolderAudioListTab: function (userid, audio_id = false, afterNewUploaded = false) {
            $('.tab_6_3 ul li a').removeClass('active');
            $('#folder_uploaded_audios').addClass('active');
            $('.loader').show();
            $.ajax({
                type: "POST",
                cache: true,
                url: site_url + '/powerpanel/media/folder_uploaded_audios',
                dataType: "json",
                data: {
                    'userid': userid
                },
                success: function (data) {
                    $('.loader').hide();
                    var lastPopedPopover;
                    $('.popovers').popover();
                    // close last displayed popover
                    $(document).on('click.bs.popover.data-api', function (e) {
                        if (lastPopedPopover) {
                            lastPopedPopover.popover('hide');
                        }
                    });
                    $('.audios_upload').hide();
                    $('.audios_html').hide();
                    $('.trashed_audios').hide();
                    $('.tab_6_4').show();
                    $('.user_uploaded_audios').show();
                    $('.user_uploaded_audios').html(data.Doc_html);
                    var multiple_selection = $('.multiple-selection').data('multiple');
                    if (multiple_selection == false || multiple_selection == undefined) {
                        $('#note').text('Please select the audio and click on "Insert Media" button to proceed. You can insert only one audio.');
                    } else {
                        $('#note').text('Please select the audio(s) and click on "Insert Media" button to proceed.');
                    }

                    var data_id = $('#control_id').val();

                    if (audio_id != false) {
                        var audioIDs = audio_id;
                        if (!ckOpen) {

                            if (data_id == 'Composer_audio') {
                                var audioIDs = $('#frmSectionOnlyAudio').find('.imgip1').val();
                            } else {
                                var oldaudioIDs = $('input[name="audio_id"]').val();
                            }

                            if (oldaudioIDs != "") {
                                audioIDs = oldaudioIDs + ',' + audio_id;
                            }
                        }
                    } else {
                        if (data_id == 'Composer_audio') {
                            var audioIDs = $('#frmSectionOnlyAudio').find('.imgip1').val();
                        } else {
                            var audioIDs = $('input[name="audio_id"]').val();
                        }

                    }

                    if (typeof audioIDs !== "undefined" && audioIDs != "") {
                        var arr = $.unique(audioIDs.split(','));
                        audioIDs = arr.join(",");
                    }

                    if (!ckOpen || afterNewUploaded) {
                        MediaManager.selectAudio(audioIDs);
                    }
                    $('input[name="audioName"]').removeClass('hide');
                    $('input[name="audioName"]').keyup(function () {
                        if (($(this).val().length % 3) == 0) {
                            var audioName = $(this).val();
                            MediaManager.searchByAudioName(audioName);
                            $('#audio_page_no').val(1);
                        }
                    });
                    if (sidebargallery) {
                        $(".title_section #insert_audio").hide();
                        $(".title_section #delete_audio").hide();
                         $('input[name="audioName"]').addClass('hide');
                    } else {
                        $(".title_section #insert_audio").show();
                        $(".title_section #delete_audio").hide();
                          $('input[name="audioName"]').addClass('hide');
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {},
                complete: function () {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                async: true

            });
        },
        setFolderDocumentListTab: function (userid, doc_id = false, afterNewUploaded = false,folder_id=false) {
            $('.tab_6_3 ul li a').removeClass('active');
            $('#folder_uploaded_docs').addClass('active');
            $('.loader').show();
            $.ajax({
                type: "POST",
                cache: true,
                url: site_url + '/powerpanel/media/folder_uploaded_docs',
                dataType: "json",
                data: {
                    'userid': userid
                },
                success: function (data) {
                    $('.loader').hide();
                    var lastPopedPopover;
                    $('.popovers').popover();
                    // close last displayed popover
                    $(document).on('click.bs.popover.data-api', function (e) {
                        if (lastPopedPopover) {
                            lastPopedPopover.popover('hide');
                        }
                    });
                    $('.docs_upload').hide();
                    $('.docs_html').hide();
                    $('.trashed_docs').hide();
                    $('.tab_6_4').show();
                    $('.user_uploaded_docs').show();
                    $('.user_uploaded_docs').html(data.Doc_html);
                    var multiple_selection = $('.multiple-selection').data('multiple');
                    if (multiple_selection == false || multiple_selection == undefined) {
                        $('#note').text('Please select the document and click on "Insert Media" button to proceed. You can insert only one document.');
                    } else {
                        $('#note').text('Please select the document(s) and click on "Insert Media" button to proceed.');
                    }

                    var data_id = $('#control_id').val();

                    if (doc_id != false) {
                        var docIDs = doc_id;
                        if (!ckOpen) {

                            if (data_id == 'Composer_doc') {
                                var docIDs = $('#frmSectionOnlyDocument').find('.imgip1').val();
                            } else {
                                var olddocIDs = $('input[name="doc_id"]').val();
                            }

                            if (olddocIDs != "") {
                                docIDs = olddocIDs + ',' + doc_id;
                            }
                        }
                    } else {
                        if (data_id == 'Composer_doc') {
                            var docIDs = $('#frmSectionOnlyDocument').find('.imgip1').val();
                        } else {
                            var docIDs = $('input[name="doc_id"]').val();
                        }

                    }

                    if (typeof docIDs !== "undefined" && docIDs != "") {
                        var arr = $.unique(docIDs.split(','));
                        docIDs = arr.join(",");
                    }
                    if (folder_id != false) {
                        FolderDocument(folder_id, docIDs);
                    }

                    if (!ckOpen || afterNewUploaded) {
                        MediaManager.selectDocument(docIDs);
                    }
                    $('input[name="docName"]').removeClass('hide');
                    $('input[name="docName"]').keyup(function () {
                        if (($(this).val().length % 3) == 0) {
                            var docName = $(this).val();
                            MediaManager.searchByDocName(docName);
                            $('#doc_page_no').val(1);
                        }
                    });
                    // if (sidebargallery) {
                    //     $(".title_section #insert_document").hide();
                    //     $(".title_section #delete_document").hide();
                    //      $('input[name="docName"]').addClass('hide');
                    // } else {
                    //     $(".title_section #insert_document").show();
                    //     $(".title_section #delete_document").hide();
                    //       $('input[name="docName"]').addClass('hide');
                    // }
                },
                error: function (xhr, ajaxOptions, thrownError) {},
                complete: function () {
                    $('[data-toggle="tooltip"]').tooltip();
                },
                async: true

            });
        },
        setAudioListTab: function(userid, aud_id = false) {
            $('.tab_6_3 ul li a').removeClass('active');
            $('#user_uploaded_auds').addClass('active');
            $('.loader').show();
            $.ajax({
                type: "POST",
                cache: true,
                url: site_url + '/powerpanel/media/user_uploaded_auds',
                dataType: "html",
                data: {
                    'userid': userid
                },
                success: function(data) {
                    $('.loader').hide();
                    var lastPopedPopover;
                    $('.popovers').popover();
                    // close last displayed popover
                    $(document).on('click.bs.popover.data-api', function(e) {
                        if (lastPopedPopover) {
                            lastPopedPopover.popover('hide');
                        }
                    });
                    $('.auds_upload').hide();
                    $('.auds_html').hide();
                    $('.trashed_auds').hide();
                    $('.tab_6_4').show();
                    $('.user_uploaded_auds').show();
                    $('.user_uploaded_auds').html(data);
                    var multiple_selection = $('.multiple-selection').data('multiple');
                    if (multiple_selection == false || multiple_selection == undefined) {
                        $('#note').text('Please select the audio and click on "Insert Media" button to proceed. You can insert only one audio.');
                    } else {
                        $('#note').text('Please select the audio(s) and click on "Insert Media" button to proceed.');
                    }
                    if (aud_id != false) {
                        var audIDs = aud_id;
                    } else {
                        var audIDs = $('input[name="aud_id"]').val();
                    }
                    MediaManager.selectAudio(audIDs);
                    $('input[name="audName"]').removeClass('hide');
                    $('input[name="audName"]').keyup(function() {
                       if( ($(this).val().length % 3) == 0){
                        var audName = $(this).val();
                        MediaManager.searchByAudioName(audName);
                       }
                    });
                },
                error: function(xhr, ajaxOptions, thrownError) {},
                async: true
            });
        },
        emptyTrash: function(mediaType = false) {

            var trashTabs = {
                Image: "MediaManager.setTrashedImageTab(window.user_id);",
                Video: "MediaManager.setTrashedVideoTab(window.user_id);",
                Document: "MediaManager.setDocumentListTab(window.user_id);",
                Audio: "MediaManager.setAudioListTab(window.user_id);"
            };

            $("#emptyTrashMedia" + mediaType).modal("show");
            $(".empty_trash_" + mediaType)
                .off("click")
                .on('click', function() {
                    if (mediaType) {
                        $(".loader").show();
                        $.ajax({
                            type: "POST",
                            cache: true,
                            url: site_url + "/powerpanel/media/empty_trash_" + mediaType,
                            data: {
                                mediaType: mediaType
                            },
                            success: function(data) {
                                $(".loader").hide();
                                eval(trashTabs[mediaType]);
                            },
                            complete: function(data) {},
                            error: function(xhr, ajaxOptions, thrownError) {},
                            async: true
                        }).done(function() {
                            MediaManager.showToaster('success', mediaType + "s are removed successfully.");
                        });
                    }
                    $("#emptyTrashMedia" + mediaType).modal("hide");
                });

        },
        getImageDetails: function(image_id) {

            $('body').loader(loaderConfig);//$(".loader").show();
            $('.user_uploaded').hide();
            $(".recent_uploads").hide();
            $('.image_details').show();

            $.ajax({
                type: "POST",
                cache: true,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                data: { image_id: image_id },
                url: site_url + "/powerpanel/media/get_image_details",
                dataType: "html",
                success: function(data) {
                    $.loader.close(true);//$(".loader").hide();
                    $('.image_details').html(data);
                    $('#save_details').on('click', function() {

                        var image_title = $('input[name="image_title"]').val();
                        var image_caption = $('textarea[name="image_caption"]').val();
                        var image_alt = $('input[name="image_alt"]').val();

                        if (image_title != '' && image_caption != '' && image_alt != '') {
                            $.ajax({
                                type: "POST",
                                cache: true,
                                headers: {
                                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                                },
                                url: site_url + "/powerpanel/media/save_image_details",
                                dataType: "json",
                                data: {
                                    'image_title': image_title,
                                    'image_caption': image_caption,
                                    'image_alt': image_alt,
                                    'image_id': image_id
                                },
                                success: function(data) {

                                    if (data) {
                                        MediaManager.showToaster('success', 'Details saved successfully.');
                                    } else {
                                        MediaManager.showToaster('error', 'Oops! Something Went Wrong');
                                    }

                                },
                                error: function(xhr, ajaxOptions, thrownError) {},
                                async: true
                            });
                        } else {
                            MediaManager.showToaster('error', 'Please fill all the details');
                        }
                    });

                },
                error: function(xhr, ajaxOptions, thrownError) {},
                async: true
            });

        },
        cropImage: function(image_id) {

            $(".recent_uploads").hide();
            $('.user_uploaded').hide();
            $('.image_cropper').show();
            $('body').loader(loaderConfig);            

            $.ajax({
                type: "POST",
                cache: true,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                url: site_url + "/powerpanel/media/crop_image",
                dataType: "HTML",
                data: {
                    'image_id': image_id
                },
                success: function(data) {
                    $('.image_cropper').html(data);
                    $.loader.close(true);//$(".loader").hide();
                    $.getScript(site_url + '/resources/global/plugins/image-cropper/jquery-cropper.js');
                },
                error: function(xhr, ajaxOptions, thrownError) {},
                async: true
            });

        },
        showToaster: function(type, message) {

            if (type == "success") {

                toastr.options = {
                    closeButton: true,
                    debug: false,
                    positionClass: "toast-top-right",
                    onclick: null,
                    showDuration: "1000",
                    hideDuration: "1000",
                    timeOut: "5000",
                    extendedTimeOut: "1000",
                    showEasing: "swing",
                    hideEasing: "linear",
                    showMethod: "fadeIn",
                    hideMethod: "fadeOut"
                };
                toastr.success(message);

            } else {

                toastr.options = {
                    closeButton: true,
                    debug: false,
                    positionClass: "toast-top-right",
                    onclick: null,
                    showDuration: "1000",
                    hideDuration: "1000",
                    timeOut: "5000",
                    extendedTimeOut: "1000",
                    showEasing: "swing",
                    hideEasing: "linear",
                    showMethod: "fadeIn",
                    hideMethod: "fadeOut"
                };
                toastr.error(message);
            }
        },
        saveCroppedImage: function(image, image_id, overwrite) {
            $('body').loader(loaderConfig);
            $("#save_as_new").off('click');
            $("#save_and_overwrite").off('click');
            
            $.ajax({
                type: "POST",
                cache: true,
                url: site_url + "/powerpanel/media/save_cropped_image",
                data: {
                    image: image,
                    image_id: image_id,
                    overwrite: overwrite
                },
                success: function(data) {
                    if (data) {                        
                        if (overwrite) {
                            MediaManager.cropImage(image_id);
                        }else {
                            MediaManager.setMyUploadTab(window.user_id, data);
                        }
                        MediaManager.showToaster('success', 'Image Cropped Successfully');
                    } else {
                        MediaManager.showToaster('error', 'Oops! Something went wrong');
                    }
                    $.loader.close(true);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError);
                },
                async: true
            });

        },
        init: function() {
            var selectedVideo = $("#video_name").val();
            if (
                selectedVideo == "" ||
                selectedVideo == undefined ||
                selectedVideo == null
            ) {
                $("#video_name").hide();
            }
        }
    };
})();

$(document).on("click", ".dz-complete a", function() {
    $("#my-dropzone file").trigger("change");
});

$(document).ready(function() {
    MediaManager.init();
    $(".tab_6_3 ul.nav li a").on('click', function() {
        $(".tab_6_3 ul.nav li a").removeClass("active");
        var menu_id = $(this).attr("id");
        $("#" + menu_id).addClass("active");
    });
    $(".remove_multiple_images").on('click', function() {
        MediaManager.removeMultipleImages($(this).val());
    });
    $(".remove_multiple_videos").on('click', function() {
        MediaManager.removeMultipleVideos($(this).val());
    });
    $(".remove_multiple_document").on('click', function() {
        MediaManager.removeMultipleDocuments($(this).val());
    });
     $('.remove_multiple_audio').click(function() {
        MediaManager.removeMultipleAudios($(this).val());
    });
    $(".restore_multiple_images").on('click', function() {
        MediaManager.restoreMultipleImages();
    });
    $(".restore_multiple_videos").on('click', function() {
        MediaManager.restoreMultipleVideos();
    });
    $(".restore_multiple_documents").on('click', function() {
        MediaManager.restoreMultipleDocument();
    });
    $(".remove_multiple_document_permanently").on('click', function() {
        MediaManager.removeMultipleDocuments("trash");
    });
     $('.remove_multiple_audio_permanently').click(function() {
        MediaManager.removeMultipleAudios('trash');
    });
    $(".remove_multiple_images_permanently").on('click', function() {
        MediaManager.removeMultipleImages("trash");
    });
    $(".remove_multiple_videos_permanently").on('click', function() {
        MediaManager.removeMultipleVideos("trash");
    });
});

function openAlertDialogForImage(message = null) {
    if (message) {
        $("#alertModalForImage .alert_msg").html(message);
        $("#alertModalForImage").modal("show");
    }
}

function openAlertDialogForVideo(message = null) {
    if (message) {
        $("#alertModalForVideo .alert_msg").html(message);
        $("#alertModalForVideo").modal("show");
    }
}

function openAlertDialogForDocument(message = null) {
    if (message) {
        $("#alertModalForDocument .alert_msg").html(message);
        $("#alertModalForDocument").modal("show");
    }
}
function openAlertDialogForAudio(message = null) {
    if (message) {
        $("#alertModalForAudio .alert_msg").html(message);
        $('#alertModalForAudio').modal('show');
    }
}

$(document).ready(function() {
    $(".fileinput.fileinput-new").each(function(index) {
        if (
            $(this)
            .find("input[name^='img_id']")
            .val() == ""
        ) {
            $(this)
                .find(".removeimg")
                .hide();
            $(this)
                .find(".overflow_layer")
                .css("display", "none");
        } else {
            $(this)
                .find(".removeimg")
                .show();
            $(this)
                .find(".overflow_layer")
                .css("display", "block");
        }
    });

    $(document).on("click", ".removeimg", function(e) {
        var $parentImgDiv = $(this).parents(".fileinput.fileinput-new");
        $parentImgDiv.find("input[name^='img_id']").val("");
        $parentImgDiv.find("input[name='image_url']").val("");
        $parentImgDiv
            .find("div img")
            .attr("src", site_url + "/resources/images/upload_file.gif");
        if ($parentImgDiv.find("input[name^='img_id']").val() == "") {
            $parentImgDiv.find(".removeimg").hide();
            $parentImgDiv.find(".overflow_layer").css("display", "none");
        } else {
            $parentImgDiv.find(".removeimg").show();
            $parentImgDiv.find(".overflow_layer").css("display", "block");
        }
    });
});
function doSomethingElse() {
    $("#frmGallery .image_thumb .fileinput .input-group a").attr('data-multiple','false');
 }

 $(document).ready(function () {
    $(document).on("click", ".FoldeCreatePopup", function () {
        $('#FoldeCreatePopupModel').modal('show');
    });

    $(document).on("click", ".foldercreateids", function () {

        var foldername = $("#foldername").val();
        var foldertype = $("#foldertype").val();
        if (foldername == '') {
            alert("Please enter folder name.");
            return false;
        }
        var Folder_URL = window.site_url + "/powerpanel/folderdata";
        var name = $("#foldername").val();
        $.ajax({
            type: 'GET',
            url: Folder_URL,
            data: {
                name: name,
                type: foldertype
            },
            success: function (response) {
                var data = response.split("@@@");
                if (data[0] == 0) {
                    $('#FoldeCreatePopupModel').modal('toggle');
                    $("#foldername").val('');
                    alert('Folder name already exits.');
                    return false;
                } else {
                    $('#FoldeCreatePopupModel').modal('toggle');
                    $("#foldername").val('');
                    $("#folderreplace_" + data[1]).html(data[0]);
                }
            }
        });
    });
    
});
$(document).on('click', '.doc-copy', function () {
    $('body').append('<input type="text" value="' + $(this).data('docurl') + '" id="docUrl"></input>');
    var copyText = document.getElementById("docUrl");
    copyText.select();
    document.execCommand("copy");
    $('#docUrl').remove();
    toastr.success("Document URL copied to clipboard!");
});
$(document).on('click', '.audio-copy', function () {
    $('body').append('<input type="text" value="' + $(this).data('audiourl') + '" id="audioUrl"></input>');
    var copyText = document.getElementById("audioUrl");
    copyText.select();
    document.execCommand("copy");
    $('#audioUrl').remove();
    toastr.success("Audio URL copied to clipboard!");
});
function Documentfolderselect(id){
    $(".doc_folder_id").val(id);
}
function FolderDocument(fid, imgIDs = false) {
    var Doument_URL = window.site_url + "/powerpanel/GetFolderDocument";
    $.ajax({
        type: 'GET',
        url: Doument_URL,
        data: {
            fid: fid,
            imgIDs: imgIDs
        },
        success: function (data) {
            $(".title_section #delete_document").show();
              $('input[name="docName"]').removeClass('hide');
            $("#folderdocumentreplace").html(data);
        }
    });
}
function Imagefolderselect(id){
    $(".img_folder_id").val(id);
}
function Documentfolderselect(id){
    $(".doc_folder_id").val(id);
}
function Audiofolderselect(id){
    $(".audio_folder_id").val(id);
}
function FolderImages(fid, imgIDs = false) {
var Image_URL = window.site_url + "/powerpanel/FolderImages";
$.ajax({
    type: 'GET',
    url: Image_URL,
    data: {
        fid: fid,
        imgIDs: imgIDs
    },
    success: function (data) {
        $(".title_section #delete_image").show();
          $('input[name="imageName"]').removeClass('hide');
        $("#folderimagereplace").html(data);
    }
});
}
function FolderDocument(fid, imgIDs = false) {
var Doument_URL = window.site_url + "/powerpanel/GetFolderDocument";
$.ajax({
    type: 'GET',
    url: Doument_URL,
    data: {
        fid: fid,
        imgIDs: imgIDs
    },
    success: function (data) {
        $(".title_section #delete_document").show();
          $('input[name="docName"]').removeClass('hide');
        $("#folderdocumentreplace").html(data);
    }
});
}
function FolderAudio(fid, imgIDs = false) {
var Audio_URL = window.site_url + "/powerpanel/GetFolderAudio";
$.ajax({
    type: 'GET',
    url: Audio_URL,
    data: {
        fid: fid,
        imgIDs: imgIDs
    },
    success: function (data) {
        $(".title_section #delete_audio").show();
         $('input[name="audioName"]').removeClass('hide');
        $("#folderaudioreplace").html(data);
    }
});
}