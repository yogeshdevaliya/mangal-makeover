@extends('layouts.main')
@section('content')
  <div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h4>
      <strong>Expenses</strong>
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
          <div class="float-right"><button type="button" class="form-btn" expenses-add><i class="fas fa-pencil-alt"></i>&nbsp;Add Expenses</button></div>
        </div>
        <div class="clearfix"></div><br/>
        <!-- /.box-header -->
        <div class="row" style="display: none;" expenses-form-row>
          <div class="col-md-12">
            @if($mode == 'add')
               <form method="POST" action="{{ url('admin/expenses/add') }}" enctype="multipart/form-data">
            @else
               <form method="POST" action="{{ url('admin/expenses/update') }}" enctype="multipart/form-data">
            @endif
            @csrf
            <input type="hidden" name="expense_id" value="{{ ($mode == 'add' ? '' : $expense->id) }}">
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label for="date" class="col-form-label"><strong>{{ __('Date') }}</strong></strong><span class="required clr-red">*</span></label>
                    <input type="text" name="date" class="form-input form-control datepicker" id="date" value="{{ ($mode == 'add' ? \Carbon\Carbon::now()->format('Y-m-d') : \Carbon\Carbon::parse($expense->date)->format('Y-m-d')) }}" autocomplete="off" required>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="title" class="col-form-label"><strong>{{ __('Title') }}</strong><span class="required clr-red">*</span></label>
                  <input type="text" name="title" class="form-input form-control" id="title" placeholder="Enter Title" value="{{ ($mode == 'edit' ? $expense->title : old('title')) }}" autocomplete="off" required>
                  @if ($errors->has('title'))
                      <span class="invalid-feedback required-error" role="alert">
                          <strong>{{ $errors->first('title') }}</strong>
                      </span>
                  @endif
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="description" class="col-form-label"><strong>{{ __('Description') }}</strong></label>
                    <textarea name="description" class="form-input form-control" id="description" placeholder="Enter Description" autocomplete="off">{{ ($mode == 'edit' ? $expense->description : old('description')) }}</textarea>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label for="total_amount" class="col-form-label"><strong>{{ __('Total Amount') }}</strong><span class="required clr-red">*</span></label>
                  <input type="number" name="total_amount" min="0.01" onkeypress="isFloat(event)" class="form-input form-control" id="total_amount" placeholder="Enter Total Amount" value="{{ ($mode == 'edit' ? $expense->total_amount : old('total_amount')) }}" step="any" autocomplete="off" required>
                  @if ($errors->has('total_amount'))
                      <span class="invalid-feedback required-error" role="alert">
                          <strong>{{ $errors->first('total_amount') }}</strong>
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
        <div class="clearfix"></div>

        <?php $startDate = \Carbon\Carbon::parse($startDate)->format('Y-m-d');?>
        <?php $endDate = \Carbon\Carbon::parse($endDate)->format('Y-m-d');?>

        <div class="row">
          <div class="col-md-12">
            <!-- form start -->
            <form method="GET" action="" enctype="multipart/form-data">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="from" class="col-form-label"><strong>{{ __('Date From') }}</strong></label>
                      <input type="text" name="from" class="form-input form-control datepicker" id="from" value="{{ $startDate }}" autocomplete="off">
                  </div>
                </div>
                <div class="col-md-3">
                   <div class="form-group">
                      <label for="to" class="col-form-label"><strong>{{ __('Date To') }}</strong></label>
                      <input type="text" name="to" class="form-input form-control datepicker" id="to" value="{{ $endDate }}" autocomplete="off">
                  </div>
                </div>
                <div class="col-md-1">
                  <div class="clearfix"></div><br />
                  <button type="submit" class="form-btn">
                     Filter
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div class="clearfix"></div><br />
        <div class="row">
          <div class="col-md-12">
            <a href="{{ url('admin/print/expenses?from='.$startDate.'&to='.$endDate) }}" class="form-btn btn-mini" target="_blank">Print</a>
          </div>
        </div>
        <div class="clearfix"></div><br />
        <!-- Table start -->
        <div class="table-responsive">
          <table class="table table-hover table-bordered" id="tableId">
            <thead>
             <tr>
              <th>Sr. No</th>
              <th>Date</th>
              <th>Title</th>
              <th>Description</th>
              <th>Total Amount</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @if(count($expenses) > 0)
              <?php
$rowCount = 0;
$totla_expense = 0;
?>
              @foreach($expenses as $key => $expense)
                <?php
$rowCount = $rowCount + 1;
$totla_expense = $totla_expense + $expense->total_amount;
?>
                <tr>
                  <td>{{ $rowCount }}</td>
                  <td>{{ \Carbon\Carbon::parse($expense->date)->format('d M, Y') }}</td>
                  <td>{{ $expense->title }}</td>
                  <td>{{ ($expense->description == '' ? 'NA' : $expense->description) }}</td>
                  <td>{{ number_format($expense->total_amount, 2) }}</td>
                  <td>
                      -
                    {{-- <form class="d-b" action="{{ url('admin/expense/delete')}}" method="POST">
                      @csrf
                      <input type="hidden" name="client_id" value="{{ $expense1->id }}">
                      <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this expense?'); "><i class="fas fa-trash"></i>&nbsp;Delete</button>
                    </form> --}}
                  </td>
                </tr>
              @endforeach
              <tr>
                <td>Total</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>Total: <strong>{{ number_format($totla_expense, 2) }}</strong></td>
              </tr>
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
    $('[expenses-form-row]').slideToggle();
  @endif

  $('[expenses-add]').click(function (){
     $('[expenses-form-row]').slideToggle();
  });
</script>
@endsection