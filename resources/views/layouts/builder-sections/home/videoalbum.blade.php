@php
$videogalleryurl = App\Helpers\MyLibrary::getFront_Uri('pages', 2)['uri'];
@endphp
@if(isset($data['videogallery']) && !empty($data['videogallery']) && count($data['videogallery']) > 0)
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
<section class="videogallery_sec">
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
        <div class="videogallery_slide">
            <div class="row">
                @foreach($data['videogallery'] as $videogallery)

                @if(isset($videogallery->fkIntImgId))
                @php                          
                $itemImg = App\Helpers\resize_image::resize($videogallery->fkIntImgId);
                @endphp
                @else 
                @php
                $itemImg = $CDN_PATH.'assets/images/videogallery-img1.jpg';
                @endphp
                @endif

                @if(isset($videogallery->custom['description']))
                @php
                $description = $videogallery->custom['description'];
                @endphp
                @else 
                @php
                $description = $videogallery->varShortDescription;
                @endphp
                @endif
                @if($data['cols'] == 'list')
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="videogallery_post listing clearfix">
                        @if(isset($videogallery->fkIntImgId) && $videogallery->fkIntImgId != '')
                        <div class="videogallery_img">
                            <div class="thumbnail-container">
                                <div class="thumbnail">

                                    <img src="{{ $itemImg }}" alt="{{ $videogallery->varTitle }}">

                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="info">
                            @if(isset($videogallery->dtDateTime) && $videogallery->dtDateTime != '')
                            <div class="date">{{ date('l d M, Y',strtotime($videogallery->dtDateTime)) }}</div>
                            @endif
                            <h5 class="sub_title">{{ $videogallery->varTitle }}</h5>
                            @if(isset($description) && $description != '')
                            <p>{!! (strlen($description) > 150) ? substr($description, 0, 150).'...' : $description !!}</p>
                            @endif

                        </div>
                    </div>
                </div>
                @else
                <div class="{{ $cols }} animated fadeInUp">
                    <div class="videogallery_post">
                        @if(isset($videogallery->fkIntImgId) && $videogallery->fkIntImgId != '')
                        <div class="videogallery_img">
                            <div class="thumbnail-container">
                                <div class="thumbnail">

                                    <img src="{{ $itemImg }}" alt="{{ $videogallery->varTitle }}">

                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="info">
                            @if(isset($videogallery->dtDateTime) && $videogallery->dtDateTime != '')
                            <div class="date">{{ date('l d M, Y',strtotime($videogallery->dtDateTime)) }}</div>
                            @endif
                            <h5 class="sub_title">{{ $videogallery->varTitle }}</h5>
                            @if(isset($description) && $description != '')
                            <p>{!! (strlen($description) > 80) ? substr($description, 0, 80).'...' : $description !!}</p>
                            @endif

                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            <div class="row">
                <div class="col-sm-12 col-xs-12 animated fadeInUp text-center">               
                    <a class="btn ac-border btn-more" href="{!! $videogalleryurl !!}" title="More All VideoGallery">More All VideoGallery</a>               
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
<div class="videogallery_slide">
    <div class="row">
        @foreach($data['videogallery'] as $videogallery)

        @if(isset($videogallery->fkIntImgId))
        @php                          
        $itemImg = App\Helpers\resize_image::resize($videogallery->fkIntImgId);
        @endphp
        @else 
        @php
        $itemImg = $CDN_PATH.'assets/images/videogallery-img1.jpg';
        @endphp
        @endif

        @if(isset($videogallery->custom['description']))
        @php
        $description = $videogallery->custom['description'];
        @endphp
        @else 
        @php
        $description = $videogallery->varShortDescription;
        @endphp
        @endif
        @if($data['cols'] == 'list')
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="videogallery_post listing clearfix">
                @if(isset($videogallery->fkIntImgId) && $videogallery->fkIntImgId != '')
                <div class="videogallery_img">
                    <div class="thumbnail-container">
                        <div class="thumbnail">

                            <img src="{{ $itemImg }}" alt="{{ $videogallery->varTitle }}">

                        </div>
                    </div>
                </div>
                @endif
                <div class="info">
                    @if(isset($videogallery->dtDateTime) && $videogallery->dtDateTime != '')
                    <div class="date">{{ date('l d M, Y',strtotime($videogallery->dtDateTime)) }}</div>
                    @endif
                    <h5 class="sub_title">{{ $videogallery->varTitle }}</h5>
                    @if(isset($description) && $description != '')
                    <p>{!! (strlen($description) > 150) ? substr($description, 0, 150).'...' : $description !!}</p>
                    @endif

                </div>
            </div>
        </div>
        @else
        <div class="{{ $cols }} animated fadeInUp">
            <div class="videogallery_post">
                @if(isset($videogallery->fkIntImgId) && $videogallery->fkIntImgId != '')
                <div class="videogallery_img">
                    <div class="thumbnail-container">
                        <div class="thumbnail">

                            <img src="{{ $itemImg }}" alt="{{ $videogallery->varTitle }}">

                        </div>
                    </div>
                </div>
                @endif
                <div class="info">
                    @if(isset($videogallery->dtDateTime) && $videogallery->dtDateTime != '')
                    <div class="date">{{ date('l d M, Y',strtotime($videogallery->dtDateTime)) }}</div>
                    @endif
                    <h5 class="sub_title">{{ $videogallery->varTitle }}</h5>
                    @if(isset($description) && $description != '')
                    <p>{!! (strlen($description) > 80) ? substr($description, 0, 80).'...' : $description !!}</p>
                    @endif
                </div>
            </div>
        </div>
        @endif
        @endforeach
    </div>
    <div class="row">
        <div class="col-sm-12 col-xs-12 animated fadeInUp text-center">               
            <a class="btn ac-border btn-more" href="{!! $videogalleryurl !!}" title="More All VideoGallery">More All VideoGallery</a>               
        </div>
    </div>
</div>
@endif
@endif