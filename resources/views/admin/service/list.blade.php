@extends('layouts.main')
@section('content')
  <div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h4>
      <strong>Services</strong>
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
          <div class="float-right"><a href="{{ url('admin/services/create') }}" class="btn btn-primary"><i class="fas fa-pencil-alt"></i>&nbsp;Add Service</a></div>
          <div class="clearfix"></div><br/>
        </div>
        <!-- /.box-header -->
        <!-- Table start -->
        <div class="table-responsive">
          <table class="table table-hover table-bordered" id="tableId">
            <thead>
             <tr>
              <th>Sr. No</th>
              <th>Name</th>
              <th>Service Category Name</th>
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
              @foreach($services as $key => $service1)
                <tr>
                  <td>{{ ++$key }}</td>
                  <td>{{ $service1->name }}</td>
                  <td>{{ $service1->category->name }}</td>
                  <td>{{ $service1->duration }}</td>
                  <td>{{ $service1->price }}</td>
                  <td>{{ ($service1->description == ''  ? 'NA' : $service1->description) }}</td>
                  @role('super_admin')
                    <td class="display-flex">
                      {{-- service edit form start--}}
                      <a href="{{ url('admin/services/'.$service1->id.'/edit') }}" class="btn btn-primary btn-sm"><i class="fas fa-pencil-alt"></i>&nbsp;Edit</a>
                      {{-- service edit form start--}}
                      &nbsp;
                      |
                      &nbsp;
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
  $(function () {
    $("table").DataTable();
  });
</script>
@endsection