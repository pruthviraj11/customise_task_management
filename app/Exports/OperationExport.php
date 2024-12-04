<?php

namespace App\Exports;

use App\Models\InquiryPayment;
use App\Models\InquiryStatus;
use App\Models\User;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RowType;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OperationExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public $branch;
    public $start_date;
    public $end_date;
    public $agent;

    public function __construct($filter_data = [])
    {
        $this->agent = $filter_data['agent'];
        $this->start_date = $filter_data['start_date'];
        $this->end_date = $filter_data['end_date'];
        $this->branch = $filter_data['branch'];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $inquiry_status = InquiryStatus::get();

        $data = User::role(array('Consultant'))
            ->with(['inquiryPayments' => function ($query) {
                if ($this->agent){
                    $query->where('users.id', $this->agent);
                }
                if ($this->branch){
                    $query->where('users.branch', $this->branch);
                }
                if ($this->start_date){
                    $query->whereDate('inquiries.created_at', '>=', $this->start_date);
                }
                if ($this->end_date){
                    $query->whereDate('inquiries.created_at', '<=', $this->end_date);
                }
            }, 'inquiries', 'branch_detail'])
            ->leftJoin('branches', 'users.branch', '=', 'branches.id')
            ->select([
                'users.*',
                'branches.name as branch_name',
                'branches.branch_code',
            ]);



$data = $data->get();
        foreach ($data as $user){
            $statusCounts = array();
            foreach ($inquiry_status as $status){
                $inquiry_status_count = InquiryPayment::leftJoin('inquiries', 'inquiry_payments.inquiry_id', '=', 'inquiries.id')
                    ->where('inquiries.agent_id', $user->id)
                    ->where('inquiry_payments.inquiry_status', $status->id)->count();
                $inquiry_status_count = $inquiry_status_count > 0 ? $inquiry_status_count : 0;
                $statusCounts[Str::slug($status->name)] = $inquiry_status_count;
            }
            $user->status_counts = $statusCounts;
        }

        return $data;
    }

    public function headings(): array
    {
        // TODO: Implement headings() method.

        $inquiry_status = InquiryStatus::pluck('name')->toArray();

        $headers = [
            'Agent Name',
            'Branch',
        ];
        $newHeadings = array_merge($headers, $inquiry_status);

        return $newHeadings;
    }

    public function map($row): array
    {
        // TODO: Implement map() method.
        $totalInquiries = $row->inquiries->count();
        $totalInquiriesWithPayments = $row->inquiries->filter(function ($inquiry) {
            return $inquiry->inquiryPayments->isNotEmpty();
        })->count();

        $mainRows = [
            $row->first_name . ' ' . $row->last_name,
            $row->branch_name.' ('.$row->branch_code.')',
        ];
        $finalRows = array_merge($mainRows, $row->status_counts);
        return $finalRows;
    }
}
