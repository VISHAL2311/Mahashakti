var ignoreItems = '';
var selectedItems = '';
var recTitle = [];

var BoatModule = function () {
    // public functions
    return {
        reInitSortable: function () {
            $("#section-container").sortable('destroy');
            $("#section-container").sortable({
                placeholder: "ui-state-highlight",
                handle: '.fa-arrows-alt'
            });
        },
        moduleSectionsBoat: function (caption, config, configTxt, recids, recTitle, edit, template, layoutType, extra_class) {
            recids = recids.split(',');
            recTitle = recTitle.split(',');

            if (recids == '') {
                recids = [];
                recTitle = [];
            }

            recids = $.unique(recids);
            recTitle = $.unique(recTitle);

            var section = '';

            if (edit == 'N') {
                var iCount = 'item-' + ($('.ui-state-default').length + 1);

                section += '<div class="ui-state-default">';
                section += '<i title="Drag" class="action-icon move fa fa-arrows-alt"></i>';
                section += '<a href="javascript:;" data-filter="' + template + '" class="close-btn" title="Delete">';
                section += '<i class="action-icon delete fa fa-trash"></i>';
                section += '</a>';
                section += '<a href="javascript:;" data-filter="' + template + '" title="Edit" data-id="' + iCount + '" class="boat-module">';
                section += '<i class="action-icon edit fa fa-pencil"></i>';
                section += '</a>';
                section += '<div class="clearfix"></div>';
                section += '<div class="section-item defoult-module module" data-editor="' + iCount + '">';
                section += '<div class="col-md-12">';
                section += '<label><b>' + caption + '</b></label>';
                section += '<ul class="record-list">';
                $.each(recids, function (index, value) {
                    section += '<li data-id="' + value + '" id="' + value + '-item-' + iCount + '">' + recTitle[index] + '<a href="javascript:;" class="close-icon" title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a></li>';
                });
                section += '</ul>';
                section += '<a data-id="' + iCount + '" data-filter="' + template + '" title="Add more" class="add-link boat-module" href="javascript:;">';
                section += '<i class="fa fa-plus"></i>&nbsp;Add more';
                section += '</a>';
                section += '</div>';
                section += '<input id="' + iCount + '" data-extclass="' + extra_class + '" data-filter="' + template + '" type="hidden" data-config="' + config + '" data-layout="' + layoutType + '" data-caption="' + caption + '" data-type="module" value="boat" />';
                section += '<div class="clearfix"></div>';
                section += '</div>';

                if ($('#section-container .ui-state-default').length > 0) {
                    $(section).insertAfter($('#section-container .ui-state-default:last'));
                } else {
                    $('#section-container').append(section);
                }
            } else {
                var boatIds = [];
                var boatTitles = [];
                var boatCustomized = [];
                var boatDescription = [];


                $('.section-item[data-editor=' + edit + '] li').each(function (key, val) {
                    var iId = $(this).data('id');
                    boatIds.push(iId);

                    var iTitle = $(this).text();
                    boatTitles.push(iTitle);

                    var Icustomized = $(this).data('customized');
                    if (typeof Icustomized != 'undefined') {
                        boatCustomized.push(Icustomized);
                    }

                    
                    var Idescription = $(this).data('description');
                    if (typeof Idescription != 'undefined') {
                        boatDescription.push(Idescription.toString());
                    }


                });

                $.each(recids, function (index, value) {
                    boatIds.push(value);
                    boatTitles.push(recTitle[index]);
                    boatCustomized.push(false);
                    boatDescription.push('');
                });

                section += '<div class="col-md-12">';
                section += '<label><b>' + caption + '</b></label>';
                section += '<ul class="record-list">';
                $.each(boatIds, function (index, value) {
                    if (value != '') {
                        section += '<li data-customized="' + boatCustomized[index] + '"  data-description="' + boatDescription[index] + '" data-id="' + value + '" id="' + value + '-item-' + edit + '">' + boatTitles[index]  + '<a href="javascript:;" class="close-icon" title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a></li>';
                    }
                });
                section += '</ul>';
                section += '<a data-id="' + edit + '" data-filter="' + template + '" title="Add more" class="add-link boat-module" href="javascript:;">';
                section += '<i class="fa fa-plus"></i>&nbsp;Add more';
                section += '</a>';
                section += '</div>';
                section += '<input id="' + edit + '" data-extclass="' + extra_class + '" data-filter="' + template + '" type="hidden" data-config="' + config + '" data-layout="' + layoutType + '" data-caption="' + caption + '" data-type="module" value="boat" />';
                section += '<div class="clearfix"></div>';
                $('#section-container').find('div[data-editor=' + edit + ']').html(section);
            }
        },
        boatTemplate: function (val, config, template, edit, configTxt, layout, extra_class) {
            var section = '';

            if (edit == 'N') {
                var iCount = 'item-' + ($('.ui-state-default').length + 1);
                section += '<div class="ui-state-default">';
                section += '<i title="Drag" class="action-icon move fa fa-arrows-alt"></i>';
                section += '<a href="javascript:;" data-filter="' + template + '" class="close-btn" title="Delete">';
                section += '<i class="action-icon delete fa fa-trash"></i>';
                section += '</a>';
                section += '<a href="javascript:;" data-filter="' + template + '" title="Edit" data-id="' + iCount + '" class="boat-template">';
                section += '<i class="action-icon edit fa fa-pencil"></i>';
                section += '</a>';
                section += '<div class="clearfix"></div>';
                section += '<div class="defoult-module section-item boatTemplate" data-editor="' + iCount + '">';
                section += '<div class="col-md-12"><label><b>' + val + '</b></label><ul><li>Template: ' + template + '</li></ul></div>';
                section += '<input data-extclass="' + extra_class + '" id="' + iCount + '" data-layout="' + layout + '" data-type="' + template + '" data-config="' + config + '" type="hidden" class="txtip" value="' + val + '"/>';
                section += '<div class="clearfix"></div>';
                section += '</div>';
                section += '</div>';

                if ($('#section-container .ui-state-default').length > 0) {
                    $(section).insertAfter($('#section-container .ui-state-default:last'));
                } else {
                    $('#section-container').append(section);
                }

            } else {
                section += '<div class="col-md-12"><label><b>' + val + '</b></label><ul><li>Template: ' + template + '</li></ul></div>';
                section += '<input id="' + edit + '" data-extclass="' + extra_class + '" data-layout="' + layout + '" data-type="' + template + '" data-config="' + config + '" type="hidden" class="txtip" value="' + val + '"/>';
                section += '<div class="clearfix"></div>';
                $('#section-container').find('div[data-editor=' + edit + ']').html(section);
            }
        },
        submitFrmSectionBoatModuleTemplate: function () {
            if ($('#frmSectionBoatModuleTemplate').validate().form()) {
                var edit = $('#frmSectionBoatModuleTemplate input[name=editing]').val() != '' ? $('#frmSectionBoatModuleTemplate input[name=editing]').val() : 'N';
                $('#no-content').addClass('hide');
                $('#has-content').removeClass('hide');
                var val = $('#frmSectionBoatModuleTemplate input[name=section_title]').val();
                var extra_class = $('#frmSectionBoatModuleTemplate input[name=extra_class]').val();
                var template = $('#frmSectionBoatModuleTemplate input[name=template]').val();
                var config = $('#frmSectionBoatModuleTemplate select[name=section_config]').val();
                var configTxt = $('#frmSectionBoatModuleTemplate .config option:selected').text();
                var layout = $('#frmSectionBoatModuleTemplate select[name=layoutType]').val();
                BoatModule.boatTemplate(val, config, template, edit, configTxt, layout, extra_class);
                $('#sectionBoatModuleTemplate').modal('hide');
            }
        },
        submitFrmSectionBoatModule: function () {
            if ($('#frmSectionBoatModule').validate().form()) {
                var edit = $('#frmSectionBoatModule input[name=editing]').val() != '' ? $('#frmSectionBoatModule input[name=editing]').val() : 'N';
                $('#no-content').addClass('hide');
                $('#has-content').removeClass('hide');
                var template = $('#frmSectionBoatModule input[name=template]').val();
                var extra_class = $('#frmSectionBoatModule input[name=extra_class]').val();
                var imgCaption = $('#frmSectionBoatModule input[name=section_title]').val();
                var config = $('#frmSectionBoatModule .config').val();
                var configTxt = $('#frmSectionBoatModule .config option:selected').text();
                var layoutType = $('#frmSectionBoatModule select[name=layoutType]').val();
                var recids = $('#frmSectionBoatModule input[name=selectedIds]').val();
                var recTitle = $('#frmSectionBoatModule input[name=selectedTitles]').val();

                BoatModule.moduleSectionsBoat(imgCaption, config, configTxt, recids, recTitle, edit, template, layoutType, extra_class);
                BoatModule.reInitSortable();
                $('#sectionBoatModule').modal('hide');
            }
        }
    };
}();

