<template>
	<div>
		<div class="row">
      <div class="col-md-4">
        <input type="text" @keyup="getResults()" v-model="input_search" class="form-input form-control" placeholder="Search">
      </div>
      <div class="col-md-6">
      	<div class="float-right">
      		<a href="invoices/create" class="form-btn" client-service-add><i class="fas fa-pencil-alt"></i>&nbsp;Add Invoice</a>
      	</div>
      </div>
    </div>
    <div class="clearfix"></div><br />
		<div class="table-responsive">
			<table class="table table-hover table-bordered" id="tableId">
				<thead>
					<tr>
						<th>Sr. No</th>
						<th>Invoice#</th>
						<th>Name</th>
						<th>Grand Total</th>
						<th>Billing Date</th>
						<th>Payment Type</th>
						<th>Notes</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(clientInvoice, key) in invoiceData.data">
						<td>{{ key+1 }}</td>
						<td>{{ clientInvoice.invoice_number }}</td>
						<td>{{ clientInvoice.client.name }}</td>
						<td>{{ (clientInvoice.grand_total ? clientInvoice.grand_total : 'NA') }}</td>
						<td>{{ (clientInvoice.bill_date ? clientInvoice.bill_date : 'NA') }}</td>
						<td>{{ clientInvoice.payment_type }}</td>
						<td>{{ (clientInvoice.notes ? clientInvoice.notes : 'NA') }}</td>
						<td>
							<div v-if="clientInvoice.is_settle_debit == 0"> 
								<div class="row">
									<div class="col-md-12 display-flex">
										<a :href="'invoices/'+clientInvoice.id+'/print'" class="btn btn-primary btn-sm"><i class="mdi mdi-file-pdf"></i>&nbsp;Generate Bill</a>
										<a :href="'invoices/'+clientInvoice.id+'/edit'" class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i>&nbsp;Edit</a>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="row mt-2">
									<div class="col-md-12 display-flex">
										<button type="button" class="btn btn-primary btn-sm d-b" data-toggle="modal" :data-target="'#viewInvoice'+clientInvoice.id"><i class="fas fa-eye"></i>&nbsp;View</button>
										<form class="d-b" action="admin/invoices/delete" method="POST">
											<input type="hidden" name="invoice_id" :value="clientInvoice.id">
											<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this invoice?'); "><i class="fas fa-trash"></i>&nbsp;Delete</button>
										</form>
									</div>
								</div>
							</div>
							<div v-else>
								<div class="row mt-2">
									<div class="col-md-12">
										<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" :data-target="'#viewInvoice'+clientInvoice.id"><i class="fas fa-eye"></i>&nbsp;View</button>
										<button type="button" class="btn btn-success btn-sm" data-toggle="modal" :data-target="'#editInvoice'+clientInvoice.id"><i class="fas fa-pencil-alt"></i>&nbsp;Edit</button>
									</div>
								</div>
								<div class="modal fade" tabindex="-1" role="dialog" :id="'editInvoice'+clientInvoice.id" aria-labelledby="myLargeModalLabel" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title" id="exampleModalLabel">Edit Invoice<strong>(#{{ clientInvoice.invoice_number }})</strong></h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<form action="admin/invoice/change/billing-date" method="POST">
												<div class="modal-body">
													<div class="row">
														<input type="hidden" name="invoice_id" :value="clientInvoice.id">
														<div class="col-md-4">
															<div class="form-group">
																<label for="billing_date" class="col-form-label"><strong>Billing Date</strong><span class="required clr-red">*</span></label>
																<input type="text" name="billing_date" class="form-input form-control datepicker" id="billing_date" placeholder="Billing Date" :value="clientInvoice.bill_date" autocomplete="off" required>
															</div>
														</div>
													</div>
												</div>
												<div class="modal-footer">
													<button type="submit" class="form-btn">Update</button>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
							<div class="modal fade" tabindex="-1" role="dialog" :id="'viewInvoice'+clientInvoice.id" aria-labelledby="myLargeModalLabel" aria-hidden="true">
								<div class="modal-dialog modal-lg">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title" id="exampleModalLabel">View Invoice<strong>(#{{ clientInvoice.invoice_number }})</strong></h5>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">&times;</span>
											</button>
										</div>
										<div class="modal-body">
											<div class="row">
												<div class="col-md-2 text-right">
													<span>Client:</span>
													<div class="clearfix mt-2"></div>
													<span>Payment Type:</span>
												</div>
												<div class="col-md-2 text-left">
													<strong>{{ clientInvoice.client.name }}</strong>
													<div class="clearfix mt-2"></div>
													<strong>{{ clientInvoice.payment_type }}</strong>
												</div>
												<div class="col-md-2 text-right">
													<span>Phone Number:</span>
													<div class="clearfix mt-2"></div>
													<span>Billing Date:</span>
												</div>
												<div class="col-md-5 text-left">
													<strong>{{ clientInvoice.client.phone_number }}</strong>
													<div class="clearfix mt-2"></div>
													<strong>{{ clientInvoice.bill_date }}</strong>
												</div>
											</div>

											<div class="clearfix"></div><br />
											<div v-if="clientInvoice.is_settle_debit == 0" class="row">
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
															</tr>
														</thead>
														<tbody>
															<tr v-for="(invoiceDetail, key_j) in clientInvoice.invoice_details">
																<td>{{ key_j + 1 }}</td>
																<td>{{ invoiceDetail.name }}</td>
																<td>{{ invoiceDetail.beautician_name }}</td>
																<td>{{ invoiceDetail.quantity }}</td>
																<td>{{ invoiceDetail.discount }}</td>
																<td>{{ invoiceDetail.price }}</td>
																<td>{{ invoiceDetail.total_price }}</td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>

											<div class="row">
												<div class="col-md-7">
													<span><strong>Notes:</strong></span>
													<div class="clearfix"></div>
													<p>{{ (clientInvoice.notes ? clientInvoice.notes : 'NA') }}</p>
													<div v-if="clientInvoice.settle_debit_amount > 0">
														<span><strong>Settle Debit Amount:</strong></span>
														<div class="clearfix"></div>
														<span>{{ clientInvoice.settle_debit_amount }}</span>
													</div>
													<div  v-if="clientInvoice.client_advance_payment > 0">
														<span><strong>Advance Payment:</strong></span>
														<div class="clearfix"></div>
														<span>{{ clientInvoice.client_advance_payment }}</span>
													</div>
												</div>
												<div class="col-md-4">
													<div class="row">
														<div class="col-md-10 text-right">
															<h6><strong>Discount :</strong></h6>
														</div>
														<div class="col-md-2 text-left">
															<h6><strong>{{ clientInvoice.total_discount }}</strong></h6>
														</div>
													</div>
													<div v-if="(clientInvoice.payment_type == 'DEBIT' || (clientInvoice.payment_type == 'ADVANCE_PAYMENT' && clientInvoice.debit_amount > 0))">
														<div class="row">
															<div class="col-md-10 text-right">
																<h6><strong>Debit Amount:</strong></h6>
															</div>
															<div class="col-md-2 text-left">
																<h6><strong>{{ clientInvoice.debit_amount }}</strong></h6>
															</div>
														</div>
													</div>
													<div v-if="clientInvoice.payment_type == 'ADVANCE_PAYMENT'">
														<div class="row">
															<div class="col-md-10 text-right">
																<h6><strong>Advance Payment :</strong></h6>
															</div>
															<div class="col-md-2 text-left">
																<h6><strong>{{ clientInvoice.advance_payment }}</strong></h6>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-10 text-right">
															<h6><strong>Amount Paid :</strong></h6>
														</div>
														<div class="col-md-2 text-left">
															<!-- Format -->
															<h6><strong>{{ clientInvoice.amount_paid }}</strong></h6>
														</div>
													</div>
													<div v-if="clientInvoice.payment_type == 'ON_CREDIT+DEBIT'">
														<div class="row">
															<div class="col-md-10 text-right">
																<h6><strong>Debit Amount:</strong></h6>
															</div>
															<div class="col-md-2 text-left">
																<h6><strong>{{ clientInvoice.on_credit_debit }}</strong></h6>
															</div>
														</div>
													</div>
													<div v-if="clientInvoice.payment_type == 'ON_CREDIT+CASH'">
														<div class="row">
															<div class="col-md-10 text-right">
																<h6><strong>Cash :</strong></h6>
															</div>
															<div class="col-md-2 text-left">
																<!-- Format -->
																<h6><strong>{{ clientInvoice.on_credit_cash }}</strong></h6>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-10 text-right">
															<h6><strong>Grand Total:</strong></h6>
														</div>
														<div class="col-md-2 text-left">
															<!-- Format -->
															<h6><strong>{{ clientInvoice.grand_total }}</strong></h6>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="clearfix"></div><br />
		<!-- Pagination -->
	  <pagination :data="invoiceData" :limit="5" @pagination-change-page="getResults"></pagination>
	</div>
</template>

	<script>
		var path = $('#base_path').val()+'/admin/';
		export default {
			props: ['role'],
			data() {
				return {
					invoiceData: {},
					input_search: '',
					is_search: false,
				}
			},
			mounted() {
				this.getResults();
			},
			methods: {
				getResults(page = 1) {
					let full_path = path+'invoices/lvp/get?page='+page+(this.input_search ? '&type=search&term='+this.input_search : '');
					axios.get(full_path).then(response => {
						this.invoiceData = response.data;
					});
				},
			},
		}
	</script>