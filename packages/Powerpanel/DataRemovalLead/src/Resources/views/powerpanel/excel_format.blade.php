<!doctype html>
<html>

<head>
   <title>{{ Config::get('Constant.SITE_NAME') }} Data Removal Leads</title>
</head>

<body>
   @if(isset($DataRemovalLead) && !empty($DataRemovalLead))
   <div class="row">
      <div class="col-12">
         <table class="search-result allData" id="" border="1">
            <thead>
               <tr>
                  <th style="font-weight: bold;text-align:center" colspan="6">{{ Config::get('Constant.SITE_NAME') }} {{ trans("dataremovallead::template.dataremovalleadModule.dataRemovalLeads") }}</th>
               </tr>
               <tr>
                  <th style="font-weight: bold;">{{ trans('dataremovallead::template.common.name') }}</th>
                  <th style="font-weight: bold;">{{ trans('dataremovallead::template.common.emailaddress') }}</th>
                  <th style="font-weight: bold;">{{ trans('Reason for Removal') }}</th>
                  <th style="font-weight: bold;">{{ trans('Request Status') }}</th>

                  <th style="font-weight: bold;">Record In Contact Leads</th>
                  <th style="font-weight: bold;">Record In Service Inquiry Leads</th>
                  <th style="font-weight: bold;">Record In Boat Inquiry Leads</th>

                  <th style="font-weight: bold;">{{ trans('Ip') }}</th>
                  <th style="font-weight: bold;">{{ trans('dataremovallead::template.dataremovalleadModule.receivedDateTime') }}</th>
               </tr>
            </thead>
            <tbody>


               @foreach($DataRemovalLead as $row)

               @php
               $countRecordcontactUs = '';
               $countRecordgetAEstimate = '';
               $countRecordServiceinquiry = '';
               $countRecordBoatinquiry = '';
               $countRecord1 = '';

               $countRecord = DB::table('contact_lead')->where('varEmail', $row->varEmail)->where('chrPublish','=','Y')->where('chrDelete','=','N')->count();
               $countRecordServiceInquiry = DB::table('serviceinquiry_lead')->where('varEmail', $row->varEmail)->where('chrPublish','=','Y')->where('chrDelete','=','N')->count();
               $countRecordBoatInquiry = DB::table('boatinquiry_lead')->where('varEmail', $row->varEmail)->where('chrPublish','=','Y')->where('chrDelete','=','N')->count();


               if ($countRecord > 0 || $countRecordServiceInquiry > 0 || $countRecordBoatInquiry > 0) {
               $countRecord1 = 'true';

               }else{
               $countRecord1 = '';
               }

               @endphp

               <tr>
                  <td>{{ $row->varName }}</td>
                  <td>{{ \App\Helpers\MyLibrary::getDecryptedString($row->varEmail) }}</td>
                  <td>{!! (!empty($row->varReason))? nl2br($row->varReason):'N/A' !!}</td>
                  @php $status = 'N/A'; @endphp
                  @if ($row->varRequeststatus == 'Y')
                  @php $status = 'Confirmed'; @endphp
                  @elseif ($row->varRequeststatus == 'N')
                  @php $status = 'Not Confirmed'; @endphp
                  @endif
                  <td>{{ $status }}</td>
                  @if($countRecord1 == '')
                  <td>N/A</td>
                  <td>N/A</td>
                  <td>N/A</td>
                  @else

                  @if($countRecord > 0)
                  <td>
                     <p>Contact Leads Count : </p>
                     <a title="View Record location" href="{{ url('powerpanel/contact-us/' . \app\Helpers\Mylibrary::getDecryptedString($row->varEmail))}}">View Record location ( {{$countRecord}} )</a>
                  </td>
                  @else
                  <td>
                     N/A
                  </td>
                  @endif

                  @if($countRecordServiceInquiry > 0)
                  <td>
                     <p>Service Inquiry Leads Count : </p>
                     <a title="View Record location" href="{{ url('powerpanel/service-inquiry/' . \app\Helpers\Mylibrary::getDecryptedString($row->varEmail))}}">View Record location ( {{$countRecordServiceInquiry}} )</a>
                  </td>
                  @else
                  <td>
                     N/A
                  </td>
                  @endif

                  @if($countRecordBoatInquiry > 0)
                  <td>
                     <p>Boat Inquiry Leads Count : </p>
                     <a title="View Record location" href="{{ url('powerpanel/boat-inquiry/' . \app\Helpers\Mylibrary::getDecryptedString($row->varEmail))}}">View Record location ( {{$countRecordBoatInquiry}} )</a>
                  </td>
                  @else
                  <td>
                     N/A
                  </td>
                  @endif


                  @endif

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