var BoatDataTable = function () {
    // public functions
    return {
        //main function
        init: function (from, to) {
            var sort = $('#sectionBoatModule #columns').val();
            var ajaxUrl = site_url + '/powerpanel/boat/get_builder_list';

            jQuery.ajax({
                type: "POST",
                url: ajaxUrl,
                dataType: 'JSON',
                data: {
                    critaria: $('#frmSectionBoatModule input[name=template]').val(),
                    columns: sort[0],
                    order: sort[1],
                    catValue: $('#sectionBoatModule #category-id').val(),
                    status: '',
                    searchValue: $('#sectionBoatModule #searchfilter').val(),
                    start: from,
                    length: to,
                    ignore: ignoreItems,
                    selected: selectedItems
                },
                async: false,
                success: function (result) {
                    $('#sectionBoatModule #record-table').append(result.data);
                    $('input[name=total_records]').val(result.recordsTotal);
                    $('input[name=found]').val(result.found);
                    if(result.recordsTotal == 0 || result.found == 0) {
                        $('#frmSectionBoatModule').find('.addSection').attr('disabled','disabled');
                    }else{
                        $('#frmSectionBoatModule').find('.addSection').removeAttr('disabled');
                    }

                },
                error: function (req, err) {
                    console.log('error:' + err);
                }
            });
        },
        getCategory: function () {

            var ajaxUrl = site_url + '/powerpanel/boat-category/get_builder_list';
            jQuery.ajax({
                type: "POST",
                url: ajaxUrl,
                dataType: 'HTML',
                async: false,
                success: function (result) {
                    $('#sectionBoatModule #category-id').html(result);
                }
            });

        }
    };
}();

