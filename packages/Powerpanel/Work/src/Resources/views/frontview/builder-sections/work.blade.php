@if(isset($data['work']) && count($data['work']) > 0)
<section class="ourwork-section" data-aos="fade-up">
    <div class="container-fluid p-0">        
        <div class="slider vertical-slider">
            <div class="slick">    
            @foreach($data['work'] as $index => $workImages)            
                <div class="item">   
                    <div class="thumbnail-container"><div class="thumbnail">                
                        <img class="lazy" loading="lazy" data-src="{!! App\Helpers\resize_image::resize($workImages->fkIntImgId,1349,604) !!}" src="{!! App\Helpers\resize_image::resize($workImages->fkIntImgId,1349,604) !!}" alt="{{$workImages->varTitle}}" title="{{ucwords($workImages->varTitle)}}" />
                    </div></div>
                </div>
            @endforeach
            </div>
            <div class="content" data-aos="fade-right">
                <div class="desc">
                    <h3 class="cm-title text-uppercase">Our Work</h3>
                    <ul>
                        <li><a href="{{ url('our-work')}}" title="All Work">All Work</a></li>
                        @foreach($data['work'] as $index => $workTitle)
                        <li><a href="{{ url('our-work/'.$workTitle->alias->varAlias) }}" title="{{ucwords($workTitle->varTitle)}}">{{$workTitle->varTitle}}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>                
    </div>
</section>
@endif