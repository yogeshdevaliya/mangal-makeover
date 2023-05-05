$('[services-beautician]').select2({
  placeholder: "Select Beautician",
  allowClear: true
});

$('[data-target]').on('click', function() {
  var model = $(this).attr('data-target');
  var model_id = model.substr(1);
});

$('[data-add-beautician]').click(function () {
  var clientId = Number($(this).attr('data-client-id'));
  $('#service_start_title_'+clientId).addClass('d-n');
  $('#service_start_table_'+clientId).removeClass('d-n');

  $('[services-beautician]').select2('destroy');
  var source   = $("#client-service-row-template").html();
  var template = Handlebars.compile(source);

  var context = {
    'row_count' : serviceRowCount,
     'client_id' : clientId
  };
  var serviceRowHtml = template(context);
  $('#service_start_body_'+clientId).append(serviceRowHtml);

  $('[services-beautician]').select2({
    placeholder: "Select Beautician",
    allowClear: true
  });
  serviceRowCount = serviceRowCount + 1;
});

var invoiceItemsList = [];
var invoiceItemsArr = [];

$("body").delegate("[data-item-type-search]", "change", function() {
  var rowCountId = Number($(this).attr('row-id'));
  var productName = $('#item_name_'+rowCountId).val();

  if(productName.trim() != ''){
     getInvoiceItemsByKeyword(rowCountId);

    setTimeout(function(){
      if(invoiceItemsArr.length == 0){
        $('#item_name_'+rowCountId).val('');
      }
    }, 200);
  }
});

$("body").delegate("[data-item-quantity]", "keyup", function() {
  var rowCountId = Number($(this).attr('row-id'));
  var productName = $('#item_name_'+rowCountId).val();

  if(productName.trim() != ''){
    checkItemPriceByRowId(rowCountId);

    setTimeout(function(){
      getInvoiceItemGrandTotal(rowCountId);
    }, 200);
  }
});

$("body").delegate("[data-item-discount]", "keyup", function() {
  var rowCountId = Number($(this).attr('row-id'));
  var productName = $('#item_name_'+rowCountId).val();

  if(productName.trim() != ''){
    checkItemQuantityByRowId(rowCountId);

    setTimeout(function(){
      getInvoiceItemGrandTotal(rowCountId);
    }, 200);
  }
});

$("body").delegate("[data-item-price]", "keyup", function() {
  var rowCountId = Number($(this).attr('row-id'));
  var productName = $('#item_name_'+rowCountId).val();

  if(productName.trim() != ''){
    checkItemQuantityByRowId(rowCountId);

    setTimeout(function(){
      getInvoiceItemGrandTotal(rowCountId);
    }, 200);
  }
});

$("body").delegate("[data-item-search]", "keyup", function() {
  var rowCountId = Number($(this).attr('row-id'));

  getInvoiceItemsByKeyword(rowCountId);
});


onLoadGetInvoiceItems();

function onLoadGetInvoiceItems(argument) {
  var base_path = $('#base_path').val();
  $.ajax({
    type:"POST",
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    url:base_path+"/admin/invoice/items/get",
    success:function (resultItems) {
      invoiceItemsList = resultItems;
    }
  });
}

function getInvoiceItemsByKeyword(rowCountId) {
  $('#item_search_list_loading_'+rowCountId).removeClass('d-n');
  var itemKeyword = $('#item_name_'+rowCountId).val();
 
  checkItemQuantityByRowId(rowCountId);

  $('#item_id_'+rowCountId).val('');
  $('#item_discount_'+rowCountId).val('');
  $('#item_price_'+rowCountId).val('');
  $('#item_total_price_'+rowCountId).val('');
  $('#item_search_list_'+rowCountId).addClass('d-n');
  $('#item_search_list_'+rowCountId).empty();
  
  getInvoiceItemGrandTotal(rowCountId);
  invoiceItemsArr = [];

  if(itemKeyword.trim() != ''){

    var itemType = $('#item_type_'+rowCountId).val();

    invoiceItemsArr = invoiceItemsList.filter((item) => {
      if(itemType == item.item_type){
        return item.name.toLowerCase().includes(itemKeyword.toLowerCase());
      }
    });

    if(invoiceItemsArr.length > 0){
      $('#item_search_list_'+rowCountId).empty();
      $('#item_search_list_'+rowCountId).removeClass('d-n');
      $.each(invoiceItemsArr, function(key,item) {
        var itemList = '<li class="list-group-item" style="cursor:pointer" data-item-list data-item-name="'+item.name+'" data-key="'+key+'" row-id="'+rowCountId+'"><span class="search-result-title">'+item.name+'</span></li>';
        $('#item_search_list_'+rowCountId).append(itemList);
      });
    }

    $('#item_search_list_loading_'+rowCountId).addClass('d-n');
  }else{
    $('#item_search_list_loading_'+rowCountId).addClass('d-n');
  }
}

