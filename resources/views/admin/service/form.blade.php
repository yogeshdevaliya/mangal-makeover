@extends('layouts.main')

@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h4>
      <strong>{{ ($mode == 'add' ? 'Add' : 'Edit') }} Service</strong>
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
            <h5 class="box-title">Enter  Service's details here</h5>
          	<div class="clearfix"></div><br />
          </div>
          <!-- /.box-header -->
          <div class="box-body">
          <!-- form start -->
            @if($mode == 'add')
               <form method="POST" action="{{ url('admin/services/add') }}" enctype="multipart/form-data">
            @else
               <form method="POST" action="{{ url('admin/services/update') }}" enctype="multipart/form-data">
            @endif
            @csrf

            <input type="hidden" name="service_id" value="{{ ($mode == 'add' ? '' : $service->id) }}">

            <div class="form-group row">
              <label for="service_name" class="col-md-2 col-form-label"><strong>{{ __('Service Name') }}</strong><span class="required clr-red">*</span></label>
              <div class="col-md-6">
                <input type="text" name="service_name" class="form-input form-control" id="service_name" placeholder="Enter Service Name" value="{{ ($mode == 'edit' ? $service->name : '') }}" autocomplete="off" required>
                @if ($errors->has('service_name'))
                    <span class="invalid-feedback required-error" role="alert">
                        <strong>{{ $errors->first('service_name') }}</strong>
                    </span>
                @endif
              </div>
            </div>

            <div class="form-group row">
              <label for="service_category" class="col-md-2 col-form-label"><strong>{{ __('Service Category') }}</strong><span class="required clr-red">*</span></label>
              <div class="col-md-6">
              	<select class="form-control form-input" name="service_category" id="service_category" autocomplete="off" required>
                	@foreach($serviceCategories as $key => $serviceCategory)
                	  <option value="{{ $serviceCategory->id }}" {{ ($mode == 'add' ? '' : ($service->service_category_id == $serviceCategory->id ? 'selected' : '')) }}>{{ $serviceCategory->name }}</option>
                	@endforeach
                </select>
                @if ($errors->has('service_category'))
                  <span class="invalid-feedback required-error" role="alert">
                    <strong>{{ $errors->first('service_category') }}</strong>
                  </span>
                @endif
              </div>
            </div>

            <div class="form-group row">
              <label for="duration" class="col-md-2 col-form-label"><strong>{{ __('Duration') }}</strong><span class="required clr-red">*</span></label>
              <div class="col-md-6">
                <input type="number" name="duration" class="form-control form-input" id="duration" placeholder="Enter Duration" value="{{ ($mode == 'edit' ? $service->duration : '') }}" step="any" autocomplete="off" required>
                @if ($errors->has('duration'))
                    <span class="invalid-feedback required-error" role="alert">
                        <strong>{{ $errors->first('duration') }}</strong>
                    </span>
                @endif
              </div>
            </div>

            <div class="form-group row">
              <label for="price" class="col-md-2 col-form-label"><strong>{{ __('Price') }}</strong><span class="required clr-red">*</span></label>
              <div class="col-md-6">
                <input type="number" name="price" class="form-control form-input" id="price" placeholder="Enter Price" value="{{ ($mode == 'edit' ? $service->price : '') }}" step="any" autocomplete="off" required>
                @if ($errors->has('price'))
                    <span class="invalid-feedback required-error" role="alert">
                        <strong>{{ $errors->first('price') }}</strong>
                    </span>
                @endif
              </div>
            </div>

            <div class="form-group row">
              <label for="description" class="col-md-2 col-form-label"><strong>{{ __('Description') }}</strong></label>
              <div class="col-md-6">
                <textarea name="description" class="form-control form-input" id="description" placeholder="Enter Description" autocomplete="off">{{ ($mode == 'edit' ? $service->description : '') }}</textarea>
              </div>
            </div>

            <div class="form-group row mb-0">
                <div class="col-md-8 text-right">
                    <button type="submit" class="form-btn">
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
  $("#service_category").select2({
    placeholder: "Select service category",
  });
</script>
@endsection