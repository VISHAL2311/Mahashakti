@if(!empty($data['brands']) && count($data['brands'])>0)
<section class="brand-section inner-page-container">
    <div class="container">
        <div class="brand" id="accordion">
            <div class="row">
                @foreach($data['brands'] as  $index => $brand)
                    <div class="col-12">
                        <div class="card" data-aos="fade-in">
                            <div class="card-header" id="brandHeading-{{$brand->id}}">
                                <div class="mb-0">
                                    <h3 class="brand-title" data-toggle="collapse" data-target="#brandCollapse-{{$brand->id}}" data-aria-expanded="true" data-aria-controls="brandCollapse-{{$brand->id}}">
                                        {{ htmlspecialchars_decode($brand->varTitle) }}
                                    </h3>
                                </div>
                            </div>
                            <div id="brandCollapse-{{$brand->id}}" class="collapse" aria-labelledby="brandHeading-{{$brand->id}}" data-parent="#accordion">
                                <div class="card-body">
                                    {!! htmlspecialchars_decode($brand->txtDescription) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                @if(isset($data['brands']) && $data['brands']->total() > $data['brands']->perPage())
                <div class="col-12">
                    <div class="pagination" id="ClientReviews">
                        {{ $data['brands']->links() }}
                    </div>
                </div>
                @endif
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