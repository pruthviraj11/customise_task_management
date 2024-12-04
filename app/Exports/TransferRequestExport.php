<?php

namespace App\Exports;

use App\Models\InquiryTransferRequest;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RowType;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransferRequestExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public $inquiry_id;
    public $start_date;
    public $end_date;
    public $status;
    public $agent;

    public function __construct($filter_data = [])
    {
        $this->inquiry_id = $filter_data['inquiry_id'];
        $this->start_date = $filter_data['start_date'];
        $this->end_date = $filter_data['end_date'];
        $this->status = $filter_data['status'];
        $this->agent = $filter_data['agent'];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = InquiryTransferRequest::select('transfer_inquiries_requests.*', 'branches.name', 'branches.branch_code', 'inquiries.email', 'inquiries.mobile_one', 'inquiries.first_name', 'inquiries.last_name', 'inquiries.inquiry_no', 'inquiries.middle_name', 'users.last_name as user_last_name', 'users.first_name as user_first_name', 'agents.last_name as agent_last_name', 'agents.first_name as agent_first_name')
            ->leftJoin('branches', 'branches.id', '=', 'transfer_inquiries_requests.branch_id')
            ->leftJoin('inquiries', 'inquiries.id', '=', 'transfer_inquiries_requests.inquiry_id')
            ->leftJoin('users', 'transfer_inquiries_requests.created_by', '=', 'users.id')
            ->leftJoin('users as agents', 'transfer_inquiries_requests.assign_to', '=', 'agents.id');

        if ($this->inquiry_id){
            $query->where('transfer_inquiries_requests.inquiry_id', $this->inquiry_id);
        }
        if ($this->start_date) {
            $query->whereDate('transfer_inquiries_requests.created_at', '>=', $this->start_date);
        }
        if($this->end_date){
            $query->whereDate('transfer_inquiries_requests.created_at', '<=', $this->end_date);
        }
        if ($this->status){
            $query->where('transfer_inquiries_requests.status', $this->status);
        }
        if (Auth::user()->hasPermissionTo('inquiry-transfer-admin')) {
            if ($this->agent){
                $query = $query->where('assign_to', $this->agent);
            }
        } else {
            if (isUserConsultant()){
                $query = $query->where('assign_to', Auth::user()->id);
            }else{
                $query = $query->where('assign_to', $this->agent);
            }
        }
        return $query->get();
    }

    public function headings(): array
    {
        // TODO: Implement headings() method.
        $headings = [
            'Inquiry No',
            'First Name',
            'Last Name',
            'Email',
            'Phone No',
            'Branch',
            'Branch Code',
            'From User',
            'To Consultant',
            'Requested on Date',
            'Action on Date',
            'Status'
        ];
        return $headings;
    }

    public function map($row): array
    {
        // TODO: Implement map() method.
        $rows = [
            $row->inquiry_no,
            $row->first_name,
            $row->last_name,
            $row->email,
            $row->mobile_one,
            $row->name,
            $row->branch_code,
            $row->user_first_name.' '.$row->user_last_name,
            $row->agent_first_name.' '.$row->agent_last_name,
            $row->created_at,
            $row->updated_at,
            $row->status
        ];
        return $rows;
    }
}
