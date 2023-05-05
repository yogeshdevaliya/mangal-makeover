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
            <a href="{{ url('admin/print/employee/'.$employeeId.'/reports?from='.$startDate.'&to='.$endDate) }}" class="form-btn btn-mini" target="_blank">Print</a>
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
              <th>Client Name</th>
              <th>Client Contact</th>
              <th>Service</th>
              <th>Product</th>
              <th>Package</th>
              <th>Grand Total</th>
            </tr>
          </thead>
           <tbody>
            @if(count($invoices) > 0)
              <?php
$rowCount = 0;
$total_service_total = 0;
$total_product_total = 0;
$total_package_total = 0;
$total_grand_total = 0;
?>

              @foreach($invoices as $key => $invoice)
                <?php
$rowCount = $rowCount + 1;
$invoice_service_total = 0;
$invoice_product_total = 0;
$invoice_package_total = 0;
$invoice_grand_total = 0;
?>
                <tr>
                  <td>{{ $rowCount }}</td>
                  <td>{{ \Carbon\Carbon::parse($invoice['bill_date'])->format('d M, Y') }}</td>
                  <td>{{ $invoice['client']['name'] }}</td>
                  <td>{{ $invoice['client']['phone_number'] }}</td>
                  <td>
                   <!-- Employee Invoice -->
                    @if(count($invoice['invoice_detail']) > 0)
                      <ul class="fs-15">
                        <?php $isServiceExist = 0;?>
                        @foreach($invoice['invoice_detail'] as $invoiceDetail)
                          @if($invoiceDetail['item_type'] == 'SERVICE')
                              <?php $isServiceExist = 1;
$beauticianCount = count(explode(',', $invoiceDetail['beautician_id']));
$invoice_total_price = $invoiceDetail['total_price'] / $beauticianCount;
?>
                              <li>{{ $invoiceDetail['name'] }}&nbsp;<strong>({{ number_format($invoice_total_price, 2) }} Rs.)</strong></li>
                              <?php $invoice_service_total = $invoice_service_total + $invoice_total_price;?>
                          @endif
                        @endforeach
                        @if($isServiceExist == 0)
                          <li class="ls-none">NA</li>
                        @endif
                      </ul>
                    @else
                      <span>NA</span>
                    @endif
                  </td>
                  <td>
                   <!-- Employee Invoice -->
                    @if(count($invoice['invoice_detail']) > 0)
                      <ul class="fs-15">
                        <?php $isProductExist = 0;?>
                        @foreach($invoice['invoice_detail'] as $invoiceDetail)
                          @if($invoiceDetail['item_type'] == 'PRODUCT')
                              <?php $isProductExist = 1;
$beauticianCount = count(explode(',', $invoiceDetail['beautician_id']));
$invoice_total_price = $invoiceDetail['total_price'] / $beauticianCount;
?>
                              <li>{{ $invoiceDetail['name'] }}&nbsp;<strong>({{ number_format($invoice_total_price, 2) }} Rs.)</strong></li>
                              <?php $invoice_product_total = $invoice_product_total + $invoice_total_price;?>
                          @endif
                        @endforeach
                        @if($isProductExist == 0)
                          <li class="ls-none">NA</li>
                        @endif
                      </ul>
                    @else
                      <span>NA</span>
                    @endif
                  </td>
                  <td>
                    <!-- Employee Invoice -->
                    @if(count($invoice['invoice_detail']) > 0)
                      <ul class="fs-15">
                        <?php $isPackageExist = 0;?>
                        @foreach($invoice['invoice_detail'] as $invoiceDetail)
                          @if($invoiceDetail['item_type'] == 'PACKAGE')
                              <?php $isPackageExist = 1;
$beauticianCount = count(explode(',', $invoiceDetail['beautician_id']));
$invoice_total_price = $invoiceDetail['total_price'] / $beauticianCount;
?>
                              <li>{{ $invoiceDetail['name'] }}&nbsp;<strong>({{ number_format($invoice_total_price, 2) }} Rs.)</strong></li>
                              <?php $invoice_package_total = $invoice_package_total + $invoice_total_price;?>
                          @endif
                        @endforeach
                        @if($isPackageExist == 0)
                          <li class="ls-none">NA</li>
                        @endif
                      </ul>
                    @else
                      <span>NA</span>
                    @endif
                  </td>
                  <?php $invoice_grand_total = $invoice_service_total + $invoice_product_total + $invoice_package_total;?>
                  <td>{{  number_format($invoice_grand_total, 2) }}</td>
                </tr>
                <?php $total_service_total = $total_service_total + $invoice_service_total;?>
                <?php $total_product_total = $total_product_total + $invoice_product_total;?>
                <?php $total_package_total = $total_package_total + $invoice_package_total;?>
                <?php $total_grand_total = $total_grand_total + $invoice_grand_total;?>
              @endforeach
              <tr>
                <td>Total</td>
                <td></td>
                <td></td>
                <td></td>
                <td><strong>{{  number_format($total_service_total, 2) }}</strong></td>
                <td><strong>{{  number_format($total_product_total, 2) }}</strong></td>
                <td><strong>{{  number_format($total_package_total, 2) }}</strong></td>
                <td><strong>{{  number_format($total_grand_total, 2) }}</strong></td>
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
</script>
@endsection