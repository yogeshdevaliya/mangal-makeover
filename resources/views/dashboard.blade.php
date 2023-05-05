@extends('layouts.main')
@section('content')
    <div class="float-left">
        <h4><strong>Running Services</strong></h4>
    </div>
    <div class="float-right">
        <a href="{{ url('admin/clients?action=create') }}" class="form-btn">Create Client</a>
    </div>
    <div class="clearfix"></div><br />
    @if (session('res'))
        <div class="alert alert-{{ session('res')['status'] }}" data-dismiss="alert">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>{{ session('res')['message'] }}</strong>
        </div>
        <div class="clearfix"></div><br />
    @endif
    <?php $serviceRowCount = 0; ?>
    <table class="table table-hover table-bordered" table-id>
        <thead>
            <tr>
                <th>Sr. No</th>
                <th>Name</th>
                @role('super_admin')
                    <th>Phone Number</th>
                @endrole
                <th>Running Services & Beautician</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $runnigServiceRowCount = 0; ?>
            @foreach ($all_clients as $key => $client)
                @if ($client->running_services)
                    <?php $runnigServiceRowCount = $runnigServiceRowCount + 1; ?>
                    <tr>
                        <td>{{ $runnigServiceRowCount }}</td>
                        <td>{{ $client->name }}</td>
                        @role('super_admin')
                            <td>{{ $client->phone_number }}</td>
                        @endrole
                        <td>
                            <!-- Edit And Add Services -->
                            @if (count($client->running_services) > 0)
                                <ul class="fs-15">
                                    @foreach ($client->running_services as $key1 => $running_service)
                                        <li>{{ $running_service->name }}&nbsp;<strong>({{ $running_service->beautician_name }})</strong>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span>NA</span>
                            @endif
                        </td>
                        <td class="display-flex">
                            <!-- Edit And Add Services -->
                            @if (count($client->running_services) > 0)
                                <a class="form-btn btn-mini" data-toggle="modal"
                                    data-target="{{ '#model_' . $client->id }}"><i class="mdi mdi-pencil"></i>&nbsp;Edit</a>
                            @else
                                <a class="form-btn btn-mini" data-toggle="modal"
                                    data-target="{{ '#model_' . $client->id }}"><i class="mdi mdi-plus"></i>&nbsp;Add
                                    Services</a>
                            @endif
                            <div class="modal fade" id="{{ 'model_' . $client->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl" role="document">
                                    <div class="modal-content">
                                        <form method="POST"
                                            action="{{ url('admin/client/' . $client->id . '/service/start') }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">
                                                    {{ count($client->running_services) == 0 ? 'Add' : 'Edit' }} Services
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body" id="client_service_modal_{{ $client->id }}">
                                                <h4 class="text-center title-light {{ count($client->running_services) == 0 ? '' : 'd-n' }}"
                                                    id="service_start_title_{{ $client->id }}">Or Start Services!</h4>
                                                <div class="text-right">
                                                    <button type="button" class="btn btn-success" data-add-beautician
                                                        data-client-id="{{ $client->id }}"><i
                                                            class="mdi mdi-plus"></i>&nbsp;Add</button>
                                                </div>
                                                <div class="clearfix"></div><br />
                                                <table
                                                    class="table {{ count($client->running_services) == 0 ? 'd-n' : '' }}"
                                                    id="service_start_table_{{ $client->id }}">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col" class="w-25">Service & Products & Package
                                                            </th>
                                                            <th scope="col">Beautician</th>
                                                            <th scope="col">Quantity</th>
                                                            <th scope="col">Discount</th>
                                                            <th scope="col">Price</th>
                                                            <th scope="col">Total Price</th>
                                                            <th scope="col"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="service_start_body_{{ $client->id }}">
                                                        @if (count($client->running_services) > 0)
                                                            @foreach ($client->running_services as $key1 => $running_service)
                                                                <?php $serviceRowCount = $serviceRowCount + 1; ?>
                                                                <tr data-table-row-{{ $serviceRowCount }}>
                                                                    <td>
                                                                        <input type="hidden" class="form-control"
                                                                            id="item_id_{{ $serviceRowCount }}"
                                                                            name="client_services[{{ $serviceRowCount }}][item_id]"
                                                                            value="{{ $running_service->item_id }}"
                                                                            autocomplete="off">
                                                                        <div class="input-group" data-item-group>
                                                                            <select class="form-control invoice-item-type"
                                                                                id="item_type_{{ $serviceRowCount }}"
                                                                                name="client_services[{{ $serviceRowCount }}][item_type]"
                                                                                data-item-type-search
                                                                                row-id="{{ $serviceRowCount }}" required>
                                                                                <option value="SERVICE"
                                                                                    {{ $running_service->item_type == 'SERVICE' ? 'selected' : '' }}>
                                                                                    Service</option>
                                                                                <option value="PACKAGE"
                                                                                    {{ $running_service->item_type == 'PACKAGE' ? 'selected' : '' }}>
                                                                                    Package</option>
                                                                                <option value="PRODUCT"
                                                                                    {{ $running_service->item_type == 'PRODUCT' ? 'selected' : '' }}>
                                                                                    Product</option>
                                                                            </select>
                                                                            <input type="text" class="form-control w-50"
                                                                                id="item_name_{{ $serviceRowCount }}"
                                                                                name="client_services[{{ $serviceRowCount }}][name]"value="{{ $running_service->name }}"
                                                                                autocomplete="nope" data-item-search
                                                                                row-id="{{ $serviceRowCount }}" required>
                                                                        </div>
                                                                        <span
                                                                            id="item_search_list_loading_{{ $serviceRowCount }}"
                                                                            class="d-n"><i
                                                                                class="fas fa-spinner fa-spin"></i>&nbsp;Loading...</span>
                                                                        <ul class="list-group invoice-item-list d-n"
                                                                            id="item_search_list_{{ $serviceRowCount }}">
                                                                        </ul>
                                                                    </td>
                                                                    <td>
                                                                        <?php $beauticianArr = explode(',', $running_service->beautician_id); ?>
                                                                        <select
                                                                            name="client_services[{{ $serviceRowCount }}][beautician_id][]"
                                                                            class="form-control" services-beautician
                                                                            required multiple>
                                                                            @foreach ($employees as $key => $employee)
                                                                                <option value="{{ $employee->id }}"
                                                                                    {{ in_array($employee->id, $beauticianArr) == 1 ? 'selected' : '' }}>
                                                                                    {{ $employee->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td><input type="number" class="form-control"
                                                                            id="item_quantity_{{ $serviceRowCount }}"
                                                                            name="client_services[{{ $serviceRowCount }}][quantity]"
                                                                            min="1"
                                                                            value="{{ $running_service->quantity }}"
                                                                            onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                                            autocomplete="off" data-item-quantity
                                                                            row-id="{{ $serviceRowCount }}" required></td>
                                                                    <td><input type="number" class="form-control"
                                                                            id="item_discount_{{ $serviceRowCount }}"
                                                                            name="client_services[{{ $serviceRowCount }}][discount]"
                                                                            min="0" step="any"
                                                                            value="{{ $running_service->discount }}"
                                                                            onkeypress="isFloat(event)" autocomplete="off"
                                                                            data-item-discount
                                                                            row-id="{{ $serviceRowCount }}"></td>
                                                                    <td><input type="number" class="form-control"
                                                                            id="item_price_{{ $serviceRowCount }}"
                                                                            name="client_services[{{ $serviceRowCount }}][price]"
                                                                            min="0" step="any"
                                                                            onkeypress="isFloat(event)"
                                                                            value="{{ $running_service->price }}"
                                                                            autocomplete="off" required data-item-price
                                                                            row-id="{{ $serviceRowCount }}"></td>
                                                                    <td><input type="number" class="form-control"
                                                                            id="item_total_price_{{ $serviceRowCount }}"
                                                                            name="client_services[{{ $serviceRowCount }}][total_price]"
                                                                            min="0" step="any"
                                                                            value="{{ $running_service->total_price }}"
                                                                            onkeypress="isFloat(event)" autocomplete="off"
                                                                            data-item-total-price
                                                                            row-id="{{ $serviceRowCount }}" required
                                                                            readonly></td>
                                                                    <td><i class="mdi mdi-close-box fs-20 csr-ptr"
                                                                            data-delete-icon
                                                                            row-id="{{ $serviceRowCount }}"
                                                                            client-id="{{ $client->id }}"></i></td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Close</button>
                                                @if (count($client->running_services) == 0)
                                                    <button type="submit" class="form-btn">Add</button>
                                                @else
                                                    <button type="submit" class="form-btn">Update</button>
                                                @endif
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @if (count($client->running_services) > 0)
                                &nbsp;|&nbsp;
                                <!-- End Services -->
                                <button type="button" class="btn btn-danger btn-sm" client-end-service-popup
                                    client-id="{{ $client->id }}"><i class="mdi mdi-close"></i>&nbsp;End</button>
                                <!-- Modal -->

                                <div class="modal fade" id="end_service_modal_{{ $client->id }}" tabindex="-1"
                                    role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">End Service</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ url('/admin/invoices/create') }}">
                                                <div class="modal-body">
                                                    <label class="modal-title">Client:
                                                        <strong>{{ $client->name }}</strong></label>
                                                    <div class="clearfix"></div>
                                                    <label class="modal-title">Services:
                                                        <?php
                                                        $is_service_discount_applied = 0;
                                                        $grand_total = 0;
                                                        ?>

                                                        @if (count($client->running_services) > 0)
                                                            <ul>
                                                                @foreach ($client->running_services as $r_service)
                                                                    @if ($r_service->discount > 0)
                                                                        <?php
                                                                        $is_service_discount_applied = 1;
                                                                        ?>
                                                                    @endif
                                                                    <li>{{ $r_service->name }}&nbsp;<strong
                                                                            id="client_running_service_id_{{ $r_service->id }}">({{ number_format($r_service->total_price, 2) }}
                                                                            Rs.)</strong></li>
                                                                    <?php
                                                                    $grand_total = $grand_total + $r_service->total_price;
                                                                    ?>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <span>NA</span>
                                                        @endif
                                                    </label>
                                                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                                                    <input type="hidden"
                                                        id="is_service_discount_applied_{{ $client->id }}"
                                                        value="{{ $is_service_discount_applied }}">
                                                    <input type="hidden" id="client_grand_total_{{ $client->id }}"
                                                        value="{{ $grand_total }}">
                                                    <input type="hidden" id="client_total_advance_{{ $client->id }}"
                                                        value="{{ $client->total_advance }}">
                                                    <div class="clearfix"></div>
                                                    <label><strong>Grand Total: <span
                                                                id="client_total_advance_html_{{ $client->id }}">{{ number_format($grand_total, 2) }}
                                                                Rs.</span></strong></label>
                                                    <div class="row">
                                                        <div class="col-md-5">
                                                            <div class="form-group">
                                                                <label for="payment_type"
                                                                    class="col-form-label"><strong>{{ __('Payment Type') }}</strong><span
                                                                        class="required clr-red">*</span></label>
                                                                <select class="form-input form-control"
                                                                    id="payment_type_{{ $client->id }}"
                                                                    name="payment_type" autocomplete="off"
                                                                    client-service-payment-type
                                                                    client-id="{{ $client->id }}" required>
                                                                    <option value="CASH">CASH</option>
                                                                    <option value="CARD">CARD</option>
                                                                    <option value="ONLINE">ONLINE</option>
                                                                    <option value="DEBIT">DEBIT</option>
                                                                    <option value="ADVANCE_PAYMENT">ADVANCE PAYMENT
                                                                    </option>

                                                                    @if ($client->total_advance > 0)
                                                                        @if ($client->total_advance >= $grand_total)
                                                                            <option value="ON_CREDIT">ON CREDIT</option>
                                                                        @else
                                                                            <option value="ON_CREDIT+CASH">ON CREDIT + CASH
                                                                            </option>
                                                                        @endif
                                                                        <option value="ON_CREDIT+DEBIT">ON CREDIT + DEBIT
                                                                        </option>
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-7">
                                                            <div class="row d-n"
                                                                id="client_end_service_row_1_{{ $client->id }}">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <div class="d-n"
                                                                            id="payment_debit_{{ $client->id }}">
                                                                            <label for="payment_type"
                                                                                class="col-form-label"><strong>{{ __('Debit Amount') }}</strong><span
                                                                                    class="required clr-red">*</span></label>
                                                                            <input type="number" step="any"
                                                                                id="client_debit_amount_{{ $client->id }}"
                                                                                class="form-control"
                                                                                onkeypress="isFloat(event)"
                                                                                client-service-debit-amount
                                                                                client-id="{{ $client->id }}">
                                                                        </div>

                                                                        <div class="d-n"
                                                                            id="payment_advance_{{ $client->id }}">
                                                                            <label for="payment_type"
                                                                                class="col-form-label"><strong>{{ __('Advance Payment') }}</strong><span
                                                                                    class="required clr-red">*</span></label>
                                                                            <input type="number" step="any"
                                                                                id="client_advance_payment_{{ $client->id }}"
                                                                                class="form-control"
                                                                                onkeypress="isFloat(event)"
                                                                                client-service-advance-payment
                                                                                client-id="{{ $client->id }}">
                                                                        </div>

                                                                        <div class="d-n"
                                                                            id="payment_discount_{{ $client->id }}">
                                                                            <label for="payment_type"
                                                                                class="col-form-label"><strong>{{ __('Discount') }}</strong><span
                                                                                    class="required clr-red">*</span></label>
                                                                            <input type="number" step="any"
                                                                                id="client_discount_{{ $client->id }}"
                                                                                class="form-control"
                                                                                onkeypress="isFloat(event)"
                                                                                client-service-discount
                                                                                client-id="{{ $client->id }}">
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="payment_type"
                                                                            class="col-form-label"><strong>{{ __('Amount Paid') }}</strong><span
                                                                                class="required clr-red">*</span></label>
                                                                        <input type="number" step="any"
                                                                            id="client_amount_paid_{{ $client->id }}"
                                                                            class="form-control"
                                                                            onkeypress="isFloat(event)"
                                                                            client-service-amount-paid
                                                                            client-id="{{ $client->id }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row d-n"
                                                                id="client_end_service_row_2_{{ $client->id }}">

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="payment_type"
                                                                            class="col-form-label"><strong>{{ __('Amount Paid') }}</strong><span
                                                                                class="required clr-red">*</span></label>
                                                                        <input type="number" step="any"
                                                                            id="on_credit_amount_paid_{{ $client->id }}"
                                                                            class="form-control"
                                                                            onkeypress="isFloat(event)"
                                                                            on-credit-service-amount-paid
                                                                            client-id="{{ $client->id }}">
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="payment_type"
                                                                            class="col-form-label"><strong>{{ __('Remain Advance') }}</strong><span
                                                                                class="required clr-red">*</span></label>
                                                                        <input type="number" step="any"
                                                                            id="on_credit_remain_advance_{{ $client->id }}"
                                                                            class="form-control"
                                                                            onkeypress="isFloat(event)"
                                                                            on-credit-service-remain-advance
                                                                            client-id="{{ $client->id }}">
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6 d-n"
                                                                    id="on_credit_cash_row_{{ $client->id }}">
                                                                    <div class="form-group">
                                                                        <label for="payment_type"
                                                                            class="col-form-label"><strong>{{ __('Cash') }}</strong><span
                                                                                class="required clr-red">*</span></label>
                                                                        <input type="number" step="any"
                                                                            id="on_credit_cash_{{ $client->id }}"
                                                                            class="form-control"
                                                                            onkeypress="isFloat(event)"
                                                                            on-credit-service-cash
                                                                            client-id="{{ $client->id }}">
                                                                        <input type="hidden"
                                                                            id="on_credit_due_cash_{{ $client->id }}"
                                                                            class="form-control">
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6 d-n"
                                                                    id="on_credit_debit_row_{{ $client->id }}">
                                                                    <div class="form-group">
                                                                        <label for="payment_type"
                                                                            class="col-form-label"><strong>{{ __('Debit') }}</strong><span
                                                                                class="required clr-red">*</span></label>
                                                                        <input type="number" step="any"
                                                                            id="on_credit_debit_{{ $client->id }}"
                                                                            class="form-control"
                                                                            onkeypress="isFloat(event)"
                                                                            on-credit-service-debit
                                                                            client-id="{{ $client->id }}">
                                                                        <input type="hidden"
                                                                            id="on_credit_due_debit_{{ $client->id }}"
                                                                            class="form-control">
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <span>Amount Due:
                                                                <strong>{{ number_format($client->total_debit, 2) }}</strong></span>
                                                            <div class="clearfix"></div>
                                                            <span>Total Advance:
                                                                <strong>{{ number_format($client->total_advance, 2) }}</strong></span>
                                                            <div class="clearfix"></div>
                                                            <div class="row">
                                                                <div class="col-md-12 mt-2">
                                                                    @if ($client->total_debit > 0)
                                                                        <div class="custom-control custom-checkbox">
                                                                            <input type="checkbox"
                                                                                class="custom-control-input"
                                                                                name="is_settle_debit"
                                                                                id="is_settle_debit_{{ $client->id }}"
                                                                                value="1"
                                                                                data-client-invoice-settle-debit
                                                                                client-id="{{ $client->id }}"
                                                                                debit-amount="{{ $client->total_debit }}">
                                                                            <label class="custom-control-label"
                                                                                for="is_settle_debit_{{ $client->id }}"><strong>Settle
                                                                                    Debit Amount</strong></label>
                                                                        </div>
                                                                        <div class="row mt-2"
                                                                            id="settle_debit_row_{{ $client->id }}"
                                                                            style="display: none;">
                                                                            <div class="col-md-8">
                                                                                <div class="form-group">
                                                                                    <label for="debit_amount_client"
                                                                                        class="col-form-label"><strong>Debit
                                                                                            Amount:</strong><span
                                                                                            class="required clr-red">*</span></label>
                                                                                    <input type="number"
                                                                                        class="form-control"
                                                                                        onkeypress="isFloat(event)"
                                                                                        name="settle_debit_amount"
                                                                                        id="debit_amount_client_{{ $client->id }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        <div
                                                                            class="custom-control custom-checkbox pt-event">
                                                                            <input type="checkbox"
                                                                                class="custom-control-input"
                                                                                id="is_settle_debit_{{ $client->id }}">
                                                                            <label class="custom-control-label"
                                                                                for="is_settle_debit_{{ $client->id }}"><strong>Settle
                                                                                    Debit Amount</strong></label>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="clearfix"></div>
                                                                <div class="col-md-12 mt-2">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox"
                                                                            class="custom-control-input"
                                                                            name="is_advance_payment"
                                                                            id="is_advance_payment_{{ $client->id }}"
                                                                            value="1"
                                                                            data-client-invoice-advance-payment
                                                                            client-id="{{ $client->id }}">
                                                                        <label class="custom-control-label"
                                                                            for="is_advance_payment_{{ $client->id }}"><strong>Advance
                                                                                Payment</strong></label>
                                                                    </div>
                                                                    <div class="row mt-2"
                                                                        id="advance_payment_row_{{ $client->id }}"
                                                                        style="display: none;">
                                                                        <div class="col-md-8">
                                                                            <div class="form-group">
                                                                                <label for="client_advance_payment"
                                                                                    class="col-form-label"><strong>Advance
                                                                                        Payment:</strong><span
                                                                                        class="required clr-red">*</span></label>
                                                                                <input type="number" min="0"
                                                                                    class="form-control"
                                                                                    onkeypress="isFloat(event)"
                                                                                    name="client_advance_payment"
                                                                                    id="client_direct_advance_payment_{{ $client->id }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="payment_type"
                                                                    class="col-form-label"><strong>{{ __('Notes') }}</strong></label>
                                                                <textarea name="notes" class="form-control" placeholder="Add your notes(optional)"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span class="fs-20 d-n"
                                                        id="client_end_service_loading_{{ $client->id }}"><i
                                                            class="fas fa-spinner fa-spin"></i>&nbsp;Loading...</span>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" name="from" value="save"
                                                        class="form-btn"
                                                        id="client_end_service_btn_save_{{ $client->id }}">Save &
                                                        Close</button>
                                                    <button type="submit" name="from" value="running"
                                                        class="form-btn"
                                                        id="client_end_service_btn_bill_{{ $client->id }}">Generate
                                                        Bill</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            &nbsp;|&nbsp;
                            {{-- client running delete form start --}}
                            <form class="d-b" action="{{ url('admin/client/service/delete') }}" method="POST">
                                @csrf
                                <input type="hidden" name="client_id" value="{{ $client->id }}">
                                <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this running service?'); "><i
                                        class="fas fa-trash"></i></button>
                            </form>
                            {{-- client running delete form over --}}
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <hr />
    <div class="clearfix"></div><br />
    <h4><strong>All Clients</strong></h4>
    <div class="clearfix"></div><br />

    <div class="table-responsive">
        <table class="table table-hover table-bordered" table-id>
            <thead>
                <tr>
                    <th>Sr. No</th>
                    <th>Name</th>
                    @role('super_admin')
                        <th>Phone Number</th>
                        <th>Action</th>
                    @endrole
                    <th>Service</th>
                </tr>
            </thead>
            <tbody>
                <?php $clientRowCount = 0; ?>
                @foreach ($all_clients as $key => $cl)
                    <?php $clientRowCount = $clientRowCount + 1; ?>
                    <tr>
                        <td>{{ $clientRowCount }}</td>
                        <td>{{ $cl->name }}</td>
                        @role('super_admin')
                            <td>{{ $cl->phone_number }}</td>
                            <td class="display-flex">
                                {{-- client edit form start --}}
                                <a href="{{ url('admin/clients/' . $cl->id . '/edit') }}" class="btn btn-primary btn-sm"><i
                                        class="fas fa-pencil-alt"></i>&nbsp;Edit</a>&nbsp;|&nbsp;
                                <form class="d-b" action="{{ url('admin/clients/delete') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="client_id" value="{{ $cl->id }}">
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this client?'); "><i
                                            class="fas fa-trash"></i>&nbsp;Delete</button>
                                </form>
                                &nbsp;|&nbsp;
                                {{-- client delete form start --}}
                                <div class="d-b">
                                    <button type="button" class="btn btn-success btn-sm" data-client-settle-debit
                                        client-id="{{ $cl->id }}" debit-amount="{{ $cl->total_debit }}"
                                        {{ $cl->total_debit <= 0 ? 'disabled' : '' }}><i
                                            class="fas fa-rupee-sign"></i>&nbsp;Settle Debit Amount</button>
                                </div>&nbsp;|&nbsp;
                                {{-- client delete form start --}}
                                <form class="d-b" action="{{ url('admin/client/reset') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="client_id" value="{{ $cl->id }}">
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to reset this client and invoice detail?'); "><i
                                            class="fas fa-redo-alt"></i>&nbsp;Reset</button>
                                </form>
                            </td>
                        @endrole
                        <td>
                            @if ($cl->service != 1)
                                <div class="modal fade" id="employee_modal_{{ $cl->id }}" role="dialog"
                                    aria-labelledby="employee_ar_modal" aria-hidden="true">
                                    <div class="modal-dialog modal-xl" role="document">
                                        <div class="modal-content">
                                            <form method="POST"
                                                action="{{ url('admin/client/' . $cl->id . '/service/start') }}">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="employee_ar_modal">Add Services &
                                                        Beautician</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body" id="client_service_modal_{{ $cl->id }}">
                                                    <h4 class="text-center title-light"
                                                        id="service_start_title_{{ $cl->id }}">Or Start Services!
                                                    </h4>
                                                    <div class="text-right">
                                                        <button type="button" class="btn btn-success" data-add-beautician
                                                            data-client-id="{{ $cl->id }}"><i
                                                                class="mdi mdi-plus"></i>&nbsp;Add</button>
                                                    </div>
                                                    <div class="clearfix"></div><br />
                                                    <table class="table d-n"
                                                        id="service_start_table_{{ $cl->id }}">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col" class="w-25">Service & Products &
                                                                    Package</th>
                                                                <th scope="col">Beautician</th>
                                                                <th scope="col">Quantity</th>
                                                                <th scope="col">Discount</th>
                                                                <th scope="col">Price</th>
                                                                <th scope="col">Total Price</th>
                                                                <th scope="col"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="service_start_body_{{ $cl->id }}">
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="submit" class="form-btn btn-mini"><i
                                                            class="mdi mdi-check"></i>&nbsp;Start</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                    data-target="#employee_modal_{{ $cl->id }}"><i
                                        class="mdi mdi-check"></i>&nbsp;Start</button>
                            @else
                                <h5 class="badge badge-secondary">Running</h5>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <script id="client-service-row-template" type="text/x-handlebars-template">
      <tr data-table-row-@{{ row_count }}>
        <td>
          <input type="hidden" class="form-control" id="item_id_@{{ row_count }}" name="client_services[@{{ row_count }}][item_id]" autocomplete="off">
          <div class="input-group" data-item-group>
            <select class="form-control invoice-item-type" id="item_type_@{{ row_count }}" name="client_services[@{{ row_count }}][item_type]" data-item-type-search row-id="@{{ row_count }}" required>
              <option value="SERVICE" selected>Service</option>
              <option value="PACKAGE">Package</option>
              <option value="PRODUCT">Product</option>
            </select>
            <input type="text" class="form-control w-50"  id="item_name_@{{ row_count }}" name="client_services[@{{ row_count }}][name]" autocomplete="off" data-item-search row-id="@{{ row_count }}" required>
          </div>
          <span id="item_search_list_loading_@{{ row_count }}" class="d-n"><i class="fas fa-spinner fa-spin"></i>&nbsp;Loading...</span>
          <ul class="list-group invoice-item-list d-n" id="item_search_list_@{{ row_count }}">
          </ul>
        </td>
        <td>
          <select name="client_services[@{{ row_count }}][beautician_id][]" class="form-control" services-beautician required multiple>
            @foreach($employees as $key => $employee)
              <option value="{{ $employee->id }}">{{ $employee->name }}</option>
            @endforeach
          </select>
        </td>
        <td><input type="number" class="form-control" id="item_quantity_@{{ row_count }}" name="client_services[@{{ row_count }}][quantity]" min="1" value="1" onkeypress="return event.charCode >= 48 && event.charCode <= 57" autocomplete="off" data-item-quantity row-id="@{{ row_count }}" required></td>
        <td><input type="number" class="form-control" id="item_discount_@{{ row_count }}" name="client_services[@{{ row_count }}][discount]" min="0" step="any" onkeypress="isFloat(event)" autocomplete="off" data-item-discount row-id="@{{ row_count }}"></td>
        <td><input type="number" class="form-control" id="item_price_@{{ row_count }}" name="client_services[@{{ row_count }}][price]" min="0" step="any" onkeypress="isFloat(event)" autocomplete="off" data-item-price row-id="@{{ row_count }}" required></td>
        <td><input type="number" class="form-control" id="item_total_price_@{{ row_count }}" name="client_services[@{{ row_count }}][total_price]" min="0" step="any" onkeypress="isFloat(event)" autocomplete="off" data-item-total-price row-id="@{{ row_count }}" required readonly></td>
        <td><i class="mdi mdi-close-box fs-20 csr-ptr" data-delete-icon row-id="@{{ row_count }}" client-id="@{{ client_id }}"></i></td>
      </tr>
    </script>
    </div>
    <div class="clearfix"></div><br />

    {{-- Birthdays --}}
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h4 class="p0 m0"><i class="mdi mdi-cake"></i>&nbsp;Birthdays</h4>
                </div>
                <div class="card-body">
                    @if (count($birthdays) > 0)
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Phonenumber</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $birthdayRowCount = 0; ?>
                                @foreach ($birthdays as $k => $client)
                                    <?php $birthdayRowCount = $birthdayRowCount + 1; ?>
                                    <tr>
                                        <th scope="row">{{ $birthdayRowCount }}</th>
                                        <td>{{ $client->name }}</td>
                                        <td>{{ $client->phone_number }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <h5 class="title-light">No birthdays found!</h5>
                    @endif
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h4 class="p0 m0"><i class="mdi mdi-cake"></i>&nbsp;Anniversary</h4>
                </div>
                <div class="card-body">
                    @if (count($anniversaries) > 0)
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Phonenumber</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $anniversaryRowCount = 0; ?>
                                @foreach ($anniversaries as $k => $client)
                                    <?php $anniversaryRowCount = $anniversaryRowCount + 1; ?>
                                    <tr>
                                        <th scope="row">{{ $anniversaryRowCount }}</th>
                                        <td>{{ $client->name }}</td>
                                        <td>{{ $client->phone_number }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <h5 class="title-light">No anniversary found!</h5>
                    @endif
                </div>
            </div>
        </div>
        <!-- /Settle Debit Modal Start-->
        @include('include.settle-debit-modal')
        <!-- /Settle Debit Modal Over-->
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        $(function() {
            $("[table-id]").DataTable({
                "ordering": false
            });
            $('[data-services]').select2();
        });

        var serviceRowCount = Number('{{ $serviceRowCount }}') + 1;
    </script>
    <script src="{{ asset('js/dashboard.js') }}" type="text/javascript"></script>
@endsection
