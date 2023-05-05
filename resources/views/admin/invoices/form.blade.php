@extends('layouts.main')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h4>
      <strong>{{ ($mode == 'edit' ? 'Edit' : 'Add') }} Invoice</strong>
    </h4>
  </section>
  <div class="clearfix"></div>
  <br />
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <!-- Horizontal Form -->
        <div class="box box-warning">
          <div class="box-header with-border">
            <h5 class="box-title">Generate new bill</h5>
            <div class="clearfix"></div>
            <br />
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            @if (session('res'))
              <div class="alert alert-{{ session('res')['status'] }}" data-dismiss="alert">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>{{ session('res')['message'] }}</strong>
              </div>
            @endif
            <!-- form start -->
            <form method="POST" action="{{ url('admin/invoices/add') }}" enctype="multipart/form-data">
              @csrf
              <input type="hidden" name="client_id" class="form-control" value="{{ ($mode == 'add'  ? '' : $client->id) }}" id="client_id">
              <input type="hidden" name="invoice_id" id="invoice_id" value="{{ ($mode == 'edit' ? $invoice->id : '') }}" class="form-control">
              <input type="hidden" name="mode" id="mode" value="{{ $mode }}" class="form-control">
              <div class="row">
                <div class="col-md-10">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="client_name" class="col-form-label"><strong>{{ __('Client Name') }}</strong><span class="required clr-red">*</span></label>
                        @if($mode == 'add')
                          <input type="text" name="client_name" class="form-input form-control" id="client_name" placeholder="Client Name" autocomplete="off" required data-client-name>
                        @else
                         <input type="text" name="client_name" class="form-input form-control" id="client_name" placeholder="Client Name" value="{{ $client->name }}" autocomplete="off" required>
                        @endif
                        <ul class="list-group invoice-client-list d-n" data-client-list></ul>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="phone_number" class="col-form-label"><strong>{{ __('Phone Number') }}</strong><span class="required clr-red">*</span></label>
                        <input type="phone_number" name="phone_number" value="{{ ($mode == 'add'  ? '' : $client->phone_number) }}" class="form-input form-control" id="phone_number" placeholder="Phone Number" autocomplete="off" onkeypress="return event.charCode >= 48 && event.charCode <= 57" required>
                        <span class="invalid-feedback required-error d-n" role="alert" id="is_phone_number_error">
                          <strong>The phone number has already been taken.</strong>
                        </span>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="gender" class="col-form-label"><strong>{{ __('Gender') }}</strong><span class="required clr-red">*</span></label>
                        <select class="form-input form-control" name="gender" id="gender" autocomplete="off" required>
                          <option {{ ($mode == 'add' ? '' : ($client->gender == 'MALE' ? 'selected' : '')) }} value="MALE">Male</option>
                          <option {{ ($mode == 'add' ? '' : ($client->gender == 'FEMALE' ? 'selected' : '')) }} value="FEMALE">Female</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="payment_type" class="col-form-label"><strong>{{ __('Payment Type') }}</strong><span class="required clr-red">*</span></label>
                        <select class="form-input form-control" name="payment_type" id="payment_type" autocomplete="off" required payment-type>
                          <option value="CASH" {{ ($mode == 'edit'  ? ($invoice->payment_type == 'CASH' ? 'selected' : '') : '') }}>CASH</option>
                          <option value="CARD" {{ ($mode == 'edit'  ? ($invoice->payment_type == 'CARD' ? 'selected' : '') : '') }}>CARD</option>
                          <option value="ONLINE" {{ ($mode == 'edit'  ? ($invoice->payment_type == 'ONLINE' ? 'selected' : '') : '') }}>ONLINE</option>
                          <option value="DEBIT" {{ ($mode == 'edit'  ? ($invoice->payment_type == 'DEBIT' ? 'selected' : '') : '') }}>DEBIT</option>
                          <option value="ADVANCE_PAYMENT" {{ ($mode == 'edit'  ? ($invoice->payment_type == 'ADVANCE_PAYMENT' ? 'selected' : '') : '') }}>ADVANCE PAYMENT</option>
                          @if($mode == 'edit')
                            @if($invoice->payment_type == 'ON_CREDIT+CASH')
                              <option value="ON_CREDIT+CASH" {{ ($mode == 'edit'  ? ($invoice->payment_type == 'ON_CREDIT+CASH' ? 'selected' : '') : '') }}>ON CREDIT + CASH</option>
                            @else
                              <option value="ON_CREDIT" {{ ($mode == 'edit'  ? ($invoice->payment_type == 'ON_CREDIT' ? 'selected' : '') : '') }}>ON CREDIT</option>
                            @endif

                            @if($invoice->payment_type == 'ON_CREDIT+DEBIT')
                               <option value="ON_CREDIT+DEBIT" {{ ($mode == 'edit'  ? ($invoice->payment_type == 'ON_CREDIT+DEBIT' ? 'selected' : '') : '') }}>ON CREDIT + DEBIT</option>
                            @endif
                          @else
                            <option value="ON_CREDIT" {{ ($mode == 'edit'  ? ($invoice->payment_type == 'ON_CREDIT' ? 'selected' : '') : '') }}>ON CREDIT</option>
                          @endif
                        </select>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="billing_date" class="col-form-label"><strong>{{ __('Billing Date') }}</strong><span class="required clr-red">*</span></label>
                        <input type="text" name="billing_date" class="form-input form-control datepicker" id="billing_date" placeholder="Billing Date" value="{{ ($mode == 'edit'  ? \Carbon\Carbon::parse($invoice->bill_date)->format('Y-m-d') : \Carbon\Carbon::now()->format('Y-m-d')) }}" autocomplete="off" required>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="birthdate" class="col-form-label"><strong>{{ __('Birthdate') }}</strong></label>
                        <input type="text" name="birthdate" class="form-input form-control datepicker" id="birthdate" placeholder="Birthdate" value="{{ ($mode == 'add' ? '' : ( $client->dob == '' ? '' : $client->dob)) }}" autocomplete="off">
                      </div>
                    </div>
                    {{--
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="bill_time" class="col-form-label"><strong>{{ __('Billing Time') }}</strong><span class="required clr-red">*</span></label>
                        <input type="time" name="bill_time" class="form-input form-control" id="bill_time" placeholder="Billing Time" autocomplete="off" required>
                      </div>
                    </div>
                    --}}
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="notes" class="col-form-label"><strong>Notes</strong></label>
                    <textarea id="notes" name="notes" class="form-input txtarea" placeholder="Billing Notes">{{ ($mode == 'edit' ? $invoice->notes : '') }}</textarea>
                  </div>
                </div>
              </div>
              <div class="clearfix"></div>
              <br />
              <div class="row">
                <div class="col-md-12">
                  <table class="table" id="invoiceTable">
                    <thead>
                      <tr>
                        <th scope="col" class="w-3">Sl.No.</th>
                        <th scope="col" class="w-40">Service & Products & Package</th>
                        <th scope="col" class="w-15">Beautician</th>
                        <th scope="col" class="">Quantity</th>
                        <th scope="col" class="">Discount</th>
                        <th scope="col" class="w-15">Price</th>
                        <th scope="col" class="w-15">Total Price</th>
                        <th scope="col"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $r_grand_total = 0;
