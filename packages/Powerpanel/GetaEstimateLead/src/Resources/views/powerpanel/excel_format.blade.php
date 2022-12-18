<!doctype html>
<html>
  <head>
    <title>{{ Config::get('Constant.SITE_NAME') }} Get Free Estimate Form Leads</title>
  </head>
  <body>
      @if(isset($GetaEstimateLead) && !empty($GetaEstimateLead))
          <div class="row">
           <div class="col-12">
              <table class="search-result allData" id="" border="1">
                 <thead>
                  <tr>
                        <th style="font-weight: bold;text-align:center" colspan="7">{{ Config::get('Constant.SITE_NAME') }} {{ trans("getaestimatelead::template.getaestimateleadModule.getaEstimateLeads") }}</th>
                   </tr>
                    <tr>
                       <th style="font-weight: bold;">{{ trans('getaestimatelead::template.common.name') }}</th>
                       <th style="font-weight: bold;">{{ trans('getaestimatelead::template.common.email') }}</th>
                       <th style="font-weight: bold;">{{ trans('getaestimatelead::template.common.phone') }}</th>
                       <th style="font-weight: bold;">{{ trans('Interested In') }}</th>
                       <th style="font-weight: bold;">{{ trans('getaestimatelead::template.getaestimateleadModule.message') }}</th>
                       <th style="font-weight: bold;">{{ trans('Ip') }}</th>
                       <th style="font-weight: bold;">{{ trans('getaestimatelead::template.getaestimateleadModule.receivedDateTime') }}</th>
                    </tr>
                 </thead>
                 <tbody>
                  @foreach($GetaEstimateLead as $row)
                    <tr>
                       <td>{{ $row->varName }}</td>
                       <td>{{ \App\Helpers\MyLibrary::getDecryptedString($row->varEmail) }}</td>
                       <td>{{ (!empty($row->varPhoneNo)?\App\Helpers\MyLibrary::getDecryptedString($row->varPhoneNo):'-') }}</td>
                       @if (!empty($row->fkIntServiceId))
                           @php $service = ''; @endphp
                           @php $serviceIDs = $row->fkIntServiceId; @endphp
                           @php $selService = \Powerpanel\Services\Models\Services::getServiceNameById($serviceIDs); @endphp
                           @php $service .= $selService['varTitle']; @endphp
                       @endif
                       <td>{{ (!empty($row->fkIntServiceId)? $service:'N/A')  }}</td>
                       @php
                       $msg = \App\Helpers\MyLibrary::getDecryptedString(strip_tags($row->txtUserMessage));
                       @endphp
                       <td>{!! (!empty($row->txtUserMessage)? nl2br($msg):'N/A') !!}</td>
                       <td>{!! (!empty($row->varIpAddress)? $row->varIpAddress:'N/A') !!}</td>
                       <td>{{ date(''.Config::get('Constant.DEFAULT_DATE_FORMAT').' '.Config::get('Constant.DEFAULT_TIME_FORMAT').'',strtotime($row->created_at)) }}</td>
                    </tr>
                  @endforeach
                 </tbody>
              </table>
           </div>
        </div>
      @endif
  </html>