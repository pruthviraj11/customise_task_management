@extends('layouts/contentLayoutMaster')

@section('title', 'User add')

@section('vendor-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
@endsection

@section('page-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
@endsection

@section('content')

<form action="#" method="POST">
  @csrf
  <section id="multiple-column-form">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Edit User Details</h4>
        </div>
        <div class="card-body">
          <form class="form">
            <div class="row">
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="first-name-column">Username</label>
                  <input
                    type="text"
                    id="first-name-column"
                    class="form-control"
                    placeholder="Username"
                    name="name"
                    value="{{ old('name') ? old('name') : $user->name }}"
                  />
                  <span class="text-danger">
                      @error('name')
                        {{ $message }}
                      @enderror
                  </span>
                </div>
              </div>
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="last-name-column">First Name</label>
                  <input
                    type="text"
                    id="last-name-column"
                    class="form-control"
                    placeholder="First Name"
                    name="fullname"
                    value="{{ old('fullname') ? old('fullname') : $user->fullname }}"
                  />
                  <span class="text-danger">
                      @error('fullname')
                        {{ $message }}
                      @enderror
                  </span>
                </div>
              </div>
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="email-id-column">Email</label>
                  <input
                    type="email"
                    id="email-id-column"
                    class="form-control"
                    name="email"
                    placeholder="Email"
                    value="{{ old('email') ? old('email') : $user->email }}"
                  />
                  <span class="text-danger">
                      @error('email')
                        {{ $message }}
                      @enderror
                  </span>
                </div>
              </div>
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="company-column">Contact</label>
                  <input
                    type="text"
                    id="company-column"
                    class="form-control"
                    name="contact"
                    placeholder="Contact"
                    value="{{ old('contact') ? old('contact') : $user->contact }}"
                  />
                  <span class="text-danger">
                      @error('contact')
                        {{ $message }}
                      @enderror
                  </span>
                </div>
              </div>
              <div class="col-md-6 col-12">
                <div class="mb-1">
                  <label class="form-label" for="country-floating">Password</label>
                  <input
                    type="password"
                    id="country-floating"
                    class="form-control"
                    name="password"
                    placeholder="password"
                    value="{{ old('password') ? old('password') : $user->password }}"
                  />
                  <span class="text-danger">
                      @error('password')
                        {{ $message }}
                      @enderror
                  </span>
                </div>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-primary me-1">Update</button>
                <button type="reset" class="btn btn-outline-secondary">Reset</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
</form>
@endsection

@section('vendor-script')
  {{-- Vendor js files --}}
  {{-- @yield('links') --}}
  {{-- @include('content/apps/user/script_links') --}}
@endsection

@section('page-script')
  {{-- Page js files --}}
  {{-- @include('content/apps/user/script_links') --}}
  {{-- @yield('links') --}}
@endsection