$("body").delegate("[data-item-list]", "click", function() {
  var rowCountId = Number($(this).attr('row-id'));
  var rowArrKey = Number($(this).attr('data-key'));

  checkItemQuantityByRowId(rowCountId);
  checkItemPriceByRowId(rowCountId);

  var itemArr = invoiceItemsArr[rowArrKey];

  setTimeout(function(){
    $('#item_id_'+rowCountId).val(itemArr.id);
    $('#item_name_'+rowCountId).val(itemArr.name);
    $('#item_type_'+rowCountId).val(itemArr.item_type);

    var itemPrice = Number(itemArr.price);
    $('#item_price_'+rowCountId).val(Number(itemPrice).toFixed(2));

    getInvoiceItemGrandTotal(rowCountId);
  }, 100);
});

$("body").delegate("[data-item-group]", "blur", function() {
  setTimeout(function(){
    $('.invoice-item-list').addClass('d-n');
    invoiceItemsArr = [];
  }, 200);
});


function checkItemQuantityByRowId(itemRowId) {
  var productQuantity = $('#item_quantity_'+itemRowId).val();

  if(productQuantity == ''){
    $('#item_quantity_'+itemRowId).val(1);
  }else{
     productQuantity = Number(productQuantity);

     if(productQuantity <= 0){
        $('#item_quantity_'+itemRowId).val(1);
     }
  }
}

function checkItemPriceByRowId(itemRowId) {
  var productPrice = $('#item_price_'+itemRowId).val();

  var priceZero = 0;

  if(productPrice == ''){
    $('#item_price_'+itemRowId).val(Number(priceZero).toFixed(2));
  }else{
     productPrice = Number(productPrice);

     if(productPrice <= 0){
        $('#item_price_'+itemRowId).val(Number(priceZero).toFixed(2));
     }
  }
}

function getInvoiceItemGrandTotal(rowCountId) {
  var itemPrice = Number(($('#item_price_'+rowCountId).val() == '' ? 0 : $('#item_price_'+rowCountId).val()));
  var itemQuantity = Number(($('#item_quantity_'+rowCountId).val() == '' ? 1 : $('#item_quantity_'+rowCountId).val()));
  var itemDiscount = Number(($('#item_discount_'+rowCountId).val() == '' ? 0 : $('#item_discount_'+rowCountId).val()));

  if($('#item_price_'+rowCountId).val() == ''){
    var itemTotalPrice = 0;
    $('#item_total_price_'+rowCountId).val('');
  }else{
    itemPrice = Number(itemPrice);

    if(itemPrice <= 0){
      itemPrice = 0;
    }
    var itemTotalPrice = itemPrice * itemQuantity;
    itemTotalPrice = itemTotalPrice - itemDiscount;

    $('#item_total_price_'+rowCountId).val(Number(itemTotalPrice).toFixed(2));
  }
}

function isFloat(event){
  if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
  event.preventDefault();
  }
}

$("body").delegate("[data-delete-icon]", "click", function() {
  var rowCountId = Number($(this).attr('row-id'));
  var clientId = Number($(this).attr('client-id'));
  $('[data-table-row-'+rowCountId+']').remove();
  var serviceRowLength = $('#service_start_body_'+clientId+ ' tr').length;
  if(serviceRowLength == 0){
    $('#service_start_title_'+clientId).removeClass('d-n');
    $('#service_start_table_'+clientId).addClass('d-n');
  }
});

