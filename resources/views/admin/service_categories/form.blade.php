@extends('layouts.main')

@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h4>
      <strong>{{ ($mode == 'add' ? 'Add' : 'Edit') }} Service Category</strong>
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
            <h5 class="box-title">Enter  Service Category's details here</h5>
          	<div class="clearfix"></div><br />
          </div>
          <!-- /.box-header -->
          <div class="box-body">
          <!-- form start -->
            @if($mode == 'add')
               <form method="POST" action="{{ url('admin/categories/add') }}" enctype="multipart/form-data">
            @else
               <form method="POST" action="{{ url('admin/categories/update') }}" enctype="multipart/form-data">
            @endif
            @csrf

            <input type="hidden" name="category_id" value="{{ ($mode == 'add' ? '' : $serviceCategory->id) }}">

            <div class="form-group row">
              <label for="category_name" class="col-md-2 col-form-label"><strong>{{ __('Category Name') }}</strong><span class="required clr-red">*</span></label>
              <div class="col-md-6">
                <input type="text" name="category_name" class="form-control" id="category_name" placeholder="Enter Category Name" value="{{ ($mode == 'edit' ? $serviceCategory->name : '') }}" required>
                @if ($errors->has('category_name'))
                    <span class="invalid-feedback required-error" role="alert">
                        <strong>{{ $errors->first('category_name') }}</strong>
                    </span>
                @endif
              </div>
            </div>

            <div class="form-group row">
              <label for="description" class="col-md-2 col-form-label"><strong>{{ __('Description') }}</strong></label>
              <div class="col-md-6">
                <textarea name="description" class="form-control" id="description" placeholder="Enter Description">{{ ($mode == 'edit' ? $serviceCategory->description : '') }}</textarea>
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

</script>
@endsection