@extends('layouts.app')
@section('styles')
  {{-- Print --}}
  <link href="{{ asset('css/print.css') }}" rel="stylesheet">
@endsection
@section('content')
    <table class="table">
      <thead class="thead-light">
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
  <div class="row mt-4" print-back-row>
    <div class="col-md-5 text-center">
      <a class="btn btn-primary text-center" href="{{ url('/admin/employees') }}"><i class="fas fa-arrow-alt-circle-left"></i>&nbsp;Back To Employees</a>
      <button class="btn btn-success" data-print-btn><i class="fas fa-print"></i>&nbsp;Print</button>
    </div>
  </div>
@endsection
@section('scripts')
<script type="text/javascript">
  $('[data-print-btn]').click(function () {
    $('[print-back-row]').remove();
    window.print();
  });

  $(document).ready(function() {
    $('[print-back-row]').remove();
    window.print();
  });
</script>
@endsection