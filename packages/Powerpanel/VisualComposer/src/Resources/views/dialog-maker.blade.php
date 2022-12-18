<!-- Modal -->
<div class="ac-modal modal fade bd-example-modal-lg composer-element-popup" id="pgBuiderSections" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="ac-modal-table">
        <div class="ac-modal-center">
            <div class="modal-dialog">
                <div class="modal-content">
                    {!! Form::open(['method' => 'post','id'=>'frmPageComponantData']) !!}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span>×</span>
                        </button>
                        <h5 class="modal-title" id="exampleModalLabel"><b>Add Elements</b></h5>
                    </div>
                    <div class="modal-body" id="visualComposer-modal">
                        <ul class="nav nav-tabs" role="tablist">
                            @foreach($visualData as $key => $data)
                                @php 
                                    $expand = $key=='0'?'false':'true';
                                    $userAccess = true;
                                    if(isset($data['varModuleName']) && !empty($data['varModuleName'])){
                                        $userAccess = Auth::user()->can($data['varModuleName'].'-list');
                                    } else if( $data['varTitle'] == 'Templates' || $data['varTitle'] == 'Forms' ){
                                        if($data['varTitle'] == 'Templates') {
                                            $userAccess = Config::get('Constant.DEFAULT_PAGETEMPLATE') == 'Y';
                                        } else {
                                            $userAccess = Config::get('Constant.DEFAULT_FORMBUILDER') == 'Y';
                                        }
                                    }
                                  $classname =  str_replace(" ","",strtolower($data['varTitle']));
                                @endphp
                                @if($userAccess)
                                    <li role="presentation" class="{{$data['varClass']}}"  data-tabing="{{$classname}}" id="{{$classname}}_tab">
                                        <a href="#{{$classname}}" title="{{$data['varTitle']}}" class="{{$classname}}_tab" data-toggle="tab" aria-controls="{{strtolower($data['varTitle'])}}" role="{{strtolower($data['varTitle'])}}" aria-expanded="{{$expand}}">
                                            <span class="tab_text">{{$data['varTitle']}}</span>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                        <div class="tab-content">
                            @foreach($visualData as $key => $data)
                                @php
                                    $active = $key=='0'?' active':'';
                                    $userAccess = true;
                                    if(isset($data['varModuleName']) && !empty($data['varModuleName'])){
                                        $userAccess = Auth::user()->can($data['varModuleName'].'-list');
                                    }
                                    $classname =  str_replace(" ","",strtolower($data['varTitle']));
                                @endphp
                                @if($userAccess)
                                    <div class="tab-pane mcscroll{{$active}}" role="tabpanel" id="{{$classname}}">
                                        <ul>
                                            @foreach($data['child'] as $index => $childData)
                                                @php
                                                    $userChildAccess = true;
                                                    if(isset($childData['varModuleName']) && !empty($childData['varModuleName'])){
                                                        $userChildAccess = Auth::user()->can($childData['varModuleName'].'-list');
                                                    }
                                                @endphp
                                                @if($userChildAccess)
                                                    @if($data['varTitle'] == 'Templates' || $data['varTitle'] == 'Forms' ) 
                                                        <li>
                                                            @if($data['varTitle'] == 'Templates')
                                                                <a title="{{$childData['varTitle']}}" class="{{$childData['varClass']}}" onclick="GetSetTemplateData({{ $childData['id'] }})" href="javascript:;">
                                                            @else
                                                                <a title="{{$childData['varTitle']}}" class="{{$childData['varClass']}}" onclick="GetSetFormBuilderData({{ $childData['id'] }})" href="javascript:;">
                                                            @endif
                                                                <span><i class="{{$childData['varIcon']}}" aria-hidden="true"></i></span>{{$childData['varTitle']}}
                                                            </a>
                                                        </li>
                                                    @else 
                                                        <li>
                                                            <a title="{{$childData['varTitle']}}" data-filter="{{$childData['varClass']}}" class="{{$childData['varClass']}}" href="javascript:;">
                                                                <span><i class="{{$childData['varIcon']}}" aria-hidden="true"></i></span>{{$childData['varTitle']}}
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endif    
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                            @endforeach
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($visualComposerTemplate as $key => $data)    
    @include($data)
@endforeach
<script src="{{ url('resources/pages/scripts/packages/visualcomposer/jquery.min.js') }}" type="text/javascript"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;sensor=false&amp;libraries=places&key=AIzaSyDMdWyeX2VR9DZVhXh46mOJQveRHpavLWI"></script>