<div class="modal fade" id="sendEmail" tabindex="-1" aria-labelledby="sendEmailTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <button type="button" id="sendEmailClose" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body px-sm-5 mx-50 pb-4">
                <h1 class="text-center mb-1" id="sendEmailTitle">Send Email</h1>
{{--                <p class="text-center">This is the miscellaneous receipt form for the package selected</p>--}}

                <form action="" id="send_email_form" class="send_email_form" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div class="col-md-12 col-sm-12 mb-2">
                            <label for="select_workshop" class="form-label">WorkShop</label>
                            <select name="workshop_no" id="workshop_no" class="form-select select2">
                                <option value="">Select Workshop</option>
                                <option value="1">Workshop One</option>
                                <option value="2">Workshop Two</option>
                                <option value="3">Workshop Three</option>
                                <option value="4">Workshop Four</option>
                                <option value="5">One To One Session</option>
                            </select>
                        </div>
                        <div class="col-12 text-center">
                            <button class="btn btn-primary send_email_submit" type="submit">Submit</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

