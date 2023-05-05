@extends('layouts.main')

@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h4>
      <strong>{{ ($mode == 'add' ? 'Add' : 'Edit') }} client service</strong>
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
            <h5 class="box-title">Enter  Client Service's details here</h5>
          	<div class="clearfix"></div><br />
          </div>
          <!-- /.box-header -->
          <div class="box-body">
          <!-- form start -->
            @if($mode == 'add')
               <form method="POST" action="{{ url('admin/client/services/add') }}" enctype="multipart/form-data">
            @else
               <form method="POST" action="{{ url('admin/client/services/update') }}" enctype="multipart/form-data">
            @endif
            @csrf

            <input type="hidden" name="client_service_id" value="{{ ($mode == 'add' ? '' : $clientService->id) }}">

            <div class="form-group row">
              <label for="client" class="col-md-2 col-form-label"><strong>{{ __('Client') }}</strong><span class="required clr-red">*</span></label>
              <div class="col-md-6">
                <select class="form-control" name="client" id="client" required>
                  @foreach($clients as $key => $client)
                    <option value="{{ $client->id }}" {{ ($mode == 'add' ? '' : (in_array($client->id, $clientService->client_id) == 1 ? 'selected' : '')) }}>{{ $client->name }}</option>
                  @endforeach
                </select>
                @if ($errors->has('client'))
                  <span class="invalid-feedback required-error" role="alert">
                    <strong>{{ $errors->first('client') }}</strong>
                  </span>
                @endif
              </div>
            </div>

            <div class="form-group row">
              <label for="service" class="col-md-2 col-form-label"><strong>{{ __('Service') }}</strong><span class="required clr-red">*</span></label>
              <div class="col-md-6">
                <select class="form-control" name="service[]" id="service" multiple required>
                  @foreach($services as $key => $service)
                    <option value="{{ $service->id }}" {{ ($mode == 'add' ? '' : (in_array($service->id, $clientServiceIds) == 1 ? 'selected' : '')) }}>{{ $service->name }}</option>
                  @endforeach
                </select>
                @if ($errors->has('service'))
                  <span class="invalid-feedback required-error" role="alert">
                    <strong>{{ $errors->first('service') }}</strong>
                  </span>
                @endif
              </div>
            </div>

            <div class="form-group row">
              <label for="service_date" class="col-md-2 col-form-label"><strong>{{ __('Service Date') }}</strong><span class="required clr-red">*</span></strong></label>
              <div class="col-md-6">
                 <input type="text" name="service_date" class="form-control datepicker" id="service_date" placeholder="Enter Service Date" value="{{ ($mode == 'edit' ? $clientService->service_date : '') }}" autocomplete="off">
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

  $("#client").select2({
    placeholder: "Select client",
  });

  $("#service").select2({
    placeholder: "Select service",
  });

  $('.datepicker').datepicker({
    format: 'yyyy-mm-dd'
  });
</script>
@endsection