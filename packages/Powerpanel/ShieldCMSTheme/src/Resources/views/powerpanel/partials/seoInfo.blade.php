@php $metaInfoRequired = true; @endphp
@if(isset($metaRequired) && $metaRequired==true )
@php $metaInfoRequired = true; @endphp
@elseif(isset($metaRequired) && $metaRequired==false)
@php $metaInfoRequired = false; @endphp
@endif

<h3 class="form-section">{{ trans('template.common.seoinformation') }}</h3>														
<div class="row" id="edit_seo">
    <div class="col-md-12">	 
        <div class="form-group"> 
            @if(!empty($inf))
            @php  $Display = 'none'  @endphp
            <button type="button" id='seo_edit' class="btn btn-green-drake"><i class="fa fa-pencil"></i> Edit Snippet</button>
            <button type="button" id='seo_edit_time' class="btn btn-green-drake" style="display: none; margin-left: 0px;"><i class="fa fa-pencil"></i> Edit Snippet</button>
            <div class="seo_editor">
                @if(isset($inf) && isset($inf['varMetaTitle']))
                @php  $metaTitle = $inf['varMetaTitle']  @endphp
                @else
                @php  $metaTitle = null  @endphp
                @endif
                @php if(!empty($inf_highLight['varMetaTitle']) && !empty($inf['varMetaTitle']) && ($inf_highLight['varMetaTitle'] != $inf['varMetaTitle'])){
                $Class_metatitle = " highlitetext";
                }else{
                $Class_metatitle = "";
                } 
                @endphp
                <h4><span class="{!! $Class_metatitle !!}" id="meta_title">{{ $metaTitle }}</span></h4>
                <p class="seo_link">
                    @if(isset($inf) && isset($inf['varURL']))
                    @php  $varURL = $inf['varURL']  @endphp
                    @else
                    @php  $varURL = null  @endphp
                    @endif
                    <a href="{{url('/'.$varURL)}}" target="_blank">{{url('/'.$varURL)}}</a>
                </p>  
                @if(isset($inf) && isset($inf['varMetaDescription']))
                @php  $metaDescription = $inf['varMetaDescription']  @endphp
                @else
                @php  $metaDescription = null  @endphp
                @endif
                @php if(!empty($inf_highLight['varMetaDescription']) && !empty($inf['varMetaDescription']) && ($inf_highLight['varMetaDescription'] != $inf['varMetaDescription'])){
                $Class_metaDescription = " highlitetext";
                }else{
                $Class_metaDescription = "";
                } 
                @endphp
                <p><span class="{{ $Class_metaDescription }}" id="meta_description">{{ $metaDescription }}</span></p>
            </div>
            @else
            @php  $Display = 'none'  @endphp
            <button type="button" id='auto-generate' class="btn btn-green-drake" onclick="generate_seocontent1('@if(!empty($form)){{ $form }}@endif');">{{ trans('template.common.autogenerate') }}</button>
            <button type="button" id='seo_edit' class="btn btn-green-drake"><i class="fa fa-pencil"></i> Edit Snippet</button>
            <button type="button" id='seo_edit_time' class="btn btn-green-drake" style="display: none; margin-left: 0px;"><i class="fa fa-pencil"></i> Edit Snippet</button>
            <div class="seo_editor" style="display: none;">
                <h4><span id="meta_title"></span></h4>
                <p class="seo_link">
                    <a onClick="generatePreview('{{url('/previewpage?url='.(url('/')))}}');" class="snippet_alias"></a>
                </p>  
                <p><span id="meta_description"></span></p>
            </div>
            @endif
        </div>
    </div>
