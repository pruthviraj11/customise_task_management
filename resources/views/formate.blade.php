<div class="col-md-12 row" id="qualification_row">
    <div class="col-md-3">
        <label class="form-label" for="level">Level</label>
        <select class="form-select select2" id="level[]" name="qualifications_level[]">
            <option value="">0</option>
            <option value="1">1</option>
            <option value="2">2</option>
        </select></div>
    <div class="col-md-3">
        <label for="course" class="form-label">Course</label>
        <input type="text" class="form-control" name="qualifications_course[]" id="course[]" aria-describedby="course">
    </div>
    <div class="col-md-3">
        <label for="specialization" class="form-label">Specialization</label>
        <input type="text" class="form-control" id="specialization[]" name="qualifications_specialization[]"
               aria-describedby="specialization">
    </div>
    <div class="col-md-3">
        <label for="school_college" class="form-label">Schoole/Collage</label>
        <input type="text" class="form-control" id="school_college[]" name="qualifications_school_college[]"
               aria-describedby="school_college">
    </div>
    <div class="col-md-3">
        <label for="board_university" class="form-label">Board/Uni</label>
        <input type="text" class="form-control" id="board_university[]" name="qualifications_board_university[]"
               aria-describedby="Board/Uni">
    </div>
    <div class="col-md-3">
        <label for="year_of_passing" class="form-label">Year Of Passing</label>
        <input type="text" class="form-control" id="year_of_passing[]" name="qualifications_year_of_passing[]"
               aria-describedby="YearofPassing">
    </div>
    <div class="col-md-3">
        <label for="percent_cgpa" class="form-label">%CGPS</label>
        <input type="text" class="form-control" id="percent_cgpa[]" name="qualifications_percent_cgpa[]"
               aria-describedby="cgpa">
    </div>
    <div class="col-md-3">
        <label for="location" class="form-label">Location</label>
        <input type="text" class="form-control" id="location[]" name="qualifications_location[]"
               aria-describedby="location">
    </div>
    <div class="col-md-3">
        <label for="start_date" class="form-label">Start Date</label>
        <input type="date" class="form-control" id="start_date[]" name="qualifications_start_date[]"
               aria-describedby="StartDate">
    </div>
    <div class="col-md-3">
        <label for="end_date" class="form-label">End Date</label>
        <input type="date" class="form-control" id="end_date[]" name="qualifications_end_date[]"
               aria-describedby="end_date">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="visa_type">Visa Type</label>
        <select class="form-select select2" id="visa_type[]" name="qualifications_visa_type[]">
            <option value="">Tourist visa</option>
            <option value="1">Journalist visa</option>
            <option value="2">Medical visa</option>
        </select></div>
    <div class="col-md-3">
        <label for="comments" class="form-label">Comments </label>
        <input type="text" class="form-control" id="comment[]" name="qualifications_comment[]"
               aria-describedby="comment">
    </div>
    <div class="row">
        <div class="d-flex justify-content-end ">
            <button class="btn btn-icon btn-Danger" id="remove_qualification" type="button" data-repeater-create>
                <span>Remove</span>
            </button>
        </div>
    </div>
