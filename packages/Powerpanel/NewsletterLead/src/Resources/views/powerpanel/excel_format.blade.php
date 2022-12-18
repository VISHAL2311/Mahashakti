<!doctype html>
<html>
  <head>
    <title>{{ Config::get('Constant.SITE_NAME') }} Newsletter Leads</title>
  </head>
  <body>
      @if(isset($newsletterLeads) && !empty($newsletterLeads))
          <div class="row">
           <div class="col-12">
              <table class="search-result allData" id="" border="1">
                 <thead>
                  <tr>
                        <th style="font-weight: bold;text-align:center" colspan="4">{{ Config::get('Constant.SITE_NAME') }} {{ trans("template.newslettersModule.newslettersLeads") }}</th>
                   </tr>
                    <tr>
                       <th style="font-weight: bold;">{{ trans('template.common.email') }}</th>
                       <th style="font-weight: bold;">{{ trans('template.newslettersModule.subscribed') }}</th>
                       <th style="font-weight: bold;">{{ trans('Ip') }}</th>
                       <th style="font-weight: bold;">{{ trans('template.contactleadModule.receivedDateTime') }}</th>
                    </tr>
                 </thead>
                 <tbody>
                  @foreach($newsletterLeads as $row)
                    <tr>
                       <td>{!! \App\Helpers\Mylibrary::getDecryptedString($row->varEmail) !!}</td>
                        @php $subscribe = '-'; @endphp
                        @if ($row->chrSubscribed == 'Y')
                              @php  $subscribe = 'Subscribe'; @endphp
                        @elseif ($row->chrSubscribed == 'N')
                              @php   $subscribe = 'Unsubscribe'; @endphp
                        @endif
                       <td>{{ $subscribe }}</td>
                       <td>{!! (!empty($row->varIpAddress)? $row->varIpAddress:'N/A') !!}</td>
                       <td>{{ date('' . Config::get('Constant.DEFAULT_DATE_FORMAT') . ' ' . Config::get('Constant.DEFAULT_TIME_FORMAT') . '', strtotime($row->created_at)) }}</td>
                    </tr>
                  @endforeach
                 </tbody>
              </table>
           </div>
        </div>
      @endif
  </html>
