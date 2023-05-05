@extends('layouts.main')
@section('content')
  <div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h4>
      <strong>Client Services</strong>
    </h4>
   <div class="clearfix"></div><br/>
    @if (session('res'))
     <div class="alert alert-{{ session('res')['status'] }}" data-dismiss="alert">
       <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
       <strong>{{ session('res')['message'] }}</strong>
     </div>
    @endif
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <!-- Horizontal Form -->
      <div class="box box-warning">
        <div class="box-header with-border">
          <div class="float-right"><button type="button" class="form-btn" client-service-add><i class="fas fa-pencil-alt"></i>&nbsp;Add Client Service</button></div>
          <div class="clearfix"></div><br/>
        </div>
        <!-- /.box-header -->
        <div class="row"  style="display: none;" client-service-form-row>
          <div class="col-md-12">
             <!-- form start -->
              @if($mode == 'add')
                 <form method="POST" action="{{ url('admin/client/services/add') }}" enctype="multipart/form-data">
              @else
                 <form method="POST" action="{{ url('admin/client/services/update') }}" enctype="multipart/form-data">
              @endif
              @csrf
              <input type="hidden" name="client_service_id" value="{{ ($mode == 'add' ? '' : $clientService->id) }}">

              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="client" class="col-form-label"><strong>{{ __('Client') }}</strong><span class="required clr-red">*</span></label>
                    <select class="form-input form-control" name="client" id="client" autocomplete="off" required>
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
                <div class="col-md-5">
                  <div class="form-group">
                    <label for="service" class="col-form-label"><strong>{{ __('Service') }}</strong><span class="required clr-red">*</span></label>
                    <select class="form-input form-control" name="service[]" id="service" autocomplete="off" multiple required>
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
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="service_date" class="col-form-label"><strong>{{ __('Service Date') }}</strong><span class="required clr-red">*</span></label>
                    <input type="text" name="service_date" class="form-input form-control datepicker" id="service_date" placeholder="Enter Service Date" value="{{ ($mode == 'edit' ? $clientService->service_date : '') }}" autocomplete="off" required>
                  </div>
                </div>
                <div class="col-md-1">
                  <div class="clearfix"></div><br />
                  <button type="submit" class="form-btn">
                    {{ ($mode == 'add' ? 'Add' : 'Edit') }}
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div class="clearfix"></div><br />
        <!-- Table start -->
        <div class="table-responsive">
          <table class="table table-hover table-bordered" id="tableId">
            <thead>
             <tr>
              <th>Sr. No</th>
              <th>Name</th>
              <th>Service Name</th>
              <th>Description</th>
              <th>Grand Total</th>
              <th>Service Date</th>
              <th>Action</th>
            </tr>
          </thead>
           <tbody>
            @if(count($clientServices) > 0)
              @foreach($clientServices as $key => $clientService)
                <tr>
                  <td>{{ ++$key }}</td>
                  <td>{{ $clientService->client->name }}</td>
                  <td>{{ ($clientService->service == ''  ? 'NA' : $clientService->service) }}</td>
                  <td>{{ ($clientService->description == ''  ? 'NA' : $clientService->description) }}</td>
                  <td>{{ ($clientService->grand_total == ''  ? 'NA' : $clientService->grand_total) }}</td>
                   <td>{{ ($clientService->service_date == ''  ? 'NA' : \Carbon\Carbon::parse($clientService->service_date)->format('d M, Y')) }}</td>
                  <td class="display-flex">
                   {{-- client service edit form start--}}
                    {{-- <a href="{{ url('admin/clients/'.$client->id.'/edit') }}" class="btn btn-primary btn-sm"><i class="fas fa-pencil-alt"></i>&nbsp;Edit</a> --}}
                    {{-- client edit form start--}}
                   {{--  &nbsp;
                    |
                    &nbsp; --}}
                    {{-- client service delete form start--}}
                    <form class="d-b" action="{{ url('admin/client/service/delete')}}" method="POST">
                      @csrf
                      <input type="hidden" name="client_service_id" value="{{ $clientService->id }}">
                      <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this client service?'); "><i class="fas fa-trash"></i>&nbsp;Delete</button>
                    </form>
                    {{-- client service delete form start--}}
                  </td>
                </tr>
              @endforeach
            @endif
           </tbody>
         </table>
       </div>
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
  $(function () {
    $("table").DataTable();

      $("#client").select2({
      placeholder: "Select client",
    });

    $("#service").select2({
      placeholder: "Select service",
    });

    $('.datepicker').datepicker({
      format: 'yyyy-mm-dd',
      autoclose:true
    });
  });

  @if($mode == 'edit')
    $('[client-service-form-row]').slideToggle();
  @endif

  $('[client-service-add]').click(function (){
    $('[client-service-form-row]').slideToggle();
  });
</script>
@endsection