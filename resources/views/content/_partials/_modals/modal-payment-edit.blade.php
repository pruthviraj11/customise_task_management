<div class="modal fade" id="shareProject" tabindex="-1" aria-labelledby="shareProjectTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-sm-5 mx-50 pb-4">
                <h1 class="text-center mb-1 edit_payment" id="shareProjectTitle">Payment Details</h1>
                {{--                        <p class="text-center">This are the services</p>--}}

                <form action="{{ route('app-inquiry-payment-update') }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row parent_div">
                        <div class="col-md-4 col-sm-12 mb-1">
                            <input type="hidden" name="inquiry_id"
                                   value="" id="inquiry_id">

                            <input type="hidden" name="inquiry_payment_id"
                                   value="" id="inquiry_payment_id">
                            <label class="form-label" for="full_name">
                                Full Name</label>
                            <input type="text" class="form-control"
                                   placeholder="Full Name"
                                   name="full_name" id="full_name" disabled
                                   value="">
                            <span class="text-danger">
                                                    @error('full_name')
                                {{ $message }}
                                @enderror
                                                </span>
                        </div>
                        <div class="col-md-4 col-sm-12 mb-1">
                            <label class="form-label" for="email">
                                Email</label>
                            <input type="text" class="form-control"
                                   placeholder="Email"
                                   name="email" id="email" disabled
                                   value="">
                            <span class="text-danger">
                                                    @error('email')
                                {{ $message }}
                                @enderror
                                                </span>
                        </div>
                        <div class="col-md-4 col-sm-12 mb-1">
                            <label class="form-label" for="phone">
                                Phone</label>
                            <input type="text" class="form-control"
                                   placeholder="Phone" id="phone"
                                   name="phone" disabled
                                   value="">
                            <span class="text-danger">
                                                    @error('phone')
                                {{ $message }}
                                @enderror
                                                </span>
                        </div>
                        <div class="col-md-4 col-sm-12 mb-1">
                            <label for="package_id">Package</label>
                            <select name="package_id" id="package_id"
                                    class="form-select select2" disabled>
                                {{--                                    <option value="">Select Package</option>--}}
                                {{--                                    @foreach($mapped_packages as $package)--}}
                                {{--                                        <option--}}
                                {{--                                            value="{{ $package->package_id }}" {{ old('package_id') == $package->package_id ? 'selected' : ($inquiry_payment ? ($inquiry_payment->package_id == $package->package_id ? 'selected' : '' ) : '') }}>{{ $package->package_name }}</option>--}}
                                {{--                                    @endforeach--}}
                            </select>
                            <span class="text-danger">
                                                    @error('package_id')
                                {{ $message }}
                                @enderror
                                                </span>
                        </div>
                        <div class="col-md-4 col-sm-12 mb-1">
                            <label class="form-label" for="package_amount">
                                Package Amount</label>
                            <input type="text" id="package_amount" class="form-control"
                                   placeholder="Select Package to Get Amount"
                                   name="package_amount" readonly
                                   value="">
                            <span class="text-danger">
                                                    @error('package_amount')
                                {{ $message }}
                                @enderror
                                                </span>
                        </div>
                        <div class="col-md-4 col-sm-12 mb-1">
                            <label class="form-label" for="package_discounted_amount">
                                Discounted Amount</label>
                            <input type="text" id="package_discounted_amount" class="form-control"
                                   placeholder="Select Package to Get Amount"
                                   name="package_discounted_amount" readonly
                                   value="">
                            <span class="text-danger">
                                                    @error('package_discounted_amount')
                                {{ $message }}
                                @enderror
                                                </span>
                        </div>
                        <div class="col-md-4 col-sm-12 mb-1">
                            <label class="form-label" for="inquiry_status">
                                Package
                                Status</label>
                            <select name="inquiry_status" class="form-select select2" id="inquiry_status">

                            </select>
                            <span class="text-danger">
                                        @error('inquiry_status')
                                {{ $message }}
                                @enderror
                                    </span>
                        </div>
                        <div class="col-md-4 col-sm-12 mb-1 d-flex flex-row align-items-end">
                            <button class="btn btn-primary view_comments">QC Review</button>
                        </div>
                        <hr>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <h2>File Discussion:</h2>
                            <div class="row">
                                <div class="col-md-4 col-sm-12 mb-1">
                                    <label for="expected_travel_date"
                                           class="form-label">Expected Travel Date</label>
                                    <input type="date" class="form-control"
                                           id="expected_travel_date"
                                           name="expected_travel_date"
                                           value=""
                                           aria-describedby="expected_travel_date">
                                    @error('expected_travel_date')
                                    <span class="text-danger">
                                                {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-4 col-sm-12 mb-1">
                                    <label for="purpose_of_visit"
                                           class="form-label">Purpose of Visit</label>
                                    <input type="text" class="form-control"
                                           id="purpose_of_visit"
                                           name="purpose_of_visit"
                                           value=""
                                           placeholder="Enter Purpose of Visit"
                                           aria-describedby="purpose_of_visit">
                                    @error('purpose_of_visit')
                                    <span class="text-danger">
                                                {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-4 col-sm-12 mb-1">
                                    <label for="funds_required"
                                           class="form-label">Required Funds</label>
                                    <input type="text" class="form-control"
                                           id="funds_required"
                                           name="funds_required"
                                           value=""
                                           placeholder="Enter Required Funds"
                                           aria-describedby="funds_required">
                                    @error('funds_required')
                                    <span class="text-danger">
                                                {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-4 col-sm-12 mb-1">
                                    <label for="estimated_submission_date"
                                           class="form-label">Estimated Submission Date</label>
                                    <input type="date" class="form-control"
                                           id="estimated_submission_date"
                                           name="estimated_submission_date"
                                           value=""
                                           aria-describedby="estimated_submission_date">
                                    @error('estimated_submission_date')
                                    <span class="text-danger">
                                                {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-4 col-sm-12 mb-1">
                                    <label for="family_tie"
                                           class="form-label">Family Tie</label>
                                    <input type="text" class="form-control"
                                           id="family_tie"
                                           name="family_tie"
                                           value=""
                                           placeholder="Enter Family Tie"
                                           aria-describedby="family_tie">
                                    @error('family_tie')
                                    <span class="text-danger">
                                                {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                                {{--<div class="col-md-4 col-sm-12 mb-1">
                                    <label for="expected_visa_date"
                                           class="form-label">Expected Visa Date</label>
                                    <input type="date" class="form-control"
                                           id="expected_visa_date"
                                           name="expected_visa_date"
                                           value=""
                                           aria-describedby="expected_visa_date">
                                    @error('expected_visa_date')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>--}}

                                <div class="col-md-12 col-sm-12 mb-1">
                                    <label class="form-label"
                                           for="file_discussion">Discussion</label>
                                    <textarea
                                        class="form-control"
                                        id="file_discussion"
                                        rows="3"
                                        name="file_discussion"
                                        placeholder="Discussion"
                                    >{{ old('file_discussion') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mt-1" id="travel_history_data">

                        </div>
                        <div class="col-12 mt-3 mb-5">
                            <hr>
                            <div id="mappedPackage">

                            </div>
                        </div>
                        <div class="col-md-2 col-sm-2">
                            <label for="hard_copy_received">Agreement Hard Copy Received</label>
                            <div class="form-check mt-1 form-check-primary form-switch">
                                <input type="checkbox" name="hard_copy_received"
                                       {{ userRole() != accountRole() ? 'disabled' : '' }} class="form-check-input"
                                       value="yes"
                                       id="hard_copy_received"/>
                            </div>
                        </div>
                        @if(userRole() == accountRole())

                            <div class="col-12 row my-2">
                                <div class="col-md-2 col-sm-2">
                                    <label for="customSwitch3">Apply Custom Tax</label>
                                    <div class="form-check mt-1 form-check-primary form-switch">
                                        <input type="checkbox"
                                               {{ userRole() != accountRole() ? 'disabled' : '' }} class="form-check-input customSwitch3"
                                               value="yes"
                                               id="customSwitch3"/>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-4 custom_gst_section" id="custom_gst_section" style="display: none;">
                                    <label for="custom_gst">GST% (Enter Value in Percent)</label>
                                    <input type="number" placeholder="Enter Value in Percent" id="custom_gst"
                                           name="custom_gst" class="form-control mt-1"
                                           value="{{ old('custom_gst') ?? '' }}">
                                </div>
                                <div class="col-md-3 col-sm-4 custom_tds_section" id="custom_tds_section" style="display: none;">
                                    <label for="custom_tds">TDS% (Enter Value in Percent)</label>
                                    <input type="number" placeholder="Enter Value in Percent" id="custom_tds"
                                           name="custom_tds" class="form-control mt-1"
                                           value="{{ old('custom_tds') ?? '' }}">
                                </div>
                            </div>
                            <div class="col-12 row my-2">
                                @if(userRole() == accountRole())
                                <div class="col-md-2 col-sm-4">
                                    <label for="payment_mode" class="form-label">Payment Mode</label>
                                    <select name="payment_mode" id="payment_mode"
                                             class="form-select select2"></select>
                                </div>
                                @endif
                                <div class="col-md-3 col-sm-4 transaction_id_section" id="transaction_id_section" style="display: none;">
                                    <label for="transaction_id" class="form-label">Transaction Id</label>
                                    <input type="text" name="transaction_id" placeholder="Enter Transaction Id"
                                           id="transaction_id" class="form-control mt-1">
                                </div>
                                <div class="col-md-3 col-sm-4 cheque_section" id="cheque_section" style="display: none;">
                                    <label for="cheque_no" class="form-label">Cheque No</label>
                                    <input type="text" placeholder="Enter Cheque No" name="cheque_no" id="cheque_no"
                                           class="form-control mt-1">
                                </div>
                                <div class="col-md-3 col-sm-4 bank_and_branch_section" id="bank_and_branch_section" style="display: none;">
                                    <label for="bank_and_branch" class="form-label">Bank and Branch</label>
                                    <input type="text" placeholder="Enter Bank Name (Branch Name)"
                                           name="bank_and_branch" id="bank_and_branch" class="form-control mt-1">
                                </div>
                            </div>
                        @endif
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary me-1">Submit</button>
                            {{--                                    <button type="reset" class="btn btn-outline-secondary">Reset</button>--}}
                        </div>

                        <div class="row receipt">

                        </div>
                        <div class="row mice_receipt">

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

