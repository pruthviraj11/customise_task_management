<div class="modal fade" id="checklist" tabindex="-1" aria-labelledby="checklistTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <button type="button" id="checklistClose" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body px-sm-5 mx-50 pb-4">
                <h1 class="text-center mb-1" id="checklistTitle">Checklist</h1>
                <p class="text-center">This is the checklist for the package selected</p>

                <form action="{{ route('app-inquiry-checklist-update') }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="inquiry_payment_id" id="checklist_payment_id" value="">
                    <div class="row checklistData custom-options-checkable g-1 mb-3" id="checklistData">

                    </div>
                    <button class="btn btn-primary" type="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
