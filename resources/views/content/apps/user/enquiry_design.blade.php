@extends('layouts/contentLayoutMaster')
@section('title', 'User add')
@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
@endsection
@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
@endsection
@section('content')
    {{-- Navbar_Card --}}

    <nav class="navbar navbar-light bg-light Sharch_nav">
        <div class="container-fluid  Sharch_nav_fluid">

            <div class="d-flex justify-content-between align-items-center w-100">
                <div class="input-group input-group-merge ms-1 search">
                    <input type="text" class="form-control" id="chat-search" placeholder="Search or start a new chat"
                        aria-label="Search" aria-describedby="chat-search">
                    <span class="input-group-text search_btn"><svg xmlns="http://www.w3.org/2000/svg" width="14"
                            height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-linejoin="round" class="feather feather-search text-muted">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg></span>
                </div>
                <div class="head_btn">

                    <a href="#" id="transfer" class="btn transfer justify-content-center">Transfer</a>

                    <a href="#" id="follow" class="btn follow">Follow
                        Up</a>
                </div>
            </div>

        </div>
    </nav>

    {{-- End Navbar_Card --}}
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="container enquiry_form">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="">
                        <div class="card-head p-2 d-flex justify-content-between">
                            <p>Enquiry No:12345</p>
                            <p class="text-end">Owner:Excepteur Sint</p>
                        </div>
                        <div class="mt-1">
                            <div class="accordion" id="accordionExample">
                                <div class="accordion-item border">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button Main_title" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            Personal Details
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show"
                                        aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <div class="form_details p-1 border">
                                                <div class="row g-1">
                                                    <div class="col-md-12 row">
                                                        <div class="col-md-2 mt-3 text-center col-sm-6">
                                                            <img src="{{ asset('images/avatars/1.png') }}" alt=""
                                                                width="100px" class="avtar_img"><br>
                                                            {{-- <button class="addfiles btn btn-primary btn-sm">Choose
                                                            Photo</button> --}}

                                                            {{-- <input id="fileupload" class="avatar_img" type="file"
                                                            name="files[]" multiple> --}}
                                                            <div id="yourBtn" class="btn btn-primary btn-sm"
                                                                onclick="getFile()" style="width: 100%;">Choose Photo</div>
                                                            <div style='height: 0px;width: 0px; overflow:hidden;'>
                                                                <input id="upfile" type="file" value="profile_picture"
                                                                    onchange="sub(this)" />
                                                            </div>

                                                        </div>

                                                        <div class="col-md-10 col-sm-12">
                                                            <div class="row">
                                                                <div class="col-md-3 col-sm-6">
                                                                    <label for="firstname" class="form-label"><b>First
                                                                            Name</b><span class="red">*</span></label>
                                                                    <input type="text" name="first_name"
                                                                        class="form-control" id="firstname"
                                                                        aria-describedby="firstname" required
                                                                        placeholder="Firstname">
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label for="middelname" class="form-label">Middel
                                                                        Name<span class="red">*</span></label>
                                                                    <input type="text" name="middle_name"
                                                                        class="form-control" id="middelname" required
                                                                        aria-describedby="middelname"
                                                                        placeholder="Middelname">
                                                                </div>
                                                                <div class="col-md-3 col-sm-6">
                                                                    <label for="lastlname" class="form-label">Last
                                                                        Name<span class="red">*</span></label>
                                                                    <input type="text" name="last_name"
                                                                        class="form-control" id="lastname"
                                                                        aria-describedby="middelname"
                                                                        placeholder="Lastname" required>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label for="dob" class="form-label">Date of
                                                                        Birth<span class="red">*</span></label>
                                                                    <input type="date" name="dob"
                                                                        class="form-control" id="dob"
                                                                        aria-describedby="dob" required>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label" for="gender">Gender<span
                                                                            class="red">*</span></label>
                                                                    <select class="form-select select2" name="gender"
                                                                        id="gender" name="gender">
                                                                        <option value="">Select gender</option>
                                                                        <option value="Male">Male</option>
                                                                        <option value="Female">Female</option>
                                                                        <option value="Other">Other</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label"
                                                                        for="Marital_status">Marital_status<span
                                                                            class="red">*</span></label>
                                                                    <select class="form-select select2"
                                                                        id="Marital_status" name="marital_status">
                                                                        <option value="">Select Marital_status
                                                                        </option>
                                                                        <option value="married">married</option>
                                                                        <option value="unmarried">unmarried</option>
                                                                        <option value="divorced">divorced</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <label for="Mobilenumber" class="form-label">Mobile
                                                                        Number<span class="red">*</span></label>
                                                                    <input type="tel" name="mobile_number"
                                                                        class="form-control" required>
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <label for="Mobilenumber2" class="form-label">Mobile
                                                                        Number
                                                                        2<span class="red">*</span></label>
                                                                    <input type="tel" name="mob_number"
                                                                        class="form-control" required>
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <label for="homephone" class="form-label">Home
                                                                        Phone<span class="red">*</span></label>
                                                                    <input type="tel" class="form-control" required>
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <label for="officephone" class="form-label">Office
                                                                        Phone<span class="red">*</span></label>
                                                                    <input type="tel" class="form-control" required>
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <label for="passportnumber"
                                                                        class="form-label">Passport
                                                                        Number<span class="red">*</span></label>
                                                                    <input type="text" name="passport_no"
                                                                        class="form-control" id="passportnumber" required
                                                                        aria-describedby="passportnumber">
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <label for="passportexdate"
                                                                        class="form-label">Passport Expier Date<span
                                                                            class="red">*</span></label>
                                                                    <input type="date" class="form-control"
                                                                        id="passportexdate" name="date_passportexpier"
                                                                        aria-describedby="passportexdate" required>
                                                                </div>


                                                                <div class="col-md-3">
                                                                    <label for="skypid" class="form-label">SkypId<span
                                                                            class="red">*</span></label>
                                                                    <input type="text" name="skype_id"
                                                                        class="form-control mb-1" id="skypid"
                                                                        aria-describedby="skypid" required>
                                                                </div>


                                                                <div class="col-md-3">
                                                                    <label for="email" class="form-label">Email<span
                                                                            class="red">*</span></label>
                                                                    <input type="email" class="form-control"
                                                                        id="email" aria-describedby="email" required>
                                                                </div>


                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Other_Details_accordion  Start --}}

                                                <div class="accordion-item mt-2">
                                                    <h2 class="accordion-header" id="headingTwo">
                                                        <button class="accordion-button collapsed other_details"
                                                            type="button" data-bs-toggle="collapse"
                                                            data-bs-target="#collapseTwo" aria-expanded="false"
                                                            aria-controls="collapseTwo">
                                                            <b>Other Details</b>
                                                        </button>
                                                    </h2>
                                                    <div id="collapseTwo" class="accordion-collapse collapse"
                                                        aria-labelledby="headingTwo" data-bs-parent="accodion_one">
                                                        <div class="accordion-body p-2 otherdetails">
                                                            <div class="row g-1">
                                                                <div class="col-md-12 row ">
                                                                    <div class="col-md-3">
                                                                        <label class="form-label"
                                                                            for="Nationality">Nationality</label>
                                                                        <select class="form-select select2"
                                                                            id="Nationality" name="nationality">
                                                                            <option value="">Select Nationality
                                                                            </option>
                                                                            <option value="married">Indian</option>
                                                                            <option value="unmarried">American</option>
                                                                            <option value="divorced">Afghan</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label class="form-label"
                                                                            for="enquirystage">inquiry
                                                                            Stage</label>
                                                                        <select class="form-select select2"
                                                                            id="enquirystage" name="inquiry_stage">
                                                                            <option value="">Select Enquiry
                                                                                Stage</option>
                                                                            <option value="india">Indian</option>
                                                                            <option value="America">American</option>
                                                                            <option value="Afghan">Afghan</option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-3">
                                                                        <label class="form-label" for="country">Country
                                                                            Of
                                                                            Birth</label>
                                                                        <select class="form-select select2" id="country"
                                                                            name="country_of_birth">
                                                                            <option value="">Birth Country</option>
                                                                            <option value="india">Indian</option>
                                                                            <option value="America">American</option>
                                                                            <option value="Afghan">Afghan</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label class="form-label" for="country">Visa
                                                                            Type</label>
                                                                        <select class="form-select select2" id="visatype"
                                                                            name="visatype">
                                                                            <option value="">Birth Country</option>
                                                                            <option value="1">Business Visa</option>
                                                                            <option value="2">Project Visa</option>
                                                                            <option value="3">Tourist Visa</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label class="form-label"
                                                                            for="country_pref">Country
                                                                            Prefrence</label>
                                                                        <select class="form-select select2"
                                                                            id="country_pref"
                                                                            name="country_of_preference">
                                                                            <option value="">Country Prefrence
                                                                            </option>
                                                                            <option value="1">A</option>
                                                                            <option value="2">B</option>
                                                                            <option value="3">C</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label for="enquiry_date"
                                                                            class="form-label">Enquiry
                                                                            Date</label>
                                                                        <input type="date" name="enquiry_date"
                                                                            class="form-control" id="dob"
                                                                            aria-describedby="enquiry_date" required>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label class="form-label"
                                                                            for="Enquiry_Source">Enquiry
                                                                            Source</label>
                                                                        <select class="form-select select2"
                                                                            name="enquiry_source" id="Enquiry_Source"
                                                                            name="inquiry_source">
                                                                            <option value="">Enquiry_Source</option>
                                                                            <option value="1">A</option>
                                                                            <option value="2">B</option>
                                                                            <option value="3">C</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label for="budget"
                                                                            class="form-label">Budget</label>
                                                                        <input type="text" name="budget"
                                                                            class="form-control" id="budget"
                                                                            aria-describedby="budget" name="budget"
                                                                            placeholder="Lastname">
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label class="form-label"
                                                                            for="Enquiry_Desc">Enquiry
                                                                            Desc</label>
                                                                        <input type="text" class="form-control"
                                                                            id="inquiry_description"
                                                                            aria-describedby="inquiry_description"
                                                                            name="inquiry_description"
                                                                            placeholder="Enquiry Dec">
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label for="occupation"
                                                                            class="form-label">Occupation</label>
                                                                        <input type="text" name="occupation"
                                                                            class="form-control" id="occupation"
                                                                            aria-describedby="occupation">
                                                                    </div>
                                                                    <div class="col-md-4 mt-2">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            name="enquiry_remarks" valu=""
                                                                            id="flexCheckChecked" />
                                                                        <label class="form-check-label"
                                                                            for="flexCheckChecked">Do
                                                                            Not Contact</label>
                                                                    </div>
                                                                    <div class="col-md-12 ">

                                                                        <label for="enquiryremark"
                                                                            class="form-label">Enquiry
                                                                            Remarks</label>
                                                                        <textarea class="form-control" id="enquiryremark"name="enquiry_remarks" aria-describedby="enquiryremark"
                                                                            rows="4"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- Other_Details_accordion  End --}}

                                                {{-- Maling_Address_accordion  Start --}}


                                                <div class="accordion-item mt-2">
                                                    <h2 class="accordion-header" id="headingThree">
                                                        <button class="accordion-button collapsed Maling_Address"
                                                            type="button" data-bs-toggle="collapse"
                                                            data-bs-target="#collapseThree" aria-expanded="false"
                                                            aria-controls="collapseThree">
                                                            <b>Maling Address</b>
                                                        </button>
                                                    </h2>
                                                    <div id="collapseThree" class="accordion-collapse collapse"
                                                        aria-labelledby="headingThree" data-bs-parent="accodion_one">
                                                        <div class="accordion-body p-2 malingaddress">
                                                            <div class="row g-1">
                                                                <div class="col-md-12 row">
                                                                    <span class="Acod_three mb-2">Present Address</span>
                                                                    <div class="col-md-4">
                                                                        <label class="form-label"
                                                                            for="list_country">Country</label>
                                                                        <select class="form-select select2"
                                                                            id="list_country" name="list_country"
                                                                            name="mail_address">
                                                                            <option value="">Country</option>
                                                                            <option value="1">A</option>
                                                                            <option value="2">B</option>
                                                                            <option value="3">C</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label class="form-label"
                                                                            for="list_State">State</label>
                                                                        <select class="form-select select2"
                                                                            id="list_State" name="mail_state">
                                                                            <option value="">State</option>
                                                                            <option value="1">A</option>
                                                                            <option value="2">B</option>
                                                                            <option value="3">C</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label class="form-label"
                                                                            for="list_City">City</label>
                                                                        <select class="form-select select2" id="list_City"
                                                                            name="mail_city">
                                                                            <option value="">City</option>
                                                                            <option value="1">A</option>
                                                                            <option value="2">B</option>
                                                                            <option value="3">C</option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-4">
                                                                        <label class="form-label"
                                                                            for="list_Zip">Zip</label>
                                                                        <select class="form-select select2" id="list_Zip"
                                                                            name="mail_zip_code">
                                                                            <option value="">Zip</option>
                                                                            <option value="1">A</option>
                                                                            <option value="2">B</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <label for="Address"
                                                                            class="form-label">Address</label>
                                                                        <input type="text" name="address"
                                                                            class="form-control" id="Address"
                                                                            aria-describedby="Address">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Maling_Address_accordion  End --}}

                                                    {{-- Spouse_Information_accordion  Start --}}

                                                    <div class="accordion-item mt-2">
                                                        <h2 class="accordion-header" id="headingFour">
                                                            <button class="accordion-button collapsed Spouse_Information"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#collapsefour" aria-expanded="false"
                                                                aria-controls="collapsefour">
                                                                <b>Spouse Information</b>
                                                            </button>
                                                        </h2>
                                                        <div id="collapsefour" class="accordion-collapse collapse"
                                                            aria-labelledby="headingFour" data-bs-parent="accodion_one">
                                                            <div class="accordion-body p-2 malingaddress">
                                                                <div class="row g-1">
                                                                    <div class="col-md-12 row">
                                                                        <div class="col-md-3"><label for="SpouseFirstName"
                                                                                class="form-label">Spouse First
                                                                                Name</label>
                                                                            <input type="text" class="form-control"
                                                                                id="SpouseFirstName"
                                                                                name="spouse_first_name"
                                                                                aria-describedby="SpouseFirstName">
                                                                        </div>
                                                                        <div class="col-md-3"><label
                                                                                for="SpouseMiddelName"
                                                                                class="form-label">Spouse Middel
                                                                                Name</label>
                                                                            <input type="text" class="form-control"
                                                                                id="SpouseMiddelName"
                                                                                name="spouse_middle_namex"
                                                                                aria-describedby="SpouseMiddelName">
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label for="SpouseLastName"
                                                                                class="form-label">Spouse
                                                                                Last Name</label>
                                                                            <input type="text" class="form-control"
                                                                                id="SpouseLastName"
                                                                                name="spouse_last_name"
                                                                                aria-describedby="SpouseLastName">
                                                                        </div>
                                                                        <div class="col-md-3"><label for="dateofmerriage"
                                                                                class="form-label">Date of Merriage</label>
                                                                            <input type="date" name="date_of_marriage"
                                                                                class="form-control" id="dateofmerriage"
                                                                                aria-describedby="dateofmerriage">
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label for="spousedate"
                                                                                class="form-label">Spouse
                                                                                Birthdate</label>
                                                                            <input type="date" class="form-control"
                                                                                id="spousedate" name="spouse_dob"
                                                                                aria-describedby="spousedate">
                                                                        </div>


                                                                        <div class="col-md-3">
                                                                            <label for="Spouseeducation"
                                                                                class="form-label">Spouse
                                                                                Education Level</label>
                                                                            <input type="text"
                                                                                name="spouse_education_level"
                                                                                class="form-control" id="Spouseeducation"
                                                                                aria-describedby="Spouseeducation">
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label class="form-label"
                                                                                for="Medium">spouse_medium_of_education</label>
                                                                            <select class="form-select select2"
                                                                                id="Medium" name="medium">
                                                                                <option value="">Medium
                                                                                </option>
                                                                                <option value="1">A</option>
                                                                                <option value="2">B</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label for="noofchild" class="form-label">No
                                                                                Of
                                                                                Child</label>
                                                                            <input type="text" name="no_of_child"
                                                                                class="form-control" id="noofchild"
                                                                                aria-describedby="noofchild">
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label for="workexpirence"
                                                                                class="form-label">Spouse
                                                                                Work Experience</label>
                                                                            <input type="text" name="experience_remark"
                                                                                class="form-control" id="workexpirence"
                                                                                aria-describedby="workexpirence"
                                                                                name="	spouse_work_experience_remark">
                                                                        </div>

                                                                        <div class="col-md-9">
                                                                            <label for="jobduties" class="form-label">
                                                                                Job Duties</label>
                                                                            <input type="text" name="job_duties"
                                                                                class="form-control" id="jobduties"
                                                                                aria-describedby="jobduties">
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <div class="col-md">
                                                                                <input class="form-check-input Newsletter"
                                                                                    type="checkbox" value=""
                                                                                    name="is_subscribe_newsletter"
                                                                                    id="Newsletter">
                                                                                <label class="form-check-label checkedbtn"
                                                                                    for="Newsletter">
                                                                                    Is Subscribe Newsletter
                                                                                </label>
                                                                                <input class="form-check-input"
                                                                                    type="checkbox"
                                                                                    name="is_relative_in_preferred_country"
                                                                                    value="" id="prefcompony">
                                                                                <label class="form-check-label checkedbtn"
                                                                                    for="prefcompony">
                                                                                    Is Reletive in Pref Country
                                                                                </label>

                                                                                <div class="col-md mt-2 fourspan">
                                                                                    <P>
                                                                                        Do You Or Your Spouse Have Any
                                                                                        Relation
                                                                                        In
                                                                                        The Intersted Country?
                                                                                    </P>
                                                                                    <input type="radio" name="radio"
                                                                                        id="one"
                                                                                        class="radio"><label
                                                                                        for="one">Yes</label>
                                                                                    <input type="radio" name="radio"
                                                                                        id="one_no" class="radio">
                                                                                    <label for="one_no">No</label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Spouse_Information_accordion  End --}}

                                                    {{-- Additional_Question_accordion  Start --}}

                                                    <div class="accordion-item mt-2">
                                                        <h2 class="accordion-header" id="headingFive">
                                                            <button class="accordion-button collapsed collapsefive"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#collapsefive" aria-expanded="false"
                                                                aria-controls="collapsefive">
                                                                <b>Additional Questions</b>
                                                            </button>
                                                        </h2>
                                                        <div id="collapsefive" class="accordion-collapse collapse"
                                                            aria-labelledby="headingFive" data-bs-parent="accodion_one">
                                                            <div class="accordion-body p-2 malingaddress">
                                                                <div class="row">
                                                                    <div class="col-md-12 row">
                                                                        <div class="col-md-3">
                                                                            <label for="workexpirence"
                                                                                class="form-label">Have
                                                                                you
                                                                                been Abroad Before?</label>
                                                                            <input type="text" name="que1"
                                                                                class="form-control" id="workexpirence"
                                                                                aria-describedby="workexpirence">
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label for="workexpirence"
                                                                                class="form-label">Countries
                                                                                Visited</label>
                                                                            <input type="text" name="que2"
                                                                                class="form-control" id="workexpirence"
                                                                                aria-describedby="workexpirence">
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label class="form-label" for="typevisa">Visa
                                                                                Type</label>
                                                                            <select class="form-select select2"
                                                                                id="typevisa" name="typevisa">
                                                                                <option value="">Tourist visa
                                                                                </option>
                                                                                <option value="1">Journalist visa
                                                                                </option>
                                                                                <option value="2">Medical visa
                                                                                </option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label for="workexpirence"
                                                                                class="form-label">Duration
                                                                                Of Your Stay</label>
                                                                            <input type="text" name="que4"
                                                                                class="form-control" id="workexpirence"
                                                                                aria-describedby="workexpirence">
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label for="workexpirence"
                                                                                class="form-label">Have
                                                                                Been Refused For Visa?</label>
                                                                            <input type="text" name="que5"
                                                                                class="form-control" id="workexpirence"
                                                                                aria-describedby="workexpirence">
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label for="workexpirence"
                                                                                class="form-label">Countries Applied
                                                                                For</label>
                                                                            <input type="text" name="que6"
                                                                                class="form-control" id="workexpirence"
                                                                                aria-describedby="workexpirence">
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label for="workexpirence"
                                                                                class="form-label">Rejection eason </label>
                                                                            <input type="text" name="que7"
                                                                                class="form-control" id="workexpirence"
                                                                                aria-describedby="workexpirence">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Additional_Question_accordion  End --}}

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Qualification_accodion_start --}}
                                <div class="accordion mt-1" id="accordionSix">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingSix">
                                            <button class="accordion-button Six_title" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseSix"
                                                aria-expanded="true" aria-controls="collapseSix">
                                                Qualification
                                            </button>
                                        </h2>
                                        <div id="collapseSix" class="accordion-collapse collapse "
                                            aria-labelledby="headingSix" data-bs-parent="#accordionSix">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    {{-- <div class="col-md-12 row">
                                                    <div class="col-md-3">
                                                        <label class="form-label" for="Level">Level</label>
                                                        <select class="form-select select2" id="Level"
                                                            name="Level">
                                                            <option value="">0</option>
                                                            <option value="1">1</option>
                                                            <option value="2">2</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="Course" class="form-label">Course
                                                        </label>
                                                        <input type="text" class="form-control" id="Course"
                                                            aria-describedby="Course">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="Specialization" class="form-label">Specialization
                                                        </label>
                                                        <input type="text" class="form-control" id="Specialization"
                                                            aria-describedby="Specialization">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="Schoole/Collage" class="form-label">Schoole/Collage
                                                        </label>
                                                        <input type="text" class="form-control" id="Schoole/Collage"
                                                            aria-describedby="Schoole/Collage">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="Board/Uni" class="form-label">Board/Uni
                                                        </label>
                                                        <input type="text" class="form-control" id="Board/Uni"
                                                            aria-describedby="Board/Uni">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="YearofPassing" class="form-label">Year Of Passing
                                                        </label>
                                                        <input type="text" class="form-control" id="YearofPassing"
                                                            aria-describedby="YearofPassing">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="cgpa" class="form-label">%CGPS
                                                        </label>
                                                        <input type="text" class="form-control" id="cgpa"
                                                            aria-describedby="cgpa">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="StartDate" class="form-label">Start Date
                                                        </label>
                                                        <input type="text" class="form-control" id="StartDate"
                                                            aria-describedby="StartDate">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="EndDate" class="form-label">End Date
                                                        </label>
                                                        <input type="text" class="form-control" id="EndDate"
                                                            aria-describedby="EndDate">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="Location" class="form-label">Location
                                                        </label>
                                                        <input type="text" class="form-control" id="Location"
                                                            aria-describedby="Location">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label" for="typevisa">Visa Type</label>
                                                        <select class="form-select select2" id="typevisa"
                                                            name="typevisa">
                                                            <option value="">Tourist visa</option>
                                                            <option value="1">Journalist visa</option>
                                                            <option value="2">Medical visa</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="Comments" class="form-label">Comments
                                                        </label>
                                                        <input type="text" class="form-control" id="Comments"
                                                            aria-describedby="Comments">
                                                    </div>
                                                    <div class="row">
                                                        <div class="d-flex justify-content-end ">
                                                            <button class="btn btn-icon btn-Danger" id="remove"
                                                                type="button" data-repeater-create>
                                                                <i data-feather="plus" class="me-25"></i>
                                                                <span>Remove</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div> --}}
                                                    <div id="new_qualification"></div>
                                                    <div class="col-md-10">
                                                        <button class="btn btn-icon btn-primary" id="add_qualification"
                                                            type="button" data-repeater-create>
                                                            <i data-feather="plus" class="me-25"></i>
                                                            <span>Add New</span>
                                                        </button>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        {{-- Qualification_accodion_End --}}

                                        {{-- work_experience_accodion_start --}}
                                        <div class="accordion mt-1" id="accordionSeven">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingSeven">
                                                    <button class="accordion-button Seven_title" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseSeven"
                                                        aria-expanded="true" aria-controls="collapseSeven">
                                                        Work Experience
                                                    </button>
                                                </h2>
                                                <div id="collapseSeven" class="accordion-collapse collapse "
                                                    aria-labelledby="headingSeven" data-bs-parent="#accordionSeven">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            {{-- <div class="col-md-12 row">
                                                            <div class="col-md-4">
                                                                <label for="compony" class="form-label">Compony
                                                                </label>
                                                                <input type="text" class="form-control" id="compony"
                                                                    aria-describedby="compony">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label for="Designation" class="form-label">Designation
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                    id="Designation" aria-describedby="Designation">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label for="Job_Repository" class="form-label">Job
                                                                    Repository
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                    id="Job_Repository" aria-describedby="Job_Repository">
                                                            </div>

                                                            <div class="col-md-4">
                                                                <label for="Duration" class="form-label">Duration
                                                                </label>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <label for="Duration_year" class="form-label">Yrs
                                                                        </label>
                                                                        <input type="text" class="form-control"
                                                                            id="Duration_year"
                                                                            aria-describedby="Duration_year">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="Duration_year" class="form-label">Mths
                                                                        </label>
                                                                        <input type="text" class="form-control"
                                                                            id="Duration" aria-describedby="Duration">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 mt-1">
                                                                <label for="fromdate" class="form-label">Form Date
                                                                </label>
                                                                <input type="text" class="form-control" id="fromdate"
                                                                    aria-describedby="fromdate">
                                                            </div>
                                                            <div class="col-md-4 mt-1">
                                                                <label for="todate" class="form-label">To Date
                                                                </label>
                                                                <input type="text" class="form-control" id="todate"
                                                                    aria-describedby="todate">
                                                            </div>

                                                            <div class="col-md-4">
                                                                <label for="Location" class="form-label">Location
                                                                </label>
                                                                <input type="text" class="form-control" id="Location"
                                                                    aria-describedby="Location">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label" for="typevisa">Visa Type</label>
                                                                <select class="form-select select2" id="typevisa"
                                                                    name="typevisa">
                                                                    <option value="">Tourist visa</option>
                                                                    <option value="1">Journalist visa</option>
                                                                    <option value="2">Medical visa</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label for="NOC" class="form-label">NOC
                                                                </label>
                                                                <input type="text" class="form-control" id="noc"
                                                                    aria-describedby="noc">
                                                            </div>
                                                            <div class="row">
                                                                <div class="d-flex justify-content-end ">
                                                                    <button class="btn btn-icon btn-Danger" id="remove"
                                                                        type="button" data-repeater-create>
                                                                        <i data-feather="plus" class="me-25"></i>
                                                                        <span>Remove</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div> --}}
                                                            <div id="work_experience"></div>
                                                            <div class="col-10">
                                                                <button class="btn btn-icon btn-primary"
                                                                    id="work_experience_add" type="button"
                                                                    data-repeater-create>
                                                                    <i data-feather="plus" class="me-25"></i>
                                                                    <span>Add New</span>
                                                                </button>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- work_experience_accodion_end --}}

                                        {{-- address_accodion_start --}}
                                        <div class="accordion mt-1" id="accordionEight">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingEight">
                                                    <button class="accordion-button Eight_title" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseEight"
                                                        aria-expanded="true" aria-controls="collapseEight">
                                                        Address
                                                    </button>
                                                </h2>
                                                <div id="collapseEight" class="accordion-collapse collapse "
                                                    aria-labelledby="headingEight" data-bs-parent="#accordionEight">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            {{-- <div class="col-md-12 row">
                                                            <div class="col-md-4">
                                                                <label class="form-label"
                                                                    for="list_country">Country</label>
                                                                <select class="form-select select2" id="list_country"
                                                                    name="list_country">
                                                                    <option value="">Country</option>
                                                                    <option value="1">A</option>
                                                                    <option value="2">B</option>
                                                                    <option value="3">C</option>
                                                                </select>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <label class="form-label" for="list_State">State</label>
                                                                <select class="form-select select2" id="list_State"
                                                                    name="list_State">
                                                                    <option value="">State</option>
                                                                    <option value="1">A</option>
                                                                    <option value="2">B</option>
                                                                    <option value="3">C</option>
                                                                </select>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <label class="form-label" for="list_City">City</label>
                                                                <select class="form-select select2" id="list_City"
                                                                    name="list_City">
                                                                    <option value="">City</option>
                                                                    <option value="1">A</option>
                                                                    <option value="2">B</option>
                                                                    <option value="3">C</option>
                                                                </select>
                                                            </div>


                                                            <div class="col-md-2">
                                                                <label for="fromdate" class="form-label">Form Date
                                                                </label>
                                                                <input type="text" class="form-control" id="fromdate"
                                                                    aria-describedby="fromdate">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label for="todate" class="form-label">To Date
                                                                </label>
                                                                <input type="text" class="form-control" id="todate"
                                                                    aria-describedby="todate">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label for="Address" class="form-label">Address</label>
                                                                <input type="text" class="form-control" id="Address"
                                                                    aria-describedby="Address">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label for="pin" class="form-label">Pin</label>
                                                                <input type="text" class="form-control" id="pin"
                                                                    aria-describedby="pin">
                                                            </div>
                                                            <div class="row">
                                                                <div class="d-flex justify-content-end ">
                                                                    <button class="btn btn-icon btn-Danger" id="remove"
                                                                        type="button" data-repeater-create>
                                                                        <i data-feather="plus" class="me-25"></i>
                                                                        <span>Remove</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div> --}}
                                                            <div id="Address_new"></div>
                                                            <div class="col-10">
                                                                <button class="btn btn-icon btn-primary" id="Address_add"
                                                                    type="button" data-repeater-create>
                                                                    <i data-feather="plus" class="me-25"></i>
                                                                    <span>Add New</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- address_accodion_end --}}

                                            {{-- Document_accodion_start --}}
                                            <div class="accordion mt-1" id="accordionNine">
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="headingNine">
                                                        <button class="accordion-button Nine_title" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapseNine"
                                                            aria-expanded="true" aria-controls="collapseNine">
                                                            Document
                                                        </button>
                                                    </h2>
                                                    <div id="collapseNine" class="accordion-collapse collapse "
                                                        aria-labelledby="headingNine" data-bs-parent="#accordionNine">
                                                        <div class="accordion-body">
                                                            Document
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- Document_accodion_end --}}

                                                {{-- Travel_History_accodion_start --}}
                                                <div class="accordion mt-1" id="accordionTen">
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingTen">
                                                            <button class="accordion-button Ten_title" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#collapseTen"
                                                                aria-expanded="true" aria-controls="collapseTen">
                                                                Travel History
                                                            </button>
                                                        </h2>
                                                        <div id="collapseTen" class="accordion-collapse collapse"
                                                            aria-labelledby="headingTen" data-bs-parent="#accordionTen">
                                                            <div class="accordion-body">
                                                                <div class="row">
                                                                    {{-- <div class="col-md-12 row">
                                                                    <div class="col-md-2">
                                                                        <label for="startdate" class="form-label">Start
                                                                            Date
                                                                        </label>
                                                                        <input type="text" class="form-control"
                                                                            id="startdate" aria-describedby="startdate">
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label for="enddate" class="form-label">End Date
                                                                        </label>
                                                                        <input type="text" class="form-control"
                                                                            id="enddate" aria-describedby="enddate">
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <label for="Days" class="form-label">Days
                                                                        </label>
                                                                        <input type="text" class="form-control"
                                                                            id="Days" aria-describedby="Days">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="form-label"
                                                                            for="Destination">Destination</label>
                                                                        <select class="form-select select2"
                                                                            id="Destination" name="Destination">
                                                                            <option value="">State</option>
                                                                            <option value="1">A</option>
                                                                            <option value="2">B</option>
                                                                            <option value="3">C</option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <label class="form-label" for="country">Visa
                                                                            Type</label>
                                                                        <select class="form-select select2" id="visatype"
                                                                            name="visatype">
                                                                            <option value="1">Business Visa</option>
                                                                            <option value="2">Project Visa</option>
                                                                            <option value="3">Tourist Visa</option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <label for="purpose" class="form-label">Purpose
                                                                            Of
                                                                            Travel
                                                                        </label>
                                                                        <input type="text" class="form-control"
                                                                            id="purpose" aria-describedby="purpose">
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="d-flex justify-content-end ">
                                                                            <button class="btn btn-icon btn-Danger"
                                                                                id="remove" type="button"
                                                                                data-repeater-create>
                                                                                <i data-feather="plus" class="me-25"></i>
                                                                                <span>Remove</span>
                                                                            </button>
                                                                        </div>
                                                                    </div>

                                                                </div> --}}

                                                                    <div id="Travel_new"></div>
                                                                    <div class="col-10">
                                                                        <button class="btn btn-icon btn-primary"
                                                                            id="Tavel_add" type="button"
                                                                            data-repeater-create>
                                                                            <i data-feather="plus" class="me-25"></i>
                                                                            <span>Add New</span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {{-- Travel_History_accodion_end --}}

                                                    {{-- Timeline_accodion_start --}}
                                                    <div class="accordion mt-1" id="accordionElevne">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingEleven">
                                                                <button class="accordion-button  Eleven_title"
                                                                    type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapseEleven" aria-expanded="true"
                                                                    aria-controls="collapseEleven">
                                                                    Time Line
                                                                </button>
                                                            </h2>
                                                            <div id="collapseEleven" class="accordion-collapse collapse"
                                                                aria-labelledby="headingEleven"
                                                                data-bs-parent="#accordionElevne">
                                                                <div class="accordion-body">
                                                                    Time Line
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- Timeline_accodion_end --}}

                                                        <div class="text-center mt-3 button_save">
                                                            <button type="button" class="btn savebtn">Save</button>
                                                            <button type="button" class="btn savebtn">Save and
                                                                New</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
    </form>