$('[data-client-invoice-settle-debit]').on('click', function() {
  var clientId = Number($(this).attr('client-id'));
  var debitAmount = Number($(this).attr('debit-amount'));

  $('#debit_amount_client_'+clientId).val('').removeAttr('min').removeAttr('max');

  if(this.checked){
    $('#debit_amount_client_'+clientId).val(debitAmount).attr({
      "max" : debitAmount,
      "min" : 0,
    });
    $('#debit_amount_client_'+clientId).prop('required', true);
  }else{
    $('#debit_amount_client_'+clientId).prop('required', false);
  }

  $('#settle_debit_row_'+clientId).slideToggle();
});

$('[data-client-invoice-advance-payment]').on('click', function() {
  var clientId = Number($(this).attr('client-id'));
  $('#client_direct_advance_payment_'+clientId).val('');

  if(this.checked){
    $('#client_direct_advance_payment_'+clientId).prop('required', true);
  }else{
    $('#client_direct_advance_payment_'+clientId).prop('required', false);
  }

  $('#advance_payment_row_'+clientId).slideToggle();
});

$('[client-service-advance-payment]').keyup(function () {
  var clientId = Number($(this).attr('client-id'));
  checkAdvancePayment(clientId);
});

$('[client-service-advance-payment]').blur(function () {
  var clientId = Number($(this).attr('client-id'));
  checkAdvancePayment(clientId);
});

function checkAdvancePayment(clientId) {
  if($('#client_advance_payment_'+clientId).val() != '' && $('#client_grand_total_'+clientId).val() != ''){
    var advancePayment = Number($('#client_advance_payment_'+clientId).val());
    $('#client_amount_paid_'+clientId).val(Number(advancePayment).toFixed(2));
  }else{
    if($('#client_grand_total_'+clientId).val() != ''){
      setTimeout(function(){
        if($('#client_advance_payment_'+clientId).val() == ''){
          $('#client_advance_payment_'+clientId).val('0');
          $('#client_amount_paid_'+clientId).val('0');
        }
      }, 2000);
    }
  }
}

$('[client-service-amount-paid]').keyup(function () {
  var clientId = Number($(this).attr('client-id'));

  if($('#payment_type_'+clientId).val() == 'CASH' || $('#payment_type_'+clientId).val() == 'CARD' || $('#payment_type_'+clientId).val() == 'ONLINE'){
    var is_service_discount_applied = Number($('#is_service_discount_applied_'+clientId).val());

    if(is_service_discount_applied == 1){
      checkClientRunningServicesDiscount(clientId, 0);
    }else{
      checkAmountPaid(clientId);
    }
  }else{
    checkAmountPaid(clientId);
  }
});

function checkAmountPaid(clientId) {
  if($('#client_amount_paid_'+clientId).val() != '' && $('#client_grand_total_'+clientId).val() != ''){
    var grandTotal = Number($('#client_grand_total_'+clientId).val());
    var amountPaid = Number($('#client_amount_paid_'+clientId).val());
    var debitAmount = grandTotal - amountPaid;

    if($('#payment_type_'+clientId).val() == 'CASH' || $('#payment_type_'+clientId).val() == 'CARD' || $('#payment_type_'+clientId).val() == 'ONLINE'){
      $('#client_discount_'+clientId).val(Number(debitAmount).toFixed(2));
    }else{
      $('#client_debit_amount_'+clientId).val(Number(debitAmount).toFixed(2));
    }
  }else{
    if($('#client_grand_total_'+clientId).val() != ''){
      var grandTotal = Number($('#client_grand_total_'+clientId).val());

      if($('#payment_type_'+clientId).val() == 'CASH' || $('#payment_type_'+clientId).val() == 'CARD' || $('#payment_type_'+clientId).val() == 'ONLINE'){
        $('#client_discount_'+clientId).val(Number(grandTotal).toFixed(2));
      }else{
        $('#client_debit_amount_'+clientId).val(Number(grandTotal).toFixed(2));
      }

      setTimeout(function(){
        if($('#client_amount_paid_'+clientId).val() == ''){
          $('#client_amount_paid_'+clientId).val('0');
        }
      }, 2000);
    }
  }
}

