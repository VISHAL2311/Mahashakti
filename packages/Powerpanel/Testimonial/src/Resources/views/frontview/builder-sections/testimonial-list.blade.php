@if(isset($data['testimonial']) && $data['testimonial']->count() > 0)
<section class="inner-page-container testimonial">
    <div class="container">
        <div class="row">
            @foreach($data['testimonial'] as $testimonial_info)
            <div class="col-xl-4 col-lg-6 col-sm-12 my-3" data-aos="fade-up">
                <div class="testimonial-detail text-center">
                    <div class="desc">
                        <i class="icon-quote"></i>
                        {!! htmlspecialchars_decode(nl2br($testimonial_info->txtDescription)) !!}
                    </div>
                    <div class="testimonial-name">
                        <div class="test_title">
                            - {{ htmlspecialchars_decode(str_limit($testimonial_info->varTitle, 50)) }}
                        </div>
                        @if(isset($testimonial_info->varStarRating) && !empty($testimonial_info->varStarRating))
                        @php $rate = ''; @endphp
                        @for($i = 1; $i <= 5; $i++) @php $check=$i <=$testimonial_info->varStarRating ? 'icon-rating-star' : ''; @endphp
                            @php $rate .= '<span class="'.$check.'"></span>&nbsp;'; @endphp
                            @endfor
                            <div class="ratting">
                                {!! $rate !!}
                            </div>
                            @endif
                    </div>
                </div>
            </div>
            @endforeach
            @if(isset($data['testimonial']) && $data['testimonial']->total() > $data['testimonial']->perPage())
            <div class="col-12">
                <div class="pagination" id="ClientReviews">
                    {{ $data['testimonial']->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@else
<section class="inner-page-container">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h2>Coming Soon...</h2>
            </div>
        </div>
    </div>
</section>
@endif