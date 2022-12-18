filterSortArr = [];
searchArr = [];
filterSortArr[0] = "";
filterSortArr[1] = "";

var isOrderEnabled = false;



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


function changeOrderFields(ref_field) {
    if(isOrderEnabled == false){
        filterSortArr[1] = "ASC";
    }
    isOrderEnabled = true;
    $('#order-str-icon').removeClass('orderdisable');
    $("#orderByFieldName1").val(ref_field.attr("sort-field"));
    var sortfield = ref_field.attr("sort-field");
    $(".sort-active").removeClass("sort-active");
    ref_field.addClass("sort-active");
    var sortfieldobj = {
        sortby: sortfield
    };
    filterSortArr[0] = sortfield;
    searchBoats();
}

function changeOrder(ref) {
   
    if (isOrderEnabled) {
        
        if (ref.attr("order-str") == "DESC") {
            ref.attr("order-str", "ASC");
            ref.attr("title", "Accending");
            ref.html('<i class="fa fa-sort-amount-asc"></i>');
            $("#orderTypeAscOrDesc1").val("ASC");
        } else {
            ref.attr("order-str", "DESC");
            ref.attr("title", "Descending");
            $("#orderTypeAscOrDesc1").val("DESC");
            ref.html('<i class="fa fa-sort-amount-desc"></i>');
        }
        filterSortArr[1] = ref.attr("order-str");
        searchBoats();
    }

}

function searchBoatCategory(ref) {

    filterSortArr[2] = ref.attr("id");

    searchBoats();
    window.scrollTo({
        top: 50,
        left: 0,
        behavior: 'smooth'
    });

}
function clearBoatfilters() {
    window.scrollTo({
        top: 50,
        left: 0,
        behavior: 'smooth'
    });
    filterSortArr = [];
    searchArr = [];
    
    isOrderEnabled = false;
    $('#order-str-icon').addClass('orderdisable');

    $("input[name=category]")
        .not(':button, :submit, :reset, :hidden')
        .prop('checked', false)
        .prop('selected', false);
    $("#brandselect").val("").selectpicker('refresh');
    $("input[name=condition]")
        .not(':button, :submit, :reset, :hidden')
        .prop('checked', false)
        .prop('selected', false);
    $("input[name=inStock]")
        .not(':button, :submit, :reset, :hidden')
        .prop('checked', false)
        .prop('selected', false);

    let js_range_mass = $(".lengthRange").data("ionRangeSlider");
    js_range_mass.reset();
    let js_yearrange_mass = $(".yearRange").data("ionRangeSlider");
    js_yearrange_mass.reset();
    let js_pricerange_mass = $(".priceRange").data("ionRangeSlider");
    js_pricerange_mass.reset();


    $('.sort-class').removeClass("sort-active");
    $('.sort-icon').removeClass("sort-active");
    const ref = $('#order-str-icon');
    ref.attr("order-str", "ASC");
    ref.attr("title", "Accending");
    ref.html('<i class="fa fa-sort-amount-asc"></i>');
    $("#orderTypeAscOrDesc1").val("ASC");
    $('.slider').each(function () {
        var options = $(this).slider('option');
        $(this).slider('values', [options.min, options.max]);
    });
    searchBoats();
}
function searchformBoats() {

    var unser = $('#search-form').serializeArray();
    searchArr = unser;
    searchBoats();
  
}

function searchBoats(page) {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': CSRF_TOKEN } });
    $.ajax({
        type: "POST",
        url: SEARCH_URL,
        data: { filterSortArr: filterSortArr, searchArr: searchArr, page: page },
        error: function (error) {
        },
        success: function (res) {
            $('#all_boats').html(res.response_html);
            $('#total_count').html(res.total_count);

        }
    });
}