$('[client-service-debit-amount]').keyup(function () {
  var clientId = Number($(this).attr('client-id'));
  checkDebitAmount(clientId);
});

$('[client-service-debit-amount]').blur(function () {
  var clientId = Number($(this).attr('client-id'));
  checkDebitAmount(clientId);
});

function checkDebitAmount(clientId) {
  if($('#client_debit_amount_'+clientId).val() != '' && $('#client_grand_total_'+clientId).val() != ''){
    var grandTotal = Number($('#client_grand_total_'+clientId).val());
    var debitAmount = Number($('#client_debit_amount_'+clientId).val());

    var amountPaid = grandTotal - debitAmount;
    $('#client_amount_paid_'+clientId).val(Number(amountPaid).toFixed(2));
  }else{
    if($('#client_grand_total_'+clientId).val() != ''){
      var grandTotal = Number($('#client_grand_total_'+clientId).val());
      $('#client_amount_paid_'+clientId).val(Number(grandTotal).toFixed(2));
      setTimeout(function(){
        if($('#client_debit_amount_'+clientId).val() == ''){
          $('#client_debit_amount_'+clientId).val('0');
        }
      }, 2000);
    }
  }
}

$('[client-service-discount]').keyup(function () {
  var clientId = Number($(this).attr('client-id'));
  var is_service_discount_applied = Number($('#is_service_discount_applied_'+clientId).val());

  if(is_service_discount_applied == 1){
    checkClientRunningServicesDiscount(clientId, 1);
  }else{
    checkDiscount(clientId);
  }
});

function checkClientRunningServicesDiscount(clientId, isCheckDiscount) {
  $('#client_end_service_loading_'+clientId).removeClass('d-n');
  $('#client_end_service_btn_save_'+clientId).addClass('pt-event');
  $('#client_end_service_btn_bill_'+clientId).addClass('pt-event');

  var base_path = $('#base_path').val();
  $.ajax({
    type:"POST",
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    url:base_path+"/admin/client/running-services",
    data:{ 'client_id' : clientId},
    success:function (runningServices) {
      var grandTotal = 0;
      $.each(runningServices, function(key, service) {
        var discount  = Number(service.discount);
        var price  = Number(service.total_price);
        var totalPrice = price + discount;

        $('#client_running_service_id_'+service.id).html('('+Number(totalPrice).toFixed(2)+' Rs.)');
        grandTotal = grandTotal + totalPrice;
      });

      $('#client_grand_total_'+clientId).val(grandTotal);
      $('#client_total_advance_html_'+clientId).html(Number(grandTotal).toFixed(2)+' Rs.');
      $('#is_service_discount_applied_'+clientId).val('0');

      $('#client_end_service_loading_'+clientId).addClass('d-n');
      $('#client_end_service_btn_save_'+clientId).removeClass('pt-event');
      $('#client_end_service_btn_bill_'+clientId).removeClass('pt-event');

      if(isCheckDiscount == 1){
        checkDiscount(clientId);
      }else{
        checkAmountPaid(clientId);
      }

      checkEndServicePaymentType(clientId);
    }
  });
}

function checkDiscount(clientId) {
  if($('#client_discount_'+clientId).val() != '' && $('#client_grand_total_'+clientId).val() != ''){
    var grandTotal = Number($('#client_grand_total_'+clientId).val());
    var discount = Number($('#client_discount_'+clientId).val());

    var amountPaid = grandTotal - discount;
    $('#client_amount_paid_'+clientId).val(Number(amountPaid).toFixed(2));
  }else{
    if($('#client_grand_total_'+clientId).val() != ''){
      var grandTotal = Number($('#client_grand_total_'+clientId).val());
      $('#client_amount_paid_'+clientId).val(Number(grandTotal).toFixed(2));
      setTimeout(function(){
        if($('#client_discount_'+clientId).val() == ''){
          $('#client_discount_'+clientId).val('0');
        }
      }, 2000);
    }
  }
}