@endsection
<style>
    .accordion-button:not(.collapsed)::after {
        background-image: url('{{ asset('images/svg/download.svg') }}') !important;
    }

    .accordion-button::after {
        flex-shrink: 0;
        width: 1rem;
        height: 1rem;
        margin-left: auto;
        content: "";
        background-image: url('{{ asset('images/svg/download.svg') }}') !important;
        background-repeat: no-repeat;
        background-size: 1rem;
        transition: transform 0.2s ease-in-out;
    }
</style>
@section('vendor-script')
    <script>
        function getFile() {
            document.getElementById("upfile").click();
        }

        $(document).ready(function() {

            // Qualification Add More & Delete Start
            $("#add_qualification").click(function() {
                newRowAdd =
                    '<div class="col-md-12 row" id="qualification_row"><div class="col-md-3"><label class="form-label" for="level">Level</label><select class="form-select select2" id="level" name="level[]"> <option value="">0</option> <option value="1">1</option> <option value="2">2</option></select> </div> <div class="col-md-3"><label for="course" class="form-label">Course</label><input type="text" class="form-control" name="course[]" id="course" aria-describedby="course"></div><div class="col-md-3"><label for="specialization" class="form-label">Specialization</label><input type="text" class="form-control" id="specialization" name="specialization[]" aria-describedby="Specialization"></div> <div class="col-md-3"><label for="school_college" class="form-label">Schoole/Collage</label><input type="text" class="form-control" id="school_college"  name="school_college[]"  aria-describedby="Schoole/Collage"> </div> <div class="col-md-3"><label for="board_uni" class="form-label">Board/Uni</label><input type="text" class="form-control" id="board_uni"  name="board_uni[]"  aria-describedby="Board/Uni"> </div> <div class="col-md-3"><label for="year_of_passing" class="form-label">Year Of Passing</label><input type="text" class="form-control" id="year_of_passing" name="year_of_passing[]" aria-describedby="YearofPassing"> </div> <div class="col-md-3"><label for="percent_cgpa" class="form-label">%CGPS</label><input type="text" class="form-control" id="percent_cgpa" name="percent_cgpa[]" aria-describedby="cgpa"> </div> <div class="col-md-3"><label for="location" class="form-label">Location</label><input type="text" class="form-control" id="location" name="location[]" aria-describedby="Location"> </div><div class="col-md-3"><label for="start_date" class="form-label">Start Date</label><input type="date" class="form-control" id="start_date"  name="start_date[]"  aria-describedby="StartDate"> </div> <div class="col-md-3"><label for="end_date" class="form-label">End Date</label><input type="date" class="form-control" id="end_date" name="end_date"[] aria-describedby="EndDate"> </div>  <div class="col-md-3"><label class="form-label" for="visa_type">Visa Type</label><select class="form-select select2" id="visa_type" name="visa_type[]">    <option value="">Tourist visa</option>    <option value="1">Journalist visa</option>    <option value="2">Medical visa</option></select> </div> <div class="col-md-3"> <label for="comments" class="form-label">Comments </label> <input type="text" class="form-control" id="comments" name="comments[]" aria-describedby="Comments"> </div> <div class="row"> <div class="d-flex justify-content-end "> <button class="btn btn-icon btn-Danger" id="remove_qualification" type="button" data-repeater-create> <span>Remove</span> </button> </div> </div>';
                $('#new_qualification').append(newRowAdd);
            });

            $("body").on("click", "#remove_qualification", function() {
                $(this).parents("#qualification_row").remove();
            });
            // Qualification Add More & Delete End



            // Work Experiance Add More & Delete Start

            // <div class="col-md-4"> <label for="duration" class="form-label">Duration</label> <div class="row"> <div class="col-md-6"> <label for="Duration_year" class="form-label">Yrs </label> <input type="text" class="form-control" id="Duration_year" aria-describedby="Duration_year"> </div> <div class="col-md-6"> <label for="Duration_year" class="form-label">Mths </label> <input type="text" class="form-control" id="Duration" aria-describedby="Duration"> </div> </div> </div> <div class="col-md-4 mt-1">
            $("#work_experience_add").click(function() {
                work_experience_new =
                    '<div class="col-md-12 row" id="work_experience_row"> <div class="col-md-4"> <label for="company" class="form-label">Company </label> <input type="text" class="form-control" id="company" name="company[]" aria-describedby="company"> </div> <div class="col-md-4"> <label for="designation" class="form-label">Designation </label> <input type="text" class="form-control" id="designation" name="designation[]" aria-describedby="designation"> </div> <div class="col-md-4"> <label for="job_repository" class="form-label">Job Repository </label> <input type="text" class="form-control" id="job_repository" name="job_repository[]" aria-describedby="Job_Repository"> </div> <div class="col-md-4"> <label for="from_date" class="form-label">Form Date </label> <input type="date" class="form-control" id="from_date" name="from_date[]" aria-describedby="from_date"> </div> <div class="col-md-4"> <label for="to_date" class="form-label">To Date </label> <input type="date" class="form-control" id="to_date" name="to_date[]" aria-describedby="to_date"> </div> <div class="col-md-4"> <label for="location" class="form-label">Location </label> <input type="text" class="form-control" id="location" name="location[]" aria-describedby="location"> </div> <div class="col-md-4"> <label class="form-label" for="visa_type">Visa Type</label> <select class="form-select select2" id="visa_type" name="visa_type[]"> <option value="">Tourist visa</option> <option value="1">Journalist visa</option> <option value="2">Medical visa</option> </select> </div> <div class="col-md-4"> <label for="NOC" class="form-label">NOC </label> <input type="text" class="form-control" id="noc" aria-describedby="noc" name="noc"> </div> <div class="row"> <div class="d-flex justify-content-end "> <button class="btn btn-icon btn-Danger" id="remove_work_experience" type="button"> <span>Remove</span> </button> </div> </div> </div>';
                $('#work_experience').append(work_experience_new);
            });

            $("body").on("click", "#remove_work_experience", function() {
                $(this).parents("#work_experience_row").remove();
            });

            // Work Experiance Add More & Delete End


            // Address Add More & Delete Start
            $("#Address_add").click(function() {
                Address_new =
                    '<div class="col-md-12 row" id="address_row"><div class="col-md-4"><label class="form-label" for="list_country">Country</label> <select class="form-select select2" id="list_country" name="list_country[]"> <option value="">Country</option> <option value="1">A</option> <option value="2">B</option> <option value="3">C</option> </select> </div> <div class="col-md-4"> <label class="form-label" for="list_State">State</label> <select class="form-select select2" id="list_State" name="list_State[]"> <option value="">State</option> <option value="1">A</option> <option value="2">B</option> <option value="3">C</option> </select> </div> <div class="col-md-4"> <label class="form-label" for="list_City">City</label> <select class="form-select select2" id="list_City" name="list_City[]"> <option value="">City</option> <option value="1">A</option> <option value="2">B</option> <option value="3">C</option> </select> </div> <div class="col-md-2"> <label for="fromdate" class="form-label">Form Date </label> <input type="date" class="form-control" id="fromdate" name="formdate[]" aria-describedby="fromdate"> </div> <div class="col-md-2"> <label for="todate" class="form-label">To Date </label> <input type="date" class="form-control" id="todate" aria-describedby="todate"> </div> <div class="col-md-4"> <label for="Address" class="form-label">Address</label> <input type="text" class="form-control" id="Address" aria-describedby="Address"> </div> <div class="col-md-4"> <label for="pin" class="form-label">Pin</label> <input type="text" class="form-control" id="pin" aria-describedby="pin"> </div> <div class="row"> <div class="d-flex justify-content-end "> <button class="btn btn-icon btn-Danger" id="remove_address" type="button"> <span>Remove</span> </button> </div></div></div><div id="Address"></div>';
                $('#Address_new').append(Address_new);
            });

            $("body").on("click", "#remove_address", function() {
                $(this).parents("#address_row").remove();
            });
            //  Address Add More & Delete End



            // Travel Add More & Delete Start

            $("#Tavel_add").click(function() {
                travel_new =
                    '<div class="col-md-12 row" id="travel_row"> <div class="col-md-2"> <label for="startdate" class="form-label">Start Date </label> <input type="date" class="form-control" id="startdate" aria-describedby="startdate"> </div> <div class="col-md-2"> <label for="enddate" class="form-label">End Date </label> <input type="date" name="enddate[]" class="form-control" id="enddate" aria-describedby="enddate"> </div> <div class="col-md-2"> <label for="Days" class="form-label">Days </label> <input type="text" class="form-control" id="Days" aria-describedby="Days"> </div> <div class="col-md-6"> <label class="form-label" for="Destination">Destination</label> <select class="form-select select2" id="Destination" name="Destination[]"> <option value="">State</option> <option value="1">A</option> <option value="2">B</option> <option value="3">C</option> </select> </div> <div class="col-md-6"> <label class="form-label" for="country">Visa Type</label> <select class="form-select select2" id="visatype" name="visatype[]"> <option value="1">Business Visa</option> <option value="2">Project Visa</option> <option value="3">Tourist Visa</option> </select> </div> <div class="col-md-6"> <label for="purpose" class="form-label">Purpose Of Travel </label> <input type="text" class="form-control" id="purpose" aria-describedby="purpose"> </div> <div class="row"> <div class="d-flex justify-content-end "> <button class="btn btn-icon btn-Danger" id="remove_travel" type="button"><span>Remove</span></button></div></div></div>';
                $('#Travel_new').append(travel_new);
            });

            $(document).on("click", "#remove_travel", function() {
                $(this).parents("#travel_row").remove();
            });

            // Travel Add More & Delete End

        });

        $(document).on("click", ".remove_dynamic_data", function() {
            $ids = $(this).attr("id");
            $("#dynamic" + $ids + "").remove();
        });

        function sub(obj) {
            var file = obj.value;
            var fileName = file.split("\\");
            document.getElementById("yourBtn").innerHTML = fileName[fileName.length - 1];
            document.myForm.submit();
            event.preventDefault();
        }
    </script>
    {{-- @yield('links') --}}
    {{-- @include('content/apps/user/script_links') --}}
@endsection
@section('page-script')
    {{-- @yield('links') --}}
    {{-- @include('content/apps/user/script_links') --}}
@endsection
