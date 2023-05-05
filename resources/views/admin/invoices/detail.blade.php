@extends('layouts.app')
@section('styles')
  {{-- Print --}}
  <link href="{{ asset('css/print.css') }}" rel="stylesheet">
  <style>
    .print-layout {
      border: 1px solid #2c2c2c;
      outline: 1px solid #2c2c2c;
    }
  </style>
@endsection
@section('content')
  <div class="print-layout">
    <h2 class="title">Makeover Studio</h2>
    <div class="clearfix"></div>
    <div class="underline"></div>
    <h6>Phone: +91-xxx-xxx-xxxx</h6>
    <h6>Email: makeover-studio@gmail.com</h6>
    <hr/>
    <div class="clearfix"></div>
    <div class="row">
      <div class="col">
        <label class="heading">Bill To:</label>
        <div class="underline"></div>
        <?php $final_invoice = $invoice;?>
        <p>{{ $invoice->client->name }}</p>
        <p>{{ $invoice->client->email }}</p>
        <p>{{ $invoice->client->phone_number }}</p>
      </div>
      <div class="col">
        <label class="heading"><strong>Inovice Details:</strong></label>
        <div class="underline"></div>
        <p><strong>Invoice Number:</strong>&nbsp;#{{ $invoice->invoice_number }}</p>
        <p><strong>Invoice Date:</strong>&nbsp;{{ $invoice->bill_date }}</p>
      </div>
    </div>
    <div class="clearfix"></div><br />
    <table class="table">
      <thead class="thead-light">
        <tr class="text-center">
          <th scope="col">#</th>
          <th scope="col">Items</th>
          {{-- <th scope="col">Beautician</th> --}}
          <th scope="col">Type</th>
          <th scope="col">Price</th>
          <th scope="col">Discount</th>
          <th scope="col">Quantity</th>
          <th scope="col">Total</th>
        </tr>
      </thead>
      <tbody>
        <?php $rowCount = 0;?>
        @foreach($invoice->invoice_details as $key=>$invoice)
         <?php $rowCount = $rowCount + 1;?>
          <tr class="text-center">
            <th scope="row">{{ $rowCount }}</th>
            <td>{{ $invoice->name }}</td>
            {{-- <td>{{ $invoice->beautician_name }}</td> --}}
            <td>{{ strtolower($invoice->item_type) }}</td>
            <td>{{ number_format($invoice->price, 2) }}</td>
            <td>{{  $invoice->discount }}</td>
            <td>{{ $invoice->quantity }}</td>
            <td>{{ number_format($invoice->total_price, 2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <div class="row">
       <div class="col-md-7">
          <span><strong>Notes:</strong></span>
          <div class="clearfix"></div>
          <p>{{ ($final_invoice->notes == '' ? 'NA': $final_invoice->notes) }}</p>

          @if($final_invoice->settle_debit_amount > 0)
           <div class="underline"></div>
            <span><strong>Settle Debit Amount:</strong></span>
            <div class="clearfix"></div>
            <span>{{ number_format($final_invoice->settle_debit_amount, 2) }}</span>
          @endif
           @if($final_invoice->client_advance_payment > 0)
           <div class="underline"></div>
            <span><strong>Advance Payment:</strong></span>
            <div class="clearfix"></div>
            <span>{{ number_format($final_invoice->client_advance_payment, 2) }}</span>
          @endif
       </div>
       <div class="col-md-5">
          <div class="underline"></div>
          <h6 class="float-right">Discount: <strong>{{ number_format($final_invoice->total_discount, 2) }}</strong></h6>
          <div class="underline"></div>
          @if($final_invoice->payment_type == 'DEBIT' || ($final_invoice->payment_type == 'ADVANCE_PAYMENT' && $final_invoice->debit_amount > 0))
            <h6 class="float-right">Debit Amount: <strong>{{ number_format($final_invoice->debit_amount, 2) }}</strong></h6>
            <div class="underline"></div>
          @endif
          @if($final_invoice->payment_type == 'ADVANCE_PAYMENT')
            <h6 class="float-right">Advance Payment: <strong>{{ number_format($final_invoice->advance_payment, 2) }}</strong></h6>
            <div class="underline"></div>
          @endif
          <h6 class="float-right">Amount Paid: <strong>{{ number_format($final_invoice->amount_paid, 2) }}</strong></h6>
          <div class="underline"></div>
          @if($final_invoice->payment_type == 'ON_CREDIT+CASH')
            <h6 class="float-right">Cash: <strong>{{ number_format($final_invoice->on_credit_cash, 2) }}</strong></h6>
            <div class="underline"></div>
          @endif
          @if($final_invoice->payment_type == 'ON_CREDIT+DEBIT')
            <h6 class="float-right">Debit Amount: <strong>{{ number_format($final_invoice->on_credit_debit, 2) }}</strong></h6>
            <div class="underline"></div>
          @endif
          <h6 class="float-right">Grand Total: <strong>{{ number_format($final_invoice->grand_total, 2) }}</strong></h6>
          <div class="underline"></div>
       </div>
    </div>
  </div>

  <div class="row mt-4" print-back-row>
    <div class="col-md-5 text-center">
      <a class="btn btn-primary text-center" href="{{ url('/admin/invoices') }}"><i class="fas fa-arrow-alt-circle-left"></i>&nbsp;Back To Invoices</a>
      <button class="btn btn-success" data-print-btn="{{ $final_invoice->invoice_number }}"><i class="fas fa-print"></i>&nbsp;Print</button>
    </div>
  </div>
@endsection
@section('scripts')
<script type="text/javascript">
  $('[data-print-btn]').click(function () {
    var invoiceNumber = $(this).attr('data-print-btn');
    document.title='invoice_'+ invoiceNumber;
    $('[print-back-row]').remove();

    window.print();
  });
</script>
@endsection