var validateSectionBoat = function () {
    var handleSectionBoat = function () {
        $("#frmSectionBoatModule").validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            ignore: [],
            rules: {
                section_title: {
                    required: true,
                    noSpace: true
                },
                // section_config: {
                //     required: true,
                //     noSpace: true
                // },
                // layoutType: {
                //     required: true,
                // },
                'delete[]': {
                    required: {
                        depends: function () {
                            return $('#frmSectionBoatModule input[name="editing"]').val() == '';
                        }
                    }
                }
            },
            messages: {
                section_title: "Caption is required",
                section_config: "Configurations is required",
                layoutType: "Please select layout",
                'delete[]': "Please select at least one record",
            },
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else if (element.hasClass('chkChoose')) {
                    error.insertBefore($('#frmSectionBoatModule .table-container .table:first'));
                } else if (element.attr('class') == 'ck-area') {
                    error.insertAfter(element.next('.ck-editor'));
                } else if (element.attr('name') == 'selector') {
                    error.insertAfter(element.closest('ul'));
                } else {
                    error.insertAfter(element);
                }
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                var errors = validator.numberOfInvalids();
                if (errors) {
                    $.loader.close(true);
                }
                $('.alert-danger', $('#frmSectionBoatModule')).show();
            },
            highlight: function (element) { // hightlight error inputs
                if ($(element).hasClass('chkChoose')) {
                    $(element).closest('td').addClass('has-error'); // set error class to the control group       
                } else {
                    $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
                }
            },
            unhighlight: function (element) {
                if ($(element).hasClass('chkChoose')) {
                    $(element).closest('td').removeClass('has-error');
                } else {
                    $(element).closest('.form-group').removeClass('has-error'); // set error class to the control group
                }
            },
            submitHandler: function (form) {
                BoatModule.submitFrmSectionBoatModule();
                return false;
            }
        });

        $('#frmSectionBoatModule input').keypress(function (e) {
            if (e.which == 13) {
                BoatModule.submitFrmSectionBoatModule();
                return false;
            }
        });
    }
    return {
        //main function to initiate the module
        init: function () {
            handleSectionBoat();
        },
        reset: function () {
            var validator = $("#frmSectionBoatModule").validate();
            validator.resetForm();
        }
    };
}();

