@php
$abouturl = '';
@endphp
@if($data['alignment'] == 'home-lft-txt')
<section class="about_sec">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 about-left">
                <h1 class="cm-title text-uppercase" data-aos="fade-left">{{ $data['title'] }}</h1>
                <div class="info" data-aos="fade-right">
                    {!! $data['content'] !!}
                </div>
                <a class="ac-btn" href="{{url('about-us')}}" title="About Us" data-aos="fade-left">ABOUT US</a>
            </div>
        </div>
    </div>
</section>
@elseif($data['alignment'] == 'home-rt-txt')
<section class="about_sec" data-aos="fade-up">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 about-left">
                <h3 class="cm-title text-uppercase">{{ $data['title'] }}</h3>                
                <div class="info">
                    {!! $data['content'] !!}
                </div>
                <a class="ac-btn" href="{!! $data['btnurl'] !!}" title="About Us">About Us</a>
            </div>
        </div>
    </div>
</section>
@elseif($data['alignment'] == 'home-top-txt')
<section class="about_sec" data-aos="fade-up">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-xs-12 about-left">
                <div class='about_full'>
                    <h1 class="cm-title text-uppercase">{{ $data['title'] }}</h1>
                    <div class="info">
                        {!! $data['content'] !!}
                    </div>
                    <a class="ac-btn" href="{!! $data['btnurl'] !!}" title="About Us">About Us</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endif