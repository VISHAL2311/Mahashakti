var ignoreItems = '';
var selectedItems = '';
var recTitle = [];

var WorkModule = function () {
    // public functions
    return {
        reInitSortable: function () {
            $("#section-container").sortable('destroy');
            $("#section-container").sortable({
                placeholder: "ui-state-highlight",
                handle: '.fa-arrows-alt'
            });
        },
        moduleSectionsWork: function (caption, config, configTxt, recids, recTitle, edit, template, layoutType, extra_class) {
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
                section += '<a href="javascript:;" data-filter="' + template + '" title="Edit" data-id="' + iCount + '" class="work-module">';
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
                section += '<a data-id="' + iCount + '" data-filter="' + template + '" title="Add more" class="add-link work-module" href="javascript:;">';
                section += '<i class="fa fa-plus"></i>&nbsp;Add more';
                section += '</a>';
                section += '</div>';
                section += '<input id="' + iCount + '" data-extclass="' + extra_class + '" data-filter="' + template + '" type="hidden" data-config="' + config + '" data-layout="' + layoutType + '" data-caption="' + caption + '" data-type="module" value="work" />';
                section += '<div class="clearfix"></div>';
                section += '</div>';

                if ($('#section-container .ui-state-default').length > 0) {
                    $(section).insertAfter($('#section-container .ui-state-default:last'));
                } else {
                    $('#section-container').append(section);
                }
            } else {
                var workIds = [];
                var workTitles = [];
                var workCustomized = [];
                var workDescription = [];


                $('.section-item[data-editor=' + edit + '] li').each(function (key, val) {
                    var iId = $(this).data('id');
                    workIds.push(iId);

                    var iTitle = $(this).text();
                    workTitles.push(iTitle);

                    var Icustomized = $(this).data('customized');
                    if (typeof Icustomized != 'undefined') {
                        workCustomized.push(Icustomized);
                    }


                    var Idescription = $(this).data('description');
                    if (typeof Idescription != 'undefined') {
                        workDescription.push(Idescription.toString());
                    }


                });

                $.each(recids, function (index, value) {
                    workIds.push(value);
                    workTitles.push(recTitle[index]);
                    workCustomized.push(false);
                    workDescription.push('');
                });

                section += '<div class="col-md-12">';
                section += '<label><b>' + caption + '</b></label>';
                section += '<ul class="record-list">';
                $.each(workIds, function (index, value) {
                    if (value != '') {
                        section += '<li data-customized="' + workCustomized[index] + '"  data-description="' + workDescription[index] + '" data-id="' + value + '" id="' + value + '-item-' + edit + '">' + workTitles[index] + '<a href="javascript:;" class="close-icon" title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a></li>';
                    }
                });
                section += '</ul>';
                section += '<a data-id="' + edit + '" data-filter="' + template + '" title="Add more" class="add-link work-module" href="javascript:;">';
                section += '<i class="fa fa-plus"></i>&nbsp;Add more';
                section += '</a>';
                section += '</div>';
                section += '<input id="' + edit + '" data-extclass="' + extra_class + '" data-filter="' + template + '" type="hidden" data-config="' + config + '" data-layout="' + layoutType + '" data-caption="' + caption + '" data-type="module" value="work" />';
                section += '<div class="clearfix"></div>';
                $('#section-container').find('div[data-editor=' + edit + ']').html(section);
            }
        },
        workTemplate: function (val, config, template, edit, configTxt, layout, extra_class) {
            var section = '';

            if (edit == 'N') {
                var iCount = 'item-' + ($('.ui-state-default').length + 1);
                section += '<div class="ui-state-default">';
                section += '<i title="Drag" class="action-icon move fa fa-arrows-alt"></i>';
                section += '<a href="javascript:;" data-filter="' + template + '" class="close-btn" title="Delete">';
                section += '<i class="action-icon delete fa fa-trash"></i>';
                section += '</a>';
                section += '<a href="javascript:;" data-filter="' + template + '" title="Edit" data-id="' + iCount + '" class="work-template">';
                section += '<i class="action-icon edit fa fa-pencil"></i>';
                section += '</a>';
                section += '<div class="clearfix"></div>';
                section += '<div class="defoult-module section-item workTemplate" data-editor="' + iCount + '">';
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
        submitFrmSectionWorkModuleTemplate: function () {
            if ($('#frmSectionWorkModuleTemplate').validate().form()) {
                var edit = $('#frmSectionWorkModuleTemplate input[name=editing]').val() != '' ? $('#frmSectionWorkModuleTemplate input[name=editing]').val() : 'N';
                $('#no-content').addClass('hide');
                $('#has-content').removeClass('hide');
                var val = $('#frmSectionWorkModuleTemplate input[name=section_title]').val();
                var extra_class = $('#frmSectionWorkModuleTemplate input[name=extra_class]').val();
                var template = $('#frmSectionWorkModuleTemplate input[name=template]').val();
                var config = $('#frmSectionWorkModuleTemplate select[name=section_config]').val();
                var configTxt = $('#frmSectionWorkModuleTemplate .config option:selected').text();
                var layout = $('#frmSectionWorkModuleTemplate select[name=layoutType]').val();
                WorkModule.workTemplate(val, config, template, edit, configTxt, layout, extra_class);
                $('#sectionWorkModuleTemplate').modal('hide');
            }
        },
        submitFrmSectionWorkModule: function () {
            if ($('#frmSectionWorkModule').validate().form()) {
                var edit = $('#frmSectionWorkModule input[name=editing]').val() != '' ? $('#frmSectionWorkModule input[name=editing]').val() : 'N';
                $('#no-content').addClass('hide');
                $('#has-content').removeClass('hide');
                var template = $('#frmSectionWorkModule input[name=template]').val();
                var extra_class = $('#frmSectionWorkModule input[name=extra_class]').val();
                var imgCaption = $('#frmSectionWorkModule input[name=section_title]').val();
                var config = $('#frmSectionWorkModule .config').val();
                var configTxt = $('#frmSectionWorkModule .config option:selected').text();
                var layoutType = $('#frmSectionWorkModule select[name=layoutType]').val();
                var recids = $('#frmSectionWorkModule input[name=selectedIds]').val();
                var recTitle = $('#frmSectionWorkModule input[name=selectedTitles]').val();

                WorkModule.moduleSectionsWork(imgCaption, config, configTxt, recids, recTitle, edit, template, layoutType, extra_class);
                WorkModule.reInitSortable();
                $('#sectionWorkModule').modal('hide');
            }
        }
    };
}();

var WorkDataTable = function () {
    // public functions
    return {
        //main function
        init: function (from, to) {
            var sort = $('#sectionWorkModule #columns').val();
            var ajaxUrl = site_url + '/powerpanel/work/get_builder_list';

            jQuery.ajax({
                type: "POST",
                url: ajaxUrl,
                dataType: 'JSON',
                data: {
                    critaria: $('#frmSectionWorkModule input[name=template]').val(),
                    columns: sort[0],
                    order: sort[1],
                    catValue: $('#sectionWorkModule #category-id').val(),
                    status: '',
                    searchValue: $('#sectionWorkModule #searchfilter').val(),
                    start: from,
                    length: to,
                    ignore: ignoreItems,
                    selected: selectedItems
                },
                async: false,
                success: function (result) {
                    $('#sectionWorkModule #record-table').append(result.data);
                    $('input[name=total_records]').val(result.recordsTotal);
                    $('input[name=found]').val(result.found);
                    if (result.recordsTotal == 0 || result.found == 0) {
                        $('#frmSectionWorkModule').find('.addSection').attr('disabled', 'disabled');
                    } else {
                        $('#frmSectionWorkModule').find('.addSection').removeAttr('disabled');
                    }

                },
                error: function (req, err) {
                    console.log('error:' + err);
                }
            });
        },
        getCategory: function () {

            var ajaxUrl = site_url + '/powerpanel/work-category/get_builder_list';
            jQuery.ajax({
                type: "POST",
                url: ajaxUrl,
                dataType: 'HTML',
                async: false,
                success: function (result) {
                    $('#sectionWorkModule #category-id').html(result);
                }
            });

        }
    };
}();

var validateSectionWork = function () {
    var handleSectionWork = function () {
        $("#frmSectionWorkModule").validate({
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
                            return $('#frmSectionWorkModule input[name="editing"]').val() == '';
                        }
                    }
                }
            },
            messages: {
                section_title: "Caption is required",
                // section_config: "Configurations is required",
                // layoutType: "Please select layout",
                'delete[]': "Please select at least one record",
            },
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else if (element.hasClass('chkChoose')) {
                    error.insertBefore($('#frmSectionWorkModule .table-container .table:first'));
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
                $('.alert-danger', $('#frmSectionWorkModule')).show();
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
                WorkModule.submitFrmSectionWorkModule();
                return false;
            }
        });

        $('#frmSectionWorkModule input').keypress(function (e) {
            if (e.which == 13) {
                WorkModule.submitFrmSectionWorkModule();
                return false;
            }
        });
    }
    return {
        //main function to initiate the module
        init: function () {
            handleSectionWork();
        },
        reset: function () {
            var validator = $("#frmSectionWorkModule").validate();
            validator.resetForm();
        }
    };
}();

