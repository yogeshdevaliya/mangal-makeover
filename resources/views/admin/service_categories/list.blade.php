@extends('layouts.main')
@section('content')
  <div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
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
    <div class="col-md-6">
      <h4>
        <strong>Service Categories</strong>
      </h4>
      <div class="clearfix"></div><br />
      <!-- Horizontal Form -->
      <div class="box box-warning">
        <div class="box-header with-border">
          <div class="float-right">
             <button type="button" class="form-btn" category-add><i class="fas fa-pencil-alt"></i>&nbsp;Add Category</button>
           </div>
          <div class="clearfix"></div><br/>
        </div>
        <!-- /.box-header -->
        <div class="row" style="display: none;" category-form-row>
          <div class="col-md-12">
            @if($serviceCategoryMode == 'add')
               <form method="POST" action="{{ url('admin/categories/add') }}" enctype="multipart/form-data">
            @else
               <form method="POST" action="{{ url('admin/categories/update') }}" enctype="multipart/form-data">
            @endif
            @csrf
            <input type="hidden" name="category_id" value="{{ ($serviceCategoryMode == 'add' ? '' : $serviceCategory->id) }}">
            <div class="form-group row">
              <label for="category_name" class="col-md-4 col-form-label"><strong>{{ __('Category Name') }}</strong><span class="required clr-red">*</span></label>
              <div class="col-md-8">
                <input type="text" name="category_name" class="form-input form-control" id="category_name" placeholder="Enter Category Name" value="{{ ($serviceCategoryMode == 'edit' ? $serviceCategory->name : '') }}" autocomplete="off" required>
                @if ($errors->has('category_name'))
                    <span class="invalid-feedback required-error" role="alert">
                        <strong>{{ $errors->first('category_name') }}</strong>
                    </span>
                @endif
              </div>
            </div>

            <div class="form-group row">
              <label for="description" class="col-md-4 col-form-label"><strong>{{ __('Description') }}</strong></label>
              <div class="col-md-8">
                <textarea name="description" class="form-input form-control" id="description" placeholder="Enter Description" autocomplete="off">{{ ($serviceCategoryMode == 'edit' ? $serviceCategory->description : '') }}</textarea>
              </div>
            </div>

            <div class="form-group row mb-0">
              <div class="col-md-12 text-right">
                <button type="submit" class="form-btn">
                     {{ ($serviceCategoryMode == 'add' ? 'Add' : 'Edit') }}
                </button>
              </div>
            </div>
           </form>
          </div>
        </div>
        <div class="clearfix"></div><br />
        <!-- Table start -->
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="tableId">
            <thead>
             <tr>
              <th>Index</th>
              <th>Name</th>
              <th>Description</th>
              @role('super_admin')
                <th>Action</th>
              @endrole
            </tr>
          </thead>
           <tbody>
           	@if(count($serviceCategories) > 0)
              <?php $serviceCatRowCount = 0;?>
              @foreach($serviceCategories as $key => $serviceCategory1)
                 <?php $serviceCatRowCount = $serviceCatRowCount + 1;?>
              	<tr>
              		<td>{{ $serviceCatRowCount }}</td>
              		<td>{{ $serviceCategory1->name }}</td>
              		<td>{{ ($serviceCategory1->description == ''  ? 'NA' : $serviceCategory1->description) }}</td>
                  @role('super_admin')
                		<td class="display-flex">
                			{{-- category edit form start--}}
                      <a href="{{ url('admin/categories/'.$serviceCategory1->id.'/edit') }}" class="btn btn-primary btn-sm"><i class="fas fa-pencil-alt"></i>&nbsp;Edit</a>&nbsp;
                      <form class="d-b" action="{{ url('admin/categories/delete')}}" method="POST">
                        @csrf
                        <input type="hidden" name="category_id" value="{{ $serviceCategory1->id }}">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?'); "><i class="fas fa-trash"></i>&nbsp;Delete</button>
                      </form>
                      {{-- category delete form start--}}
                		</td>
                  @endrole
              	</tr>
                @endforeach
            @else
              <tr>
  	            <td colspan="4" align="center"><h5><strong>No categories added yet!</strong></h5></td>
  	          </tr>
            @endif
           </tbody>
         </table>
       </div>
     </div>
     <!-- /.box -->
    </div>
    <div class="col-md-6">
      <h4>
        <strong>Services</strong>
      </h4>
      <div class="clearfix"></div><br />
      <!-- Horizontal Form -->
      <div class="box box-warning">
        <div class="box-header with-border">
          <div class="float-right"><button type="button" class="form-btn" service-add><i class="fas fa-pencil-alt"></i>&nbsp;Add Service</button></div>
          <div class="clearfix"></div><br/>
        </div>
        <!-- /.box-header -->
        <div class="row" style="display: none;" service-form-row>
          <div class="col-md-12">
             @if($serviceMode == 'add')
                 <form method="POST" action="{{ url('admin/services/add') }}" enctype="multipart/form-data">
              @else
                 <form method="POST" action="{{ url('admin/services/update') }}" enctype="multipart/form-data">
              @endif
              @csrf

              <input type="hidden" name="service_id" value="{{ ($serviceMode == 'add' ? '' : $service->id) }}">

              <div class="form-group row">
                <label for="service_name" class="col-md-4 col-form-label"><strong>{{ __('Service Name') }}</strong><span class="required clr-red">*</span></label>
                <div class="col-md-8">
                  <input type="text" name="service_name" class="form-input form-control" id="service_name" placeholder="Enter Service Name" value="{{ ($serviceMode == 'edit' ? $service->name : '') }}" autocomplete="off" required>
                  @if ($errors->has('service_name'))
                      <span class="invalid-feedback required-error" role="alert">
                          <strong>{{ $errors->first('service_name') }}</strong>
                      </span>
                  @endif
                </div>
              </div>

              <div class="form-group row" id="select2">
                <label for="service_category" class="col-md-4 col-form-label"><strong>{{ __('Service Category') }}</strong><span class="required clr-red">*</span></label>
                <div class="col-md-8">
                  <select class="form-input form-control" name="service_category" id="service_category" autocomplete="off" required>
                    @foreach($serviceCategories as $key => $serviceCategory)
                      <option value="{{ $serviceCategory->id }}" {{ ($serviceMode == 'add' ? '' : ($service->service_category_id == $serviceCategory->id ? 'selected' : '')) }}>{{ $serviceCategory->name }}</option>
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
                <label for="duration" class="col-md-4 col-form-label"><strong>{{ __('Duration') }}</strong></label>
                <div class="col-md-8">
                  <input type="number" name="duration" class="form-input form-control" id="duration" placeholder="Enter Duration" min="0" onkeypress="isFloat(event)" value="{{ ($serviceMode == 'edit' ? $service->duration : '') }}" step="any" autocomplete="off">
                  @if ($errors->has('duration'))
                      <span class="invalid-feedback required-error" role="alert">
                          <strong>{{ $errors->first('duration') }}</strong>
                      </span>
                  @endif
                </div>
              </div>

              <div class="form-group row">
                <label for="price" class="col-md-4 col-form-label"><strong>{{ __('Price') }}</strong><span class="required clr-red">*</span></label>
                <div class="col-md-8">
                  <input type="number" name="price" class="form-input form-control" id="price" placeholder="Enter Price" min="0.01" onkeypress="isFloat(event)" value="{{ ($serviceMode == 'edit' ? $service->price : '') }}" step="any" autocomplete="off" required>
                  @if ($errors->has('price'))
                      <span class="invalid-feedback required-error" role="alert">
                          <strong>{{ $errors->first('price') }}</strong>
                      </span>
                  @endif
                </div>
              </div>

              <div class="form-group row">
                <label for="description" class="col-md-4 col-form-label"><strong>{{ __('Description') }}</strong></label>
                <div class="col-md-8">
                  <textarea name="description" class="form-input form-control" id="description" placeholder="Enter Description" autocomplete="off">{{ ($serviceMode == 'edit' ? $service->description : '') }}</textarea>
                </div>
              </div>

              <div class="form-group row mb-0">
                  <div class="col-md-12 text-right">
                      <button type="submit" class="form-btn">
                        {{ ($serviceMode == 'add' ? 'Add' : 'Edit') }}
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
              <th>Index</th>
              <th>Name</th>
              <th>Category</th>
              <th>Duration</th>
              <th>Price</th>
              <th>Description</th>
              @role('super_admin')
                <th>Action</th>
              @endrole
            </tr>
          </thead>
           <tbody>
            @if(count($services) > 0)
              <?php $serviceRowCount = 0;?>
              @foreach($services as $key => $service1)
                <?php $serviceRowCount = $serviceRowCount + 1;?>
                <tr>
                  <td>{{ $serviceRowCount }}</td>
                  <td>{{ $service1->name }}</td>
                  <td>{{ $service1->category->name }}</td>
                  <td>{{ ($service1->duration == ''  ? 'NA' : $service1->duration) }}</td>
                  <td>{{ $service1->price }}</td>
                  <td>{{ ($service1->description == ''  ? 'NA' : $service1->description) }}</td>
                  @role('super_admin')
                    <td class="display-flex">
                      {{-- service edit form start--}}
                      <a href="{{ url('admin/services/'.$service1->id.'/edit') }}" class="btn btn-primary btn-sm"><i class="fas fa-pencil-alt"></i>&nbsp;Edit</a>&nbsp;
                      {{-- service delete form start--}}
                      <form class="d-b" action="{{ url('admin/services/delete')}}" method="POST">
                        @csrf
                        <input type="hidden" name="service_id" value="{{ $service1->id }}">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this service?'); "><i class="fas fa-trash"></i>&nbsp;Delete</button>
                      </form>
                      {{-- service delete form start--}}
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

  $(document).ready(function() {
    $("table").DataTable({
      "ordering": false
    });
  });

  function isFloat(event){
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
      event.preventDefault();
    }
  }

  @if($serviceCategoryMode == 'edit')
    $('[category-form-row]').slideToggle();
  @endif

  $('[category-add]').click(function (){
    $('[category-form-row]').slideToggle();
  });

  @if($serviceMode == 'edit')
    $('[service-form-row]').slideToggle();
  @endif

  $('[service-add]').click(function (){
     $('[service-form-row]').slideToggle();
  });

  $("#service_category").select2({
    placeholder: "Select service category",
  });
</script>
@endsection