@php
$publicationurl = App\Helpers\MyLibrary::getFront_Uri('pages', 2)['uri'];
@endphp
@if(isset($data['publication']) && !empty($data['publication']) && count($data['publication']) > 0)
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
<section class="publication_sec">
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
            @foreach($data['publication'] as $publication)
            @if(isset($publication->custom['img']))
            @php                          
            $itemImg = App\Helpers\resize_image::resize($publication->custom['img']);
            @endphp
            @else 
            @php
            $itemImg = App\Helpers\resize_image::resize($publication->fkIntImgId);
            @endphp
            @endif

            @if(isset($publication->custom['description']))
            @php
            $description = $publication->custom['description'];
            @endphp
            @else 
            @php
            $description = $publication->varShortDescription;
            @endphp
            @endif
            @if($data['cols'] == 'list')
            <div class="col-md-12 col-sm-12 col-xs-12 animated fadeInUp">
                <div class="publication_post publication_post_center">
                    @if(isset($publication->dtDateTime) && $publication->dtDateTime != '')
                    <div class="date">
                        <span>{{ date('d',strtotime($publication->dtDateTime)) }}</span><span>{{ date('M',strtotime($publication->dtDateTime)) }}</span><span>{{ date('Y',strtotime($publication->dtDateTime)) }}</span>
                    </div>
                    @endif
                    <div class="info">
                        <h5 class="sub_title"><a href="javascript:;" title="{{ $publication->varTitle }}" alt="{{ $publication->varTitle }}">{{ $publication->varTitle }}</a></h5>
                        <div class="info_dtl">
                            @if(isset($description) && $description != '')
                            <p>{!! (strlen($description) > 150) ? substr($description, 0, 150).'...' : $description !!}</p>
                            @endif
                            <a href="javascript:;" class="n-more" title="Read More">[ Read More ]</a>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="{{ $cols }} animated fadeInUp">
                <div class="publication_post">
                    @if(isset($publication->dtDateTime) && $publication->dtDateTime != '')
                    <div class="date">
                        <span>{{ date('d',strtotime($publication->dtDateTime)) }}</span><span>{{ date('M',strtotime($publication->dtDateTime)) }}</span><span>{{ date('Y',strtotime($publication->dtDateTime)) }}</span>
                    </div>
                    @endif
                    <div class="info">
                        <h5 class="sub_title"><a href="javascript:;" title="{{ $publication->varTitle }}" alt="{{ $publication->varTitle }}">{{ $publication->varTitle }}</a></h5>
                        <div class="info_dtl">
                            @if(isset($description) && $description != '')
                            <p>{!! (strlen($description) > 80) ? substr($description, 0, 80).'...' : $description !!}</p>
                            @endif
                            <a href="javascript:;" class="n-more" title="Read More">[ Read More ]</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>

        <div class="row">
            <div class="col-sm-12 col-xs-12 animated fadeInUp text-center">
                <a class="btn ac-border btn-more" href="{!! $publicationurl !!}" title="More All Publications">More All Publications</a>
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
<div class="row">
    @foreach($data['publication'] as $publication)
    @if(isset($publication->custom['img']))
    @php                          
    $itemImg = App\Helpers\resize_image::resize($publication->custom['img']);
    @endphp
    @else 
    @php
    $itemImg = App\Helpers\resize_image::resize($publication->fkIntImgId);
    @endphp
    @endif

    @if(isset($publication->custom['description']))
    @php
    $description = $publication->custom['description'];
    @endphp
    @else 
    @php
    $description = $publication->varShortDescription;
    @endphp
    @endif
    @if($data['cols'] == 'list')
    <div class="col-md-12 col-sm-12 col-xs-12 animated fadeInUp">
        <div class="publication_post publication_post_center">
            @if(isset($publication->dtDateTime) && $publication->dtDateTime != '')
            <div class="date">
                <span>{{ date('d',strtotime($publication->dtDateTime)) }}</span><span>{{ date('M',strtotime($publication->dtDateTime)) }}</span><span>{{ date('Y',strtotime($publication->dtDateTime)) }}</span>
            </div>
            @endif
            <div class="info">
                <h5 class="sub_title"><a href="javascript:;" title="{{ $publication->varTitle }}" alt="{{ $publication->varTitle }}">{{ $publication->varTitle }}</a></h5>
                <div class="info_dtl">
                    @if(isset($description) && $description != '')
                    <p>{!! (strlen($description) > 150) ? substr($description, 0, 150).'...' : $description !!}</p>
                    @endif
                    <a href="javascript:;" class="n-more" title="Read More">[ Read More ]</a>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="{{ $cols }} animated fadeInUp">
        <div class="publication_post">
            @if(isset($publication->dtDateTime) && $publication->dtDateTime != '')
            <div class="date">
                <span>{{ date('d',strtotime($publication->dtDateTime)) }}</span><span>{{ date('M',strtotime($publication->dtDateTime)) }}</span><span>{{ date('Y',strtotime($publication->dtDateTime)) }}</span>
            </div>
            @endif
            <div class="info">
                <h5 class="sub_title"><a href="javascript:;" title="{{ $publication->varTitle }}" alt="{{ $publication->varTitle }}">{{ $publication->varTitle }}</a></h5>
                <div class="info_dtl">
                    @if(isset($description) && $description != '')
                    <p>{!! (strlen($description) > 80) ? substr($description, 0, 80).'...' : $description !!}</p>
                    @endif
                    <a href="javascript:;" class="n-more" title="Read More">[ Read More ]</a>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endforeach
</div>

<div class="row">
    <div class="col-sm-12 col-xs-12 animated fadeInUp text-center">
        <a class="btn ac-border btn-more" href="{!! $publicationurl !!}" title="More All Publications">More All Publications</a>
    </div>
</div>
@endif
@endif