<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RowType;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
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
        $data = User::role(array('Consultant'))
            ->with(['inquiryPayments' => function ($query) {
                if ($this->branch) {
                    $query->where('inquiries.branch_id', $this->branch);
                }
                if ($this->agent) {
                    $query->where('inquiries.agent_id', $this->agent);
                }
                if ($this->start_date) {
                    $query->whereDate('inquiries.created_at', '>=', $this->start_date);
                }
                if ($this->end_date) {
                    $query->whereDate('inquiries.created_at', '<=', $this->end_date);
                }
            }, 'branch_detail'])->withCount([
                'inquiries as total_inquiries' => function ($query) {
                    if ($this->start_date) {
                        $query->whereDate('inquiries.created_at', '>=', $this->start_date);
                    }
                    if ($this->end_date) {
                        $query->whereDate('inquiries.created_at', '<=', $this->end_date);
                    }
                },
                'inquiryPayments as total_inquiry_payments',
                'inquiries as total_inquiries_without_payments' => function ($query) {
                    if ($this->start_date) {
                        $query->whereDate('inquiries.created_at', '>=', $this->start_date);
                    }
                    if ($this->end_date) {
                        $query->whereDate('inquiries.created_at', '<=', $this->end_date);
                    }
                    $query->doesntHave('inquiryPayments');
                },
                'inquiries as total_inquiries_with_payments' => function ($query) {
                    if ($this->start_date) {
                        $query->whereDate('inquiries.created_at', '>=', $this->start_date);
                    }
                    if ($this->end_date) {
                        $query->whereDate('inquiries.created_at', '<=', $this->end_date);
                    }
                    $query->has('inquiryPayments');
                },
            ]);

        $response = $data->get();
        return $response;
    }

    public function headings(): array
    {
        // TODO: Implement headings() method.
        $headers = [
            'Agent Name',
            'Branch',
            'Total Inquiries',
            'Total Registered',
            'Total Amount Received',
            'Total Outstanding Amount',
            'Total Revenue Expected'
        ];
        return $headers;
    }

    public function map($row): array
    {
        // TODO: Implement map() method.
        $totalRevenue = $row->inquiryPayments->sum('total_paid_amount');
        $totalOutstanding = $row->inquiryPayments->sum('total_outstanding_amount');

        return [
            $row->first_name . ' ' . $row->last_name,
            $row->branch_name,
            $row->total_inquiries,
            $row->total_inquiries_with_payments,
            $totalRevenue,
            $totalOutstanding,
            $totalRevenue + $totalOutstanding,
        ];
    }
}
