<div class="modal fade" id="miscellaneousReceipt" tabindex="-1" aria-labelledby="miscellaneousReceiptTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <button type="button" id="miscellaneousReceiptClose" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body px-sm-5 mx-50 pb-4">
                <h1 class="text-center mb-1" id="miscellaneousReceiptTitle">Add Miscellaneous Receipt</h1>
                <p class="text-center">This is the miscellaneous receipt form for the package selected</p>

                <form action="{{ route('app-inquiry-miscellaneous-receipt-add') }}" id="miscellaneous_receipt_form" class="miscellaneous_receipt_form" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="inquiry_payment_id" id="miscellaneous_receipt_payment_id" value="">
                    <input type="hidden" name="inquiry_id" id="miscellaneous_receipt_inquiry_id" value="">
                    <input type="hidden" name="package_id" id="miscellaneous_receipt_package_id" value="">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="" class="form-label">First Name</label>
                            <input type="text" readonly name="first_name" id="first_name" class="form-control" value="">
                        </div>
                        <div class="col-md-6">
                            <label for="" class="form-label">Last Name</label>
                            <input type="text" readonly name="last_name" id="last_name" class="form-control" value="">
                        </div>
                        <div class="col-md-6">
                            <label for="" class="form-label">Package Name</label>
                            <input type="text" readonly name="package_name" id="package_name" class="form-control" value="">
                        </div>
                        <div class="col-md-6">
                            <label for="" class="form-label">Agent Name</label>
                            <input type="text" readonly name="agent_name" id="agent_name" class="form-control" value="">
                        </div>
                    </div>
                    <hr>
                    <div class="row my-2" id="miscellaneousReceiptAddMore">
                        {{--<div class="col-12 row parent_div_item">
                            <div class="col-md-5 mb-1">
                                <label for="" class="form-label">Particular</label>
                                <select name="particular[]" id="particular[]" class="form-select select2">
                                    <option value="">Select Particular</option>
                                    <option value="File Process Fees">File Process Fees</option>
                                    <option value="Application Fees">Application Fees</option>
                                    <option value="Visa Fees">Visa Fees</option>
                                    <option value="Affidavit/Notary/Translation">Affidavit/Notary/Translation</option>
                                    <option value="Valuation Report">Valuation Report</option>
                                    <option value="CA Report">CA Report</option>
                                    <option value="Air Ticket">Air Ticket</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-1">
                                <label for="" class="form-label">Quantity</label>
                                <input type="number" class="form-control" name="quantity[]" id="quantity[]" value="">
                            </div>
                            <div class="col-md-3 mb-1">
                                <label for="" class="form-label">Amount</label>
                                <input type="number" class="form-control" name="amount[]" id="amount[]" value="">
                            </div>
                            <div class="col-md-2 align-self-end mb-1">
                                <button class="btn btn-danger align-self-end remove_item" type="button" id="remove_item">Remove</button>
                            </div>
                        </div>--}}
                    </div>
                    <div class="row my-1">
                        <div class="col-md-12 text-end">
                            <button class="btn btn-primary" type="button" id="add_item">Add Item</button>
                        </div>
                    </div>

                    <div class="col-12 row my-2">
                        <div class="col-md-2 col-sm-2">
                            <label for="customSwitch3">Apply Tax</label>
                            <div class="form-check mt-1 form-check-primary form-switch">
                                <input type="checkbox" {{ userRole() != accountRole() ? 'disabled' : '' }} class="form-check-input customSwitch3" value="yes"
                                       id="customSwitch3"/>
                            </div>
                        </div>
                        <div class="col-md-4 custom_gst_section" id="custom_gst_section" style="display: none;">
                            <label for="custom_gst">GST% (Enter Value in Percent)</label>
                            <input type="number" placeholder="Enter Value in Percent" id="custom_gst"
                                   name="custom_gst" class="form-control mt-1"
                                   value="{{ old('custom_gst') ?? '' }}">
                        </div>
                        <div class="col-md-4 custom_tds_section" id="custom_tds_section" style="display: none;">
                            <label for="custom_tds">TDS% (Enter Value in Percent)</label>
                            <input type="number" placeholder="Enter Value in Percent" id="custom_tds"
                                   name="custom_tds" class="form-control mt-1"
                                   value="{{ old('custom_tds') ?? '' }}">
                        </div>
                    </div>
                    <div class="col-12 row my-2">
                        <div class="col-md-4" >
                            <label for="payment_mode" class="form-label">Payment Mode</label>
                            <select name="payment_mode" id="payment_mode" {{ userRole() != accountRole() ? 'disabled' : '' }} class="form-select select2"></select>
                        </div>
                        <div class="col-md-4 transaction_id_section" id="transaction_id_section" style="display: none;">
                            <label for="transaction_id" class="form-label">Transaction Id</label>
                            <input type="text" name="transaction_id" placeholder="Enter Transaction Id" id="transaction_id" class="form-control mt-1">
                        </div>
                        <div class="col-md-4 cheque_section" id="cheque_section" style="display: none;">
                            <label for="cheque_no" class="form-label">Cheque No</label>
                            <input type="text" placeholder="Enter Cheque No" name="cheque_no" id="cheque_no" class="form-control mt-1">
                        </div>
                        <div class="col-md-4 bank_and_branch_section" id="bank_and_branch_section" style="display: none;">
                            <label for="bank_and_branch" class="form-label">Bank and Branch</label>
                            <input type="text" placeholder="Enter Bank Name (Branch Name)" name="bank_and_branch" id="bank_and_branch" class="form-control mt-1">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">

                        </div>
                        <div class="col-md-2">

                        </div>
                        <div class="col-md-5">

                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

