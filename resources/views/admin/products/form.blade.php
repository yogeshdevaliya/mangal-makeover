@extends('layouts.main')

@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h4>
      <strong>{{ ($mode == 'add' ? 'Add' : 'Edit') }} Product</strong>
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
            <h5 class="box-title">Enter  Product's details here</h5>
          	<div class="clearfix"></div><br />
          </div>
          <!-- /.box-header -->
          <div class="box-body">
          <!-- form start -->
            @if($mode == 'add')
               <form method="POST" action="{{ url('admin/products/add') }}" enctype="multipart/form-data">
            @else
               <form method="POST" action="{{ url('admin/products/update') }}" enctype="multipart/form-data">
            @endif
            @csrf

            <input type="hidden" name="product_id" value="{{ ($mode == 'add' ? '' : $product->id) }}">

            <div class="form-group row">
              <label for="product_name" class="col-md-2 col-form-label"><strong>{{ __('Product Name') }}</strong><span class="required clr-red">*</span></label>
              <div class="col-md-6">
                <input type="text" name="product_name" class="form-control" id="product_name" placeholder="Enter Product Name" value="{{ ($mode == 'edit' ? $product->name : '') }}" required>
                @if ($errors->has('product_name'))
                    <span class="invalid-feedback required-error" role="alert">
                        <strong>{{ $errors->first('product_name') }}</strong>
                    </span>
                @endif
              </div>
            </div>

            <div class="form-group row">
              <label for="price" class="col-md-2 col-form-label"><strong>{{ __('Price') }}</strong><span class="required clr-red">*</span></label>
              <div class="col-md-6">
                <input type="number" name="price" class="form-control" id="price" placeholder="Enter Price" value="{{ ($mode == 'edit' ? $product->price : '') }}" step="any" required>
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
                <textarea name="description" class="form-control" id="description" placeholder="Enter Description">{{ ($mode == 'edit' ? $product->description : '') }}</textarea>
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