var boatTemplate = function () {
    var boatTemplate = function () {
        $("#frmSectionBoatModuleTemplate").validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            ignore: [],
            rules: {
                section_title: {
                    required: true,
                    noSpace: true
                },
                // layoutType: {
                //     required: true
                // },
                // section_config: {
                //     required: true,
                //     noSpace: true
                // }
            },
            messages: {
                section_title: {
                    required: "Title is required"
                },
                layoutType: {
                    required: "Layout is required"
                },
                section_config: {
                    required: "Configurations is required"
                }
            },
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else if (element.hasClass('select2')) {
                    error.insertAfter(element.next('span'));
                } else if (element.attr('class') == 'ck-area') {
                    error.insertAfter(element.next('.ck-editor'));
                } else {
                    error.insertAfter(element);
                }
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                var errors = validator.numberOfInvalids();
                if (errors) {
                    $.loader.close(true);
                }
                $('.alert-danger', $('#frmSectionBoatModuleTemplate')).show();
            },
            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error'); // set error class to the control group
            },
            submitHandler: function (form) {
                BoatModule.submitFrmSectionBoatModuleTemplate();
                return false;
            }
        });
        $('#frmSectionBoatModuleTemplate input').keypress(function (e) {
            if (e.which == 13) {
                BoatModule.submitFrmSectionBoatModuleTemplate();
                return false;
            }
        });
    }
    return {
        //main function to initiate the module
        init: function () {
            boatTemplate();
        },
        reset: function () {
            var validator = $("#frmSectionBoatModuleTemplate").validate();
            validator.resetForm();
        }
    };
}();


var range = 5;
var start = 0;
var end = range;

//..Open while add or edit section
var id = '';
var caption = '';
var template = '';

$(document).on('click', '.boat-module', function(event) {
    id = $(this).data('id');
    caption = $(this).text();
    template = $(this).data('filter');
    $('#pgBuiderSections').modal('hide');
    $('#sectionBoatModule').modal({
        backdrop: 'static',
        keyboard: false
    });
    $('#sectionBoatModule [data-dismiss="modal"]').attr( "data-toggle", "" );
    $('#sectionBoatModule [data-dismiss="modal"]').attr( "data-target", "" );
});


$(document).on('click', '.boat', function (event) {
    caption = $(this).text();
    template = $(this).data('filter');
    id = '';
    ignoreItems = '';
    $('#pgBuiderSections').modal('hide');
    $('#sectionBoatModule').modal({
        backdrop: 'static',
        keyboard: false,
        show: true
    });
});

$(document).on('click', '.boat-template', function (event) 
{
    $('#sectionBoatModuleTemplate [data-dismiss="modal"]').attr( "data-toggle", "" );
    $('#sectionBoatModuleTemplate [data-dismiss="modal"]').attr( "data-target", "" );

    $('#pgBuiderSections').modal('hide');
    $('#sectionBoatModuleTemplate').modal({
        backdrop: 'static',
        keyboard: false
    });

    var id = $(this).data('id');
    var layout = '';
    if (typeof id != 'undefined') 
    {
        var extclass = $('#' + id).data('extclass');
        var value = $('#' + id).val();
        layout = $('#' + id).data('layout');
        var config = $('#' + id).data('config');

        $('#frmSectionBoatModuleTemplate input[name=editing]').val(id);
        $('#frmSectionBoatModuleTemplate input[name=section_title]').val($.trim(value));
        $('#frmSectionBoatModuleTemplate select[name=layoutType] option[value=' + layout + ']').prop('selected', true);
        $('#frmSectionBoatModuleTemplate .config option[value=' + config + ']').prop('selected', true);
        $('#frmSectionBoatModuleTemplate input[name=extra_class]').val(extclass);
        $('#frmSectionBoatModuleTemplate .addSection').text('Update');
        $('#frmSectionBoatModuleTemplate #exampleModalLabel b').text('Edit Boat');

    } else {

        var value = $(this).text();
        $('#frmSectionBoatModuleTemplate input[name=editing]').val('');
        $('#frmSectionBoatModuleTemplate input[name=section_title]').val($.trim(value));
        //$('#frmSectionBoatModuleTemplate select[name=layoutType] option:first').prop('selected', true);
        $('#frmSectionBoatModuleTemplate input[name=extra_class]').val('');
        $('#frmSectionBoatModuleTemplate .addSection').text('Add');
        $('#frmSectionBoatModuleTemplate #exampleModalLabel b').text('Add Boat');

        $('#sectionBoatModuleTemplate [data-dismiss="modal"]').attr( "data-toggle", "modal" );
//        $('#sectionBoatModuleTemplate [data-dismiss="modal"]').attr( "data-target", "#pgBuiderSections" );

    }

    $('#frmSectionBoatModuleTemplate').find('input[name=template]').val($(this).data('filter'));

});

