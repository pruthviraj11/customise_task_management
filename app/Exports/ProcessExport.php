<?php

namespace App\Exports;

use App\Models\InquiryPayment;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RowType;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProcessExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public $type;
    public $start_date;
    public $end_date;
    public $agent;
    public function __construct($data)
    {
        $this->type = $data['type'];
        $this->start_date = $data['start_date'];
        $this->end_date = $data['end_date'];
        $this->agent = $data['agent'];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = InquiryPayment::leftJoin('inquiries', 'inquiry_payments.inquiry_id', '=', 'inquiries.id')
            ->leftJoin('packages', 'inquiry_payments.package_id', '=', 'packages.id')
            ->leftJoin('users', function ($join) {
                $join->on('inquiries.created_by', '=', 'users.id')
                    ->whereNull('inquiries.agent_id');
            })->leftJoin('users as agents', 'inquiries.agent_id', '=', 'agents.id')
            ->whereNotNull('inquiries.registration_no');

        if ($this->agent) {
            $data->where('inquiries.agent_id', $this->agent);
        }

        if ($this->start_date != '') {
            $data->whereDate('inquiry_payments.created_at', '>=', $this->start_date);
        }
        if ($this->end_date != '') {
            $data->whereDate('inquiry_payments.created_at', '<=', $this->end_date);
        }
        if ($this->type == 'pending'){
            $data->where('inquiry_payments.inquiry_status', escalationStatusId())->where('inquiry_payments.process_verified', 0);
        }elseif ($this->type == 'approved'){
            $data->where('inquiry_payments.process_verified', 1);
        }elseif ($this->type == 'rejected'){
            $data->where('inquiry_payments.process_verified', 2);
        }
        $data->select('inquiries.inquiry_no', 'packages.package_name as package_name', 'inquiries.first_name', 'inquiries.last_name', 'inquiries.middle_name', 'inquiries.email', 'inquiries.mobile_one', 'inquiries.registration_no', 'inquiry_payments.id as inquiry_payments_id', 'inquiry_payments.*',
            DB::raw('COALESCE(CONCAT(agents.first_name, " ", agents.last_name), CONCAT(users.first_name, " ", users.last_name)) as agent_name'));
        return $data->get();
    }

    public function headings(): array
    {
        // TODO: Implement headings() method.
        $headings = [
            'Inquiry No',
            'Registration No',
            'Package Name',
            'Registration Date',
            'First Name',
            'Last Name',
            'Email',
            'Phone No',
            'Package Value before Discount (Exc. GST)',
            'Package Value after Discount (Exc. GST)',
            'Agent Name',
            'Process Status',
        ];
        return $headings;
    }

    public function map($row): array
    {
        // TODO: Implement map() method.
        $rows = [
            $row->inquiry_no,
            $row->registration_no,
            $row->package_name,
            date('d-m-Y h:i A', strtotime($row->created_at)),
            $row->first_name,
            $row->last_name,
            $row->email,
            $row->mobile_one,
            $row->total_package_amount,
            $row->total_discounted_package_amount,
            $row->agent_name,
        ];
        $newFields = [];
        if ($row->inquiry_status == qcStatusId()) {
            array_push($newFields, "Pending");
        } elseif ($row->process_verified == 1) {
            array_push($newFields, "Transferred to PRO");
        } elseif ($row->process_verified == 2) {
            array_push($newFields, "Reverted");
        } else {
            array_push($newFields, "No record for Process");
        }
        $newRow = array_merge($rows, $newFields);
        return $newRow;
    }
}
