@if(!empty($data['boat']) && count($data['boat'])>0)
<link rel="stylesheet" href="{{ $CDN_PATH.'resources/global/plugins/jquery-ui-rangepicker/jquery-ui.min.css' }}">
</link>
<section class="boat-listing-page">
    <div class="container-fliud">
        <div class="row">
            <div class="col-12">
                <div class="top-short-panels">
                    <div class="serch-result">
                        <span id="total_count"></span>
                    </div>
                    <div class="top-short-by">
                        <label>Sort By:</label>
                        <a href="javascript:void(0);" onClick="changeOrderFields($(this));" sort-field="intPrice" class="s-price sort-class sort-active" title="Price">Price</a>
                        <a href="javascript:void(0);" onClick="changeOrderFields($(this));" sort-field="varLength" class="s-length sort-class" title="Length">Length</a>
                        <a href="javascript:void(0);" onClick="changeOrderFields($(this));" sort-field="yearYear" class="s-year sort-class" title="Year">Year</a>
                        <a href="javascript:void(0);" onClick="changeOrderFields($(this));" sort-field="make" class="s-make sort-class" title="Make">Make</a>
                        <a href="javascript:void(0);" onClick="changeOrder($(this));" id="order-str-icon" order-str="ASC" class="sort-icon" title="Ascending"><i class="fa fa-sort-amount-asc" aria-hidden="true"></i></a>

                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                <div class="left-panel">
                    <div class="panel-title">
                        <h4>Search By</h4>
                    </div>
                    <div class="panel-content">
                        <div class="panel-form">
                            <form id="search-form">
                                <div class="row">
                                    <span class="form-label">Manufacturer</span>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <select class="" name="brand" data-show-subtext="true" data-live-search="true">
                                                <!--<select class="selectpicker" data-show-subtext="true" data-live-search="true">-->
                                                <option value="">Select Manufacturer</option>
                                                @foreach($data['brand'] as $catg)
                                                <option value="{{$catg->id}}">{{$catg->varTitle}}</option>
                                                @endForeach
                                                <!--<option disabled="disabled">Marvin Martinez</option>-->
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="slider-box">
                                        <label for="lengthRange">Length Range(in ft):</label>

                                        <input type="text" name="lenghRange" class="lengthRange" readonly>
                                        <div id="length-range" class="slider"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="slider-box">
                                        <label for="yearRange">Year Range:</label>
                                        <input type="text" name="yearRange" class="yearRange" readonly>
                                        <div id="year-range" class="slider"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="slider-box">
                                        <label for="priceRange">Price Range:</label>
                                        <input type="text" name="priceRange" class="priceRange" readonly>
                                        <div id="price-range" class="slider"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="radio">
                                        <span class="form-label">Condition </span>
                                        @php
                                        $checked_con = '';
                                        @endphp
                                        @foreach($data['BoatCondition'] as $checked_no => $check_val)
                                        <label>
                                            <input class="md-radiobtn" type="radio" value="{{  $check_val->id }}" name="condition" id="chrCon{{  $check_val->id }}" {{  $checked_con }}>
                                            <!-- <span class="checkmark"></span> -->
                                            {{ $check_val->varTitle }}
                                        </label>
                                        @endforeach
                                        <label>
                                            <input class="md-radiobtn" type="radio" value="0" name="condition" id="chrCon0" {{  $checked_con }}>
                                            <!-- <span class="checkmark"></span> -->
                                            All
                                        </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="radio">
                                        <span class="form-label">Availability </span>

                                        @foreach ( $data['stock'] as $st )
                                        <label>
                                            <input class="md-radiobtn" type="radio" value="{{  $st->id }}" name="inStock" id="inStock[]">
                                            <!-- <span class="checkmark"></span> -->
                                            {{ $st-> varTitle}}
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <button type="button" class="btn background-btn-blue search_data" onClick="searchformBoats(false);" title="Search"><span>Search</span></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="panel-filter panel-form">
                            <div class="panel-title">
                                <h4>Filter By</h4>
                            </div>
                            <div class="filter-contents">

                                <div class="row">
                                    <div class="radio">
                                        <span class="form-label">CATEGORY</span>
                                        @php
                                        $checked_cat = '';
                                        @endphp
                                        @foreach($data['boatcategory'] as $cat_val)
                                        <label>
                                            <input class="md-radiobtn search_data bot_cat_data" type="radio" onClick="searchBoatCategory($(this));" value="{{ $cat_val->id }}" name="category" id="{{  $cat_val->id }}" {{  $checked_cat }}>
                                            <!-- <span class="checkmark"></span> -->
                                            {{ $cat_val->varTitle }}
                                            @php
                                            $boat_count = 0;
                                            $boat_count = Powerpanel\Boat\Models\Boat::get_boat_count_category($cat_val->id,'intBoatCategoryId');

                                            @endphp
                                            <p class="counter">({!! $boat_count !!})</p>
                                        </label>
                                        @endforeach
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-sm-12">

                                        <button type="reset" class="btn background-btn-blue clear-btn" onClick="clearBoatfilters($(this))" title="Clear Filters"><span>Clear Filters</span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="boat-list-boxs" id="all_boats">
                    <div class="row" >
                        
                    </div>
                    <div id ="pagination">

                    </div>
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

<script src="{{ url('assets/js/packages/boat/boat.js') }}?{{ Config::get('Constant.VERSION') }}"></script>
<script src="{{ $CDN_PATH.'resources/global/plugins/jquery-ui-rangepicker/jquery-ui.min.js' }}" type="text/javascript"></script>
<script>
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
    });


    $(function() {
        $("#length-range").slider({
            step: 5,
            range: true,
            min: 0,
            max: 500,
            values: [0, 500],
            slide: function(event, ui) {
                $(".lengthRange").val(ui.values[0] + "-" + ui.values[1]);
            }
        });
        $(".lengthRange").val($("#length-range").slider("values", 0) + "-" + $("#length-range").slider("values", 1));
    });
    $(function() {
        $("#year-range").slider({
            step: 5,
            range: true,
            min: 1800,
            max: year,
            values: [1800, year],
            slide: function(event, ui) {
                $(".yearRange").val(ui.values[0] + "-" + ui.values[1]);
            }
        });
        $(".yearRange").val($("#year-range").slider("values", 0) + "-" + $("#year-range").slider("values", 1));
    });
    $(function() {
        $("#price-range").slider({
            step: 500,
            range: true,
            min: 0,
            max: 500000,
            values: [0, 500000],
            slide: function(event, ui) {
                $(".priceRange").val(ui.values[0] + "-" + ui.values[1]);
            }
        });
        $(".priceRange").val($("#price-range").slider("values", 0) + "-" + $("#price-range").slider("values", 1));
    });
</script>