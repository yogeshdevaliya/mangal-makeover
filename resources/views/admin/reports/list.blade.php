@extends('layouts.main')
@section('content')
  <div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h4>
      <strong>Reports</strong>
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

  <?php $startDate = \Carbon\Carbon::parse($startDate)->format('Y-m-d');?>
  <?php $endDate = \Carbon\Carbon::parse($endDate)->format('Y-m-d');?>

  <div class="row">
    <div class="col-md-12">
      <!-- Horizontal Form -->
      <div class="box box-warning">
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
            <a href="{{ url('admin/print/reports?from='.$startDate.'&to='.$endDate) }}" class="form-btn btn-mini" target="_blank">Print</a>
          </div>
        </div>
        <div class="clearfix"></div><br />

        <!-- Table start -->
        <div class="table-responsive">
          <table class="table table-hover table-bordered" id="tableId">
            <thead>
             <tr>
              <th>Sr.No</th>
              <th>Date</th>
              <th>Client Name</th>
              <th>Client Contact</th>
              <th>Service</th>
              <th>Product</th>
              <th>Package</th>
              <th>Discount</th>
              <th>Grand Total</th>
              <th>Advance Payment</th>
              <th>Debit Amount</th>
              <th>Amount Paid</th>
              <th>On Credit + Cash</th>
              <th>On Credit + Debit</th>
            </tr>
          </thead>
          <tbody>
          @if(count($invoices) > 0)
              <?php
                $rowCount = 0;
                $total_service_total = 0;
                $total_product_total = 0;
                $total_package_total = 0;
                $invoice_total_discount = 0;
                $total_grand_total = 0;
                $total_advance_payment = 0;
                $total_debit_amount = 0;
                $total_amount_paid = 0;
                $total_on_credit_cash = 0;
                $total_on_credit_debit = 0;
                ?>
	              @foreach($invoices as $key => $invoice)
	                <?php

                $rowCount = $rowCount + 1;
                $service_total = (float) ($invoice->service_total == '' ? 0 : $invoice->service_total);
                $total_service_total = $total_service_total + $service_total;
                $product_total = (float) ($invoice->product_total == '' ? 0 : $invoice->product_total);
                $total_product_total = $total_product_total + $product_total;
                $package_total = (float) ($invoice->package_total == '' ? 0 : $invoice->package_total);
                $total_package_total = $total_package_total + $package_total;
                $total_discount = (float) ($invoice->total_discount == '' ? 0 : $invoice->total_discount);
                $invoice_total_discount = $invoice_total_discount + $total_discount;
                $grand_total = (float) ($invoice->grand_total == '' ? 0 : $invoice->grand_total);
                $total_grand_total = $total_grand_total + $grand_total;
                $advance_payment = (float) ($invoice->advance_payment == '' ? 0 : $invoice->advance_payment);
                $total_advance_payment = $total_advance_payment + $advance_payment;
                $debit_amount = (float) ($invoice->debit_amount == '' ? 0 : $invoice->debit_amount);
                $total_debit_amount = $total_debit_amount + $debit_amount;
                $amount_paid = (float) ($invoice->amount_paid == '' ? 0 : $invoice->amount_paid);
                $total_amount_paid = $total_amount_paid + $amount_paid;
                $on_credit_cash = (float) ($invoice->on_credit_cash == '' ? 0 : $invoice->on_credit_cash);
                $total_on_credit_cash = $total_on_credit_cash + $on_credit_cash;
                $on_credit_debit = (float) ($invoice->on_credit_debit == '' ? 0 : $invoice->on_credit_debit);
                $total_on_credit_debit = $total_on_credit_debit + $on_credit_debit;
                ?>
								<tr>
                  <td>{{ $rowCount }}</td>
                  <td>{{ \Carbon\Carbon::parse($invoice->bill_date)->format('d M, Y') }}</td>
                  <td>{{ $invoice->client->name }}
                    <small class="sm-number">({{ $invoice->client->phone_number }})</small>
                  </td>
                  <td>{{ $invoice->client->phone_number }}</td>
                  <td>{{ number_format($invoice->service_total, 2) }}</td>
                  <td>{{ number_format($invoice->product_total, 2) }}</td>
                  <td>{{ number_format($invoice->package_total, 2) }}</td>
                  <td>{{ number_format($invoice->total_discount, 2) }}</td>
                  <td>{{ number_format($invoice->grand_total, 2) }}</td>
                  <td>{{ ($invoice->payment_type == 'ADVANCE_PAYMENT' ? number_format($invoice->advance_payment, 2) : '0.00') }}</td>
                  <td>{{ ($invoice->payment_type == 'DEBIT' ? number_format($invoice->debit_amount, 2) : '0.00') }}</td>
                  <td>{{ number_format($invoice->amount_paid, 2) }}</td>
                  <td>{{ ($invoice->payment_type == 'ON_CREDIT+CASH' ? number_format($invoice->on_credit_cash, 2) : '0.00') }}</td>
                  <td>{{ ($invoice->payment_type == 'ON_CREDIT+DEBIT' ? number_format($invoice->on_credit_debit, 2) : '0.00') }}</td>
                </tr>
              	@endforeach
              <tr>
                <td>Total</td>
                <td></td>
                <td></td>
                <td></td>
                <td><strong>{{  number_format($total_service_total, 2) }}</strong></td>
                <td><strong>{{  number_format($total_product_total, 2) }}</strong></td>
                <td><strong>{{  number_format($total_package_total, 2) }}</strong></td>
                <td><strong>{{  number_format($invoice_total_discount, 2) }}</strong></td>
                <td><strong>{{  number_format($total_grand_total, 2) }}</strong></td>
                <td><strong>{{  number_format($total_advance_payment, 2) }}</strong></td>
                <td><strong>{{  number_format($total_debit_amount, 2) }}</strong></td>
                <td><strong>{{  number_format($total_amount_paid, 2) }}</strong></td>
                <td><strong>{{  number_format($total_on_credit_cash, 2) }}</strong></td>
                <td><strong>{{  number_format($total_on_credit_debit, 2) }}</strong></td>
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
    $('.datepicker').datepicker({
      format: 'yyyy-mm-dd',
      autoclose:true
    });
  });
</script>
@endsection