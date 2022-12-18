<!doctype html>
<html>
  <head>
    <title>{{ Config::get('Constant.SITE_NAME') }} BoatInquiryLeads</title>
  </head>
  <body>
      @if(isset($BoatinquiryLead) && !empty($BoatinquiryLead))
          <div class="row">
           <div class="col-12">
              <table class="search-result allData" id="" border="1">
                 <thead>
                  <tr>
                        <th style="font-weight: bold;text-align:center" colspan="7">{{ Config::get('Constant.SITE_NAME') }} {{ trans("boatinquirylead::template.boatinquiryleadModule.boatInquiryLeads") }}</th>
                   </tr>
                    <tr>
                       <th style="font-weight: bold;">{{ trans('boatinquirylead::template.common.name') }}</th>
                       <th style="font-weight: bold;">{{ trans('boatinquirylead::template.common.email') }}</th>
                       <th style="font-weight: bold;">{{ trans('boatinquirylead::template.boatinquiryleadModule.phone') }}</th>
                       <th style="font-weight: bold;">{{ trans('boatinquirylead::template.common.boatname') }}</th>
                       <th style="font-weight: bold;">Comment</th>
                       <th style="font-weight: bold;">{{ trans('Ip') }}</th>
                       <th style="font-weight: bold;">{{ trans('boatinquirylead::template.boatinquiryleadModule.receivedDateTime') }}</th>
                    </tr>
                 </thead>
                 <tbody>
                  @foreach($BoatinquiryLead as $row)
                    <tr>
                       <td>{{ $row->varName }}</td>
                       <td>{{ \App\Helpers\MyLibrary::getDecryptedString($row->varEmail) }}</td>
                       <td>{{ (!empty($row->varPhoneNo)?\App\Helpers\MyLibrary::getDecryptedString($row->varPhoneNo):'N/A') }}</td>
                       @php $boat = ''; @endphp
                       @if(isset($row->fkIntBoatId) && $row->fkIntBoatId == 0)
                        @php $boat .= "General Enquiry"; @endphp
                       @else
                        @if (!empty($row->fkIntBoatId))
                            @php $boatIDs = $row->fkIntBoatId; @endphp
                            @php $selBoat = \Powerpanel\Boat\Models\Boat::getBoatNameById($boatIDs); @endphp
                            @php $boat .= $selBoat['varTitle']; @endphp
                        @else
                          @php $boat .= "N/A"; @endphp
                        @endif
                       @endif
                       
                       <td>{{  $boat  }}</td>
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
