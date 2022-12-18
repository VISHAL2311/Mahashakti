@if(!empty($data['boat']) && count($data['boat'])>0)

<link rel="stylesheet" href="{{ $CDN_PATH.'resources/global/plugins/ionrangeSlider/ion.rangeSlider.min.css' }}">
</link>
<section class="boat-listing-page">
    <div class="container-fliud">
        <div class="row">
            <div class="col-12">
                <div class="text-right filter-button">
                    <a href="javascript:void(0)" class="ac-btn filter-btn">
                        <i class="fa fa-filter" aria-hidden="true"></i>
                        Filter
                    </a>
                </div>
                <div class="top-short-panels">
                    <div class="serch-result">
                        <span id="total_count"></span>
                    </div>
                    <div class="top-short-by">
                        <label>Sort By:</label>
                        <a href="javascript:void(0);" onClick="changeOrderFields($(this));" sort-field="intPrice" class="s-price sort-class" title="Price">Price</a>
                        <a href="javascript:void(0);" onClick="changeOrderFields($(this));" sort-field="varLength" class="s-length sort-class" title="Length">Length</a>
                        <a href="javascript:void(0);" onClick="changeOrderFields($(this));" sort-field="yearYear" class="s-year sort-class" title="Year">Year</a>
                        <a href="javascript:void(0);" onClick="changeOrderFields($(this));" sort-field="make" class="s-make sort-class" title="Make">Make</a>
                        <a href="javascript:void(0);" onClick="changeOrder($(this));" id="order-str-icon" order-str="ASC" class="sort-icon orderdisable" title="Ascending"><i class="fa fa-sort-amount-asc" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-3 col-lg-4 col-sm-12">
                <div class="boat-panel-wrapper" data-aos="fade-right" data-aos-duration="1000">
                    <div class="close-btn"><span></span></div>
                    <div class="boat-panel">
                        <h4 class="content-title">Search By</h4>
                        <div class="panel-content">
                            <div class="panel-form panel-body">
                                <form id="search-form">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label class="form-label">Brand</label>
                                            <div class="form-group">
                                                <select class="selectpicker ac-bootstrap-select form-control" name="brand" id="brandselect" onchange="searchformBoats(false);" data-show-subtext="true" data-live-search="true"> 
                                                    <option value="">Select Brand</option>
                                                    @foreach($data['brand'] as $catg)
                                                    <option value="{{$catg->id}}">{{$catg->varTitle}}</option>
                                                    @endforeach 
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="slider-box">
                                                <label for="lengthRange" class="form-label">Length Range(in ft):</label>
                                                <input type="text" name="lenghRange" class="lengthRange" value="" data-skin="round" data-type="double" data-grid="false">
                                                <p class="range">Range: <span id="lengthrangevalues"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="slider-box">
                                                <label for="yearRange" class="form-label">Year Range:</label>
                                                <input type="text" name="yearRange" class="yearRange" value="" data-skin="round" data-type="double" data-grid="false">
                                                <p class="range">Range: <span id="yearrangevalues"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="slider-box">
                                                <label for="priceRange" class="form-label">Price Range:</label>
                                                <input type="text" name="priceRange" class="priceRange" value="" data-skin="round" data-type="double" data-grid="false">
                                                <p class="range">Range: <span id="pricerangevalues"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="radio">
                                                <label class="form-label">Condition </label>
                                                <div class="filter-list">
                                                    @php
                                                    $checked_con = '';
                                                    @endphp
                                                    @foreach($data['BoatCondition'] as $checked_no => $check_val)
                                                    <label>
                                                        <input class="md-radiobtn" type="radio" value="{{  $check_val->id }}"onchange="searchformBoats(false);" name="condition" id="chrCon{{  $check_val->id }}" {{  $checked_con }}>  <!-- <span class="checkmark"></span> -->
                                                        <span>{{ $check_val->varTitle }}</span>
                                                    </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12 mt-3">
                                            <div class="radio">
                                                <label class="form-label" class="form-label">Availability </label>
                                                <div class="filter-list">
                                                    @foreach ( $data['stock'] as $st )
                                                    <label>
                                                        <input class="md-radiobtn" type="radio" value="{{  $st->id }}" onchange="searchformBoats(false);" name="inStock" id="inStock[]"> 
                                                        <span>{{ $st-> varTitle}}</span>
                                                    </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                   
                                </form>
                            </div>
                            <div class="panel-filter panel-form">
                                <h4 class="content-title">Filter By <button type="reset" class="ac-btn clear-btn text-capitalize" onClick="clearBoatfilters($(this))" title="Clear Filters"><span>Clear All</span></button></h4>
                                <div class="filter-contents panel-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="radio">
                                                <label class="form-label">Category</label>
                                                @php
                                                $checked_cat = '';
                                                @endphp
                                                <div class="filter-list list">
                                                    @foreach($data['boatcategory'] as $cat_val)
                                                    <label>
                                                        <input class="md-radiobtn search_data bot_cat_data" type="radio" onClick="searchBoatCategory($(this));" value="{{ $cat_val->id }}" name="category" id="{{  $cat_val->id }}" {{  $checked_cat }}> 
                                                        <span>{{ $cat_val->varTitle }}</span>
                                                        @php
                                                        $boat_count = 0;
                                                        $boat_count = Powerpanel\Boat\Models\Boat::get_boat_count_category($cat_val->id,'intBoatCategoryId');

                                                        @endphp
                                                        <span class="counter pull-right text-light badge rounded-pill bg-dark">{!! $boat_count !!}</span>
                                                    </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-9 col-lg-8 col-sm-12">
                <div class="boat-list-boxs" id="all_boats">

                </div>
            </div>
        </div>
    </div>
