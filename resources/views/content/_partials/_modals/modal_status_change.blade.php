<!-- modal_status_change.blade.php -->
<h1 class="text-center mb-4">Change Status</h1>

<form method="POST" action="{{ route('app-update-payment-status') }}">
    @csrf
    @method('POST')
    <div class="row mb-3">
    <div class="col-md-4">
        <div class="form-group">
            <label for="package_name"><b>Select a Package:</b></label>
        </div>
    </div>
    <div class="col-md-8">
        <div class="form-group">
            <input type="hidden" name="inquiry_id" value="{{ $inquiry_id }}" >
            <select name="package_id" class="form-control">
                <option value="">Select package</option>
                @foreach($packages as $package)
                    <option value="{{ $package['id'] }}">{{ $package['package_name'] }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>


    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="status"><b>Select a Status :</b></label>
            </div>
        </div>
        <div class="col-md-8">
            <div class="form-group">
                <select name="status" id="status" class="form-control">
                    <option value="">Select Status</option>
                    @foreach ($inquiryStatuses as $status)
                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="text-center mt-3">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
<!-- </form> -->
