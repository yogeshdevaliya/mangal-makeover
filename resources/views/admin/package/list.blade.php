@extends('layouts.main')
@section('content')
  <div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h4>
      <strong>Package</strong>
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
          <div class="float-right"><button type="button" class="form-btn" package-add><i class="fas fa-pencil-alt"></i>&nbsp;Add Package</button></div>
          <div class="clearfix"></div><br/>
        </div>
        <!-- /.box-header -->
        <div class="row" style="display: none;" package-form-row>
          <div class="col-md-12">
            @if($mode == 'add')
               <form method="POST" action="{{ url('admin/package/add') }}" enctype="multipart/form-data">
            @else
               <form method="POST" action="{{ url('admin/package/update') }}" enctype="multipart/form-data">
            @endif
            @csrf

            <input type="hidden" name="package_id" value="{{ ($mode == 'add' ? '' : $package->id) }}">

            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label for="package_name" class="col-form-label"><strong>{{ __('Package Name') }}</strong><span class="required clr-red">*</span></label>
                  <input type="text" name="package_name" class="form-input form-control" id="package_name" placeholder="Enter Package Name" value="{{ ($mode == 'edit' ? $package->name : old('package_name')) }}" autocomplete="off" required>
                  @if ($errors->has('package_name'))
                      <span class="invalid-feedback required-error" role="alert">
                          <strong>{{ $errors->first('package_name') }}</strong>
                      </span>
                  @endif
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="service" class="col-form-label"><strong>{{ __('Service') }}</strong></label>
                   <input type="text" name="service" class="form-control" id="service" placeholder="Enter Service" value="{{ ($mode == 'edit' ? $package->service : old('service')) }}">
                  @if ($errors->has('service'))
                    <span class="invalid-feedback required-error" role="alert">
                      <strong>{{ $errors->first('service') }}</strong>
                    </span>
                  @endif
                </div>
              </div>
              <div class="col-md-3">
               <div class="form-group">
                <label for="price" class="col-form-label"><strong>{{ __('Price') }}</strong><span class="required clr-red">*</span></label>
                  <input type="number" name="price" class="form-input form-control" id="price" placeholder="Enter Price" min="0.01" onkeypress="isFloat(event)" value="{{ ($mode == 'edit' ? $package->price : old('price')) }}" step="any" autocomplete="off" required>
                  @if ($errors->has('price'))
                      <span class="invalid-feedback required-error" role="alert">
                          <strong>{{ $errors->first('price') }}</strong>
                      </span>
                  @endif
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="expire_date" class="col-form-label"><strong>{{ __('Expire Date') }}</strong></label>
                    <input type="text" name="expire_date" class="form-input form-control datepicker" id="expire_date" placeholder="Enter Expire Date" value="{{ ($mode == 'edit' ? ($package->expire_date == ''  ? '' : \Carbon\Carbon::parse($package->expire_date)->format('Y-m-d')) : old('expire_date')) }}" autocomplete="off">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                   <label for="description" class="col-form-label"><strong>{{ __('Description') }}</strong></label>
                   <textarea name="description" class="form-input form-control" id="description" placeholder="Enter Description" autocomplete="off">{{ ($mode == 'edit' ? $package->description : old('description')) }}</textarea>
                </div>
              </div>
              <div class="col-md-3">
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
              <th>Service</th>
              <th>Price</th>
              <th>Expire Date</th>
              <th>Description</th>
              @role('super_admin')
                <th>Action</th>
              @endrole
            </tr>
          </thead>
           <tbody>
            @if(count($packages) > 0)
              <?php $rowCount = 0;?>
              @foreach($packages as $key => $package1)
                <?php $rowCount = $rowCount + 1;?>
                <tr>
                  <td>{{ $rowCount }}</td>
                  <td>{{ $package1->name }}</td>
                  <td>{{ ($package1->service == '' ? 'NA' : $package1->service) }}</td>
                  <td>{{ $package1->price }}</td>
                  <td>{{ ($package1->expire_date == ''  ? 'NA' : \Carbon\Carbon::parse($package1->expire_date)->format('d M, Y')) }}</td>
                  <td>{{ ($package1->description == ''  ? 'NA' : $package1->description) }}</td>
                  @role('super_admin')
                    <td class="display-flex">
                      {{-- package edit form start--}}
                      <a href="{{ url('admin/package/'.$package1->id.'/edit') }}" class="btn btn-primary btn-sm"><i class="fas fa-pencil-alt"></i>&nbsp;Edit</a>
                      {{-- package edit form start--}}
                      &nbsp;
                      |
                      &nbsp;
                      {{-- package delete form start--}}
                      <form class="d-b" action="{{ url('admin/package/delete')}}" method="POST">
                        @csrf
                        <input type="hidden" name="package_id" value="{{ $package1->id }}">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this package?'); "><i class="fas fa-trash"></i>&nbsp;Delete</button>
                      </form>
                      {{-- package delete form start--}}
                    </td>
                  @endrole
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
    $("table").DataTable({
      "ordering": false
    });

    $('.datepicker').datepicker({
      format: 'yyyy-mm-dd',
      autoclose:true
    });
  });

  function isFloat(event){
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
      event.preventDefault();
    }
  }

  @if($mode == 'edit' || count($errors) > 0)
    $('[package-form-row]').slideToggle();
  @endif

  $('[package-add]').click(function (){
    $('[package-form-row]').slideToggle();
  });
</script>
@endsection