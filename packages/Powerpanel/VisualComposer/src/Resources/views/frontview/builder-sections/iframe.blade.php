<section class="page_section ">
    <div class="container">
        <div class="row">
            @if(isset($data['extclass']) && $data['extclass'] != '')
            @php
            $extclass = $data['extclass'];
            @endphp
            @else
            @php
            $extclass = '';
            @endphp
            @endif
            <div class="col-12 {{ $extclass }}">
                <iframe src="{{ $data['iframe'] }}" width="100%" height="450" frameborder="0" style="border:0;" allowfullscreen=""></iframe>
            </div>
        </div>
    </div>
</section>
