@if(isset($data) && $data != '')
<section class="about_section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="row align-items-center">
                    <div class="col-md-12 col-sm-12" data-aos="fade-up">
                        <h1 class="main-title text-left">{{ $data['title'] }}</h1>
                        <h4 class="s-title text-left">{{ $data['tagline'] }}</h4>
                        <div class="desc">
                            {!! $data['content'] !!}
                        </div>
                        @if((isset($data['btntitle']) && !empty($data['btntitle'])) && (isset($data['btnurl']) && !empty($data['btnurl'])))
                        <div class="request-btn-wrap">
                            <a href="{{ $data['btnurl'] }}" class="text-uppercase ac-btn" title="{{ $data['btntitle'] }}">{{ $data['btntitle'] }}</a>
                        </div>
                        @endif    
                    </div>
                </div>   
            </div>
        </div>
    </div>
</section>
@endif