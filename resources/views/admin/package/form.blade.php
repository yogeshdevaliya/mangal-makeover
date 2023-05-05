@extends('layouts.main')

@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h4>
      <strong>{{ ($mode == 'add' ? 'Add' : 'Edit') }} package</strong>
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
            <h5 class="box-title">Enter  package's details here</h5>
          	<div class="clearfix"></div><br />
          </div>
          <!-- /.box-header -->
          <div class="box-body">
          <!-- form start -->
            @if($mode == 'add')
               <form method="POST" action="{{ url('admin/package/add') }}" enctype="multipart/form-data">
            @else
               <form method="POST" action="{{ url('admin/package/update') }}" enctype="multipart/form-data">
            @endif
            @csrf

            <input type="hidden" name="package_id" value="{{ ($mode == 'add' ? '' : $package->id) }}">

            <div class="form-group row">
              <label for="package_name" class="col-md-2 col-form-label"><strong>{{ __('Package Name') }}</strong><span class="required clr-red">*</span></label>
              <div class="col-md-6">
                <input type="text" name="package_name" class="form-control" id="package_name" placeholder="Enter Package Name" value="{{ ($mode == 'edit' ? $package->name : '') }}" required>
                @if ($errors->has('package_name'))
                    <span class="invalid-feedback required-error" role="alert">
                        <strong>{{ $errors->first('package_name') }}</strong>
                    </span>
                @endif
              </div>
            </div>

            <div class="form-group row">
              <label for="service" class="col-md-2 col-form-label"><strong>{{ __('Service') }}</strong><span class="required clr-red">*</span></label>
              <div class="col-md-6">
              	<select class="form-control" name="service" id="service" required>
                	 <input type="text" name="service" class="form-control" id="service" placeholder="Enter Package Name" value="{{ ($mode == 'edit' ? $package->service : '') }}">
                </select>
                @if ($errors->has('service'))
                  <span class="invalid-feedback required-error" role="alert">
                    <strong>{{ $errors->first('service') }}</strong>
                  </span>
                @endif
              </div>
            </div>

            <div class="form-group row">
              <label for="price" class="col-md-2 col-form-label"><strong>{{ __('Price') }}</strong><span class="required clr-red">*</span></label>
              <div class="col-md-6">
                <input type="number" name="price" class="form-control" id="price" placeholder="Enter Price" value="{{ ($mode == 'edit' ? $package->price : '') }}" step="any" required>
                @if ($errors->has('price'))
                    <span class="invalid-feedback required-error" role="alert">
                        <strong>{{ $errors->first('price') }}</strong>
                    </span>
                @endif
              </div>
            </div>

            <div class="form-group row">
              <label for="expire_date" class="col-md-2 col-form-label"><strong>{{ __('Expire Date') }}</strong></label>
              <div class="col-md-6">
                 <input type="text" name="expire_date" class="form-control datepicker" id="expire_date" placeholder="Enter Expire Date" value="{{ ($mode == 'edit' ? ($package->expire_date == ''  ? '' : \Carbon\Carbon::parse($package->expire_date)->format('Y-m-d')) : '') }}" autocomplete="off">
              </div>
            </div>

            <div class="form-group row">
              <label for="description" class="col-md-2 col-form-label"><strong>{{ __('Description') }}</strong></label>
              <div class="col-md-6">
                <textarea name="description" class="form-control" id="description" placeholder="Enter Description">{{ ($mode == 'edit' ? $package->description : '') }}</textarea>
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
  $("#service").select2({
    placeholder: "Select service",
  });
  $('.datepicker').datepicker({
    format: 'yyyy-mm-dd'
  });
</script>
@endsection