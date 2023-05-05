@extends('layouts.main')
@section('content')
  <div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h4>
      <strong>Products</strong>
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
          <div class="float-right"><button type="button" class="form-btn" product-add><i class="fas fa-pencil-alt"></i>&nbsp;Add Product</button></div>
          <div class="clearfix"></div><br/>
        </div>
        <!-- /.box-header -->
        <div class="row" style="display: none;" product-form-row>
          <div class="col-md-12">
            @if($mode == 'add')
               <form method="POST" action="{{ url('admin/products/add') }}" enctype="multipart/form-data">
            @else
               <form method="POST" action="{{ url('admin/products/update') }}" enctype="multipart/form-data">
            @endif
            @csrf
            <input type="hidden" name="product_id" value="{{ ($mode == 'add' ? '' : $product->id) }}">

            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label for="product_name" class="col-form-label"><strong>{{ __('Product Name') }}</strong><span class="required clr-red">*</span></label>
                  <input type="text" name="product_name" class="form-input form-control" id="product_name" placeholder="Enter Product Name" value="{{ ($mode == 'edit' ? $product->name : old('product_name')) }}" autocomplete="off" required>
                  @if ($errors->has('product_name'))
                      <span class="invalid-feedback required-error" role="alert">
                          <strong>{{ $errors->first('product_name') }}</strong>
                      </span>
                  @endif
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="price" class="col-form-label"><strong>{{ __('Price') }}</strong><span class="required clr-red">*</span></label>
                  <input type="number" name="price" class="form-input form-control" id="price" placeholder="Enter Price" min="0.01" onkeypress="isFloat(event)" value="{{ ($mode == 'edit' ? $product->price : old('price')) }}" step="any" autocomplete="off" required>
                  @if ($errors->has('price'))
                      <span class="invalid-feedback required-error" role="alert">
                          <strong>{{ $errors->first('price') }}</strong>
                      </span>
                  @endif
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="description" class="col-form-label"><strong>{{ __('Description') }}</strong></label>
                    <textarea name="description" class="form-input form-control" id="description" placeholder="Enter Description" autocomplete="off">{{ ($mode == 'edit' ? $product->description : old('description')) }}</textarea>
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
              <th>Price</th>
              <th>Description</th>
              @role('super_admin')
                 <th>Action</th>
              @endrole
            </tr>
          </thead>
           <tbody>
            @if(count($products) > 0)
               <?php $rowCount = 0;?>
              @foreach($products as $key => $product1)
                <?php $rowCount = $rowCount + 1;?>
                <tr>
                  <td>{{ $rowCount }}</td>
                  <td>{{ $product1->name }}</td>
                  <td>{{ $product1->price }}</td>
                  <td>{{ ($product1->description == ''  ? 'NA' : $product1->description) }}</td>

                  @role('super_admin')
                    <td class="display-flex">
                      {{-- product edit form start--}}
                      <a href="{{ url('admin/products/'.$product1->id.'/edit') }}" class="btn btn-primary btn-sm"><i class="fas fa-pencil-alt"></i>&nbsp;Edit</a>
                      {{-- product edit form start--}}
                      &nbsp;
                      |
                      &nbsp;
                      {{-- product delete form start--}}
                      <form class="d-b" action="{{ url('admin/products/delete')}}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product1->id }}">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?'); "><i class="fas fa-trash"></i>&nbsp;Delete</button>
                      </form>
                      {{-- product delete form start--}}
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
  });

  function isFloat(event){
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
      event.preventDefault();
    }
  }

  @if($mode == 'edit' || count($errors) > 0)
    $('[product-form-row]').slideToggle();
  @endif

  $('[product-add]').click(function (){
    $('[product-form-row]').slideToggle();
  });
</script>
@endsection