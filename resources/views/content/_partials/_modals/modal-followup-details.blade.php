<form action="{{ route('app-follow-ups-update', encrypt($followup_results->id)) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="container">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th colspan="2" class="text-center">
                    <h1 class="text-center mb-1 title" id="shareProjectTitle">{{ $followup_results->title }}</h1>
                </th>
            </tr>
            </thead>
            <tbody>


            {{--<tr>
                <th><b>Id</b> </th>
                <td>{{ $followup_results->inquirie_id }}</td>
            </tr>--}}
            {{--<tr>
                <th><b>Name</b></th>
                <td>{{ $followup_results->first_name }}</td>
            </tr>--}}
            <tr>
                <th><b>Follow Up Type</b></th>
                <td>{{ $followup_results->followup_type_name }}</td>
            </tr>
            <tr>
                <th><b>Follow Up Status</b></th>
                <td>{{ $followup_results->followup_status_name }}</td>
            </tr>
            <tr>
                <th><b>Description</b></th>
                <td>{{ $followup_results->description }}</td>
            </tr>
            @if($followup_results->package_name)
                <tr>
                    <th><b>For Package</b></th>
                    <td>{{ $followup_results->package_name }}</td>
                </tr>
            @endif
            {{--<tr>
                <th><b>Email</b> </th>
                <td>{{ $followup_results->email }}</td>
            </tr>
            <tr>
                <th><b>Phone</b> </th>
                <td>{{ $followup_results->mobile_one }}</td>
            </tr>--}}


            <tr>
                <th><b>Created Date</b></th>
                <td>{{ date('Y-m-d g:i A', strtotime($followup_results->created_at)) }}</td>
            </tr>

            <tr>
                <th><b>Next Follow up date</b></th>
                <td>{{ date('Y-m-d g:i A', strtotime($followup_results->followup_date)) }}</td>
            </tr>
            <tr>
                <th><b>Status</b></th>
                <td>{{ $followup_results->display_name }}</td>
            </tr>
            <tr>
                <th><b>Action Status</b></th>
                <td>
                    <select class="form-select select2"
                            name="form_status" {{ $followup_results->action_status != 1 ? "disabled" : "" }} >
                        @foreach ($actionstatus as $item)
                            <option
                                value="{{ $item->id }}" {{ old('form_status') ? (old('form_status')  == $item->id ? 'selected' : '') : ($item->id == $followup_results->action_status ? 'selected' : '') }}>
                                {{ $item->display_name }}
                            </option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th><b>Note</b></th>
                <td>
                    <textarea class="form-control"
                              {{ $followup_results->action_status != 1 ? "disabled" : "" }} id="note" name="note"
                              aria-describedby="note"
                              rows="4">{{ old('note', $followup_results->note) }}</textarea>
                </td>
            </tr>
            <tr>
            <tr>
                @if($followup_results->action_status == 1)
                    <td colspan="2" class="centered-buttons" style=" text-align: center;">
                        <button type="button" id="toggleButton" class="btn btn-danger mt-1">Add Follow Up</button>
                        <button type="submit" class="btn btn-primary mt-1">Submit</button>
                    </td>
                @endif
            </tr>

            </tr>

            </tbody>
        </table>

    </div>

    <!-- project link -->
</form>

<!-- Secend Form start -->
<div class="row">
    <div class="col-md-12" id="contentDiv" style="display: none;">
        <form class="pt-0" action="{{ route('app-follow-ups-store') }}" method="POST">
            @csrf
            <div class="modal-header mb-1">
                <input type="hidden" name="inquirie_id" value="{{ $followup_results->inquirie_id}}">
            </div>
            <div class="modal-body flex-grow-1">
                <div class="mb-1">
                    <div class="col-md-12 col-sm-6 m-1">
                        <label for="title" class="form-label"><b>Title</b></label>
                        <input type="text" name="title" class="form-control" id="title" aria-describedby="title"
                               required value="{{ old('title') }}" placeholder="Title">
                        <span class="text-danger">@error('title') {{ $message }} @enderror</span>
                    </div>

                    <div class="col-md-12 col-sm-6 m-1">
                        <label for="description" class="form-label"><b>Description</b></label>
                        <input type="text" name="description" class="form-control" id="description"
                               aria-describedby="description" required value="{{ old('description') }}"
                               placeholder="Description">
                        <span class="text-danger">@error('description') {{ $message }} @enderror</span>
                    </div>

                    <div class="col-md-12 col-sm-6 m-1">
                        <label for="followup_date" class="form-label">Next Follow-Up Date</label>
                        <input type="datetime-local" name="followup_date" class="form-control" id="followup_date"
                               value="{{ old('followup_date') }}" aria-describedby="followup_date" required>
                    </div>
                    <div class="col-md-12 col-sm-6 m-1">
                        <label class="form-label" for="followupstatus">Follow up Status</label>
                        <select class="form-select select2" name="followupstatus" id="followupstatus">
                            <option value="">Select follow up Status</option>
                            @foreach ($followupStatus as $followupStatus_name)

                                <option value=" {{ $followupStatus_name->id }}">
                                    {{ $followupStatus_name->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12 col-sm-6 m-1">
                        <label class="form-label" for="followuptype">Follow up Type</label>
                        <select class="form-select select2" name="followuptype" id="followuptype">
                            <option value="">Select follow up type</option>
                            @foreach ($followuptype as $followuptype_name)
                                <option value="{{ $followuptype_name->id }}">
                                    {{ $followuptype_name->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-1">
                </div>
                <div class="mb-1 text-center">
                    <button type="submit" class="btn btn-primary me-1 data-submit">Submit</button>
                    <button class="btn btn-outline-secondary" id="hideFollowUp">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- secend form end -->


<!-- Modal for the Follow-Up form
<div class="modal fade" id="followUpModal" tabindex="-1" aria-labelledby="followUpModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

        </div>
    </div>
</div> -->
<script>
    $(document).ready(function () {
        $('#followUpModal').modal('hide');

        $('#btnfollowup_modal').click(function () {
            $('#followUpModal').modal('show');
        });
    });
    $(document).ready(function () {
        // Attach a click event handler to the button
        $("#toggleButton").click(function () {
            // Toggle the visibility of the content div
            $("#contentDiv").slideDown();
        });
        $("#hideFollowUp").click(function () {
            // Toggle the visibility of the content div
            $("#contentDiv").slideUp();
        });
    });
</script>
<script>
    //past_date_time
    $(document).ready(function () {

        var currentDateTime = new Date().toISOString().slice(0, 16);
        $('#followup_date').attr('min', currentDateTime);
    });
</script>
