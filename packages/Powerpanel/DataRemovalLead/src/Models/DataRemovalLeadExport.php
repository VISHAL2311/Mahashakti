<?php
namespace Powerpanel\DataRemovalLead\Models;
use Powerpanel\DataRemovalLead\Models\DataRemovalLead;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Request;
use Config;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;

class DataRemovalLeadExport extends \PhpOffice\PhpSpreadsheet\Cell\StringValueBinder implements FromView, ShouldAutoSize, WithCustomValueBinder
{
    public function view(): View
    {
        if (Request::get('export_type') == 'selected_records') {
            $selectedIds = array();
            if (null !== Request::get('delete')) {
                $selectedIds = Request::get('delete');
            }
            $arrResults = DataRemovalLead::getListForExport($selectedIds);

        } else {
            $filterArr['searchFilter'] = !empty(Request::get('searchValue')) ? Request::get('searchValue') : '';
            $arrResults = DataRemovalLead::getListForExport(false, $filterArr);
        }

        if (count($arrResults) > 0) {
            return view('dataremovallead::powerpanel.excel_format', ['DataRemovalLead' => $arrResults]);
        }
    }

}
