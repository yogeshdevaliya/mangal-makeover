@extends('layouts.app')
@section('styles')
    {{-- Print --}}
    <link href="{{ asset('css/print.css') }}" rel="stylesheet">
    <style type="text/css">
        .table td,
        .table th {
            font-size: 14px;
        }

        .w-20 {
            width: 20px;
        }

        .w-60 {
            width: 60px;
        }

        .w-70 {
            width: 80px;
        }

        .w-190 {
            width: 190px;
        }
    </style>
@endsection
@section('content')
    <table class="table">
        <thead class="thead-light">
            <tr>
                <th class="w-20">Sr.No</th>
                <th>Date</th>
                <th class="w-190">Client Name</th>
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
            @if (count($invoices) > 0)
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
                @foreach ($invoices as $key => $invoice)
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
                            {{-- <small class="sm-number">({{ $invoice->client->phone_number }})</small> --}}
                        </td>
                        <td>{{ $invoice->client->phone_number }}</td>
                        <td>{{ number_format($invoice->service_total, 2) }}</td>
                        <td>{{ number_format($invoice->product_total, 2) }}</td>
                        <td>{{ number_format($invoice->package_total, 2) }}</td>
                        <td>{{ number_format($invoice->total_discount, 2) }}</td>
                        <td>{{ number_format($invoice->grand_total, 2) }}</td>
                        <td>{{ $invoice->payment_type == 'ADVANCE_PAYMENT' ? number_format($invoice->advance_payment, 2) : '0.00' }}
                        </td>
                        <td>{{ $invoice->payment_type == 'DEBIT' ? number_format($invoice->debit_amount, 2) : '0.00' }}
                        </td>
                        <td>{{ number_format($invoice->amount_paid, 2) }}</td>
                        <td>{{ $invoice->payment_type == 'ON_CREDIT+CASH' ? number_format($invoice->on_credit_cash, 2) : '0.00' }}
                        </td>
                        <td>{{ $invoice->payment_type == 'ON_CREDIT+DEBIT' ? number_format($invoice->on_credit_debit, 2) : '0.00' }}
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td>Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><strong>{{ number_format($total_service_total, 2) }}</strong></td>
                    <td><strong>{{ number_format($total_product_total, 2) }}</strong></td>
                    <td><strong>{{ number_format($total_package_total, 2) }}</strong></td>
                    <td><strong>{{ number_format($invoice_total_discount, 2) }}</strong></td>
                    <td><strong>{{ number_format($total_grand_total, 2) }}</strong></td>
                    <td><strong>{{ number_format($total_advance_payment, 2) }}</strong></td>
                    <td><strong>{{ number_format($total_debit_amount, 2) }}</strong></td>
                    <td><strong>{{ number_format($total_amount_paid, 2) }}</strong></td>
                    <td><strong>{{ number_format($total_on_credit_cash, 2) }}</strong></td>
                    <td><strong>{{ number_format($total_on_credit_debit, 2) }}</strong></td>
                </tr>
            @endif
        </tbody>
    </table>
    </div>
    <div class="row mt-4" print-back-row>
        <div class="col-md-5 text-center">
            <a class="btn btn-primary text-center" href="{{ url('/admin/reports') }}"><i
                    class="fas fa-arrow-alt-circle-left"></i>&nbsp;Back To Reports</a>
            <button class="btn btn-success" data-print-btn><i class="fas fa-print"></i>&nbsp;Print</button>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        $('[data-print-btn]').click(function() {
            $('[print-back-row]').remove();
            window.print();
        });

        $(document).ready(function() {
            // $('[print-back-row]').remove();
            // window.print();
        });
    </script>
@endsection