</div>
<div id="seo_edit_dispaly" style="display:{!! $Display !!}">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group @if($errors->first('varMetaTitle')) has-error @endif">

                @php if(!empty($inf_highLight['varMetaTitle']) && !empty($inf['varMetaTitle']) && ($inf_highLight['varMetaTitle'] != $inf['varMetaTitle'])){
                $Class_metatitle = " highlitetext";
                }else{
                $Class_metatitle = "";
                } 
                @endphp
                <label class="control-label form_title {!! $Class_metatitle !!}">{{ trans('template.common.metatitle') }} 
                    @if($metaInfoRequired)
                    <span aria-required="true" class="required"> * </span>
                    @endif
                </label>      

                @if(isset($inf) && isset($inf['varMetaTitle']))
                @php  $metaTitle = $inf['varMetaTitle']  @endphp
                @else
                @php  $metaTitle = null  @endphp
                @endif

                {!! Form::text('varMetaTitle', $metaTitle , array('maxlength'=>'160','class' => 'form-control maxlength-handler metatitlespellingcheck','id'=>'varMetaTitle','autocomplete'=>'off','onkeyup'=>'MetaTitle_Function(this.value)')) !!}
                <!-- <span>Maximum 500 Characters </span> -->
                <span class="help-block">{{ $errors->first('varMetaTitle') }}</span>
            </div>
        </div>
    </div>
    <div class="row">
        {{-- <div class="col-md-6">
        <div class="form-group @if($errors->first('varMetaKeyword')) has-error @endif">
            @php if(!empty($inf_highLight['varMetaKeyword']) && !empty($inf['varMetaKeyword']) && ($inf_highLight['varMetaKeyword'] != $inf['varMetaKeyword'])){
            $Class_metakeyword = " highlitetext";
            }else{
            $Class_metakeyword = "";
            } 
            @endphp
            <label class="control-label form_title {!! $Class_metakeyword !!}">{{ trans('template.common.metakeyword') }} 
        @if($metaInfoRequired)
        <span aria-required="true" class="required"> * </span>
        @endif
        </label>
        @if(isset($inf) && isset($inf['varMetaKeyword']))
        @php  $metaKeyword = $inf['varMetaKeyword']  @endphp
        @else
        @php  $metaKeyword = null  @endphp
        @endif

        {!! Form::textarea('varMetaKeyword', $metaKeyword, 
        array(
        'maxlength'=>'200',
        'class' => 'form-control maxlength-handler',      		
        'cols' => '40', 
        'rows' => '3',
        'id' => 'varMetaKeyword'
        )) 
        !!}
        <!-- <span>Maximum 500 Characters </span> -->
        <span class="help-block">{{ $errors->first('varMetaKeyword') }}</span>
    </div>
</div> --}}
<div class="col-md-12">
    <div class="form-group @if($errors->first('varMetaDescription')) has-error @endif">
        @php if(!empty($inf_highLight['varMetaDescription']) && !empty($inf['varMetaDescription']) && ($inf_highLight['varMetaDescription'] != $inf['varMetaDescription'])){
        $Class_metaDescription = " highlitetext";
        }else{
        $Class_metaDescription = "";
        } 
        @endphp
        <label class="control-label form_title {!! $Class_metaDescription !!}">{{ trans('template.common.metadescription') }} 
            @if($metaInfoRequired)
            <span aria-required="true" class="required"> * </span>
            @endif
        </label>

        @if(isset($inf) && isset($inf['varMetaDescription']))
        @php  $metaDescription = $inf['varMetaDescription']  @endphp
        @else
        @php  $metaDescription = null  @endphp
        @endif

        {!! Form::textarea('varMetaDescription', $metaDescription, 
        array(
        'maxlength'=>'200',
        'class' => 'form-control maxlength-handler metadescspellingcheck',      		
        'cols' => '40', 
        'rows' => '3',
        'id' => 'varMetaDescription',
        'spellcheck' => 'true',
        'onkeyup'=>'MetaDescription_Function(this.value)'
        )) 
        !!}
         <!-- <span>Maximum 500 Characters </span> -->
        <span class="help-block">{{ $errors->first('varMetaDescription') }}</span>
    </div>
</div>
</div>
</div>
<script>
                    function MetaTitle_Function(value) {
                    document.getElementById("meta_title").innerHTML = value;
                    }
            function MetaDescription_Function(value) {
            document.getElementById("meta_description").innerHTML = value;
            }
</script>
