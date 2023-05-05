@extends('layouts.main')
@section('content')
  <div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="float-left">
      <h4>
        <strong>Employees</strong>
      </h4>
    </div>
    <div class="float-right">
      <div class="float-right"><button type="button" class="form-btn" client-add><i class="fas fa-pencil-alt"></i>&nbsp;Add Employee</button></div>
    </div>
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
        <!-- /.box-header -->
        <div class="row" style="display: none;" employee-form-row>
          <div class="col-md-12">
            <!-- form start -->
              @if($mode == 'add')
                 <form method="POST" action="{{ url('admin/employee/add') }}" enctype="multipart/form-data">
              @else
                 <form method="POST" action="{{ url('admin/employee/update') }}" enctype="multipart/form-data">
              @endif
              @csrf
              <input type="hidden" name="employee_id" value="{{ ($mode == 'add' ? '' : $employee->id) }}">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="employee_name" class="col-form-label"><strong>{{ __('Employee Name') }}</strong><span class="required clr-red">*</span></label>
                    <input type="text" name="employee_name" class="form-input form-control" id="employee_name" placeholder="Enter Employee Name" value="{{ ($mode == 'edit' ? $employee->name : old('employee_name')) }}" autocomplete="off" required>
                    @if ($errors->has('employee_name'))
                      <span class="invalid-feedback required-error" role="alert">
                          <strong>{{ $errors->first('employee_name') }}</strong>
                      </span>
                    @endif
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="birthdate" class="col-form-label"><strong>{{ __('Birthdate') }}</strong></label>
                    <input type="text" name="birthdate" class="form-input form-control datepicker" id="birthdate" placeholder="Enter Birthdate" value="{{ ($mode == 'edit' ? $employee->dob : old('birthdate')) }}" autocomplete="off">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="email" class="col-form-label"><strong>{{ __('Email') }}</strong></label>
                    <input type="email" name="email" class="form-input form-control" id="email" placeholder="Enter Email" value="{{ ($mode == 'edit' ? $employee->email : old('email')) }}" autocomplete="nope">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="phone_primary" class="col-form-label"><strong>{{ __('Phone Number') }}</strong><span class="required clr-red">*</span></label>
                    <input type="phone_primary" name="phone_primary" class="form-input form-control" id="phone_primary" placeholder="Enter Phone Number" onkeypress="return event.charCode >= 48 && event.charCode <= 57" value="{{ ($mode == 'edit' ? $employee->phone_primary : old('phone_primary')) }}" autocomplete="off" required>
                    @if ($errors->has('phone_primary'))
                      <span class="invalid-feedback required-error" role="alert">
                          <strong>{{ $errors->first('phone_primary') }}</strong>
                      </span>
                    @endif
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                  <label for="address" class="col-form-label"><strong>{{ __('Address') }}</strong></label>
                   <textarea name="address" class="form-input form-control" id="address" placeholder="Enter Address" autocomplete="nope">{{ ($mode == 'edit' ? $employee->address : old('address')) }}</textarea>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
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
                <th>Email</th>
                <th>Phone Number</th>
                <th>Birthday</th>
                <th>Address</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @if(count($employees) > 0)
                <?php $rowCount = 0;?>
                @foreach($employees as $key => $employee1)
                  <?php $rowCount = $rowCount + 1;?>
                  <tr>
                    <td>{{ $rowCount }}</td>
                    <td>{{ $employee1->name }}</td>
                    <td>{{ ($employee1->email == ''  ? 'NA' : $employee1->email) }}</td>
                    <td>{{ $employee1->phone_primary }}</td>
                    <td>{{ ($employee1->dob == ''  ? 'NA' : \Carbon\Carbon::parse($employee1->dob)->format('d M, Y')) }}</td>
                    <td>{{ ($employee1->address == ''  ? 'NA' : $employee1->address) }}</td>
                    <td class="display-flex">
                      {{-- employee edit form start--}}
                      <a href="{{ url('admin/employee/'.$employee1->id.'/edit') }}" class="btn btn-primary btn-sm"><i class="fas fa-pencil-alt"></i>&nbsp;Edit</a>&nbsp;|&nbsp;
                      <a href="{{ url('admin/employee/'.$employee1->id.'/view') }}" class="btn btn-success btn-sm"><i class="fas fa-eye"></i>&nbsp;View</a>&nbsp;|&nbsp;
                      <form class="d-b" action="{{ url('admin/employee/delete')}}" method="POST">
                        @csrf
                        <input type="hidden" name="employee_id" value="{{ $employee1->id }}">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this employee?'); "><i class="fas fa-trash"></i>&nbsp;Delete</button>
                      </form>
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
    $("table").DataTable({
      "ordering": false
    });

    $('.datepicker').datepicker({
      format: 'yyyy-mm-dd',
      autoclose:true
    });
  });

  @if($mode == 'edit' || count($errors) > 0)
    $('[employee-form-row]').slideToggle();
  @endif

  $('[client-add]').click(function (){
    $('[employee-form-row]').slideToggle();
  });

  $(document).ready(function() {
    var search_str = window.location.search.substr(1);
    if( search_str ) {
      if(search_str.split('=')[1] == 'create')
        $('[employee-form-row]').slideDown();
    }
  });
</script>
@endsection