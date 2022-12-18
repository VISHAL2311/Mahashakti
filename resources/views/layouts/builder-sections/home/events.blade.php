@php
$eventurl = App\Helpers\MyLibrary::getFront_Uri('pages', 3)['uri'];
@endphp
@if(isset($data['events']) && !empty($data['events']) && count($data['events']) > 0)
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
<section class="events_sec">
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
        <div class="row">
            @foreach($data['events'] as $event)
            @if(isset($event->fkIntImgId))
            @php                          
            $itemImg = App\Helpers\resize_image::resize($event->fkIntImgId);
            @endphp
            @else 
            @php
            $itemImg = $CDN_PATH.'assets/images/event_img1.jpg';
            @endphp
            @endif

            @if(isset($event->custom['description']))
            @php
            $description = $event->custom['description'];
            @endphp
            @else 
            @php
            $description = $event->varShortDescription;
            @endphp
            @endif
            @if($data['cols'] == 'list')
            <div class="col-sm-12 col-xs-12 animated fadeInUp">
                <div class="event_post listing">
                    @if(isset($event->fkIntImgId) && $event->fkIntImgId != '')
                    <div class="image">
                        <div class="thumbnail-container">
                            <div class="thumbnail">
                                <a title="{{ $event->varTitle }}" href="javascript:;">
                                    <img src="{{ $itemImg }}" alt="{{ $event->varTitle }}">
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="info">
                        <h5 class="sub_title"><a href="javascript:;">{{ $event->varTitle }}</a></h5>
                        @if(isset($description) && $description != '')
                        <p class="cat_div"> {!! (strlen($description) > 150) ? substr($description, 0, 150).'...' : $description !!}</p>
                        @endif
                        @if(isset($event->dtDateTime) && $event->dtDateTime != '')
                        <div class="date">{{ date('l d M, Y',strtotime($event->dtDateTime)) }}</div>
                        @endif
                    </div>
                    <div class="info_more text-right">
                        <a class="info_link" href="javascript:;" title="Read More">Read More <i class="fa fa-angle-double-right"></i></a>
                    </div>
                </div>
            </div>
            @else
            <div class="{{ $cols }} animated fadeInUp">
                <div class="event_post">
                    @if(isset($event->fkIntImgId) && $event->fkIntImgId != '')
                    <div class="image">
                        <div class="thumbnail-container">
                            <div class="thumbnail">
                                <a title="{{ $event->varTitle }}" href="javascript:;">
                                    <img src="{{ $itemImg }}" alt="{{ $event->varTitle }}">
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="info">
                        <h5 class="sub_title"><a href="javascript:;">{{ $event->varTitle }}</a></h5>
                        @if(isset($description) && $description != '')
                        <p class="cat_div"> {!! (strlen($description) > 80) ? substr($description, 0, 80).'...' : $description !!}</p>
                        @endif
                        @if(isset($event->dtDateTime) && $event->dtDateTime != '')
                        <div class="date">{{ date('l d M, Y',strtotime($event->dtDateTime)) }}</div>
                        @endif
                        <div class="info_more text-right">
                            <a class="info_link" href="javascript:;" title="Read More">Read More <i class="fa fa-angle-double-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endforeach          
        </div>
        <div class="row">
            <div class="col-sm-12 col-xs-12 animated fadeInUp text-center">               
                <a class="btn ac-border btn-more" href="{!! $eventurl !!}" title="View All Events">View All Events</a>               
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
<div class="row">
    @foreach($data['events'] as $event)
    @if(isset($event->fkIntImgId))
    @php                          
    $itemImg = App\Helpers\resize_image::resize($event->fkIntImgId);
    @endphp
    @else 
    @php
    $itemImg = $CDN_PATH.'assets/images/event_img1.jpg';
    @endphp
    @endif

    @if(isset($event->custom['description']))
    @php
    $description = $event->custom['description'];
    @endphp
    @else 
    @php
    $description = $event->varShortDescription;
    @endphp
    @endif
    @if($data['cols'] == 'list')
    <div class="col-sm-12 col-xs-12 animated fadeInUp">
        <div class="event_post listing">
            @if(isset($event->fkIntImgId) && $event->fkIntImgId != '')
            <div class="image">
                <div class="thumbnail-container">
                    <div class="thumbnail">
                        <a title="{{ $event->varTitle }}" href="javascript:;">
                            <img src="{{ $itemImg }}" alt="{{ $event->varTitle }}">
                        </a>
                    </div>
                </div>
            </div>
            @endif
            <div class="info">
                <h5 class="sub_title"><a href="javascript:;">{{ $event->varTitle }}</a></h5>
                @if(isset($description) && $description != '')
                <p class="cat_div"> {!! (strlen($description) > 150) ? substr($description, 0, 150).'...' : $description !!}</p>
                @endif
                @if(isset($event->dtDateTime) && $event->dtDateTime != '')
                <div class="date">{{ date('l d M, Y',strtotime($event->dtDateTime)) }}</div>
                @endif
            </div>
            <div class="info_more text-right">
                <a class="info_link" href="javascript:;" title="Read More">Read More <i class="fa fa-angle-double-right"></i></a>
            </div>
        </div>
    </div>
    @else
    <div class="{{ $cols }} animated fadeInUp">
        <div class="event_post">
            @if(isset($event->fkIntImgId) && $event->fkIntImgId != '')
            <div class="image">
                <div class="thumbnail-container">
                    <div class="thumbnail">
                        <a title="{{ $event->varTitle }}" href="javascript:;">
                            <img src="{{ $itemImg }}" alt="{{ $event->varTitle }}">
                        </a>
                    </div>
                </div>
            </div>
            @endif
            <div class="info">
                <h5 class="sub_title"><a href="javascript:;">{{ $event->varTitle }}</a></h5>
                @if(isset($description) && $description != '')
                <p class="cat_div"> {!! (strlen($description) > 80) ? substr($description, 0, 80).'...' : $description !!}</p>
                @endif
                @if(isset($event->dtDateTime) && $event->dtDateTime != '')
                <div class="date">{{ date('l d M, Y',strtotime($event->dtDateTime)) }}</div>
                @endif
                <div class="info_more text-right">
                    <a class="info_link" href="javascript:;" title="Read More">Read More <i class="fa fa-angle-double-right"></i></a>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endforeach          
</div>
@endif
@endif