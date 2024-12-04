@extends('layouts/contentLayoutMaster')

@section('title', $page_data['page_title'])

@section('vendor-style')
    {{-- Page Css files --}}
@endsection

@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-tree.css')) }}">
@endsection

@section('content')

    @if($page_data['page_title'] == "Role Add")
        <form action="{{ route('app-roles-store') }}" method="POST">
            @csrf
            @else
                <form action="{{ route('app-roles-update',encrypt($role->id)) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @endif

                    <section id="multiple-column-form">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>{{$page_data['form_title']}}</h4>
                                        <a href="{{route('app-roles-list')}}"
                                           class="col-md-2 btn btn-primary float-end">Role List</a>

                                        {{--<h4 class="card-title">{{$page_data['form_title']}}</h4>--}}
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 mb-1">
                                                <label class="form-label" for="first-name-column">Name</label>
                                                <input
                                                    type="text"
                                                    id="name-column"
                                                    class="form-control"
                                                    placeholder="Name"
                                                    name="name"
                                                    value="{{ old('name') ?? ($role?->name ?? '') }}"
                                                >
                                                <span class="text-danger">
                                          @error('name') {{ $message }} @enderror
                                        </span>
                                            </div>

                                            <div class="col-md-6 col-sm-12 mb-1">
                                                <label class="form-label" for="last-name-column">Display
                                                    Name</label>
                                                <input
                                                    type="text"
                                                    id="display-name-column"
                                                    class="form-control"
                                                    placeholder="Display Name"
                                                    name="display_name"
                                                    value="{{ old('display_name') ?? ($role?->display_name ?? '') }}"
                                                >
                                                <span class="text-danger">
                                            @error('display_name') {{ $message }}  @enderror
                                        </span>
                                            </div>

                                            <div class="col-xs-12 col-sm-12 col-md-12">
                                                <div class="form-group">
                                                    <h4>Permission:</h4>
                                                    <div class="row mt-2">
                                                        @foreach ($groupedPermissionsWithAllData as $permissionKey => $permissions)
                                                            <div class="col-md-3 col-sm-12 mb-1">
                                                                <h5>{{$permissionKey}}</h5>
                                                            </div>
                                                            <div class="col-md-9 col-sm-12">
                                                                <div class="row">
                                                                    @foreach($permissions as $value)
                                                                        <div class="col-md-2 mb-2">
                                                                            <label>{{ Form::checkbox('permissions[]', $value->id, !empty($rolePermissions) && in_array($value->id, $rolePermissions) ? true : false, array('class' => 'name')) }}
                                                                                {{ \Illuminate\Support\Str::ucfirst(last(str_replace('_', ' ', explode('-', $value->name)))) }}</label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                            <hr>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary me-1">Submit</button>
                                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </form>
                @endsection
            @section('vendor-script')
                {{-- Vendor js files --}}
@endsection

@section('page-script')
    {{-- Page js files --}}

@endsection
