<?php

namespace App\Exports;

use App\Models\FollowUp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RowType;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FollowUpExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
     * @var mixed
     */
    public $inquiry_id;
    public $start_date;
    public $end_date;
    public $action_status;
    public $follow_up_type;
    public $status;

    public function __construct($filter_data = [])
    {
        $this->inquiry_id = $filter_data['inquiry_id'];
        $this->start_date = $filter_data['start_date'];
        $this->end_date = $filter_data['end_date'];
        $this->action_status = $filter_data['action_status'];
        $this->follow_up_type = $filter_data['follow_up_type'];
        $this->status = $filter_data['status'];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $records = FollowUp::select('followups.*', 'inquiries.first_name as first_name', 'inquiries.last_name as last_name', 'packages.package_name', 'inquiries.email as email', 'inquiries.mobile_one as mobile_one', 'inquiries.inquiry_no', 'inquiries.registration_no', 'followup_action_status.display_name as display_name', 'followup_status.name as status_name', 'followups.action_status as action_status', 'followup_types.name as name', DB::raw('COALESCE(CONCAT(users.first_name, " ", users.last_name)) as agent_name'))
            ->leftJoin('followup_action_status', 'followups.action_status', '=', 'followup_action_status.id')
            ->leftJoin('followup_types', 'followups.followuptype', '=', 'followup_types.id')
            ->leftJoin('users', 'followups.user', '=', 'users.id')
            ->leftJoin('packages', 'followups.for_package', '=', 'packages.id')
            ->leftJoin('followup_status', 'followups.followupstatus', '=', 'followup_status.id')
            ->leftJoin('inquiries', 'followups.inquirie_id', '=', 'inquiries.id');
        if (!Auth::user()->hasPermissionTo('follow-up-admin')) {
            $records->where('followups.user', Auth::user()->id);
        }
        if ($this->inquiry_id) {
            $records->where('inquiries.id', $this->inquiry_id);
        }
        if ($this->start_date && $this->end_date) {
//            $records->whereBetween('followups.followup_date', [$this->start_date, $this->end_date]);
            $records->where('followups.followup_date', '>=', $this->start_date)->where('followups.followup_date', '<=', $this->end_date);
        }
        if ($this->action_status) {
            $records->where('followup_action_status.id', $this->action_status);
        }
        if ($this->follow_up_type) {
            $records->where('followup_types.id', $this->follow_up_type);
        }
        if ($this->status) {
            $records->where('followup_status.id', $this->status);
        }
        return $records->get();
    }

    public function headings(): array
    {
        // TODO: Implement headings() method.
        $headings = [
            'Inquiry No',
            'Registration No',
            'Follow Up Title',
            'Follow Up Description',
            'Follow Up Date',
            'Follow Up Type',
            'Follow Up Status',
            'Action Status',
            'Note',
            'For Package',
            'First Name',
            'Last Name',
            'Email',
            'Phone No',
            'Agent Name',
        ];
        return $headings;
    }

    public function map($row): array
    {
        // TODO: Implement map() method.
        $rows = [
            $row->inquiry_no,
            $row->registration_no,
            $row->title,
            $row->description,
            $row->followup_date,
            $row->name,
            $row->display_name,
            $row->status_name,
            $row->note ?? '-',
            $row->package_name,
            $row->first_name,
            $row->last_name,
            $row->email,
            $row->mobile_one,
            $row->agent_name
        ];
            return $rows;
    }
}
