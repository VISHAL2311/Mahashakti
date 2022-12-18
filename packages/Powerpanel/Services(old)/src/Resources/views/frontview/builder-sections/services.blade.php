@php
$blogurl = '';
@endphp
@if(isset($data['services']) && !empty($data['services']) && count($data['services']) > 0)
@php
$cols = 'col-md-4 col-sm-4 col-xs-12';
$grid = '3';
if($data['cols'] == 'grid_2_col'){
$cols = 'col-md-6 col-sm-6 col-xs-12';
$grid = '2';
}elseif ($data['cols'] == 'grid_3_col') {
$cols = 'col-md-4 col-sm-4 col-xs-12';
$grid = '3';
}elseif ($data['cols'] == 'grid_4_col') {
$cols = 'col-md-3 col-sm-3 col-xs-12';
$grid = '4';
}

if(isset($data['class'])){
$class = $data['class'];
}
if(isset($data['paginatehrml']) && $data['paginatehrml'] == true){
$pcol = $cols;
}else{
$pcol = 'item';
}
@endphp
@if(Request::segment(1) == '')
<section class="blog_sec owl-section {{ $class }}"  data-grid="{{ $grid }}">
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
                @if(isset($data['paginatehrml']) && $data['paginatehrml'] != true)
                <div class="col-12">
                    <div class="owl-carousel owl-theme owl-nav-absolute">
                        @endif
                        @foreach($data['services'] as $service)

                        @php
                        if(isset(App\Helpers\MyLibrary::getFront_Uri('services')['uri'])){
                        $moduelFrontPageUrl = App\Helpers\MyLibrary::getFront_Uri('services')['uri'];
                        $moduleFrontWithCatUrl = ($service->varAlias != false ) ? $moduelFrontPageUrl . '/' . $service->varAlias : $moduelFrontPageUrl;
                        $moduelFrontPageUrl = App\Helpers\MyLibrary::getFront_Uri('services')['uri'];
                        $recordLinkUrl = $moduelFrontPageUrl.'/'.$service->alias->varAlias;
                        }else{
                        $recordLinkUrl = '';
                        }
                        @endphp

                        @if(isset($service->fkIntImgId))
                        @php
                        $itemImg = App\Helpers\resize_image::resize($service->fkIntImgId);
                        @endphp
                        @else
                        @php
                        $itemImg = $CDN_PATH.'assets/images/blog-img1.jpg';
                        @endphp
                        @endif

                        @if(isset($service->custom['txtDescription']))
                        @php
                        $description = $service->custom['txtDescription'];
                        @endphp
                        @else
                        @php
                        $description = $service->txtDescription;
                        @endphp
                        @endif
                        @if($data['cols'] == 'list')
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="blog_post listing clearfix">
                                @if(isset($service->fkIntImgId) && $service->fkIntImgId != '')
                                <div class="blog_img">
                                    <div class="thumbnail-container">
                                        <div class="thumbnail">
                                            <a title="{{ $service->varTitle }}" href="{{ url('services/') }}/{{ $service->alias->varAlias }}">
                                                <img src="{{ $itemImg }}" alt="{{ $service->varTitle }}">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="info">
                                    @if(isset($service->dtDateTime) && $service->dtDateTime != '')
                                    <div class="date">{{ date('l d M, Y',strtotime($service->dtDateTime)) }}</div>
                                    @endif
                                    <h5 class="sub_title"><a href="" title="{{ $service->varTitle }}" alt="{{ $service->varTitle }}">{{ $service->varTitle }}</a></h5>
                                    @if(isset($description) && $description != '')
                                    <p>{!! (strlen($description) > 150) ? substr($description, 0, 150).'...' : $description !!}</p>
                                    @endif
                                    <a class="btn ac-border" href="{{ url('services/') }}/{{ $service->alias->varAlias }}" title="Read More">Read More</a>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="{{ $pcol }}">
                            <div class="blog_post">
                                @if(isset($service->fkIntImgId) && $service->fkIntImgId != '')
                                <div class="blog_img">
                                    <div class="thumbnail-container">
                                        <div class="thumbnail">
                                            <a title="{{ $service->varTitle }}" href="{{ url('services/') }}/{{ $service->alias->varAlias }}">
                                                <img src="{{ $itemImg }}" alt="{{ $service->varTitle }}">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="info">
                                    @if(isset($service->dtDateTime) && $service->dtDateTime != '')
                                    <div class="date">{{ date('l d M, Y',strtotime($service->dtDateTime)) }}</div>
                                    @endif
                                    <h5 class="sub_title"><a href="{{ url('services/') }}/{{ $service->alias->varAlias }}" title="{{ $service->varTitle }}" alt="{{ $service->varTitle }}">{{ $service->varTitle }}</a></h5>
                                    @if(isset($description) && $description != '')
                                    <p>{!! (strlen($description) > 80) ? substr($description, 0, 80).'...' : $description !!}</p>
                                    @endif
                                    <a class="btn ac-border " href="{{ url('services/') }}/{{ $service->alias->varAlias }}" title="Read More">Read More</a>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                        @if(isset($data['paginatehrml']) && $data['paginatehrml'] != true)
                    </div>
                </div>
                @endif
            </div>
            <div class="row">
                <div class="col-sm-12 col-xs-12 animated fadeInUp text-center">
                    <a class="btn ac-border btn-more" href="{{ url('services') }}" title="View All">View All</a>
                </div>
            </div>
        </div>

    </div>