$rowCount = 0;
$arrKey = 0;
?>
                      @if($mode == 'running' && count($running_services) > 0)
                        @foreach($running_services as $key => $running_service)
                            <?php
$rowCount = $rowCount + 1;
$arrKey = $key;
$r_grand_total = $r_grand_total + $running_service->total_price;
?>
                          <tr data-table-row-{{ $rowCount }}>
                            <td><strong>{{ $rowCount }}</strong><input type="hidden" class="form-control" id="item_id_{{ $rowCount }}" name="invoice_detail[{{ $arrKey }}][item_id]" value="{{ $running_service->item_id }}" autocomplete="off"></td>
                            <td>
                              <div class="input-group" data-item-group>
                                <select class="form-control invoice-item-type" id="item_type_{{ $rowCount }}" name="invoice_detail[{{ $arrKey }}][item_type]" data-item-type-search row-id="{{ $rowCount }}" required>
                                  <option value="SERVICE" {{ ($running_service->item_type == 'SERVICE' ? 'selected' : '') }}>Service</option>
                                  <option value="PACKAGE" {{ ($running_service->item_type == 'PACKAGE' ? 'selected' : '') }}>Package</option>
                                  <option value="PRODUCT" {{ ($running_service->item_type == 'PRODUCT' ? 'selected' : '') }}>Product</option>
                                </select>
                                <input type="text" class="form-control w-50"  id="item_name_{{ $rowCount }}" name="invoice_detail[{{ $arrKey }}][name]" value="{{ $running_service->name }}" autocomplete="nope" data-item-search row-id="{{ $rowCount }}" required>
                              </div>
                              <span id="item_search_list_loading_{{ $rowCount }}" class="d-n"><i class="fas fa-spinner fa-spin"></i>&nbsp;Loading...</span>
                              <ul class="list-group invoice-item-list d-n" id="item_search_list_{{ $rowCount }}">
                              </ul>
                            </td>
                            <td>
                              <?php $beauticianArr = explode(',', $running_service->beautician_id);?>

                             <select name="invoice_detail[{{ $arrKey }}][beautician_id][]" class="form-control" services-beautician required multiple>
                                @foreach($employees as $key => $employee)
                                  <option value="{{ $employee->id }}" {{ (in_array($employee->id, $beauticianArr) == 1 ? 'selected' : '')  }}>{{ $employee->name }}</option>
                                @endforeach
                              </select>
                            </td>
                            <td><input type="number" class="form-control" id="item_quantity_{{ $rowCount }}" name="invoice_detail[{{ $arrKey }}][quantity]" min="1" value="{{ $running_service->quantity }}" autocomplete="off" onkeypress="return event.charCode >= 48 && event.charCode <= 57" required data-item-quantity row-id="{{ $rowCount }}"></td>

                            <td><input type="number" class="form-control" id="item_discount_{{ $rowCount }}" name="invoice_detail[{{ $arrKey }}][discount]" min="0" value="{{ $running_service->discount }}" onkeypress="isFloat(event)" autocomplete="off" data-item-discount row-id="{{ $rowCount }}"></td>

                            <td><input type="number" class="form-control" id="item_price_{{ $rowCount }}" name="invoice_detail[{{ $arrKey }}][price]" value="{{ $running_service->price }}" onkeypress="isFloat(event)" step="any"  min="0" autocomplete="off" required data-item-price row-id="{{ $rowCount }}"></td>
                            <td><input type="number" value="{{ $running_service->total_price }}" class="form-control" id="item_total_price_{{ $rowCount }}" name="invoice_detail[{{ $arrKey }}][total_price]" autocomplete="off" required readonly></td>
                            @if($arrKey != 0)
                              <td><i class="mdi mdi-close-box fs-20 csr-ptr" data-delete-icon row-id="{{ $rowCount }}"></i></td>
                            @endif
                          </tr>
                        @endforeach
                      @elseif($mode == 'edit')
                        @foreach($invoice->invoice_details as $key => $invoiceDetail)
                            <?php