$('#frmSectionBoatModuleTemplate').on('submit', function (e) {
    e.preventDefault();
});

//..End Open while add or edit section

$('#sectionBoatModule').on('shown.bs.modal', function () {
    caption = $.trim(caption);
    //$('#datatable_boat_ajax').closest('.col-md-12').loading('start');
    validateSectionBoat.init();
    $(this).find('.group-checkable').prop('checked', false);
    selectedItems = [];
    recTitle = [];
    ignoreItems = [];
    $('#sectionBoatModule input[name=selectedIds]').val(null);
    $('#sectionBoatModule input[name=selectedTitles]').val(null);
     $('select').selectpicker('destroy');
    BoatDataTable.getCategory();
    $('#frmSectionBoatModule #category-id option:first').prop('selected', true);
    $('#frmSectionBoatModule #columns option:selected').prop('selected', false);
    $('#frmSectionBoatModule #columns option[value=varTitle]').prop('selected', true);
    $('#frmSectionBoatModule #columns option[value=asc]').prop('selected', true);
    $('#frmSectionBoatModule input[name=template]').val(template);
    var layout = '';
    if (id != '') {
        caption = $('#' + id).data('caption');
        layout = $('#' + id).data('layout');
        var config = $('#' + id).data('config');
        var extClass = $('#' + id).data('extclass');
        $('#frmSectionBoatModule input[name=editing]').val(id);
        $('#frmSectionBoatModule input[name=extra_class]').val(extClass);
        $('#frmSectionBoatModule select[name=layoutType] option[value=' + layout + ']').prop('selected', true);
        $('#frmSectionBoatModule .config').children('option[value=' + config + ']').prop('selected', true);

        $('.section-item[data-editor=' + id + '] li').each(function (key, val) {
            var iId = $(this).data('id');
            ignoreItems.push(iId);
        });

        $('#sectionBoatModule .addSection').text('Update');
        $('#sectionBoatModule #exampleModalLabel b').text('Update Boat');
    } else {
        $('#frmSectionBoatModule input[name=editing]').val('');
        $('#frmSectionBoatModule input[name=extra_class]').val('');
        //$('#frmSectionBoatModule select[name=layoutType] option:first').prop('selected', true);
        $('#frmSectionBoatModule .config').children('option[value=7]').prop('selected', true);

        $('#sectionBoatModule .addSection').text('Add');
        $('#sectionBoatModule #exampleModalLabel b').text('Boat');

        $('#sectionBoatModule [data-dismiss="modal"]').attr( "data-toggle", "modal" );
//        $('#sectionBoatModule [data-dismiss="modal"]').attr( "data-target", "#pgBuiderSections" );
    }

    $('#frmSectionBoatModule input[name=section_title]').val(caption);
    $('select').selectpicker();
    BoatDataTable.init(start, range);
    $("#frmSectionBoatModule #mcscroll").mCustomScrollbar({
        axis: "y",
        theme: "dark",
        callbacks: {
            onTotalScroll: function () {
                if ($('input[name=found]').val() > 0) {
                    if ($('#sectionBoatModule').find('#record-table tr').length < $('input[name=total_records]').val()) {
                        start += range;
                        end += range;
                        BoatDataTable.init(start, range);
                    }
                }
            }
        }
    });

}).on('hidden.bs.modal', function () {

    range = 10;
    start = 0;
    end = range;
    $('#sectionBoatModule select[name=layoutType] option[class=list]').show();
    $('#sectionBoatModule #record-table').html('');
    $(".record-list").sortable().disableSelection();
     $('#sectionBoatModule select').selectpicker('destroy');
    validateSectionBoat.reset();

});


