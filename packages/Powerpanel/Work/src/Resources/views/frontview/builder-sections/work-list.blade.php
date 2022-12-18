@if(!empty($data['work']) && count($data['work'])>0)
<section class="work_list inner-page-container">
    <div class="container">
        <div class="row">
            @foreach($data['work'] as  $index => $workdata)
            <div class="col-xl-6 col-sm-6 col-12 gap mt-5" data-aos="fade-up">
                <div class="work_box">
                    <div class="image img_hvr img-hvr_hvr">
                        <a href="{{ url('our-work') }}/{{ $workdata->alias->varAlias }}" title="{{ ucwords($workdata->varTitle) }}">
                            <div class="thumbnail-container">
                                <div class="thumbnail">
                                    <img class="lazy" data-src="{{ App\Helpers\resize_image::resize($workdata->fkIntImgId,509,286)}}" src="{{ App\Helpers\resize_image::resize($workdata->fkIntImgId,509,286)}}" alt="{{ htmlspecialchars_decode($workdata->varTitle) }}">
                                    <!-- <span class="mask">
                                        <i class="fa fa-link"></i>
                                    </span> -->
                                    <div class="overlay"></div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="info">
                        <h3 class="list_title">
                            <!-- <a href="{{ url('our-work') }}/{{ $workdata->alias->varAlias }}" title="{{ ucwords($workdata->varTitle) }}">{{ htmlspecialchars_decode(str_limit($workdata->varTitle, 20)) }} </a> -->
                            <a href="{{ url('our-work') }}/{{ $workdata->alias->varAlias }}" title="{{ ucwords($workdata->varTitle) }}">{{ htmlspecialchars_decode($workdata->varTitle) }} </a>
                        </h3>
                        <div class="short_desc">
                            
                            <p>{!! (strlen($workdata->txtShortDescription) > 120) ? substr($workdata->txtShortDescription, 0, 120).'...' : $workdata->txtShortDescription !!}</p>
                        </div>
                        <div class="work_list_btn">
                            <a class="ac-arrow-right" href="{{ url('our-work') }}/{{ $workdata->alias->varAlias }}" title="Read More">
                                <i class="icon-left-arrow"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            @if(isset($data['work']) && $data['work']->total() > $data['work']->perPage())
            <div class="col-12">
                <div class="pagination" id="ClientReviews">
                    {{ $data['work']->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@else
<section>
    <div class="inner-page-container cms">
        <div class="container">
            <section class="page_section">
                <div class="container">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h2>Coming Soon...</h2>
                        </div>	
                    </div>
                </div>  
            </section>    
        </div>
    </div>
</section>
@endif