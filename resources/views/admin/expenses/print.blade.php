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
          <th>Title</th>
          <th>Description</th>
          <th>Total Amount</th>
        </tr>
      </thead>
      <tbody>
      @if(count($expenses) > 0)
              <?php
$rowCount = 0;
$totla_expense = 0;
?>
              @foreach($expenses as $key => $expense)
                <?php
$rowCount = $rowCount + 1;
$totla_expense = $totla_expense + $expense->total_amount;
?>
            <tr>
              <td>{{ $rowCount }}</td>
              <td>{{ \Carbon\Carbon::parse($expense->date)->format('d M, Y') }}</td>
              <td>{{ $expense->title }}</td>
              <td>{{ ($expense->description == '' ? 'NA' : $expense->description) }}</td>
              <td>{{ number_format($expense->total_amount, 2) }}</td>
            </tr>
          @endforeach
          <tr>
            <td>Total</td>
            <td></td>
            <td></td>
            <td></td>
            <td>Total: <strong>{{ number_format($totla_expense, 2) }}</strong></td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>
  <div class="row mt-4" print-back-row>
    <div class="col-md-5 text-center">
      <a class="btn btn-primary text-center" href="{{ url('/admin/expenses') }}"><i class="fas fa-arrow-alt-circle-left"></i>&nbsp;Back To Expenses</a>
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