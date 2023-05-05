@extends('layouts.main')
@section('content')
  <div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="float-left">
      <h4>
        <strong>Clients</strong>
      </h4>
    </div>
    <div class="float-right">
      <div class="float-right"><button type="button" class="form-btn" client-add><i class="fas fa-pencil-alt"></i>&nbsp;Add Client</button></div>
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
        <div class="row" style="display: none;" client-form-row>
          <div class="col-md-12">
            <!-- form start -->
              @if($mode == 'add')
                <form method="POST" action="{{ url('admin/clients/add') }}" enctype="multipart/form-data">
              @else
                <form method="POST" action="{{ url('admin/clients/update') }}" enctype="multipart/form-data">
              @endif
              @csrf
              <input type="hidden" name="client_id" value="{{ ($mode == 'add' ? '' : $client->id) }}">

              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="client_name" class="col-form-label"><strong>{{ __('Client Name') }}</strong><span class="required clr-red">*</span></label>
                    <input type="text" name="client_name" class="form-input form-control" id="client_name" placeholder="Enter Client Name" value="{{ ($mode == 'edit' ? $client->name : old('client_name')) }}" autocomplete="off" required>
                    @if ($errors->has('client_name'))
                      <span class="invalid-feedback required-error" role="alert">
                          <strong>{{ $errors->first('client_name') }}</strong>
                      </span>
                    @endif
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="phone_number" class="col-form-label"><strong>{{ __('Phone Number') }}</strong><span class="required clr-red">*</span></label>
                    <input type="text" name="phone_number" class="form-input form-control" id="phone_number" placeholder="Enter Phone Number" value="{{ ($mode == 'edit' ? $client->phone_number : old('phone_number')) }}" onkeypress="return event.charCode >= 48 && event.charCode <= 57" autocomplete="off">
                    @if ($errors->has('phone_number'))
                      <span class="invalid-feedback required-error" role="alert">
                        <strong>{{ $errors->first('phone_number') }}</strong>
                      </span>
                    @endif
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="package" class="col-form-label"><strong>{{ __('Package') }}</strong></label>
                    <select class="form-input form-control" name="package[]" id="package" autocomplete="off" multiple>
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
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="email" class="col-form-label"><strong>{{ __('Email') }}</strong></label>
                      <input type="email" name="email" class="form-input form-control w-110" id="email" placeholder="Enter Email" value="{{ ($mode == 'edit' ? $client->email : old('email')) }}" autocomplete="nope">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                  <label for="address" class="col-form-label"><strong>{{ __('Address') }}</strong></label>
                   <textarea name="address" class="form-input form-control" id="address" placeholder="Enter Address" autocomplete="nope">{{ ($mode == 'edit' ? $client->address : old('address')) }}</textarea>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="anniversary" class="col-form-label"><strong>{{ __('Anniversary') }}</strong></label>
                      <input type="text" name="anniversary" class="form-input form-control datepicker" id="anniversary" placeholder="Enter Anniversary Date" value="{{ ($mode == 'edit' ? $client->anniversary : old('anniversary')) }}" autocomplete="off">
                  </div>
                </div>
                <div class="col-md-3">
                   <div class="form-group">
                      <label for="birthdate" class="col-form-label"><strong>{{ __('Birthdate') }}</strong></label>
                     <input type="text" name="birthdate" class="form-input form-control datepicker" id="birthdate" placeholder="Enter Birthdate" value="{{ ($mode == 'edit' ? $client->dob : old('birthdate')) }}" autocomplete="off">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group w-118">
                    <label for="gender" class="col-form-label"><strong>{{ __('Gender') }}</strong><span class="required clr-red">*</span></label>
                    <div class="clearfix"></div>
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
        <?php $role = 'employee'; ?>
        @role('super_admin')
          <?php $role = 'super_admin'; ?>
        @endrole
        <client-table role="{{ $role }}"></client-table>
        <!-- /Settle Debit Modal Start-->
        @include('include.settle-debit-modal')
        <!-- /Settle Debit Modal Over-->
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
    $("#package").select2({
      placeholder: "Select package",
    });

    $('.datepicker').datepicker({
      format: 'yyyy-mm-dd',
      autoclose:true
    });
  });

  @if($mode == 'edit' || count($errors) > 0)
    $('[client-form-row]').slideToggle();
  @endif

  $('[client-add]').click(function (){
    $('[client-form-row]').slideToggle();
  });

  $(document).ready(function() {
    var search_str = window.location.search.substr(1);
    if( search_str ) {
      if(search_str.split('=')[1] == 'create')
        $('[client-form-row]').slideDown();
    }
  });

  function isFloat(event){
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
      event.preventDefault();
    }
  }
</script>
@endsection