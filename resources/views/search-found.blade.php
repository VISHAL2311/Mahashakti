@if(!Request::ajax())
@extends('layouts.app')
@section('content')
@include('layouts.inner_banner')
@endif
<div id="searchtextboxTerm" style="display: none;">{{ (Request::get('frontSearch') != null) ? Request::get('frontSearch') :"" }}</div>
@if(!empty($searchResults) && count($searchResults) > 0 || !empty($searchDocs))
<section>
    <div class="inner-page-container cms search_result">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 animated fadeInUp">
                    <h2 class="serch_main_title">Found 108  matches  for <span style="color:#002b5c;font-weight: 500;">"Cayman"</span>.
                    </h2>
                    <div class="blog_post listing">
                        <div class="blog_img">
                            <div class="thumbnail-container">
                                <div class="thumbnail">
                                    <a title="" href="#">
                                        <img src="assets/images/blog-img1.jpg" alt="">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="info">
                            <div class="date">10 April, 2019</div>
                            <h5 class="sub_title"><a href="#">At vero eos et accusamus et iusto odio dignissimos</a></h5>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua...</p>
                            <a class="btn ac-border " href="#" title="Read More">Read More</a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xs-12 animated fadeInUp">
                    <div class="event_post listing">
                        <div class="image">
                            <div class="thumbnail-container">
                                <div class="thumbnail">
                                    <a title="" href="#">
                                        <img src="assets/images/event_img1.jpg" alt="">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="info">
                            <h5 class="sub_title"><a href="#">Proposed Downtown District Ordinance</a></h5>
                            <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores... </p>
                            <div class="date">15 Oct, 2019</div>
                        </div>
                        <div class="info_more text-right"><a class="info_link" href="#" title="More Events">More Events <i class="fa fa-angle-double-right"></i></a></div>
                    </div>
                </div>
                <div class="col-sm-12 animated fadeInUp" style="padding-top:30px;padding-bottom:30px  ">
                    <div class="news_post listing">
                        <div class="date">
                            <span>18</span><span>Jul 2019</span>
                        </div>
                        <div class="info">
                            <h5 class="sub_title"><a href="#">Nemo enim ipsam voluptatem quia voluptas sit aspernatur</a></h5>
                            <div class="info_dtl">
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                                    Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                    Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
                                <a href="#" class="n-more" title="Read More">[ Read More ]</a>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="col-sm-12 animated fadeInUp">
                    <div class="blog_post listing">
                        <div class="blog_img">
                            <div class="thumbnail-container">
                                <div class="thumbnail">
                                    <a title="" href="#">
                                        <img src="assets/images/blog-img1.jpg" alt="">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="info">
                            <div class="date">10 April, 2019</div>
                            <h5 class="sub_title"><a href="#">At vero eos et accusamus et iusto odio dignissimos</a></h5>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua...</p>
                            <a class="btn ac-border " href="#" title="Read More">Read More</a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xs-12 animated fadeInUp">
                    <div class="event_post listing">
                        <div class="image">
                            <div class="thumbnail-container">
                                <div class="thumbnail">
                                    <a title="" href="#">
                                        <img src="assets/images/event_img1.jpg" alt="">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="info">
                            <h5 class="sub_title"><a href="#">Proposed Downtown District Ordinance</a></h5>
                            <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores... </p>
                            <div class="date">15 Oct, 2019</div>
                        </div>
                        <div class="info_more text-right"><a class="info_link" href="#" title="More Events">More Events <i class="fa fa-angle-double-right"></i></a></div>
                    </div>
                </div>
                <div class="col-sm-12 animated fadeInUp" style="padding-top:30px;padding-bottom:30px  ">
                    <div class="news_post listing">
                        <div class="date">
                            <span>18</span><span>Jul 2019</span>
                        </div>
                        <div class="info">
                            <h5 class="sub_title"><a href="#">Nemo enim ipsam voluptatem quia voluptas sit aspernatur</a></h5>
                            <div class="info_dtl">
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                                    Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                    Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
                                <a href="#" class="n-more" title="Read More">[ Read More ]</a>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@else
<section>
    <div class="inner-page-container cms search_result">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">	
                    @if(!empty($similarWords))
                    <h3>Did you mean:</h3>
                    <p>
                        @foreach($similarWords as $word)
                        <a href="javascript:;" data-value="{{ $word }}" class="researchword">{{ $word }}</a>
                        @endforeach
                    </p>
                    @endif						
                    <p>Your Search - <b>{{ (Request::get('frontSearch') != null) ? Request::get('frontSearch') :"" }}</b> &nbsp;did not match with any records and documents.</p>
                    <h6>Suggestions:</h6>
                    <ul>
                        <li>Make sure that all the words are spelled correctly.</li>
                        <li>Try different keywords.</li>
                        <li>Try more general keywords.</li>
                    </ul>

                </div>
            </div>
        </div>
    </div>
</section>
@endif

@if(!Request::ajax())
@section('footer_scripts')
<script src="{{ $CDN_PATH.'assets/libraries/owl.carousel/js/owl.carousel.min.js' }}"></script>
<script type="text/javascript">
var pageNumber = {!! isset($currentPage) ? $currentPage : 1 !!}
;
var ajaxModuleUrl = "{{ url('/search') }}";
</script>
<script>
    $(document).ready(function (e) {
        $(document).on("click", ".researchword", function (e) {
            var currentFoundVal = $(this).attr('data-value');
            $('#frontSearchHeaderWeb').val(currentFoundVal);
            //$('#searchbtn').click();
            $("#frmFrontSearch").submit();
        });
    });
</script>
<script type="text/javascript">
    $(document).on("click", '#load-more', function () {
        pageNumber += 1;
        $.ajax({
            type: 'POST',
            start: SetBackGround(),
            url: ajaxModuleUrl,
            data: {
                page: pageNumber,
                current_page: ajaxModuleUrl,
                '_token': $('meta[name="csrf-token"]').attr('content'),
                frontSearch: $('#searchtextboxTerm').html()
            },
            dataType: "json",
            success: function (data) {
                UnSetBackGround();
                if (data.length == 0) {
                } else {
                    $(".searchres_load .newajaxLoadmorebtn").remove();
                    $('.searchres_load').append(data.html);
                    $("#gridbody_front").find('.ajaxLoadmorebtn').remove();
                    $(".searchres_load .newajaxLoadmorebtn").html(data.loadmoreHtml);
                }
            },
            complete: function () {

            },
            error: function (data) {
            },
        });
    });
</script>
<style>
    .cms .search_ol li {
        position: relative;list-style: none; padding-left: 22px;
    }
    .cms .search_ol li:before {display:none; }
    .cms .search_ol li a{color:#133649; }
    .cms .search_ol li i {
        position: absolute;left: 0;top: 3px;
    }

</style>
@endsection
@endsection
@endif