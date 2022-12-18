@php
$photoalbumurl = App\Helpers\MyLibrary::getFront_Uri('pages', 2)['uri'];
@endphp
@if(isset($data['photoalbum']) && !empty($data['photoalbum']) && count($data['photoalbum']) > 0)
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
<section class="photoalbum_sec">
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
        <div class="photoalbum_slide">
            <div class="row">
                @foreach($data['photoalbum'] as $photoalbum)

                @if(isset($photoalbum->fkIntImgId))
                @php                          
                $itemImg = App\Helpers\resize_image::resize($photoalbum->fkIntImgId);
                @endphp
                @else 
                @php
                $itemImg = $CDN_PATH.'assets/images/photoalbum-img1.jpg';
                @endphp
                @endif

                @if(isset($photoalbum->custom['description']))
                @php
                $description = $photoalbum->custom['description'];
                @endphp
                @else 
                @php
                $description = $photoalbum->varShortDescription;
                @endphp
                @endif
                @if($data['cols'] == 'list')
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="photoalbum_post listing clearfix">
                        @if(isset($photoalbum->fkIntImgId) && $photoalbum->fkIntImgId != '')
                        <div class="photoalbum_img">
                            <div class="thumbnail-container">
                                <div class="thumbnail">
                                    <a title="{{ $photoalbum->varTitle }}" href="javascript:;">
                                        <img src="{{ $itemImg }}" alt="{{ $photoalbum->varTitle }}">
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="info">
                            @if(isset($photoalbum->dtDateTime) && $photoalbum->dtDateTime != '')
                            <div class="date">{{ date('l d M, Y',strtotime($photoalbum->dtDateTime)) }}</div>
                            @endif
                            <h5 class="sub_title"><a href="javascript:;" title="{{ $photoalbum->varTitle }}" alt="{{ $photoalbum->varTitle }}">{{ $photoalbum->varTitle }}</a></h5>
                            @if(isset($description) && $description != '')
                            <p>{!! (strlen($description) > 150) ? substr($description, 0, 150).'...' : $description !!}</p>
                            @endif
                            <a class="btn ac-border " href="javascript:;" title="Read More">Read More</a>
                        </div>
                    </div>
                </div>
                @else
                <div class="{{ $cols }} animated fadeInUp">
                    <div class="photoalbum_post">
                        @if(isset($photoalbum->fkIntImgId) && $photoalbum->fkIntImgId != '')
                        <div class="photoalbum_img">
                            <div class="thumbnail-container">
                                <div class="thumbnail">
                                    <a title="{{ $photoalbum->varTitle }}" href="javascript:;">
                                        <img src="{{ $itemImg }}" alt="{{ $photoalbum->varTitle }}">
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="info">
                            @if(isset($photoalbum->dtDateTime) && $photoalbum->dtDateTime != '')
                            <div class="date">{{ date('l d M, Y',strtotime($photoalbum->dtDateTime)) }}</div>
                            @endif
                            <h5 class="sub_title"><a href="javascript:;" title="{{ $photoalbum->varTitle }}" alt="{{ $photoalbum->varTitle }}">{{ $photoalbum->varTitle }}</a></h5>
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
                    <a class="btn ac-border btn-more" href="{!! $photoalbumurl !!}" title="More All PhotoAlbum">More All PhotoAlbum</a>               
                </div>
            </div>
        </div>
    </div>
</section>
@else
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
<div class="photoalbum_slide">
    <div class="row">
        @foreach($data['photoalbum'] as $photoalbum)

        @if(isset($photoalbum->fkIntImgId))
        @php                          
        $itemImg = App\Helpers\resize_image::resize($photoalbum->fkIntImgId);
        @endphp
        @else 
        @php
        $itemImg = $CDN_PATH.'assets/images/photoalbum-img1.jpg';
        @endphp
        @endif

        @if(isset($photoalbum->custom['description']))
        @php
        $description = $photoalbum->custom['description'];
        @endphp
        @else 
        @php
        $description = $photoalbum->varShortDescription;
        @endphp
        @endif
        @if($data['cols'] == 'list')
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="photoalbum_post listing clearfix">
                @if(isset($photoalbum->fkIntImgId) && $photoalbum->fkIntImgId != '')
                <div class="photoalbum_img">
                    <div class="thumbnail-container">
                        <div class="thumbnail">
                            <a title="{{ $photoalbum->varTitle }}" href="javascript:;">
                                <img src="{{ $itemImg }}" alt="{{ $photoalbum->varTitle }}">
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                <div class="info">
                    @if(isset($photoalbum->dtDateTime) && $photoalbum->dtDateTime != '')
                    <div class="date">{{ date('l d M, Y',strtotime($photoalbum->dtDateTime)) }}</div>
                    @endif
                    <h5 class="sub_title"><a href="javascript:;" title="{{ $photoalbum->varTitle }}" alt="{{ $photoalbum->varTitle }}">{{ $photoalbum->varTitle }}</a></h5>
                    @if(isset($description) && $description != '')
                    <p>{!! (strlen($description) > 150) ? substr($description, 0, 150).'...' : $description !!}</p>
                    @endif
                    <a class="btn ac-border " href="javascript:;" title="Read More">Read More</a>
                </div>
            </div>
        </div>
        @else
        <div class="{{ $cols }} animated fadeInUp">
            <div class="photoalbum_post">
                @if(isset($photoalbum->fkIntImgId) && $photoalbum->fkIntImgId != '')
                <div class="photoalbum_img">
                    <div class="thumbnail-container">
                        <div class="thumbnail">
                            <a title="{{ $photoalbum->varTitle }}" href="javascript:;">
                                <img src="{{ $itemImg }}" alt="{{ $photoalbum->varTitle }}">
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                <div class="info">
                    @if(isset($photoalbum->dtDateTime) && $photoalbum->dtDateTime != '')
                    <div class="date">{{ date('l d M, Y',strtotime($photoalbum->dtDateTime)) }}</div>
                    @endif
                    <h5 class="sub_title"><a href="javascript:;" title="{{ $photoalbum->varTitle }}" alt="{{ $photoalbum->varTitle }}">{{ $photoalbum->varTitle }}</a></h5>
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
            <a class="btn ac-border btn-more" href="{!! $photoalbumurl !!}" title="More All Blogs">More All Blogs</a>               
        </div>
    </div>
</div>
@endif
@endif