@extends('layouts.main')

@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h4>
      <strong>{{ ($mode == 'add' ? 'Add' : 'Edit') }} client</strong>
    </h4>
  </section>
  <div class="clearfix"></div><br />
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <!-- Horizontal Form -->
        <div class="box box-warning">
          <div class="box-header with-border">
            <h5 class="box-title">Enter  Client's details here</h5>
          	<div class="clearfix"></div><br />
          </div>
          <!-- /.box-header -->
          <div class="box-body">
          <!-- form start -->
            @if($mode == 'add')
               <form method="POST" action="{{ url('admin/clients/add') }}" enctype="multipart/form-data">
            @else
               <form method="POST" action="{{ url('admin/clients/update') }}" enctype="multipart/form-data">
            @endif
            @csrf

            <input type="hidden" name="client_id" value="{{ ($mode == 'add' ? '' : $client->id) }}">

            <div class="form-group row">
              <label for="client_name" class="col-md-2 col-form-label"><strong>{{ __('Client Name') }}</strong><span class="required clr-red">*</span></label>
              <div class="col-md-6">
                <input type="text" name="client_name" class="form-control" id="client_name" placeholder="Enter Client Name" value="{{ ($mode == 'edit' ? $client->name : '') }}" autocomplete="off" required>
                @if ($errors->has('client_name'))
                    <span class="invalid-feedback required-error" role="alert">
                        <strong>{{ $errors->first('client_name') }}</strong>
                    </span>
                @endif
              </div>
            </div>

            <div class="form-group row">
              <label for="phone_number" class="col-md-2 col-form-label"><strong>{{ __('Phone Number') }}</strong><span class="required clr-red">*</span></label>
              <div class="col-md-6">
                <input type="phone_number" name="phone_number" class="form-control" id="phone_number" placeholder="Enter Phone Number" value="{{ ($mode == 'edit' ? $client->phone_number : '') }}" autocomplete="off">
              </div>
            </div>

            <div class="form-group row">
              <label for="gender" class="col-md-2 col-form-label"><strong>{{ __('Gender') }}</strong><span class="required clr-red">*</span></label>
              <div class="col-md-6">
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="female" name="gender" class="custom-control-input" value="FEMALE" autocomplete="off" {{ ($mode == 'add' ? 'checked' : ($client->gender == 'FEMALE' ? 'checked' : ''))}}>
                  <label class="custom-control-label lh-2" for="female">Female</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline mt-1">
                  <input type="radio" id="male" name="gender" class="custom-control-input" value="MALE" autocomplete="off" {{ ($mode == 'add' ? '' : ($client->gender == 'MALE' ? 'checked' : ''))}}>
                  <label class="custom-control-label lh-2" for="male">Male</label>
                </div>
                @if ($errors->has('gender'))
                    <span class="invalid-feedback required-error" role="alert">
                        <strong>{{ $errors->first('gender') }}</strong>
                    </span>
                @endif
              </div>
            </div>

            <div class="form-group row">
              <label for="email" class="col-md-2 col-form-label"><strong>{{ __('Email') }}</strong></label>
              <div class="col-md-6">
                <input type="email" name="email" class="form-control" id="email" placeholder="Enter Email" autocomplete="off" value="{{ ($mode == 'edit' ? $client->email : '') }}">
              </div>
            </div>

            <div class="form-group row">
              <label for="address" class="col-md-2 col-form-label"><strong>{{ __('Address') }}</strong></label>
              <div class="col-md-6">
                <textarea name="address" class="form-control" id="address" placeholder="Enter Address">{{ ($mode == 'edit' ? $client->address : '') }}</textarea>
              </div>
            </div>

            <div class="form-group row">
              <label for="birthdate" class="col-md-2 col-form-label"><strong>{{ __('Birthdate') }}</strong></label>
              <div class="col-md-6">
                 <input type="text" name="birthdate" class="form-control datepicker" id="birthdate" placeholder="Enter Birthdate" value="{{ ($mode == 'edit' ? $client->dob : '') }}" autocomplete="off">
              </div>
            </div>

            <div class="form-group row">
              <label for="anniversary" class="col-md-2 col-form-label"><strong>{{ __('Anniversary') }}</strong></label>
              <div class="col-md-6">
                 <input type="text" name="anniversary" class="form-control datepicker" id="anniversary" placeholder="Enter Anniversary Date" value="{{ ($mode == 'edit' ? $client->anniversary : '') }}" autocomplete="off">
              </div>
            </div>

            <div class="form-group row">
              <label for="package" class="col-md-2 col-form-label"><strong>{{ __('Package') }}</strong><span class="required clr-red">*</span></label>
              <div class="col-md-6">
                <select class="form-control" name="package[]" id="package" multiple required>
                  @foreach($packages as $key => $package)
                    <option value="{{ $package->id }}" {{ ($mode == 'add' ? '' : (in_array($package->id, $clientPackage) == 1 ? 'selected' : '')) }}>{{ $package->name }}</option>
                  @endforeach
                </select>
                @if ($errors->has('package'))
                  <span class="invalid-feedback required-error" role="alert">
                    <strong>{{ $errors->first('package') }}</strong>
                  </span>
                @endif
              </div>
            </div>

            <div class="form-group row mb-0">
              <div class="col-md-7 offset-md-7">
                <button type="submit" class="btn btn-primary">
                  {{ ($mode == 'add' ? 'Add' : 'Edit') }}
                </button>
              </div>
            </div>
          </form>
      </div>
      <!-- /.box -->
    </div>
    <!--/.col (right) -->
  </div>
  <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
@endsection
@section('scripts')
<script type="text/javascript">
  $("#package").select2({
    placeholder: "Select package",
  });

  $('.datepicker').datepicker({
    format: 'yyyy-mm-dd'
  });
</script>
@endsection