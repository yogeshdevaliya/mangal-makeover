<div class="modal fade" id="settleDebitModal" tabindex="-1" role="dialog" aria-labelledby="settleDebitModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="settleDebitModalLabel">Settle Debit Amount</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ url('admin/client/settle-debit') }}" method="POST">
        @csrf
        <input type="hidden" class="form-control" name="client_id" id="debit_amount_client_id">
        <div class="modal-body">
          <div class="form-group">
            <label for="debit_amount_client" class="col-form-label"><strong>Debit Amount:</strong><span class="required clr-red">*</span></label>
            <input type="number" class="form-control" onkeypress="isFloat(event)" name="debit_amount" id="debit_amount_client" onkeyup="checkRemainDebitAmount()" required>
            <span class="clr-green d-n" id="client_remain_debit_amount"></span>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>