var workTemplate = function () {
    var workTemplate = function () {
        $("#frmSectionWorkModuleTemplate").validate({
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
                // layoutType: {
                //     required: "Layout is required"
                // },
                // section_config: {
                //     required: "Configurations is required"
                // }
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
                $('.alert-danger', $('#frmSectionWorkModuleTemplate')).show();
            },
            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error'); // set error class to the control group
            },
            submitHandler: function (form) {
                WorkModule.submitFrmSectionWorkModuleTemplate();
                return false;
            }
        });
        $('#frmSectionWorkModuleTemplate input').keypress(function (e) {
            if (e.which == 13) {
                WorkModule.submitFrmSectionWorkModuleTemplate();
                return false;
            }
        });
    }
    return {
        //main function to initiate the module
        init: function () {
            workTemplate();
        },
        reset: function () {
            var validator = $("#frmSectionWorkModuleTemplate").validate();
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

$(document).on('click', '.work-module', function (event) {
    id = $(this).data('id');
    caption = $(this).text();
    template = $(this).data('filter');
    $('#pgBuiderSections').modal('hide');
    $('#sectionWorkModule').modal({
        backdrop: 'static',
        keyboard: false
    });
    $('#sectionWorkModule [data-dismiss="modal"]').attr("data-toggle", "");
    $('#sectionWorkModule [data-dismiss="modal"]').attr("data-target", "");
});


$(document).on('click', '.work', function (event) {
    caption = $(this).text();
    template = $(this).data('filter');
    id = '';
    ignoreItems = '';
    $('#pgBuiderSections').modal('hide');
    $('#sectionWorkModule').modal({
        backdrop: 'static',
        keyboard: false,
        show: true
    });
});

$(document).on('click', '.work-template', function (event)
{
    $('#sectionWorkModuleTemplate [data-dismiss="modal"]').attr("data-toggle", "");
    $('#sectionWorkModuleTemplate [data-dismiss="modal"]').attr("data-target", "");

    $('#pgBuiderSections').modal('hide');
    $('#sectionWorkModuleTemplate').modal({
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

        $('#frmSectionWorkModuleTemplate input[name=editing]').val(id);
        $('#frmSectionWorkModuleTemplate input[name=section_title]').val($.trim(value));
        $('#frmSectionWorkModuleTemplate select[name=layoutType] option[value=' + layout + ']').prop('selected', true);
        $('#frmSectionWorkModuleTemplate .config option[value=' + config + ']').prop('selected', true);
        $('#frmSectionWorkModuleTemplate input[name=extra_class]').val(extclass);
        $('#frmSectionWorkModuleTemplate .addSection').text('Update');
        $('#frmSectionWorkModuleTemplate #exampleModalLabel b').text('Edit Work');

    } else {

        var value = $(this).text();
        $('#frmSectionWorkModuleTemplate input[name=editing]').val('');
        $('#frmSectionWorkModuleTemplate input[name=section_title]').val($.trim(value));
        //$('#frmSectionWorkModuleTemplate select[name=layoutType] option:first').prop('selected', true);
        $('#frmSectionWorkModuleTemplate input[name=extra_class]').val('');
        $('#frmSectionWorkModuleTemplate .addSection').text('Add');
        $('#frmSectionWorkModuleTemplate #exampleModalLabel b').text('Add Work');

        $('#sectionWorkModuleTemplate [data-dismiss="modal"]').attr("data-toggle", "modal");
//        $('#sectionWorkModuleTemplate [data-dismiss="modal"]').attr( "data-target", "#pgBuiderSections" );

    }

    $('#frmSectionWorkModuleTemplate').find('input[name=template]').val($(this).data('filter'));

});

$('#frmSectionWorkModuleTemplate').on('submit', function (e) {
    e.preventDefault();
});

//..End Open while add or edit section

$('#sectionWorkModule').on('shown.bs.modal', function () {
    caption = $.trim(caption);
    //$('#datatable_work_ajax').closest('.col-md-12').loading('start');
    validateSectionWork.init();
    $(this).find('.group-checkable').prop('checked', false);
    selectedItems = [];
    recTitle = [];
    ignoreItems = [];
    $('#sectionWorkModule input[name=selectedIds]').val(null);
    $('#sectionWorkModule input[name=selectedTitles]').val(null);
    $('select').selectpicker('destroy');
    WorkDataTable.getCategory();
    $('#frmSectionWorkModule #category-id option:first').prop('selected', true);
    $('#frmSectionWorkModule #columns option:selected').prop('selected', false);
    $('#frmSectionWorkModule #columns option[value=varTitle]').prop('selected', true);
    $('#frmSectionWorkModule #columns option[value=asc]').prop('selected', true);
    $('#frmSectionWorkModule input[name=template]').val(template);
    var layout = '';
    if (id != '') {
        caption = $('#' + id).data('caption');
        layout = $('#' + id).data('layout');
        var config = $('#' + id).data('config');
        var extClass = $('#' + id).data('extclass');
        $('#frmSectionWorkModule input[name=editing]').val(id);
        $('#frmSectionWorkModule input[name=extra_class]').val(extClass);
        $('#frmSectionWorkModule select[name=layoutType] option[value=' + layout + ']').prop('selected', true);
        $('#frmSectionWorkModule .config').children('option[value=' + config + ']').prop('selected', true);

        $('.section-item[data-editor=' + id + '] li').each(function (key, val) {
            var iId = $(this).data('id');
            ignoreItems.push(iId);
        });

        $('#sectionWorkModule .addSection').text('Update');
        $('#sectionWorkModule #exampleModalLabel b').text('Update Work');
    } else {
        $('#frmSectionWorkModule input[name=editing]').val('');
        $('#frmSectionWorkModule input[name=extra_class]').val('');
        //$('#frmSectionWorkModule select[name=layoutType] option:first').prop('selected', true);
        $('#frmSectionWorkModule .config').children('option[value=7]').prop('selected', true);

        $('#sectionWorkModule .addSection').text('Add');
        $('#sectionWorkModule #exampleModalLabel b').text('Work');

        $('#sectionWorkModule [data-dismiss="modal"]').attr("data-toggle", "modal");
//        $('#sectionWorkModule [data-dismiss="modal"]').attr( "data-target", "#pgBuiderSections" );
    }

    $('#frmSectionWorkModule input[name=section_title]').val(caption);
    $('select').selectpicker();
    WorkDataTable.init(start, range);
    $("#frmSectionWorkModule #mcscroll").mCustomScrollbar({
        axis: "y",
        theme: "dark",
        callbacks: {
            onTotalScroll: function () {
                if ($('input[name=found]').val() > 0) {
                    if ($('#sectionWorkModule').find('#record-table tr').length < $('input[name=total_records]').val()) {
                        start += range;
                        end += range;
                        WorkDataTable.init(start, range);
                    }
                }
            }
        }
    });

}).on('hidden.bs.modal', function () {

    range = 10;
    start = 0;
    end = range;
    $('#sectionWorkModule select[name=layoutType] option[class=list]').show();
    $('#sectionWorkModule #record-table').html('');
    $(".record-list").sortable().disableSelection();
    $('#sectionWorkModule select').selectpicker('destroy');
    validateSectionWork.reset();

});


$('#sectionWorkModuleTemplate').on('shown.bs.modal', function () {
    $('#sectionWorkModuleTemplate select').selectpicker('');
    workTemplate.init();
}).on('hidden.bs.modal', function () {
    $('#sectionWorkModuleTemplate select').selectpicker('destroy');
    workTemplate.reset();
});

$(document).ajaxStart(function () {
    $('.table-scrollable').loader(loaderConfig);
}).ajaxComplete(function () {
    setTimeout(function () {
        $.loader.close(true);
    }, 500);
});

$(document).on('keyup', '#sectionWorkModule #searchfilter', function () {
    range = 10;
    start = 0;
    end = range;
    $('#sectionWorkModule #record-table').html('');
    WorkDataTable.init(start, range);
});

$(document).on('change', '#sectionWorkModule #columns', function () {
    range = 10;
    start = 0;
    end = range;
    $('#sectionWorkModule #record-table').html('');
    WorkDataTable.init(start, range);
});

$(document).on('change', '#sectionWorkModule #category-id', function () {
    range = 10;
    start = 0;
    end = range;
    $('#sectionWorkModule #record-table').html('');
    WorkDataTable.init(start, range);
});


//Group checkbox checking
$(document).on('change', '#sectionWorkModule .group-checkable', function () {

    if ($(this).prop('checked')) {
        $('#sectionWorkModule #record-table .chkChoose').prop('checked', true);
        $('#sectionWorkModule #record-table .chkChoose').parent().parent().parent().addClass('selected-record');
        $('#sectionWorkModule #record-table .chkChoose:checked').each(function (index, value) {
            var id = $(this).val();
            if (!selectedItems.includes(id)) {
                selectedItems.push(id);
                recTitle.push($(this).data('title'));
            }
        });
    } else {
        $('#sectionWorkModule #record-table .chkChoose').prop('checked', false);
        $('#sectionWorkModule #record-table .chkChoose').parent().parent().parent().removeClass('selected-record');
        selectedItems = [];
        recTitle = [];
    }
    $('#sectionWorkModule input[name=selectedIds]').val(selectedItems);
    $('#sectionWorkModule input[name=selectedTitles]').val(recTitle);
});

$(document).on('change', '#sectionWorkModule #record-table .chkChoose', function () {
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

    if ($('#sectionWorkModule #record-table .chkChoose:checked').length == $('#sectionWorkModule #record-table tr .chkChoose').length) {
        $('#sectionWorkModule .group-checkable').prop('checked', true);
    } else {
        $('#sectionWorkModule .group-checkable').prop('checked', false);
    }

    $('#sectionWorkModule input[name=selectedIds]').val(selectedItems);
    $('#sectionWorkModule input[name=selectedTitles]').val(recTitle);
});

$(document).on('click', '#sectionWorkModule #record-table tr', function (e) {
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

$(document).on('change', '#sectionWorkModule #columns', function () {
    if ($(this).find('option:selected').length > 1) {
        //$('#mCSB_1_container').trigger('click');
    }
});
//..Group checkbox checking