$('#sectionBoatModuleTemplate').on('shown.bs.modal', function () {
$('#sectionBoatModuleTemplate select').selectpicker('');
    boatTemplate.init();
}).on('hidden.bs.modal', function () {
     $('#sectionBoatModuleTemplate select').selectpicker('destroy');
    boatTemplate.reset();
});

$(document).ajaxStart(function () {
    $('.table-scrollable').loader(loaderConfig);
}).ajaxComplete(function () {
    setTimeout(function () {
        $.loader.close(true);
    }, 500);
});

$(document).on('keyup', '#sectionBoatModule #searchfilter', function () {
    range = 10;
    start = 0;
    end = range;
    $('#sectionBoatModule #record-table').html('');
    BoatDataTable.init(start, range);
});

$(document).on('change', '#sectionBoatModule #columns', function () {
    range = 10;
    start = 0;
    end = range;
    $('#sectionBoatModule #record-table').html('');
    BoatDataTable.init(start, range);
});

$(document).on('change', '#sectionBoatModule #category-id', function () {
    range = 10;
    start = 0;
    end = range;
    $('#sectionBoatModule #record-table').html('');
    BoatDataTable.init(start, range);
});


//Group checkbox checking
$(document).on('change', '#sectionBoatModule .group-checkable', function () {

    if ($(this).prop('checked')) {
        $('#sectionBoatModule #record-table .chkChoose').prop('checked', true);
        $('#sectionBoatModule #record-table .chkChoose').parent().parent().parent().addClass('selected-record');
        $('#sectionBoatModule #record-table .chkChoose:checked').each(function (index, value) {
            var id = $(this).val();
            if (!selectedItems.includes(id)) {
                selectedItems.push(id);
                recTitle.push($(this).data('title'));
            }
        });
    } else {
        $('#sectionBoatModule #record-table .chkChoose').prop('checked', false);
        $('#sectionBoatModule #record-table .chkChoose').parent().parent().parent().removeClass('selected-record');
        selectedItems = [];
        recTitle = [];
    }
    $('#sectionBoatModule input[name=selectedIds]').val(selectedItems);
    $('#sectionBoatModule input[name=selectedTitles]').val(recTitle);
});

$(document).on('change', '#sectionBoatModule #record-table .chkChoose', function () {
    var id = $(this).val();
    if ($(this).prop('checked')) {

        if (!selectedItems.includes(id)) {
            selectedItems.push(id);
            recTitle.push($(this).data('title'));
        }

        $(this).parent().parent().parent().addClass('selected-record');

    } else {
        selectedItems.pop(id);
        recTitle.pop($(this).data('title'));
        $(this).parent().parent().parent().removeClass('selected-record');
    }

    if ($('#sectionBoatModule #record-table .chkChoose:checked').length == $('#sectionBoatModule #record-table tr .chkChoose').length) {
        $('#sectionBoatModule .group-checkable').prop('checked', true);
    } else {
        $('#sectionBoatModule .group-checkable').prop('checked', false);
    }

    $('#sectionBoatModule input[name=selectedIds]').val(selectedItems);
    $('#sectionBoatModule input[name=selectedTitles]').val(recTitle);
});

$(document).on('click', '#sectionBoatModule #record-table tr', function (e) {
    var $cell = $(e.target).closest('td');
    if ($cell.index() > 0) {
        if ($(this).find('.chkChoose').prop('checked')) {
            $(this).find('.chkChoose').prop('checked', false).trigger('change');
            $(this).removeClass('selected-record');
        } else {
            $(this).find('.chkChoose').prop('checked', true).trigger('change');
            $(this).addClass('selected-record');
        }
    }
});

$(document).on('change', '#sectionBoatModule #columns', function () {
    if ($(this).find('option:selected').length > 1) {
        //$('#mCSB_1_container').trigger('click');
    }
});
//..Group checkbox checking