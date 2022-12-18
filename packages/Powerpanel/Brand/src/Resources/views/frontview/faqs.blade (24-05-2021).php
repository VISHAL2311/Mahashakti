@if(!Request::ajax())
@extends('layouts.app')
@section('content')
@include('layouts.inner_banner')
@endif
@if(!empty($faqs) && count($faqs)>0)
<section class="faq-section inner-page-container">
    <div class="container">
        <div class="row">
            @if(isset($PageData['response']) && !empty($PageData['response']) && $PageData['response'] != '[]')
                <div class="col-12 text-center">
                {!! $PageData['response'] !!}
                </div>
            @endif
        </div>
        <div class="faq" id="accordion">
            <div class="row">
                @foreach($faqs as  $index => $faq)
                    <div class="col-12">
                        <div class="card" data-aos="fade-in">
                            <div class="card-header" id="faqHeading-{{$faq->id}}">
                                <div class="mb-0">
                                    <h3 class="faq-title" data-toggle="collapse" data-target="#faqCollapse-{{$faq->id}}" data-aria-expanded="true" data-aria-controls="faqCollapse-{{$faq->id}}">
                                        {{ htmlspecialchars_decode($faq->varTitle) }}
                                    </h3>
                                </div>
                            </div>
                            <div id="faqCollapse-{{$faq->id}}" class="collapse" aria-labelledby="faqHeading-{{$faq->id}}" data-parent="#accordion">
                                <div class="card-body">
                                    {!! htmlspecialchars_decode($faq->txtDescription) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                @if(isset($faqs) && count($faqs)>0)
                <div class="col-12">
                    <div class="pagination" id="ClientReviews">
                        {{ $faqs->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
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

@if(!Request::ajax())
@section('footer_scripts')
@endsection
@endsection
@endif