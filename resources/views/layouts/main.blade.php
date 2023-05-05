<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Laravel') }}</title>
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Charm:400,700|Nunito:200,300,400,600,700,800,900" rel="stylesheet">
  <link rel="stylesheet" href="//cdn.materialdesignicons.com/3.2.89/css/materialdesignicons.min.css">
  {{-- Theme --}}
  <link href="{{ asset('css/theme.css') }}" rel="stylesheet">
  <!-- Styles -->
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  <!-- Cusotm CSS-->
  <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
  {{-- Bootstrap datepicker css--}}
  <link href="{{ asset('css/bootstrap-datepicker.css') }}" rel="stylesheet">
  <!-- Select2 -->
  <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
  {{-- Form elements --}}
  <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
  {{-- Jquery Datatable --}}
  {{-- <link href="{{ asset('css/jquery.dataTables.min.css') }}" rel="stylesheet"> --}}
  <!-- Fontawesome CDN-->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css">
  {{-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css"> --}}
</head>
<body>
  <div id="app">
		<main>
		  <div class="container-scroller">
        {{-- Top navbar --}}
        @include('include.topbar')
				<!-- partial -->
				<div class="page-body-wrapper">
				  {{-- Sidebar --}}
          @include('include.sidebar')
				  <!-- partial -->
				  <div class="main-panel">
            <div class="content-wrapper">
  				    @yield('content')
            </div>
  					<!-- content-wrapper ends -->
  					<!-- partial:partials/_footer.html -->
  					<footer class="footer">
  					  <div class="container-fluid clearfix">
  						<span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Creafted with <i class="mdi mdi-heart text-danger"></i> By <a href="https://techmatesofttech.com/" target="_blank">Techmates</a>
  						</span>
  					  </div>
  					</footer>
					<!-- partial -->
				  </div>
				  <!-- main-panel ends -->
				</div>
				<!-- page-body-wrapper ends -->
        <input type="hidden" name="base_path" id="base_path" value="{{ url('/') }}">
			</div>
		</main>
  </div>
  
  {{-- App js --}}
  <script src="{{ asset('js/app.js') }}"></script>
  {{-- Jquery --}}
  {{-- <script src="{{ asset('js/jquery-3.3.1.js') }}"></script> --}}
  {{-- Bootstrap js--}}
  {{-- <script src="{{ asset('js/bootstrap.min.js') }}"></script> --}}
  {{-- Select2 js--}}
  <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>
  {{-- Bootstrap datepicker js--}}
  <script src="{{ asset('js/bootstrap-datepicker.js') }}" type="text/javascript"></script>
  {{-- Jquery Datatable --}}
  {{-- <script src="{{ asset('js/jquery.dataTables.min.js') }}" type="text/javascript"></script> --}}
  {{-- <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script> --}}
  {{-- <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script> --}}
  <script src="{{ asset('js/handlebars-v4.0.12.js') }}" type="text/javascript"></script>
  {{-- <script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script> --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

  @yield('scripts')

  <script type="text/javascript">

    $('[data-client-settle-debit]').click(function () {
      var clientId = Number($(this).attr('client-id'));
      var debitAmount = Number($(this).attr('debit-amount'));

      $('#debit_amount_client').val('').removeAttr('min').removeAttr('max');
      $('#client_remain_debit_amount').addClass('d-n');

      $('#debit_amount_client_id').val(clientId);

      $('#debit_amount_client').val(debitAmount).attr({
        "max" : debitAmount,
        "min" : 0,
      });
      $('#settleDebitModal').modal('show');
    });

    function checkRemainDebitAmount() {
      var clientDebitAmount = Number($('#debit_amount_client').attr('max'));

      if($('#debit_amount_client').val() != ''){
        var debitAmount = Number($('#debit_amount_client').val());
        if(debitAmount > clientDebitAmount){
          $('#client_remain_debit_amount').addClass('d-n');
        }else{
          var finalDebitAmount = clientDebitAmount - debitAmount;

          if(finalDebitAmount <= 0){
            $('#client_remain_debit_amount').addClass('d-n');
          }else{
            $('#client_remain_debit_amount').removeClass('d-n').html('Remain debit amount is : '+finalDebitAmount.toFixed(2));
          }
        }
      }else{
        $('#client_remain_debit_amount').addClass('d-n');
      }
    }
  </script>
</body>
</html>