$('[on-credit-service-cash]').keyup(function () {
  var clientId = Number($(this).attr('client-id'));
  checkOnCreditCash(clientId);
});

$('[on-credit-service-cash]').blur(function () {
  var clientId = Number($(this).attr('client-id'));
  checkOnCreditCash(clientId);
});

function checkOnCreditCash(clientId) {
  if($('#on_credit_cash_'+clientId).val() != '' && $('#client_grand_total_'+clientId).val() != ''){
    var onCreditCash = Number($('#on_credit_cash_'+clientId).val());
    $('#on_credit_amount_paid_'+clientId).val(Number(onCreditCash).toFixed(2));
  }else{
    if($('#client_grand_total_'+clientId).val() != ''){
      setTimeout(function(){
        if($('#on_credit_cash_'+clientId).val() == ''){
          $('#on_credit_cash_'+clientId).val('0');
          $('#on_credit_amount_paid_'+clientId).val('0');
        }
      }, 2000);
    }
  }
}

$('[on-credit-service-debit]').keyup(function () {
  var clientId = Number($(this).attr('client-id'));
  checkOnCreditDebit(clientId);
});

$('[on-credit-service-debit]').blur(function () {
  var clientId = Number($(this).attr('client-id'));
  checkOnCreditDebit(clientId);
});

function checkOnCreditDebit(clientId) {
  if($('#on_credit_debit_'+clientId).val() != '' && $('#client_grand_total_'+clientId).val() != ''){
    var onCreditDebit = Number($('#on_credit_debit_'+clientId).val());
    var onCreditDueDebit = Number($('#on_credit_due_debit_'+clientId).val());
    var onCreditAmountPaid = onCreditDueDebit - onCreditDebit;
    $('#on_credit_amount_paid_'+clientId).val(Number(onCreditAmountPaid).toFixed(2));
  }else{
    if($('#client_grand_total_'+clientId).val() != ''){
      setTimeout(function(){
        if($('#on_credit_debit_'+clientId).val() == ''){
          $('#on_credit_debit_'+clientId).val('0');
          var onCreditDueDebit = Number($('#on_credit_due_debit_'+clientId).val());
          $('#on_credit_amount_paid_'+clientId).val(Number(onCreditDueDebit).toFixed(2));
        }
      }, 2000);
    }
  }
}

$('[on-credit-service-amount-paid]').keyup(function () {
  var clientId = Number($(this).attr('client-id'));
  checkRemainTotalAdvance(clientId);
});

$('[on-credit-service-amount-paid]').blur(function () {
  var clientId = Number($(this).attr('client-id'));
  checkRemainTotalAdvance(clientId);
});

