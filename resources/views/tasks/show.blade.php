@extends('layouts/contentLayoutMaster')

@section('title', 'Task List')

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">

@endsection

@section('content')
    <div class="container">
        <h3>Tasks for User ID: {{ $user_id }} (Type: {{ $type }})</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Task ID</th>
                    <th>Task Name</th>
                    <th>Due Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tasks as $task)
                    <tr>
                        <td>{{ $task->task_id }}</td>
                        <td>{{ $task->task->title ?? 'N/A' }}</td>
                        <td>{{ $task->task->due_date ?? 'N/A' }}</td>
                        <td>{{ $task->task_status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