</section>
@else
<section>
    <div class="inner-page-container cms">
        <div class="container">
            <section class="page_section">
                <div class="container">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h2>Coming Soon...</h2>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</section>
@endif

@php
$pricemax = 0;
$pricemin = 9999999999999999;
$lengthmax = 0;
$lengthmin = 9999999999999999;
$yearmax = 0;
$yearmin = 9999999999999999;
foreach($data['boat'] as $data){
if ($pricemin > $data -> intPrice) {
$pricemin = $data -> intPrice;
}
if ($pricemax < $data -> intPrice) {
    $pricemax = $data -> intPrice;
    }

    if ($lengthmin > $data -> varLength) {
    $lengthmin = $data -> varLength;
    }
    if ($lengthmax < $data -> varLength) {
        $lengthmax = $data -> varLength;
        }

        if ($yearmin > $data -> yearYear) {
        $yearmin = $data -> yearYear;
        }
        if ($yearmax < $data -> yearYear) {
            $yearmax = $data -> yearYear;
            }
            }
            $lengthmax = intval(round($lengthmax)+1);
            $lengthmin = intval(round($lengthmin)-1);

            @endphp
            <script src="{{ url('assets/js/packages/boat/boat.js') }}?{{ Config::get('Constant.VERSION') }}"></script>
            <script src="{{ $CDN_PATH.'resources/global/plugins/ionrangeSlider/ion.rangeSlider.min.js' }}" type="text/javascript"></script>
            <script>
                var pricemax = parseInt("{{ $pricemax }}");
                var pricemin = parseInt("{{ $pricemin }}");
                var lengthmax = parseInt("{{ $lengthmax }}");
                var lengthmin = parseInt("{{ $lengthmin }}");
                var yearmin = parseInt("{{ $yearmin }}");
                var yearmax = parseInt("{{ $yearmax }}");
                var CSRF_TOKEN = '{{ csrf_token() }}';
                var SEARCH_URL = 'boat_search';
                $(document).ready(function() {
                    searchBoats();
                });
                const date = new Date();
                const year = date.getFullYear();

                $(document).on('click', '.page-link', function(event) {
                    event.preventDefault();
                    var page = $(this).attr('href').split('page=')[1];
                    searchBoats(page);
                    window.scrollTo({
                        top: 100,
                        left: 0,
                        behavior: 'smooth'
                    });
                });

                var $lengthRange = $(".lengthRange");
                $lengthRange.ionRangeSlider({
                    onStart: function(data) {
                        $('#lengthrangevalues').html(data.min+' ft' + ' - ' + data.max+' ft');
                    },
                    onChange: function(data) {
                        from = data.from;
                        to = data.to;
                        $('#lengthrangevalues').html(from+' ft' + ' - ' + to+' ft');
                    },
                    onFinish: function(data) {
                        searchformBoats(false);
                    },
                    prettify_enabled: false,
                    min: lengthmin,
                    max: lengthmax,
                });

                var $yearRange = $(".yearRange");
                $yearRange.ionRangeSlider({
                    onStart: function(data) {
                        $('#yearrangevalues').html(data.min + ' - ' + data.max);
                    },
                    onChange: function(data) {
                        from = data.from;
                        to = data.to;
                        $('#yearrangevalues').html(from + ' - ' + to);
                    },
                    onFinish: function(data) {
                        searchformBoats(false);
                    },
                    prettify_enabled: false,
                    min: yearmin,
                    max: yearmax,
                });

                var $priceRange = $(".priceRange");

                $priceRange.ionRangeSlider({
                    onStart: function(data) {
                        var priceminvalue = new Intl.NumberFormat('en-US').format(data.min);
                        var pricemaxvalue = new Intl.NumberFormat('en-US').format(data.max);
                        $('#pricerangevalues').html('$'+priceminvalue + ' - ' +'$'+ pricemaxvalue);
                    },
                    onChange: function(data) {
                        var from =    new Intl.NumberFormat('en-US').format(data.from);
                        var to = new Intl.NumberFormat('en-US').format(data.to);
                        $('#pricerangevalues').html('$'+from + ' - '+'$'+to);
                    },
                    onFinish: function(data) {
                        searchformBoats(false);
                    },
                    prefix: "$",
                    min: pricemin,
                    max: pricemax,
                });

            </script>