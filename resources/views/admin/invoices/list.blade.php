@extends('layouts.main')
@section('content')
  <div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h4>
      <strong>Invoices</strong>
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
        <!-- /.box-header -->
        <!-- Table start -->
        {{-- <div class="table-responsive">
       	</div> --}}
       	<?php $role = 'employee'; ?>
        @role('super_admin')
          <?php $role = 'super_admin'; ?>
        @endrole
       	<invoice-table role="{{ $role }}"></invoice-table>
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
  setTimeout(function(){
    $('.datepicker').datepicker({
      format: 'yyyy-mm-dd',
      autoclose: true
    });
  }, 1);
</script>
@endsection