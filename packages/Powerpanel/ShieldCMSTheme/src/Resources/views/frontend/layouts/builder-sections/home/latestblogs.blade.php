@php
$blogurl = App\Helpers\MyLibrary::getFront_Uri('pages', 4)['uri'];
@endphp
@if(isset($data['blogs']) && !empty($data['blogs']) && count($data['blogs']) > 0)
@php 
$cols = 'col-md-4 col-sm-4 col-xs-12';
if($data['cols'] == 'grid_2_col'){
$cols = 'col-md-6 col-sm-6 col-xs-12';
}elseif ($data['cols'] == 'grid_3_col') {
$cols = 'col-md-4 col-sm-4 col-xs-12';
}elseif ($data['cols'] == 'grid_4_col') {
$cols = 'col-md-3 col-sm-6 col-xs-12';
}
@endphp
@if(Request::segment(1) == '')
<section class="blog_sec">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-xs-12 animated fadeInUp">
                <div class="same_title text-center">
                    @if(isset($data['title']) && $data['title'] != '')
                    <h2 class="title_div">{{ $data['title'] }}</h2>
                    @endif
                    @if(isset($data['desc']) && $data['desc'] != '')
                    <p>{!! $data['desc'] !!}</p>
                    @endif
                </div>
            </div>
        </div>  

        <div class="blog_slide">
            <div class="row">
                @foreach($data['blogs'] as $blog)

                @if(isset($blog->fkIntImgId))
                @php                          
                $itemImg = App\Helpers\resize_image::resize($blog->fkIntImgId);
                @endphp
                @else 
                @php
                $itemImg = $CDN_PATH.'assets/images/blog-img1.jpg';
                @endphp
                @endif

                @if(isset($blog->custom['description']))
                @php
                $description = $blog->custom['description'];
                @endphp
                @else 
                @php
                $description = $blog->varShortDescription;
                @endphp
                @endif
                @if($data['cols'] == 'list')
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="blog_post listing clearfix">
                        @if(isset($blog->fkIntImgId) && $blog->fkIntImgId != '')
                        <div class="blog_img">
                            <div class="thumbnail-container">
                                <div class="thumbnail">
                                    <a title="{{ $blog->varTitle }}" href="javascript:;">
                                        <img src="{{ $itemImg }}" alt="{{ $blog->varTitle }}">
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="info">
                            @if(isset($blog->dtDateTime) && $blog->dtDateTime != '')
                            <div class="date">{{ date('l d M, Y',strtotime($blog->dtDateTime)) }}</div>
                            @endif
                            <h5 class="sub_title"><a href="javascript:;" title="{{ $blog->varTitle }}" alt="{{ $blog->varTitle }}">{{ $blog->varTitle }}</a></h5>
                            @if(isset($description) && $description != '')
                            <p>{!! (strlen($description) > 150) ? substr($description, 0, 150).'...' : $description !!}</p>
                            @endif
                            <a class="btn ac-border " href="javascript:;" title="Read More">Read More</a>
                        </div>
                    </div>
                </div>
                @else
                <div class="{{ $cols }} animated fadeInUp">
                    <div class="blog_post">
                        @if(isset($blog->fkIntImgId) && $blog->fkIntImgId != '')
                        <div class="blog_img">
                            <div class="thumbnail-container">
                                <div class="thumbnail">
                                    <a title="{{ $blog->varTitle }}" href="javascript:;">
                                        <img src="{{ $itemImg }}" alt="{{ $blog->varTitle }}">
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="info">
                            @if(isset($blog->dtDateTime) && $blog->dtDateTime != '')
                            <div class="date">{{ date('l d M, Y',strtotime($blog->dtDateTime)) }}</div>
                            @endif
                            <h5 class="sub_title"><a href="javascript:;" title="{{ $blog->varTitle }}" alt="{{ $blog->varTitle }}">{{ $blog->varTitle }}</a></h5>
                            @if(isset($description) && $description != '')
                            <p>{!! (strlen($description) > 80) ? substr($description, 0, 80).'...' : $description !!}</p>
                            @endif
                            <a class="btn ac-border " href="javascript:;" title="Read More">Read More</a>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            <div class="row">
                <div class="col-sm-12 col-xs-12 animated fadeInUp text-center">               
                    <a class="btn ac-border btn-more" href="{!! $blogurl !!}" title="View All">View All</a>               
                </div>
            </div>
        </div>
    </div>
</section>
@else
<div class="row">
    <div class="col-sm-12 col-xs-12 animated fadeInUp">
        <div class="same_title text-center">
           
            @if(isset($data['desc']) && $data['desc'] != '')
            <p>{!! $data['desc'] !!}</p>
            @endif
        </div>
    </div>
</div>  

<div class="blog_slide">
    <div class="row">
        @foreach($data['blogs'] as $blog)

        @if(isset($blog->fkIntImgId))
        @php                          
        $itemImg = App\Helpers\resize_image::resize($blog->fkIntImgId);
        @endphp
        @else 
        @php
        $itemImg = $CDN_PATH.'assets/images/blog-img1.jpg';
        @endphp
        @endif

        @if(isset($blog->custom['description']))
        @php
        $description = $blog->custom['description'];
        @endphp
        @else 
        @php
        $description = $blog->varShortDescription;
        @endphp
        @endif
        @if($data['cols'] == 'list')
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="blog_post listing clearfix">
                @if(isset($blog->fkIntImgId) && $blog->fkIntImgId != '')
                <div class="blog_img">
                    <div class="thumbnail-container">
                        <div class="thumbnail">
                            <a title="{{ $blog->varTitle }}" href="javascript:;">
                                <img src="{{ $itemImg }}" alt="{{ $blog->varTitle }}">
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                <div class="info">
                    @if(isset($blog->dtDateTime) && $blog->dtDateTime != '')
                    <div class="date">{{ date('l d M, Y',strtotime($blog->dtDateTime)) }}</div>
                    @endif
                    <h5 class="sub_title"><a href="javascript:;" title="{{ $blog->varTitle }}" alt="{{ $blog->varTitle }}">{{ $blog->varTitle }}</a></h5>
                    @if(isset($description) && $description != '')
                    <p>{!! (strlen($description) > 150) ? substr($description, 0, 150).'...' : $description !!}</p>
                    @endif
                    <a class="btn ac-border " href="javascript:;" title="Read More">Read More</a>
                </div>
            </div>
        </div>
        @else
        <div class="{{ $cols }} animated fadeInUp">
            <div class="blog_post">
                @if(isset($blog->fkIntImgId) && $blog->fkIntImgId != '')
                <div class="blog_img">
                    <div class="thumbnail-container">
                        <div class="thumbnail">
                            <a title="{{ $blog->varTitle }}" href="javascript:;">
                                <img src="{{ $itemImg }}" alt="{{ $blog->varTitle }}">
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                <div class="info">
                    @if(isset($blog->dtDateTime) && $blog->dtDateTime != '')
                    <div class="date">{{ date('l d M, Y',strtotime($blog->dtDateTime)) }}</div>
                    @endif
                    <h5 class="sub_title"><a href="javascript:;" title="{{ $blog->varTitle }}" alt="{{ $blog->varTitle }}">{{ $blog->varTitle }}</a></h5>
                    @if(isset($description) && $description != '')
                    <p>{!! (strlen($description) > 80) ? substr($description, 0, 80).'...' : $description !!}</p>
                    @endif
                    <a class="btn ac-border " href="javascript:;" title="Read More">Read More</a>
                </div>
            </div>
        </div>
        @endif
        @endforeach
    </div>
</div>
@endif
@endif