function checkRemainTotalAdvance(clientId) {
  if($('#on_credit_amount_paid_'+clientId).val() != '' && $('#client_grand_total_'+clientId).val() != ''){

    var totalAdvance = Number($('#client_total_advance_'+clientId).val());
    var amountPaid = Number($('#on_credit_amount_paid_'+clientId).val());
    var grandTotal = Number($('#client_grand_total_'+clientId).val());

    if(totalAdvance <= 0){
      $('#on_credit_amount_paid_'+clientId).prop('readonly', true);
      $('#on_credit_remain_advance_'+clientId).prop('readonly', true);
      $('#on_credit_cash_'+clientId).prop('readonly', true);

      $('#client_end_service_btn_save_'+clientId).addClass('pt-event');
      $('#client_end_service_btn_bill_'+clientId).addClass('pt-event');

      $('#on_credit_remain_advance_'+clientId).val(Number(totalAdvance).toFixed(2));

      if($('#payment_type_'+clientId).val() == 'ON_CREDIT+CASH'){
        $('#on_credit_amount_paid_'+clientId).val(Number(grandTotal).toFixed(2));
        $('#on_credit_cash_'+clientId).val(Number(grandTotal).toFixed(2));
        $('#on_credit_cash_row_'+clientId).removeClass('d-n');
      }else{
        $('#on_credit_amount_paid_'+clientId).val('0');
      }
    }else{
      // if($('#payment_type_'+clientId).val() == 'ON_CREDIT'){
      //   if(amountPaid <= totalAdvance){
      //     var remainAdvance = totalAdvance - amountPaid;
      //     $('#on_credit_remain_advance_'+clientId).val(Number(remainAdvance).toFixed(2));
      //   }else{
      //     $('#on_credit_remain_advance_'+clientId).val(Number(totalAdvance).toFixed(2));
      //     $('#on_credit_amount_paid_'+clientId).val('0.00');
      //   }
      // }else{
      //   if(amountPaid > totalAdvance){
      //     var onCreditCash = grandTotal - totalAdvance;
      //     $('#on_credit_amount_paid_'+clientId).val(Number(totalAdvance).toFixed(2));
      //     $('#on_credit_remain_advance_'+clientId).val('0.00');
      //     $('#on_credit_cash_'+clientId).val(Number(onCreditCash).toFixed(2));
      //   }else{
      //     var remainAdvance = totalAdvance - amountPaid;
      //     var onCreditCash = grandTotal - amountPaid;
      //     $('#on_credit_remain_advance_'+clientId).val(Number(remainAdvance).toFixed(2));
      //     $('#on_credit_cash_'+clientId).val(Number(onCreditCash).toFixed(2));
      //   }
      // }
    }
  }else{
    if($('#client_grand_total_'+clientId).val() != ''){
      // var totalAdvance = Number($('#client_total_advance_'+clientId).val());
      // $('#on_credit_remain_advance_'+clientId).val(Number(totalAdvance).toFixed(2));

      // if($('#payment_type_'+clientId).val() == 'ON_CREDIT+CASH'){
      //   var grandTotal = Number($('#client_grand_total_'+clientId).val());
      //   $('#on_credit_cash_'+clientId).val(Number(grandTotal).toFixed(2));
      // }

      // setTimeout(function(){
      //   if($('#on_credit_amount_paid_'+clientId).val() == ''){
      //     $('#on_credit_amount_paid_'+clientId).val('0.00');
      //     checkRemainTotalAdvance(clientId);
      //   }
      // }, 2000);
    }
  }
}

$('[client-end-service-popup]').click(function () {
  var clientId = Number($(this).attr('client-id'));
  checkEndServicePaymentType(clientId);
  $('#end_service_modal_'+clientId).modal('show');
});

$('[client-service-payment-type]').change(function () {
  var clientId = Number($(this).attr('client-id'));
  checkEndServicePaymentType(clientId);
});

function checkEndServicePaymentType(clientId) {

  $('#client_end_service_row_1_'+clientId).addClass('d-n');
  $('#client_end_service_row_2_'+clientId).addClass('d-n');

  $('#client_end_service_btn_save_'+clientId).removeClass('pt-event');
  $('#client_end_service_btn_bill_'+clientId).removeClass('pt-event');

  $('#payment_debit_'+clientId).addClass('d-n');
  $('#payment_advance_'+clientId).addClass('d-n');
  $('#payment_discount_'+clientId).addClass('d-n');
  $('#on_credit_cash_row_'+clientId).addClass('d-n');
  $('#on_credit_debit_row_'+clientId).addClass('d-n');

  $('#client_advance_payment_'+clientId).prop('required', false);
  $('#client_amount_paid_'+clientId).prop('required',false);
  $('#client_debit_amount_'+clientId).prop('required',false);
  $('#client_discount_'+clientId).prop('required',false);
  $('#on_credit_amount_paid_'+clientId).prop('required', false);
  $('#on_credit_remain_advance_'+clientId).prop('required', false);
  $('#on_credit_cash_'+clientId).prop('required', false);
  $('#on_credit_due_cash_'+clientId).prop('required', false);
  $('#on_credit_debit_'+clientId).prop('required', false);
  $('#on_credit_due_debit_'+clientId).prop('required', false);

  $('#client_advance_payment_'+clientId).val('').removeAttr('min').removeAttr('max').removeAttr('name');
  $('#client_amount_paid_'+clientId).val('').removeAttr('min').removeAttr('max').removeAttr('name');
  $('#client_debit_amount_'+clientId).val('').removeAttr('min').removeAttr('max').removeAttr('name');
  $('#client_discount_'+clientId).val('').removeAttr('min').removeAttr('max').removeAttr('name');
  $('#on_credit_amount_paid_'+clientId).val('').removeAttr('min').removeAttr('max').removeAttr('name');
  $('#on_credit_remain_advance_'+clientId).val('').removeAttr('min').removeAttr('max').removeAttr('name');
  $('#on_credit_cash_'+clientId).val('').removeAttr('min').removeAttr('max').removeAttr('name');
  $('#on_credit_due_cash_'+clientId).val('').removeAttr('min').removeAttr('max').removeAttr('name');
  $('#on_credit_debit_'+clientId).val('').removeAttr('min').removeAttr('max').removeAttr('name');
  $('#on_credit_due_debit_'+clientId).val('').removeAttr('min').removeAttr('max').removeAttr('name');

  if($('#payment_type_'+clientId).val() == 'DEBIT'){
    $('#client_end_service_row_1_'+clientId).removeClass('d-n');
    $('#payment_debit_'+clientId).removeClass('d-n');

    $('#client_amount_paid_'+clientId).prop('required',true);
    $('#client_debit_amount_'+clientId).prop('required',true);

    $('#client_debit_amount_'+clientId).val($('#client_grand_total_'+clientId).val()).attr({
      "max" : $('#client_grand_total_'+clientId).val(),
      "min" : 0,
      "name" : 'debit_amount'
    });

    $('#client_amount_paid_'+clientId).val('0.00').attr({
      "max" : $('#client_grand_total_'+clientId).val(),
      "min" : 0,
      "name" : 'amount_paid'
    });
  }
  else if($('#payment_type_'+clientId).val() == 'ADVANCE_PAYMENT'){
    $('#client_end_service_row_1_'+clientId).removeClass('d-n');
    $('#payment_advance_'+clientId).removeClass('d-n');

    $('#client_advance_payment_'+clientId).prop('required', true);
    $('#client_amount_paid_'+clientId).prop('required', true);
    $('#client_amount_paid_'+clientId).prop('readonly', true);

    $('#client_advance_payment_'+clientId).val('0.00').attr({
      "min" : $('#client_grand_total_'+clientId).val(),
      "name" : 'advance_payment'
    });

    $('#client_amount_paid_'+clientId).val('0.00').attr({
      "min" : 0,
      "name" : 'amount_paid'
    });
  }
  else if($('#payment_type_'+clientId).val() == 'CASH' || $('#payment_type_'+clientId).val() == 'CARD' || $('#payment_type_'+clientId).val() == 'ONLINE'){
    $('#client_end_service_row_1_'+clientId).removeClass('d-n');
    $('#payment_discount_'+clientId).removeClass('d-n');

    $('#client_discount_'+clientId).prop('required', true);
    $('#client_amount_paid_'+clientId).prop('required', true);

    $('#client_discount_'+clientId).val('0.00').attr({
      "max" : $('#client_grand_total_'+clientId).val(),
      "min" : 0,
      "name" : 'discount'
    });

    $('#client_amount_paid_'+clientId).val($('#client_grand_total_'+clientId).val()).attr({
      "max" : $('#client_grand_total_'+clientId).val(),
      "min" : 0,
      "name" : 'amount_paid'
    });
  }
  else if($('#payment_type_'+clientId).val() == 'ON_CREDIT' || $('#payment_type_'+clientId).val() == 'ON_CREDIT+CASH' || $('#payment_type_'+clientId).val() == 'ON_CREDIT+DEBIT'){
    $('#client_end_service_row_2_'+clientId).removeClass('d-n');

    $('#on_credit_amount_paid_'+clientId).prop('required', true);
    $('#on_credit_remain_advance_'+clientId).prop('required', true);

    $('#on_credit_amount_paid_'+clientId).attr({
      "min" : 0,
      "name" : 'amount_paid'
    });

    $('#on_credit_remain_advance_'+clientId).attr({
      "min" : 0,
      "name" : 'remain_total_advance'
    });

    $('#on_credit_amount_paid_'+clientId).prop('readonly', true);
    // $('#on_credit_cash_'+clientId).prop('readonly', true);
    $('#on_credit_remain_advance_'+clientId).prop('readonly', true);

    var amountPaid = Number(($('#on_credit_amount_paid_'+clientId).val() == '' ? 0 : $('#on_credit_amount_paid_'+clientId).val()));
    var totalAdvance = Number($('#client_total_advance_'+clientId).val());
    var grandTotal = Number($('#client_grand_total_'+clientId).val());

    if(totalAdvance <= 0){
      $('#on_credit_amount_paid_'+clientId).prop('readonly', true);
      $('#on_credit_remain_advance_'+clientId).prop('readonly', true);
      $('#on_credit_cash_'+clientId).prop('readonly', true);
      $('#on_credit_debit_'+clientId).prop('readonly', true);

      $('#client_end_service_btn_save_'+clientId).addClass('pt-event');
      $('#client_end_service_btn_bill_'+clientId).addClass('pt-event');

      $('#on_credit_remain_advance_'+clientId).val(Number(totalAdvance).toFixed(2));

      if($('#payment_type_'+clientId).val() == 'ON_CREDIT+CASH'){
        $('#on_credit_amount_paid_'+clientId).val(Number(grandTotal).toFixed(2));
        $('#on_credit_cash_'+clientId).val(Number(grandTotal).toFixed(2));
        $('#on_credit_cash_row_'+clientId).removeClass('d-n');
      }else if($('#payment_type_'+clientId).val() == 'ON_CREDIT+DEBIT'){
        $('#on_credit_amount_paid_'+clientId).val('0.00');
        $('#on_credit_debit_'+clientId).val(Number(grandTotal).toFixed(2));
        $('#on_credit_debit_row_'+clientId).removeClass('d-n');
      }
      else{
        $('#on_credit_amount_paid_'+clientId).val('0.00');
      }
    }else{
      if($('#payment_type_'+clientId).val() == 'ON_CREDIT+DEBIT'){
        var onCreditDebit = grandTotal - totalAdvance;
        $('#on_credit_remain_advance_'+clientId).val('0.00');
        $('#on_credit_amount_paid_'+clientId).val('0.00');
        $('#on_credit_debit_'+clientId).val(Number(onCreditDebit).toFixed(2));
        $('#on_credit_due_debit_'+clientId).val(Number(onCreditDebit).toFixed(2));
        $('#on_credit_debit_row_'+clientId).removeClass('d-n');

        $('#on_credit_debit_'+clientId).attr({
          "min" : 0,
          "max" : onCreditDebit,
          "name" : 'on_credit_debit'
        });

        $('#on_credit_due_debit_'+clientId).attr({
          "name" : 'on_credit_due_debit'
        });
      }
      else if($('#payment_type_'+clientId).val() == 'ON_CREDIT'){
        var remainAdvance = totalAdvance - grandTotal;
        // $('#on_credit_amount_paid_'+clientId).val(Number(grandTotal).toFixed(2));
        $('#on_credit_amount_paid_'+clientId).val('0.00');
        $('#on_credit_remain_advance_'+clientId).val(Number(remainAdvance).toFixed(2));
      }
      else{
        var onCreditCash = grandTotal - totalAdvance;
        $('#on_credit_remain_advance_'+clientId).val('0.00');
        $('#on_credit_amount_paid_'+clientId).val(Number(onCreditCash).toFixed(2));
        $('#on_credit_cash_'+clientId).val(Number(onCreditCash).toFixed(2));
        $('#on_credit_due_cash_'+clientId).val(Number(onCreditCash).toFixed(2));
        $('#on_credit_cash_row_'+clientId).removeClass('d-n');

        $('#on_credit_cash_'+clientId).attr({
          "min" : onCreditCash,
          "name" : 'on_credit_cash'
        });

        $('#on_credit_due_cash_'+clientId).attr({
          "name" : 'on_credit_due_cash'
        });
      }
    }
  }
}