</section>
@else
<section class="blog_sec {{ $class }}">
    <div class="container">
        @if(isset($data['desc']) && $data['desc'] != '')
        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <p>{!! $data['desc'] !!}</p>
            </div>
        </div>
        @endif 
        <div data-grid="{{ $grid }}">
            <div class="row">
               
                        @foreach($data['services'] as $service)
                        @php
                        if(isset(App\Helpers\MyLibrary::getFront_Uri('services')['uri'])){
                        $moduelFrontPageUrl = App\Helpers\MyLibrary::getFront_Uri('services')['uri'];
                        $moduleFrontWithCatUrl = ($service->varAlias != false ) ? $moduelFrontPageUrl . '/' . $service->varAlias : $moduelFrontPageUrl;
                        $categoryRecordAlias = App\Helpers\Mylibrary::getRecordAliasByModuleNameRecordId('services',$service->intFkCategory);
                        $recordLinkUrl = $moduleFrontWithCatUrl.'/'.$service->alias->varAlias;
                        }else{
                        $recordLinkUrl = '';
                        }
                        @endphp
                        @if(isset($service->fkIntImgId))
                        @php
                        $itemImg = App\Helpers\resize_image::resize($service->fkIntImgId);
                        @endphp
                        @else
                        @php
                        $itemImg = $CDN_PATH.'assets/images/blog-img1.jpg';
                        @endphp
                        @endif

                        @if(isset($service->custom['txtDescription']))
                        @php
                        $description = $service->custom['txtDescription'];
                        @endphp
                        @else
                        @php
                        $description = $service->txtDescription;
                        @endphp
                        @endif
                        @if($data['cols'] == 'list')
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="blog_post listing clearfix">
                                @if(isset($service->fkIntImgId) && $service->fkIntImgId != '')
                                <div class="blog_img">
                                    <div class="thumbnail-container">
                                        <div class="thumbnail">
                                            <a title="{{ $service->varTitle }}" href="{{ url('services/') }}/{{ $service->alias->varAlias }}">
                                                <img src="{{ $itemImg }}" alt="{{ $service->varTitle }}">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="info">
                                    @if(isset($service->dtDateTime) && $service->dtDateTime != '')
                                    <div class="date">{{ date('l d M, Y',strtotime($service->dtDateTime)) }}</div>
                                    @endif
                                    <h5 class="sub_title"><a href="" title="{{ $service->varTitle }}" alt="{{ $service->varTitle }}">{{ $service->varTitle }}</a></h5>
                                    @if(isset($description) && $description != '')
                                    <p>{!! (strlen($description) > 150) ? substr($description, 0, 150).'...' : $description !!}</p>
                                    @endif
                                    <a class="btn ac-border" href="{{ url('services/') }}/{{ $service->alias->varAlias }}" title="Read More">Read More</a>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="{{ $cols }}">
                            <div class="blog_post">
                                @if(isset($service->fkIntImgId) && $service->fkIntImgId != '')
                                <div class="blog_img">
                                    <div class="thumbnail-container">
                                        <div class="thumbnail">
                                            <a title="{{ $service->varTitle }}" href="{{ url('services/') }}/{{ $service->alias->varAlias }}">
                                                <img src="{{ $itemImg }}" alt="{{ $service->varTitle }}">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="info">
                                    @if(isset($service->dtDateTime) && $service->dtDateTime != '')
                                    <div class="date">{{ date('l d M, Y',strtotime($service->dtDateTime)) }}</div>
                                    @endif
                                    <h5 class="sub_title"><a href="{{ url('services/') }}/{{ $service->alias->varAlias }}" title="{{ $service->varTitle }}" alt="{{ $service->varTitle }}">{{ $service->varTitle }}</a></h5>
                                    @if(isset($description) && $description != '')
                                    <p>{!! (strlen($description) > 80) ? substr($description, 0, 80).'...' : $description !!}</p>
                                    @endif
                                    <a class="btn ac-border " href="{{ url('services/') }}/{{ $service->alias->varAlias }}" title="Read More">Read More</a>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                        
            </div>
            @if(Request::segment(1) != '' && isset($data['paginatehrml']) && $data['paginatehrml'] == true)
            @if($data['services']->total() > $data['services']->perPage())
            <div class="row">
                <div class="col-sm-12 n-mt-30 text-center" data-aos="fade-up">
                    {{ $data['services']->links() }}
                </div>
            </div>
            @endif
            @endif
        </div>
    </div>
</section>
@endif
@endif
