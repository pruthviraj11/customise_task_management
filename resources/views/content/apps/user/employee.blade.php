<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Yajra DataTable</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap4.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap4.min.js"></script>
</head>
<body>

    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-5">
                    <form action="{{ route('employee.index') }}" method="POST">
                        @csrf
                <div class="form-group">
                    <label for="Salarystart">Start Of Salary</label>
                    <input type="text" class="form-control" id="Salarystart" aria-describedby="emailHelp" placeholder="Start Of Salary" name="start_salary" value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <label for="Salaryend">End Of Salary</label>
                    <input type="text" class="form-control" id="Salaryend" placeholder="End Of Salary" name="end_salary" value="{{ old('end_salary') }}">
                </div>
                <button type="submit" class="btn btn-primary" id="filter">Filter</button>
                </form> 
            </div>
        </div>
    </div>
    <section style="padding-top:60px;">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered data-table">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>salary</th>
                            <th>department</th>
                            <th>action</th>
                        </tr>
                        
                    </thead>       
                    <tbody>
                            <tr>
                            </tr>
                        </tbody>
                </table>
                  
                    </div>
                </div>
            </div>
    </section>
    
    {!! $dataTable->scripts() !!}
    <div class="container">
    
</div>   
<script type="text/javascript">
   $(document).ready(function() {
        $('.table').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: "{{ route('employee.index') }}",
                type: "GET",
                data: function (d) {
                        d.start_salary = $('#start_salary').val();
                        d.end_salary = $('#end_salary').val();
                    }
            },
            columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'salary', name: 'salary'},
            {data: 'department', name: 'department'},
            {data:'action',name:'action',orderable: false, searchable: false}]
        });
        
        $('#filter').on('click', function () {
                table.ajax.reload();
            });
    });
   

</script>
</body>
</html>