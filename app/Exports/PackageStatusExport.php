<?php

namespace App\Exports;

use App\Models\InquiryPayment;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RowType;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PackageStatusExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public $branch;
    public $visa_type_id;
    public $country_id;
    public $package_id;
    public $package_status;
    public $start_date;
    public $end_date;
    public $agent;

    public function __construct($filter_data = [])
    {
        $this->branch = $filter_data['branch'];
        $this->visa_type_id = $filter_data['visa_type_id'];
        $this->country_id = $filter_data['country_id'];
        $this->package_id = $filter_data['package_id'];
        $this->package_status = $filter_data['package_status'];
        $this->start_date = $filter_data['start_date'];
        $this->end_date = $filter_data['end_date'];
        $this->agent = $filter_data['agent'];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        $data = InquiryPayment::leftJoin('inquiries', 'inquiry_payments.inquiry_id', '=', 'inquiries.id')
            ->leftJoin('packages', 'inquiry_payments.package_id', '=', 'packages.id')
            ->leftJoin('countries', 'packages.country_id', '=', 'countries.id')
            ->leftJoin('visa_types', 'packages.visa_type_id', '=', 'visa_types.id')
            ->leftJoin('branches', 'inquiries.branch_id', '=', 'branches.id')
            ->leftJoin('inquiry_status', 'inquiry_payments.inquiry_status', '=', 'inquiry_status.id')
            ->leftJoin('users', function ($join) {
                $join->on('inquiries.created_by', '=', 'users.id')
                    ->whereNull('inquiries.agent_id');
            })->leftJoin('users as agents', 'inquiries.agent_id', '=', 'agents.id');

        if ($this->package_status){
            $data->where('inquiry_payments.inquiry_status', $this->package_status);
        }
        if ($this->agent){
            $data->where('inquiries.agent_id', $this->agent);
        }
        if ($this->branch){
            $data->where('inquiries.branch_id', $this->branch);
        }
        if ($this->package_id){
            $data->where('inquiry_payments.package_id', $this->package_id);
        }else{
            if ($this->country_id){
                $data->where('countries.id', $this->country_id);
            }
            if ($this->visa_type_id){
                $data->where('visa_types.id', $this->visa_type_id);
            }
        }
        if ($this->start_date){
            $data->whereDate('inquiry_payments.created_at', '>=', $this->start_date);
        }

        if ($this->end_date){
            $data->whereDate('inquiry_payments.created_at', '<=', $this->end_date);
        }


        $data->whereNotNull('inquiries.registration_no');


        $data->select('inquiries.*', 'packages.package_name as package_name', 'branches.name as branch_name', 'visa_types.name as visa_name', 'countries.name as country_name', 'inquiry_status.name as inquiry_status_name', 'inquiries.id as inquiry_id', 'inquiries.created_at as inquiry_created_at', 'inquiries.created_by as inquiry_created_by', 'inquiries.updated_at as inquiry_updated_at',
            DB::raw('COALESCE(CONCAT(agents.first_name, " ", agents.last_name), CONCAT(users.first_name, " ", users.last_name)) as agent_name'));

        return $data->get();
    }

    public function headings(): array
    {
        // TODO: Implement headings() method.
        $headers = [
            'Inquiry No',
            'Inquiry Name',
            'Registration No',
            'Email',
            'Phone No',
            'Package Country',
            'Visa Type',
            'Package Created On',
            'Package Name',
            'Current Status',
            'Inquiry Registered Branch',
            'Agent Name',
        ];
        return $headers;
    }

    public function map($row): array
    {
        // TODO: Implement map() method.
        $rows = [
            $row->inquiry_no,
            $row->first_name.' '.$row->last_name,
            $row->registration_no,
            $row->email,
            $row->mobile_one,
            $row->country_name,
            $row->visa_name,
            $row->created_at,
            $row->package_name,
            $row->inquiry_status_name,
            $row->branch_name,
            $row->agent_name
        ];
        return $rows;
    }
}