$rowCount = $rowCount + 1;
$arrKey = $key;
$r_grand_total = $r_grand_total + $invoiceDetail->total_price;
?>
                          <tr data-table-row-{{ $rowCount }}>
                             <td><strong>{{ $rowCount }}</strong><input type="hidden" class="form-control" id="item_id_{{ $rowCount }}" name="invoice_detail[{{ $arrKey }}][item_id]" value="{{ $invoiceDetail->item_id }}" autocomplete="off"></td>
                            <td>
                              <div class="input-group" data-item-group>
                                <select class="form-control invoice-item-type" id="item_type_{{ $rowCount }}" name="invoice_detail[{{ $arrKey }}][item_type]" data-item-type-search row-id="{{ $rowCount }}" required>
                                  <option value="SERVICE" {{ ($invoiceDetail->item_type == 'SERVICE' ? 'selected' : '') }}>Service</option>
                                  <option value="PACKAGE" {{ ($invoiceDetail->item_type == 'PACKAGE' ? 'selected' : '') }}>Package</option>
                                  <option value="PRODUCT" {{ ($invoiceDetail->item_type == 'PRODUCT' ? 'selected' : '') }}>Product</option>
                                </select>
                                <input type="text" class="form-control w-50"  id="item_name_{{ $rowCount }}" name="invoice_detail[{{ $arrKey }}][name]" value="{{ $invoiceDetail->name }}" autocomplete="nope" data-item-search row-id="{{ $rowCount }}" required>
                              </div>
                              <span id="item_search_list_loading_{{ $rowCount }}" class="d-n"><i class="fas fa-spinner fa-spin"></i>&nbsp;Loading...</span>
                              <ul class="list-group invoice-item-list d-n" id="item_search_list_{{ $rowCount }}">
                              </ul>
                            </td>
                            <td>
                              <?php $beauticianArr = explode(',', $invoiceDetail->beautician_id);?>
                             <select name="invoice_detail[{{ $arrKey }}][beautician_id][]" class="form-control" services-beautician required multiple>
                                @foreach($employees as $key => $employee)
                                  <option value="{{ $employee->id }}" {{ (in_array($employee->id, $beauticianArr) == 1 ? 'selected' : '')  }}>{{ $employee->name }}</option>
                                @endforeach
                              </select>
                            </td>
                            <td><input type="number" class="form-control" id="item_quantity_{{ $rowCount }}" name="invoice_detail[{{ $arrKey }}][quantity]" min="1" value="{{ $invoiceDetail->quantity }}" autocomplete="off" onkeypress="return event.charCode >= 48 && event.charCode <= 57" required data-item-quantity row-id="{{ $rowCount }}"></td>


                            <td><input type="number" class="form-control" id="item_discount_{{ $rowCount }}" name="invoice_detail[{{ $arrKey }}][discount]" value="{{ $invoiceDetail->discount }}" onkeypress="isFloat(event)" min="0" step="any" autocomplete="off" data-item-discount row-id="{{ $rowCount }}"></td>

                            <td><input type="number" class="form-control" id="item_price_{{ $rowCount }}" name="invoice_detail[{{ $arrKey }}][price]" value="{{ $invoiceDetail->price }}" onkeypress="isFloat(event)" step="any"  min="0" autocomplete="off" required data-item-price row-id="{{ $rowCount }}"></td>

                            <td><input type="number" value="{{ $invoiceDetail->total_price }}" class="form-control" id="item_total_price_{{ $rowCount }}" step="any" name="invoice_detail[{{ $arrKey }}][total_price]" min="0" autocomplete="off" required readonly></td>
                            @if($arrKey != 0)
                              <td><i class="mdi mdi-close-box fs-20 csr-ptr" data-delete-icon row-id="{{ $rowCount }}"></i></td>
                            @endif
                          </tr>
                        @endforeach
                      @else
                        <tr data-table-row-1>
                          <td><strong>1</strong><input type="hidden" class="form-control" id="item_id_1" name="invoice_detail[0][item_id]" autocomplete="off"></td>
                          <td>
                            <div class="input-group" data-item-group>
                              <select class="form-control invoice-item-type" id="item_type_1" name="invoice_detail[0][item_type]" data-item-type-search row-id="1" required>
                                <option value="SERVICE" selected>Service</option>
                                <option value="PACKAGE">Package</option>
                                <option value="PRODUCT">Product</option>
                              </select>
                              <input type="text" class="form-control w-50" id="item_name_1" name="invoice_detail[0][name]" autocomplete="nope" data-item-search row-id="1" required>
                            </div>
                            <span id="item_search_list_loading_1" class="d-n"><i class="fas fa-spinner fa-spin"></i>&nbsp;Loading...</span>
                            <ul class="list-group invoice-item-list d-n" id="item_search_list_1">
                            </ul>
                          </td>
                          <td>
                            <select name="invoice_detail[0][beautician_id][]" class="form-control" services-beautician required multiple>
                              @foreach($employees as $key => $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                              @endforeach
                            </select>
                          </td>
                          <td><input type="number" class="form-control" id="item_quantity_1" name="invoice_detail[0][quantity]" min="1" value="1" autocomplete="off" onkeypress="return event.charCode >= 48 && event.charCode <= 57" required data-item-quantity row-id="1"></td>

                          <td><input type="number" class="form-control" id="item_discount_1" name="invoice_detail[0][discount]" min="0" step="any"  autocomplete="off" onkeypress="isFloat(event)" data-item-discount row-id="1"></td>

                          <td><input type="number" class="form-control" id="item_price_1" name="invoice_detail[0][price]" min="0" step="any" autocomplete="off" onkeypress="isFloat(event)" required data-item-price row-id="1"></td>

                          <td><input type="number" class="form-control" id="item_total_price_1" name="invoice_detail[0][total_price]" min="0" step="any" autocomplete="off" required readonly></td>
                          <td></td>
                        </tr>
                      @endif

                      <tr>
                        <td colspan="3">
                          <input type="hidden" name="client_total_debit" id="client_total_debit" value="{{ ($mode == 'add' ? 0 : $client->total_debit) }}">

                          @if($mode == 'edit')
                            <?php $clientTotalAdvance = $client->total_advance;?>
                            @if($invoice->payment_type == 'ADVANCE_PAYMENT')
                              <?php
$paymentHistory = json_decode($invoice->payment_history);
$clientTotalAdvance = $clientTotalAdvance - $paymentHistory->advance_payment_credit;
?>
                            @elseif($invoice->payment_type == 'ON_CREDIT')
                             <?php $clientTotalAdvance = $clientTotalAdvance + $invoice->grand_total;?>
                            @elseif($invoice->payment_type == 'ON_CREDIT+CASH')
                              <?php
$paymentHistory = json_decode($invoice->payment_history);
$clientTotalAdvance = $clientTotalAdvance + $paymentHistory->advance_payment_debit - $paymentHistory->advance_payment_credit;
?>
@elseif($invoice->payment_type == 'ON_CREDIT+DEBIT')
                              <?php
$paymentHistory = json_decode($invoice->payment_history);
$clientTotalAdvance = $clientTotalAdvance + $paymentHistory->advance_payment_debit;
?>
                            @endif
                          @elseif($mode == 'running')
                             <?php $clientTotalAdvance = $client->total_advance;?>
                          @endif
                          <input type="hidden" name="client_total_advance" id="client_total_advance" value="{{ ($mode == 'add' ? '' : $clientTotalAdvance) }}">
                          <span class="{{ ($mode == 'add' ? 'd-n' : '') }}" client-total-debit-html>Amount Due: <strong client-total-debit>{{ ($mode == 'add' ? '-' : number_format($client->total_debit, 2)) }}</strong></span>
                          <div class="clearfix"></div>
                          <span class="{{ ($mode == 'add' ? 'd-n' : '') }}" client-total-advance-html>Total Advance: <strong client-total-advance>{{ ($mode == 'add' ? '-' : number_format($client->total_advance, 2)) }}</strong></span>
                        </td>
                        <td colspan="7" class="text-right"><button type="button" class="form-btn btn-sm" btn-row-add>Add Row</button></td>
                      </tr>

                      <tr class="d-n" advance-payment-row>
                        <td colspan="5" class="text-right">
                          <h6 class="mt-10"><strong>Advance Payment</strong></h6>
                        </td>
                        <td colspan="2">
                          <input type="number" step="any" name="advance_payment" id="advance_payment" value="{{ ($mode == 'edit'  ? ($invoice->payment_type == 'ADVANCE_PAYMENT' ? $invoice->amount_paid : '') : '') }}" class="form-control" onkeypress="isFloat(event)">
                        </td>
                      </tr>

                      <tr class="d-n" debit-amount-row>
                        <td colspan="5" class="text-right">
                          <h6 class="mt-10"><strong>Debit Amount</strong></h6>
                        </td>
                        <td colspan="2">
                          <input type="number" step="any" name="debit_amount" id="debit_amount" value="{{ ($mode == 'edit'  ? ($invoice->payment_type == 'DEBIT' ? $invoice->debit_amount : '') : '') }}" class="form-control" onkeypress="isFloat(event)">
                        </td>
                      </tr>

                      <tr class="d-n" amount-paid-row>
                        <td colspan="5" class="text-right">
                          <h6 class="mt-10"><strong>Amount Paid</strong></h6>
                        </td>
                        <td colspan="2">
                          <input type="number" step="any" name="amount_paid" id="amount_paid" value="{{ ($mode == 'edit'  ? $invoice->amount_paid : '') }}" class="form-control" onkeypress="isFloat(event)">
                        </td>
                      </tr>

                      <tr class="d-n" remain-total-advance-row>
                        <td colspan="5" class="text-right">
                          <h6 class="mt-10"><strong>Remain Advance</strong></h6>
                        </td>
                        <td colspan="2">
                          <input type="number" step="any" name="remain_total_advance" id="remain_total_advance" value="{{ ($mode == 'edit'  ? ($invoice->payment_type == 'ON_CREDIT' ? $client->total_advance : '0') : '') }}" class="form-control" onkeypress="isFloat(event)" readonly>
                        </td>
                      </tr>

                      <tr class="d-n" on-credit-cash-row>
                        <td colspan="5" class="text-right">
                          <h6 class="mt-10"><strong>Cash</strong></h6>
                        </td>
                        <td colspan="2">
                          <input type="number" step="any" name="on_credit_cash" id="on_credit_cash" value="{{ ($mode == 'edit'  ? ($invoice->payment_type == 'ON_CREDIT+CASH' ? $invoice->on_credit_cash : '') : '') }}" class="form-control" onkeypress="isFloat(event)">
                          <input type="hidden" name="on_credit_due_cash" id="on_credit_due_cash" class="form-control" value="{{ ($mode == 'edit' ? ($invoice->payment_type == 'ON_CREDIT+CASH' ? $paymentHistory->on_credit_due_cash : '') : '') }}">
                        </td>
                      </tr>

                      <tr class="d-n" on-credit-debit-row>
                        <td colspan="5" class="text-right">
                          <h6 class="mt-10"><strong>Debit</strong></h6>
                        </td>
                        <td colspan="2">
                          <input type="number" step="any" name="on_credit_debit" id="on_credit_debit" value="{{ ($mode == 'edit'  ? ($invoice->payment_type == 'ON_CREDIT+DEBIT' ? $invoice->on_credit_debit : '') : '') }}" class="form-control" onkeypress="isFloat(event)">
                          <input type="hidden" name="on_credit_due_debit" id="on_credit_due_debit" class="form-control" value="{{ ($mode == 'edit' ? ($invoice->payment_type == 'ON_CREDIT+DEBIT' ? $paymentHistory->on_credit_due_debit : '') : '') }}">
                        </td>
                      </tr>

                      <tr>
                        <td>
                          <h6 class="mt-10"><strong>Discount</strong></h6>
                        </td>
                        <td>
                          <input type="number" step="any" name="total_discount" id="total_discount" class="form-control w-40" value="{{ ($mode == 'edit' ? $discount : '') }}" total-discount>
                        </td>
                        <td colspan="3" class="text-right">
                          <h6 class="mt-10"><strong>Grand Total</strong></h6>
                        </td>
                        <td colspan="2">
                          <input type="number" step="any" name="grand_total" id="grand_total" value="{{ ($mode == 'edit' ? $invoice->grand_total : '') }}" class="form-control" required readonly grand-total>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="row">
                <div class="col-md-3">
                  @if($mode != 'edit')
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" name="is_settle_debit" id="is_settle_debit" value="1" data-client-invoice-settle-debit client-id="" debit-amount="">
                      <label class="custom-control-label" for="is_settle_debit"><strong>Settle Debit Amount</strong></label>
                    </div>
                  @endif
                  <div class="row mt-2" id="settle_debit_row" style="display: none;">
                    <div class="col-md-8">
                      <div class="form-group">
                        <label for="debit_amount_client" class="col-form-label"><strong>Debit Amount:</strong><span class="required clr-red">*</span></label>
                        <input type="number" class="form-control" onkeypress="isFloat(event)" name="settle_debit_amount" id="debit_amount_client" onkeyup="checkRemainDebitAmount()">
                        <span class="clr-green d-n" id="client_remain_debit_amount"></span>
                      </div>
                    </div>
                  </div>
                </div>
                 <div class="col-md-3">
                  @if($mode != 'edit')
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" name="is_advance_payment" id="is_advance_payment" value="1" data-client-invoice-advance_payment>
                      <label class="custom-control-label" for="is_advance_payment"><strong>Advance Payment</strong></label>
                    </div>
                  @endif
                  <div class="row mt-2" id="advance_payment_row" style="display: none;">
                    <div class="col-md-8">
                      <div class="form-group">
                        <label for="client_advance_payment" class="col-form-label"><strong>Advance Payment:</strong><span class="required clr-red">*</span></label>
                        <input type="number" min="0" class="form-control" onkeypress="isFloat(event)" name="client_advance_payment" id="client_advance_payment">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 text-right">
                    <div class="overlay d-n" id="loading">
                      <i class="fas fa-spinner fa-spin"></i>&nbsp;Loading...
                    </div>
                    <button type="submit" name="submit" value="generate_bill" class="form-btn" id="generate_bill_btn"><i class="mdi mdi-file-pdf"></i>&nbsp;Generate Bill</button>
                    <button type="submit" name="submit" value="save" class="form-btn" id="bill_btn"><i class="mdi mdi-printer"></i>&nbsp;Save</button>
                    <button type="button" class="form-btn" id="sms_btn"><i class="mdi mdi-message"></i>&nbsp;SMS</button>
                </div>
              </div>
            </form>
            <script id="invoice-table-row-template" type="text/x-handlebars-template">
              <tr data-table-row-@{{ row_count }}>
                <td><strong>@{{ row_count }}</strong><input type="hidden" class="form-control" id="item_id_@{{ row_count }}" name="invoice_detail[@{{ array_key }}][item_id]" autocomplete="off"></td>
                <td>
                  <div class="input-group" data-item-group>
                    <select class="form-control invoice-item-type" id="item_type_@{{ row_count }}" name="invoice_detail[@{{ array_key }}][item_type]" data-item-type-search row-id="@{{ row_count }}" required>
                      <option value="SERVICE" selected>Service</option>
                      <option value="PACKAGE">Package</option>
                      <option value="PRODUCT">Product</option>
                    </select>
                    <input type="text" class="form-control w-50"  id="item_name_@{{ row_count }}" name="invoice_detail[@{{ array_key }}][name]" autocomplete="nope" data-item-search row-id="@{{ row_count }}" required>
                  </div>
                  <span id="item_search_list_loading_@{{ row_count }}" class="d-n"><i class="fas fa-spinner fa-spin"></i>&nbsp;Loading...</span>
                  <ul class="list-group invoice-item-list d-n" id="item_search_list_@{{ row_count }}">
                  </ul>
                </td>
                <td>
                  <select name="invoice_detail[@{{ array_key }}][beautician_id][]" class="form-control" services-beautician required multiple>
                    @foreach($employees as $key => $employee)
                      <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                    @endforeach
                  </select>
                </td>
                <td><input type="number" class="form-control" id="item_quantity_@{{ row_count }}" name="invoice_detail[@{{ array_key }}][quantity]" min="1" value="1" autocomplete="off" onkeypress="return event.charCode >= 48 && event.charCode <= 57" required data-item-quantity row-id="@{{ row_count }}"></td>

                <td><input type="number" class="form-control" id="item_discount_@{{ row_count }}" name="invoice_detail[@{{ array_key }}][discount]" onkeypress="isFloat(event)" autocomplete="off" min="0" step="any" data-item-discount row-id="@{{ row_count }}"></td>

                <td><input type="number" class="form-control" id="item_price_@{{ row_count }}" name="invoice_detail[@{{ array_key }}][price]" onkeypress="isFloat(event)" min="0" step="any" autocomplete="off" required data-item-price row-id="@{{ row_count }}"></td>

                <td><input type="number" class="form-control" id="item_total_price_@{{ row_count }}" name="invoice_detail[@{{ array_key }}][total_price]" min="0" step="any" autocomplete="off" required readonly></td>
                <td><i class="mdi mdi-close-box fs-20 csr-ptr" data-delete-icon row-id="@{{ row_count }}"></i></td>
              </tr>
            </script>
          </div>
          <!-- /.box -->
        </div>
        <!--/.col (right) -->
      </div>
      <!-- /.row -->
    </div>
  </section>
  <!-- /.content -->
</div>
</section>
@endsection
@section('scripts')
<script type="text/javascript">
var invoiceClientsList = [];

var rowCountArr = [];
var invoiceItemsList = [];
var invoiceItemsArr = [];

var rowCount = Number('{{ $rowCount }}') + 1;
var rowLoopEnd = (rowCount == 1 ? 1 : rowCount - 1);
var arrKey = Number('{{ $arrKey }}');

for(var i = 1; i <= rowLoopEnd; i++){
  rowCountArr.push(i);
}

var isUpdateGrandTotal = 0;
var isUpdatePaymentType = 0;

@if($mode == 'running')
  @if(!empty(Request::get('is_settle_debit')))
    $('#is_settle_debit').prop('checked', true);
    var debitAmount = Number($('#client_total_debit').val());
    $('#debit_amount_client').val(debitAmount).attr({
      "max" : debitAmount,
      "min" : 0,
    });
    $('#debit_amount_client').prop('required', true);
    $('#settle_debit_row').slideToggle();
  @endif

  @if(!empty(Request::get('is_advance_payment')))
    $('#is_advance_payment').prop('checked', true);
    $('#client_advance_payment').val({{ Request::get('client_advance_payment') }}).prop('required', true);
    $('#advance_payment_row').slideToggle();
  @endif
@endif

@if(!empty(Request::get('payment_type')))
  @if(Request::get('payment_type') == 'CASH' || Request::get('payment_type') == 'CARD' || Request::get('payment_type') == 'ONLINE')
    <?php $totalDiscount = (Request::get('discount') == '' ? 0 : Request::get('discount'));?>
    @if($totalDiscount > 0)
      $('#total_discount').val(Number('{{ $totalDiscount }}').toFixed(2));
      $('[data-item-discount]').val('0.00');
    @endif
  @endif

  @if(Request::get('payment_type') == 'DEBIT')
    $('#amount_paid').val(Number('{{ (Request::get('amount_paid') == '' ? 0 : Request::get('amount_paid')) }}').toFixed(2));
    $('#debit_amount').val(Number('{{ (Request::get('debit_amount') == '' ? 0 : Request::get('debit_amount')) }}').toFixed(2));
    isUpdatePaymentType = 1;
  @endif

  @if(Request::get('payment_type') == 'ADVANCE_PAYMENT')
    $('#amount_paid').val(Number('{{ (Request::get('amount_paid') == '' ? 0 : Request::get('amount_paid')) }}').toFixed(2));
    $('#advance_payment').val(Number('{{ (Request::get('advance_payment') == '' ? 0 : Request::get('advance_payment'))  }}').toFixed(2));
    isUpdatePaymentType = 1;
  @endif

  @if(Request::get('payment_type') == 'ON_CREDIT' || Request::get('payment_type') == 'ON_CREDIT+CASH' || Request::get('payment_type') == 'ON_CREDIT+DEBIT')
    $('#amount_paid').val(Number('{{ (Request::get('amount_paid') == '' ? 0 : Request::get('amount_paid')) }}').toFixed(2));
    $('#remain_total_advance').val(Number('{{ (Request::get('remain_total_advance') == '' ? 0 : Request::get('remain_total_advance'))  }}').toFixed(2));

    @if(Request::get('payment_type') == 'ON_CREDIT+CASH')
      $('#on_credit_cash').val(Number('{{ (Request::get('on_credit_cash') == '' ? 0 : Request::get('on_credit_cash')) }}').toFixed(2));
      $('#on_credit_due_cash').val(Number('{{ (Request::get('on_credit_due_cash') == '' ? 0 : Request::get('on_credit_due_cash')) }}').toFixed(2));
      $('[payment-type]').append('<option value="ON_CREDIT+CASH" selected>ON CREDIT + CASH</option>');
    @endif
    @if(Request::get('payment_type') == 'ON_CREDIT+DEBIT')
      $('#on_credit_debit').val(Number('{{ (Request::get('on_credit_debit') == '' ? 0 : Request::get('on_credit_debit')) }}').toFixed(2));
       $('#on_credit_due_debit').val(Number('{{ (Request::get('on_credit_due_debit') == '' ? 0 : Request::get('on_credit_due_debit')) }}').toFixed(2));
      $('[payment-type]').append('<option value="ON_CREDIT+DEBIT" selected>ON CREDIT + DEBIT</option>');
    @endif
    isUpdatePaymentType = 1;
  @endif

  $('[payment-type]').val('{{ Request::get('payment_type') }}');
@endif

@if($mode == 'edit')
  @if($invoice->payment_type != 'CASH' && $invoice->payment_type != 'CARD' && $invoice->payment_type != 'ONLINE')
   isUpdatePaymentType = 1;
  @endif
@endif

@if($mode == 'running' && count($running_services) > 0)
  isUpdateGrandTotal = 1;
@endif
</script>
<script src="{{ asset('js/invoice-form.js') }}" type="text/javascript"></script>
@endsection