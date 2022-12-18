<section class="boat-listing-page">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="top-short-panels">
                    <div class="serch-result">
                        <span id="total_count">12 Result(s)</span>
                    </div>
                    <div class="top-short-by">
                        <label>Sort By:</label>
                        <a href="javascript:void(0);" sort-field="price" class="s-price sort-class sort-active" title="Price">Price</a>
                        <a href="javascript:void(0);" sort-field="length" class="s-length sort-class" title="Length">Length</a>
                        <a href="javascript:void(0);" sort-field="year" class="s-year sort-class" title="Year">Year</a>
                        <a href="javascript:void(0);" sort-field="make" class="s-make sort-class" title="Make">Make</a>
                        <a href="javascript:void(0);" id="order-str-icon" onclick="changeOrder($(this));" order-str="ASC" class="sort-icon" title="Ascending"><i class="s-icon tog-s-icon"></i></a>  
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
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
</script>