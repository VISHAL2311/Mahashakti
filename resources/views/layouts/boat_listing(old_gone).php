<!-- <section class="boat-listing-page">
    <div class="container-fliud">
        <div class="row">
            <div class="col-12">
                <div class="top-short-panels">
                    <div class="serch-result">
                        <span id="total_count">12 Result(s)</span>
                    </div>
                    <div class="top-short-by">
                        <label>Sort By:</label>
                        <a href="javascript:void(0);" onClick="changeOrderFields($(this));" sort-field="intPrice" class="s-price sort-class sort-active" title="Price">Price</a>
                        <a href="javascript:void(0);" onClick="changeOrderFields($(this));" sort-field="varLength" class="s-length sort-class" title="Length">Length</a>
                        <a href="javascript:void(0);" onClick="changeOrderFields($(this));" sort-field="yearYear" class="s-year sort-class" title="Year">Year</a>
                        <a href="javascript:void(0);" onClick="changeOrderFields($(this));" sort-field="make" class="s-make sort-class" title="Make">Make</a>
                        <a href="javascript:void(0);" onClick="changeOrderFields($(this));" id="order-str-icon"  order-str="ASC" class="sort-icon" title="Ascending"><i class="fa fa-sort-amount-asc" aria-hidden="true"></i></a>  
                    
                    </div>
                </div>
            </div>            
        </div>
        <div class="row">
            <div class="col-lg-3">
                <div class="left-panel">
                    <div class="panel-title"><h4>Search By</h4></div>
                    <div class="panel-content">
                        <div class="panel-form">
                            <form id="search-form">
                                <span class="form-label">Manufacturer</span>
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="boat-list-boxs">
                    <div class="row">
                        <div class="col-md-4 col-sm-6 col-12">
                            <div class="boat-card">
                                <div class="boat-card-img">
                                    <div class="thumbnail-container">
                                        <div class="thumbnail" style="background: #f5f5f5;">
                                            <img src="{{ url('/') }}/{{ ('assets/images/Rectangle 3812.jpg') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="in-stock">In Stock</div>
                                </div>
                                <div class="boat-info">
                                    <div class="boat-title">
                                        <h3 class="main-title">Custom Snorkel Excursion</h3>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <div class="sub-title"><strong>Category:</strong> Power Boats</div>
                                        <div class="condition"><strong>Condition:</strong> New</div>                                    
                                    </div>
                                    <div class="boat-footer-info">
                                        <div class="i-beam"><strong>Beam:</strong> 9'6" / 2.90m</div>
                                        <div class="i-length"><strong>Length Overall: </strong>16'7" / 5.05m</div>
                                    </div>
                                    <div class="boat-price  d-flex justify-content-center align-items-end"><strong>$65300</strong></div>
                                    <div class="overlay-btn">
                                        <a href="#" class="ac-btn">View Deails</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 col-12">
                            <div class="boat-card">
                                <div class="boat-card-img">
                                    <div class="thumbnail-container">
                                        <div class="thumbnail" style="background: #f5f5f5;">
                                            <img src="{{ url('/') }}/{{ ('assets/images/Rectangle 3812.jpg') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="comingsoon">Coming Soon</div>
                                </div>
                                <div class="boat-info">
                                    <div class="boat-title">
                                        <h3 class="main-title">Custom Snorkel Excursion</h3>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <div class="sub-title"><strong>Category:</strong> Power Boats</div>
                                        <div class="condition"><strong>Condition:</strong> New</div>                                    
                                    </div>
                                    <div class="boat-footer-info">
                                        <div class="i-beam"><strong>Beam:</strong> 9'6" / 2.90m</div>
                                        <div class="i-length"><strong>Length Overall: </strong>16'7" / 5.05m</div>
                                    </div>
                                    <div class="boat-price  d-flex justify-content-center align-items-end"><strong>$65300</strong></div>
                                    <div class="overlay-btn">
                                        <a href="#" class="ac-btn">View Deails</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 col-12">
                            <div class="boat-card">
                                <div class="boat-card-img">
                                    <div class="thumbnail-container">
                                        <div class="thumbnail" style="background: #f5f5f5;">
                                            <img src="{{ url('/') }}/{{ ('assets/images/Rectangle 3812.jpg') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="available">Available to Order</div>
                                </div>
                                <div class="boat-info">
                                    <div class="boat-title">
                                        <h3 class="main-title">Custom Snorkel Excursion</h3>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <div class="sub-title"><strong>Category:</strong> Power Boats</div>
                                        <div class="condition"><strong>Condition:</strong> New</div>                                    
                                    </div>
                                    <div class="boat-footer-info">
                                        <div class="i-beam"><strong>Beam:</strong> 9'6" / 2.90m</div>
                                        <div class="i-length"><strong>Length Overall: </strong>16'7" / 5.05m</div>
                                    </div>
                                    <div class="boat-price  d-flex justify-content-center align-items-end"><strong>$65300</strong></div>
                                    <div class="overlay-btn">
                                        <a href="#" class="ac-btn">View Deails</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> -->

<!-- 
<script>
    var t;

    var start = $('#home-banner').find('.active').attr('data-interval');
    t = setTimeout("$('#home-banner').carousel({interval: 1000});", start - 1000);

    $('#home-banner').on('slid.bs.carousel', function () {
        clearTimeout(t);
        var duration = $(this).find('.active').attr('data-interval');

        $('#home-banner').carousel('pause');
        t = setTimeout("$('#home-banner').carousel();", duration - 1000);
    })

    $('.carousel-control.right').on('click', function () {
        clearTimeout(t);
    });

    $('.carousel-control.left').on('click', function () {
        clearTimeout(t);
    });
</script> -->