<?php

namespace App\Exports;

use App\Models\PaymentReceipt;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RowType;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AccountExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public $inquiry_id;
    public $start_date;
    public $end_date;
    public $agent_id;
    public $receipt_type;

    public function __construct($filter_data = [])
    {
        $this->agent_id = $filter_data['agent_id'];
        $this->start_date = $filter_data['start_date'];
        $this->end_date = $filter_data['end_date'];
        $this->inquiry_id = $filter_data['inquiry_id'];
        $this->receipt_type = $filter_data['receipt_type'];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $payments = PaymentReceipt::leftJoin('inquiries', 'payment_receipts.inquiry_id', '=', 'inquiries.id')
            ->where('payment_receipts.created_by', auth()->user()->id)
            ->leftJoin('inquiry_payments', 'payment_receipts.inquiry_payment_id', '=', 'inquiry_payments.id')
            ->leftJoin('packages', 'inquiry_payments.package_id', '=', 'packages.id')
            ->leftJoin('users as account', 'inquiry_payments.created_by', '=', 'account.id')
            ->select('payment_receipts.*', 'packages.package_name',
                DB::raw('CONCAT(account.first_name, " ", account.last_name) as account_name'),
                DB::raw('CONCAT(inquiries.first_name, " ", inquiries.last_name) as full_name'),
                DB::raw('COALESCE(CONCAT(agents.first_name, " ", agents.last_name), CONCAT(users.first_name, " ", users.last_name)) as agent_name'));
        if ($this->start_date) {
            $payments->whereDate('payment_receipts.created_at', '>=', $this->start_date);
        }
        if ($this->end_date) {
            $payments->whereDate('payment_receipts.created_at', '<=', $this->end_date);
        }
        if ($this->inquiry_id) {
            $payments->where('payment_receipts.inquiry_id', $this->inquiry_id);
        }
        if ($this->agent_id) {
            $payments->whereDate('inquiries.agent_id', $this->agent_id);
        }
        if ($this->receipt_type == 0){
            $payments->where('payment_receipts.receipt_type', false);
        }elseif ($this->receipt_type == 1){
            $payments->where('payment_receipts.receipt_type', true);
        }
        $payments->leftJoin('users', function ($join) {
            $join->on('inquiries.created_by', '=', 'users.id')
                ->whereNull('inquiries.agent_id');
        })->leftJoin('users as agents', 'inquiries.agent_id', '=', 'agents.id');
        return $payments->get();
    }

    public function headings(): array
    {
        // TODO: Implement headings() method.
        $headers = [
            'Inquiry Name',
            'Package',
            'Paid Amount',
            'Receipt/File Name',
            'Payment Mode',
            'Transaction Id',
            'Cheque No',
            'Bank Name (Branch)',
            'Agent Name',
            'Receipt Type',
            'Account Person Name',
            'Payment Date'
        ];
        return $headers;
    }

    public function map($row): array
    {
        // TODO: Implement map() method.
        if ($row->receipt_type == 0) {
            $type = "Normal Receipt";
        } else {
            $type = "Miscellaneous Receipt";
        }
        return [
            $row->full_name,
            $row->package_name,
            $row->amount_paid,
            last(explode('/', $row->receipt_name)),
            $row->payment_mode ?? "Not Defined",
            $row->transaction_id ?? "Not Required / Entered",
            $row->cheque_no ?? "Not Required / Entered",
            $row->bank_and_branch ?? "Not Required / Entered",
            $row->agent_name,
            $type,
            $row->account_name,
            date('d-m-Y h:i A', strtotime($row->created_at))
        ];
    }
}
