@if(isset($data['testimonial']) && count($data['testimonial']) > 0)
@if(isset($data['class']) && !empty($data['class']))
<section class="testimonials_section {{ $data['class'] }}">
    @else
    <section class="testimonials_section" data-aos="fade-up">
        @endif
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h2 class="cm-title">{{ $data['title'] }}</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="testimonials_slider owl-carousel" data-aos="fade-up">
                        @foreach($data['testimonial'] as $index => $testimonial)
                        @php
                        if(isset(App\Helpers\MyLibrary::getFront_Uri('testimonial')['uri'])){
                        $moduelFrontPageUrl = App\Helpers\MyLibrary::getFront_Uri('testimonial')['uri'];
                        $recordLinkUrl = $moduelFrontPageUrl;
                        }else{
                        $recordLinkUrl = '';
                        }
                        @endphp
                        <div class="item">
                            <div class="desc">
                                {!! htmlspecialchars_decode($testimonial->txtDescription) !!}
                            </div>
                            <div class="name text-capitalize">
                                {{$testimonial->varTitle}} -
                                @if(isset($testimonial->varStarRating) && !empty($testimonial->varStarRating))
                                @php $rate = ''; @endphp
                                @for($i = 1; $i <= 5; $i++) @php $check=$i <=$testimonial->varStarRating ? 'icon-rating-star' : ''; @endphp
                                    @php $rate .= '<span class="'.$check.'"></span>&nbsp;'; @endphp
                                    @endfor
                                    <span class="ratting">
                                        {!! $rate !!}
                                    </span>
                                    @endif
                            </div>
                        </div>
                        @endforeach

                    </div>
                    <div class="testiminial-button text-center">
                        <a href="{{ $recordLinkUrl }}" class="ac-btn text-uppercase" title="View All Success Stories">View All Success Stories</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif