$(function () {
  $("#package").select2({
    placeholder: "Select package",
  });

  $('.datepicker').datepicker({
    format: 'yyyy-mm-dd',
    autoclose: true
  });
});

$('[services-beautician]').select2({
  placeholder: "Select Beautician",
  allowClear: true
});

if(isUpdatePaymentType == 1){
  setTimeout(function(){
    checkPaymentType();
  }, 200);
}

if(isUpdateGrandTotal == 1){
  setTimeout(function(){
    getInvoiceItemGrandTotal();
  }, 200);
}

$('[payment-type]').change(function () {
  checkPaymentType();
});

function isFloat(event){
  if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
  event.preventDefault();
  }
}

checkClientSettleDebitAmount();

var invoiceClientsList = [];

$("body").delegate("[data-client-name]", "keyup", function() {
  $('#client_id').val('');
  $('#phone_number').val('');
  $('#gender').val('MALE');
  $('#birthdate').val('');
  $('#client_total_debit').val('0');
  $('#client_total_advance').val('0');

  $('#is_phone_number_error').addClass('d-n');
  $('[client-total-debit-html]').addClass('d-n');
  $('[client-total-advance-html]').addClass('d-n');
  $('[data-client-list]').addClass('d-n');
  $('[data-client-list]').empty();
  checkClientSettleDebitAmount();

  invoiceClientsList = [];

  var clientKeyword = $(this).val();
  var base_path = $('#base_path').val();

  if(clientKeyword.trim() != ''){
    $.ajax({
    type:"POST",
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    url:base_path+"/admin/invoice/clients/get",
    data:{ 'keyword' : clientKeyword},
    success:function (resultClients) {
      invoiceClientsList = resultClients;
      if(invoiceClientsList.length > 0){
        $('[data-client-list]').empty();
        $('[data-client-list]').removeClass('d-n');
        $.each(invoiceClientsList, function(key, client) {
          var clientList = '<li class="list-group-item" style="cursor:pointer" data-invoice-client-list data-invoice-client-name="'+client.name+'" data-key="'+key+'"><span class="search-result-title">'+client.name+'</span></li>';
          $('[data-client-list]').append(clientList);
        });
      }
    }
   });
 }
});


$('#phone_number').keyup(function () {
  checkClientPhoneNumber();
});

function checkClientPhoneNumber() {
  var clientId = $('#client_id').val();
  var phoneNumber = $('#phone_number').val();

  $('#loading').removeClass('d-n');
  $('#is_phone_number_error').addClass('d-n');
  $('#generate_bill_btn').removeClass('pt-event');
  $('#bill_btn').removeClass('pt-event');
  $('#sms_btn').removeClass('pt-event');

  if(clientId == '' && phoneNumber != ''){
    var base_path = $('#base_path').val();
    $.ajax({
      type:"POST",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url:base_path+"/admin/client/phone-number",
      data:{ 'phone_number' : phoneNumber},
      success:function (resStatus) {
        if(resStatus == 0){
          $('#is_phone_number_error').removeClass('d-n');
          $('#generate_bill_btn').addClass('pt-event');
          $('#bill_btn').addClass('pt-event');
          $('#sms_btn').addClass('pt-event');
        }
        $('#loading').addClass('d-n');
      }
    });
  }else{
    $('#loading').addClass('d-n');
  }
}

$("body").delegate("[data-invoice-client-list]", "click", function() {
  var clientArrKey = Number($(this).attr('data-key'));

  var clientArr = invoiceClientsList[clientArrKey];
  $('#client_id').val(clientArr.id);
  $('[data-client-name]').val(clientArr.name);

  if(clientArr.phone_number != null){
     $('#phone_number').val(clientArr.phone_number);
  }
  if(clientArr.gender != null){
     $('#gender').val(clientArr.gender);
  }
  if(clientArr.dob != null){
    $('#birthdate').val(clientArr.dob);
  }

  var clientTotalDebit = Number(clientArr.total_debit).toFixed(2);
  var clientTotalAdvance = Number(clientArr.total_advance).toFixed(2);

  $('#client_total_debit').val(clientArr.total_debit);
  $('[client-total-debit]').html(clientTotalDebit);
  $('[client-total-debit-html]').removeClass('d-n');

  $('#client_total_advance').val(clientArr.total_advance);
  $('[client-total-advance]').html(clientTotalAdvance);
  $('[client-total-advance-html]').removeClass('d-n');

  checkClientSettleDebitAmount();
});

$("body").delegate("[data-client-name]", "blur", function() {
  setTimeout(function(){
    $('[data-client-list]').addClass('d-n');
    invoiceClientsList = [];
  }, 200);

  checkClientSettleDebitAmount();
});

$('[data-client-invoice-settle-debit]').on('click', function() {
  var clientId = Number($(this).attr('client-id'));
  var debitAmount = Number($(this).attr('debit-amount'));

  $('#debit_amount_client').val('').removeAttr('min').removeAttr('max');
  $('#client_remain_debit_amount').addClass('d-n');

  if(this.checked){
    $('#debit_amount_client').val(debitAmount).attr({
      "max" : debitAmount,
      "min" : 0,
    });
    $('#debit_amount_client').prop('required', true);
  }else{
    $('#debit_amount_client').prop('required', false);
  }

  $('#settle_debit_row').slideToggle();
});

$('[data-client-invoice-advance_payment]').on('click', function() {
  $('#client_advance_payment').val('');
  if(this.checked){
    $('#client_advance_payment').prop('required', true);
  }else{
    $('#client_advance_payment').prop('required', false);
  }

  $('#advance_payment_row').slideToggle();
});

function checkClientSettleDebitAmount(){
  var clientId = $('#client_id').val();
  $('[data-client-invoice-settle-debit]').removeAttr('client-id').removeAttr('debit-amount');
  $('[data-client-invoice-settle-debit]').parent().removeClass('pt-event');

  if(clientId != ''){
    var totalDebit = Number(($('#client_total_debit').val() == '' ? 0 : $('#client_total_debit').val()));
    if(totalDebit > 0){
      $('[data-client-invoice-settle-debit]').attr('client-id', clientId);
      $('[data-client-invoice-settle-debit]').attr('debit-amount', totalDebit);
    }else{
      $('[data-client-invoice-settle-debit]').parent().addClass('pt-event');
      $('#is_settle_debit').prop('checked', false);
      $('#debit_amount_client').prop('required', false);
      $('#settle_debit_row').css('display', 'none');
    }
  }else{
    $('[data-client-invoice-settle-debit]').parent().addClass('pt-event');
    $('#is_settle_debit').prop('checked', false);
    $('#debit_amount_client').prop('required', false);
    $('#settle_debit_row').css('display', 'none');
  }
}

$('[btn-row-add]').click(function () {

  $('[services-beautician]').select2('destroy');
  var source   = $("#invoice-table-row-template").html();
  var template = Handlebars.compile(source);

  rowCount = rowCount + 1;
  arrKey = arrKey + 1;

  var context = {
    'row_count' : rowCount,
    'array_key': arrKey
  };
  var invoiceRowHtml = template(context);

  if(rowCountArr.length > 0){
    var tableRowId = rowCountArr[rowCountArr.length - 1];
  }else{
    var tableRowId = 1;
  }

  $('[data-table-row-'+tableRowId+']').after(invoiceRowHtml);
  rowCountArr.push(rowCount);

  $('[services-beautician]').select2({
    placeholder: "Select Beautician",
    allowClear: true
  });
});

$("body").delegate("[data-delete-icon]", "click", function() {
  var rowCountId = Number($(this).attr('row-id'));
  rowCountArr.splice(rowCountArr.indexOf(rowCountId),1);

  $('[data-table-row-'+rowCountId+']').remove();

  setTimeout(function(){
    getInvoiceItemGrandTotal();
  }, 200);
});

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
      getInvoiceItemGrandTotal();
    }, 200);
  }
});

$("body").delegate("[data-item-price]", "keyup", function() {
  var rowCountId = Number($(this).attr('row-id'));
  var productName = $('#item_name_'+rowCountId).val();

  if(productName.trim() != ''){
    checkItemQuantityByRowId(rowCountId);

    setTimeout(function(){
      getInvoiceItemGrandTotal();
    }, 200);
  }
});

$("body").delegate("[data-item-discount]", "keyup", function() {
  var rowCountId = Number($(this).attr('row-id'));
  var productName = $('#item_name_'+rowCountId).val();
  $('#total_discount').val('0.00');

  if(productName.trim() != ''){
    checkItemQuantityByRowId(rowCountId);

    setTimeout(function(){
      getInvoiceItemGrandTotal();
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
  var base_path = $('#base_path').val();

  checkItemQuantityByRowId(rowCountId);

  $('#item_id_'+rowCountId).val('');
  $('#item_discount_'+rowCountId).val('');
  $('#item_price_'+rowCountId).val('');
  $('#item_total_price_'+rowCountId).val('');
  $('#item_search_list_'+rowCountId).addClass('d-n');
  $('#item_search_list_'+rowCountId).empty();

  getInvoiceItemGrandTotal();
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

    getInvoiceItemGrandTotal();
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

  if(productPrice == ''){
    $('#item_price_'+itemRowId).val('0');
  }else{
     productPrice = Number(productPrice);

    if(productPrice <= 0){
      $('#item_price_'+itemRowId).val(Number(priceZero).toFixed(2));
    }
  }
}

$('#total_discount').keyup(function () {
  $('[data-item-discount]').val('0.00');
  checkDiscount();
});

function checkDiscount() {
  if($('#total_discount').val() != '' && $('[grand-total]').val() != ''){
  }else{
    if($('[grand-total]').val() != ''){
      setTimeout(function(){
        if($('#total_discount').val() == ''){
          $('#total_discount').val('0');
        }
      }, 2000);
    }
  }
  getInvoiceItemGrandTotal();
}

function getInvoiceItemGrandTotal() {
  if(rowCountArr.length > 0){
    var invoiceGrandTotal = 0;

    var totalDiscount = Number(($('[total-discount]').val() == '' ? 0 : $('[total-discount]').val()));

    var totalItemDiscount = 0;

    $.each(rowCountArr, function(key,val) {
      var itemPrice = Number(($('#item_price_'+val).val() == '' ? 0 : $('#item_price_'+val).val()));
      var itemQuantity = Number(($('#item_quantity_'+val).val() == '' ? 1 : $('#item_quantity_'+val).val()));
      var itemDiscount = Number(($('#item_discount_'+val).val() == '' ? 0 : $('#item_discount_'+val).val()));

      if($('#item_price_'+val).val() == ''){
        var itemTotalPrice = 0;
        $('#item_total_price_'+val).val('');
      }else{
        itemPrice = Number(itemPrice);

        if(itemPrice <= 0){
          itemPrice = 0;
        }
        var itemTotalPrice = itemPrice * itemQuantity;
        itemTotalPrice = itemTotalPrice - itemDiscount;

        $('#item_total_price_'+val).val(Number(itemTotalPrice).toFixed(2));
      }

      totalItemDiscount = totalItemDiscount + itemDiscount;
      invoiceGrandTotal = invoiceGrandTotal + itemTotalPrice;
    });

    if(totalDiscount > 0){
      $('[data-item-discount]').val('0.00');
      invoiceGrandTotal = invoiceGrandTotal - totalDiscount;
    }

    if(totalItemDiscount > 0){
      $('#total_discount').val('0.00');
    }

    if(invoiceGrandTotal <= 0){
      var priceZero = 0;
      $('[grand-total]').val(Number(priceZero).toFixed(2));
    }else{
      $('[grand-total]').val(Number(invoiceGrandTotal).toFixed(2));
    }
  }else{
    $('[grand-total]').val('');
  }

  if(isUpdatePaymentType == 0){
    checkPaymentType();
  }
}

$('#advance_payment').keyup(function () {
  checkAdvancePayment();
});

$('#advance_payment').blur(function () {
  checkAdvancePayment();
});

function checkAdvancePayment() {
  if($('#advance_payment').val() != '' && $('[grand-total]').val() != ''){
    var advancePayment = Number($('#advance_payment').val());
    $('#amount_paid').val(Number(advancePayment).toFixed(2));
  }else{
    if($('[grand-total]').val() != ''){
      setTimeout(function(){
        if($('#advance_payment').val() == ''){
          $('#advance_payment').val('0');
          $('#amount_paid').val('0');
        }
      }, 2000);
    }
  }
}

$('#on_credit_cash').keyup(function () {
  checkOnCreditCash();
});

$('#on_credit_cash').blur(function () {
  checkOnCreditCash();
});

function checkOnCreditCash() {
  if($('#on_credit_cash').val() != '' && $('[grand-total]').val() != ''){
    var onCreditCash = Number($('#on_credit_cash').val());
    $('#amount_paid').val(Number(onCreditCash).toFixed(2));
  }else{
    if($('[grand-total]').val() != ''){
      setTimeout(function(){
        if($('#on_credit_cash').val() == ''){
          $('#on_credit_cash').val('0');
          $('#amount_paid').val('0');
        }
      }, 2000);
    }
  }
}

$('#on_credit_debit').keyup(function () {
  checkOnCreditDebit();
});

$('#on_credit_debit').blur(function () {
  checkOnCreditDebit();
});

function checkOnCreditDebit() {
  if($('#on_credit_debit').val() != '' && $('[grand-total]').val() != ''){
    var onCreditDebit = Number($('#on_credit_debit').val());
    var onCreditDueDebit = Number($('#on_credit_due_debit').val());
    var onCreditAmountPaid = onCreditDueDebit - onCreditDebit;
    $('#amount_paid').val(Number(onCreditAmountPaid).toFixed(2));
  }else{
    if($('[grand-total]').val() != ''){
      setTimeout(function(){
        if($('#on_credit_debit').val() == ''){
          $('#on_credit_debit').val('0');
          var onCreditDueDebit = Number($('#on_credit_due_debit').val());
          $('#amount_paid').val(Number(onCreditDueDebit).toFixed(2));
        }
      }, 2000);
    }
  }
}

$('#amount_paid').keyup(function () {
  if($('[payment-type]').val() == 'ON_CREDIT' || $('[payment-type]').val() == 'ON_CREDIT+CASH'){
    checkRemainTotalAdvance();
  }else{
    checkAmountPaid();
  }
});

$('#amount_paid').blur(function () {
  if($('[payment-type]').val() == 'ON_CREDIT' || $('[payment-type]').val() == 'ON_CREDIT+CASH'){
    checkRemainTotalAdvance();
  }else{
    checkAmountPaid();
  }
});

function checkAmountPaid() {
  if($('#amount_paid').val() != '' && $('[grand-total]').val() != ''){
    var grandTotal = Number($('[grand-total]').val());
    var amountPaid = Number($('#amount_paid').val());

    var debitAmount = grandTotal - amountPaid;
    $('#debit_amount').val(Number(debitAmount).toFixed(2));
  }else{
    if($('[grand-total]').val() != ''){
      var grandTotal = Number($('[grand-total]').val());
      $('#debit_amount').val(Number(grandTotal).toFixed(2));
      setTimeout(function(){
        if($('#amount_paid').val() == ''){
          $('#amount_paid').val('0');
        }
      }, 2000);
    }
  }
}

function checkRemainTotalAdvance() {
  if($('#amount_paid').val() != '' && $('[grand-total]').val() != ''){
    var totalAdvance = Number(($('#client_total_advance').val() == '' ? 0 : $('#client_total_advance').val()));
    var amountPaid = Number($('#amount_paid').val());
    var grandTotal = Number($('[grand-total]').val());

    if(totalAdvance <= 0){
      $('#amount_paid').prop('readonly', true);
      $('#remain_total_advance').prop('readonly', true);
      $('#on_credit_cash').prop('readonly', true);

      $('#generate_bill_btn').addClass('pt-event');
      $('#bill_btn').addClass('pt-event');
      $('#sms_btn').addClass('pt-event');

      $('#remain_total_advance').val(Number(totalAdvance).toFixed(2));

      if($('[payment-type]').val() == 'ON_CREDIT+CASH'){
        $('#amount_paid').val(Number(grandTotal).toFixed(2));
        $('#on_credit_cash').val(Number(grandTotal).toFixed(2));
        $('[on-credit-cash-row]').removeClass('d-n');
      }else{
        $('#amount_paid').val('0');
      }
    }else{
      // if($('[payment-type]').val() == 'ON_CREDIT'){
      //   if(amountPaid <= totalAdvance){
      //     var remainAdvance = totalAdvance - amountPaid;
      //     $('#remain_total_advance').val(Number(remainAdvance).toFixed(2));
      //   }else{
      //     $('#remain_total_advance').val(Number(totalAdvance).toFixed(2));
      //     $('#amount_paid').val('0.00');
      //   }
      // }else{
      //   if(amountPaid > totalAdvance){
      //     var onCreditCash = grandTotal - totalAdvance;
      //     $('#amount_paid').val(Number(totalAdvance).toFixed(2));
      //     $('#remain_total_advance').val('0.00');
      //     $('#on_credit_cash').val(Number(onCreditCash).toFixed(2));
      //   }else{
      //     var remainAdvance = totalAdvance - amountPaid;
      //     var onCreditCash = grandTotal - amountPaid;
      //     $('#remain_total_advance').val(Number(remainAdvance).toFixed(2));
      //     $('#on_credit_cash').val(Number(onCreditCash).toFixed(2));
      //   }
      // }
    }
  }else{
    // if($('[grand-total]').val() != ''){
    //   var totalAdvance = Number(($('#client_total_advance').val() == '' ? 0 : $('#client_total_advance').val()));
    //   $('#remain_total_advance').val(Number(totalAdvance).toFixed(2));

    //   if($('[payment-type]').val() == 'ON_CREDIT+CASH'){
    //     var grandTotal = Number($('[grand-total]').val());
    //     $('#on_credit_cash').val(Number(grandTotal).toFixed(2));
    //   }

    //   setTimeout(function(){
    //     if($('#amount_paid').val() == ''){
    //       $('#amount_paid').val('0.00');
    //       checkRemainTotalAdvance();
    //     }
    //   }, 2000);
    // }
  }
}

$('#debit_amount').keyup(function () {
    checkDebitAmount();
});

$('#debit_amount').blur(function () {
    checkDebitAmount();
});

function checkDebitAmount(){
  if($('#debit_amount').val() != '' && $('[grand-total]').val() != ''){
    var grandTotal = Number($('[grand-total]').val());
    var debitAmount = Number($('#debit_amount').val());

    var amountPaid = grandTotal - debitAmount;
    $('#amount_paid').val(Number(amountPaid).toFixed(2));
  }else{
    if($('[grand-total]').val() != ''){
      var grandTotal = Number($('[grand-total]').val());
      $('#amount_paid').val(Number(grandTotal).toFixed(2));
      setTimeout(function(){
        if($('#debit_amount').val() == ''){
          $('#debit_amount').val('0');
        }
      }, 2000);
    }
  }
}

function checkPaymentType(){

  setTimeout(function(){

    var totalAdvance = Number(($('#client_total_advance').val() == '' ? 0 : $('#client_total_advance').val()));
    var grandTotal = Number(($('[grand-total]').val() == '' ? 0 : $('[grand-total]').val()));

    if(totalAdvance > 0){
      if(totalAdvance >= grandTotal){
        if($('[payment-type]').val() == 'ON_CREDIT' || $('[payment-type]').val() == 'ON_CREDIT+CASH'){
          var option = '<option value="ON_CREDIT" selected>ON CREDIT</option>';
        }else{
          var option = '<option value="ON_CREDIT">ON CREDIT</option>';
        }

        $("#payment_type option[value='ON_CREDIT']").remove();
        $("#payment_type option[value='ON_CREDIT+CASH']").remove();
        $('[payment-type]').append(option);
      }
      else{
        if($('[payment-type]').val() == 'ON_CREDIT' || $('[payment-type]').val() == 'ON_CREDIT+CASH'){
          var option = '<option value="ON_CREDIT+CASH" selected>ON CREDIT + CASH</option>';
        }else{
          var option = '<option value="ON_CREDIT+CASH">ON CREDIT + CASH</option>';
        }

        $("#payment_type option[value='ON_CREDIT']").remove();
        $("#payment_type option[value='ON_CREDIT+CASH']").remove();
        $('[payment-type]').append(option);
      }

      if($('[payment-type]').val() != 'ON_CREDIT+DEBIT'){
        $("#payment_type option[value='ON_CREDIT+DEBIT']").remove();
        $('[payment-type]').append('<option value="ON_CREDIT+DEBIT">ON CREDIT + DEBIT</option>');
      }
    }else{
      $("#payment_type option[value='ON_CREDIT']").remove();
      $("#payment_type option[value='ON_CREDIT+CASH']").remove();
      $("#payment_type option[value='ON_CREDIT+DEBIT']").remove();
      $('[payment-type]').append('CASH');
    }

    $('#generate_bill_btn').removeClass('pt-event');
    $('#bill_btn').removeClass('pt-event');
    $('#sms_btn').removeClass('pt-event');

    $('[amount-paid-row]').addClass('d-n');
    $('[debit-amount-row]').addClass('d-n');
    $('[advance-payment-row]').addClass('d-n');
    $('[remain-total-advance-row]').addClass('d-n');
    $('[on-credit-cash-row]').addClass('d-n');
    $('[on-credit-debit-row]').addClass('d-n');

    $('#advance_payment').prop('required', false);
    $('#debit_amount').prop('required',false);
    $('#amount_paid').prop('required',false);
    $('#remain_total_advance').prop('required',false);
    $('#on_credit_cash').prop('required',false);
    $('#on_credit_due_cash').prop('required',false);
    $('#on_credit_debit').prop('required',false);
    $('#on_credit_due_debit').prop('required',false);

    if(isUpdatePaymentType == 0){
      $('#advance_payment').val('').removeAttr('min').removeAttr('max');
      $('#amount_paid').val('').removeAttr('min').removeAttr('max');
      $('#debit_amount').val('').removeAttr('min').removeAttr('max');
      $('#remain_total_advance').val('').removeAttr('min').removeAttr('max');
      $('#on_credit_cash').val('').removeAttr('min').removeAttr('max');
      $('#on_credit_due_cash').val('').removeAttr('min').removeAttr('max');
      $('#on_credit_debit').val('').removeAttr('min').removeAttr('max');
      $('#on_credit_due_debit').val('').removeAttr('min').removeAttr('max');
    }

    $('#total_discount').attr({
      "min" : 0
    });

    if($('[payment-type]').val() == 'DEBIT'){
      $('[debit-amount-row]').removeClass('d-n');
      $('[amount-paid-row]').removeClass('d-n');

      $('#debit_amount').prop('required',true);
      $('#amount_paid').prop('required',true);

      if($('[grand-total]').val() != ''){
        if(isUpdatePaymentType == 1){
          isUpdatePaymentType = 0;
        }else{
          $('#amount_paid').val('0.00');
          $('#debit_amount').val($('[grand-total]').val());
        }

        $('#amount_paid').attr({
          "max" : $('[grand-total]').val(),
          "min" : 0
        });

        $('#debit_amount').attr({
          "max" : $('[grand-total]').val(),
          "min" : 0
        });
      }
    }else if($('[payment-type]').val() == 'ADVANCE_PAYMENT'){
      $('[advance-payment-row]').removeClass('d-n');
      $('[amount-paid-row]').removeClass('d-n');

      $('#advance_payment').prop('required',true);
      $('#amount_paid').prop('required', true);
      $('#amount_paid').prop('readonly', true);

      if($('[grand-total]').val() != ''){
        if(isUpdatePaymentType == 1){
          isUpdatePaymentType = 0;
        }else{
          $('#amount_paid').val('0.00');
          $('#advance_payment').val('0.00');
        }

        $('#advance_payment').attr({
          "min" : $('[grand-total]').val(),
        });

        $('#amount_paid').attr({
          "min" : 0
        });
      }
    }else if($('[payment-type]').val() == 'ON_CREDIT' || $('[payment-type]').val() == 'ON_CREDIT+CASH' || $('[payment-type]').val() == 'ON_CREDIT+DEBIT'){

      $('[amount-paid-row]').removeClass('d-n');
      $('[remain-total-advance-row]').removeClass('d-n');

      $('#amount_paid').prop('required', true);
      $('#remain_total_advance').prop('required',true);

      if($('[grand-total]').val() != ''){
        $('#amount_paid').attr({
          "min" :0
        });

        $('#remain_total_advance').attr({
          "min" : 0,
        });

        $('#amount_paid').prop('readonly', true);
        // $('#on_credit_cash').prop('readonly', true);
        $('#remain_total_advance').prop('readonly', true);

        var amountPaid = Number(($('#amount_paid').val() == '' ? 0 : $('#amount_paid').val()));
        var totalAdvance = Number(($('#client_total_advance').val() == '' ? 0 : $('#client_total_advance').val()));
        var grandTotal = Number($('[grand-total]').val());

        if(totalAdvance <= 0){
          if(isUpdatePaymentType == 1){
             isUpdatePaymentType = 0;
          }
          $('#amount_paid').prop('readonly', true);
          $('#remain_total_advance').prop('readonly', true);
          $('#on_credit_cash').prop('readonly', true);
          $('#on_credit_debit').prop('readonly', true);

          $('#generate_bill_btn').addClass('pt-event');
          $('#bill_btn').addClass('pt-event');
          $('#sms_btn').addClass('pt-event');

          $('#remain_total_advance').val(Number(totalAdvance).toFixed(2));

          if($('[payment-type]').val() == 'ON_CREDIT+CASH'){
            $('#amount_paid').val(Number(grandTotal).toFixed(2));
            $('#on_credit_cash').val(Number(grandTotal).toFixed(2));
            $('[on-credit-cash-row]').removeClass('d-n');
          }else if($('[payment-type]').val() == 'ON_CREDIT+DEBIT'){
            $('#amount_paid').val('0.00');
            $('#on_credit_debit').val(Number(grandTotal).toFixed(2));
            $('[on-credit-debit-row]').removeClass('d-n');
          }
          else{
            $('#amount_paid').val('0.00');
          }
        }else{
          if($('[payment-type]').val() == 'ON_CREDIT+DEBIT'){
            if(isUpdatePaymentType == 1){
              isUpdatePaymentType = 0;
            }else{
              var onCreditDebit = grandTotal - totalAdvance;
              $('#amount_paid').val('0.00');
              $('#remain_total_advance').val('0.00');
              $('#on_credit_debit').val(Number(onCreditDebit).toFixed(2));
              $('#on_credit_due_debit').val(Number(onCreditDebit).toFixed(2));
            }

            $('[on-credit-debit-row]').removeClass('d-n');
            $('#on_credit_debit').attr({
              "min" : 0,
              "max" : onCreditDebit,
            });
          }
          else if($('[payment-type]').val() == 'ON_CREDIT'){
            if(isUpdatePaymentType == 1){
               isUpdatePaymentType = 0;
            }
            var remainAdvance = totalAdvance - grandTotal;
            // $('#amount_paid').val(Number(grandTotal).toFixed(2));
            $('#amount_paid').val('0.00');
            $('#remain_total_advance').val(Number(remainAdvance).toFixed(2));
          }
          else{
            if(isUpdatePaymentType == 1){
              isUpdatePaymentType = 0;
            }else{
              var onCreditCash = grandTotal - totalAdvance;
              $('#remain_total_advance').val('0.00');
              $('#amount_paid').val(Number(onCreditCash).toFixed(2));
              $('#on_credit_cash').val(Number(onCreditCash).toFixed(2));
              $('#on_credit_due_cash').val(Number(onCreditCash).toFixed(2));
            }

            $('[on-credit-cash-row]').removeClass('d-n');
            $('#on_credit_cash').attr({
              "min" : onCreditCash,
            });
          }
        }
      }
    }
  }, 100);
}