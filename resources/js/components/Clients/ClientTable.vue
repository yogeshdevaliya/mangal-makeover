<template>
	<div>
    <div class="row">
      <div class="col-md-4">
        <input type="text" @keyup="getResults()" v-model="input_search" class="form-input form-control" placeholder="Search">
      </div>
    </div>
    <div class="clearfix"></div><br />
    <div class="table-responsive">
  		<table class="table table-hover table-bordered">
        <thead>
          <tr>
            <th>Sr. No</th>
            <th>Name</th>
            <th v-if="role == 'super_admin'">Phone Number</th>
            <th v-if="role == 'super_admin'">Email</th>
            <th v-if="role == 'super_admin'">Address</th>
            <th>Gender</th>
            <th>Birthdate</th>
            <th>Anniversary</th>
            <th>Package Name</th>
            <th>Total Debit</th>
            <th>Total Advance</th>
            <th v-if="role == 'super_admin'">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(client, key) in clientData.data">
            <td>{{ key + 1 }}</td>
            <td>{{ client.name }}</td>
            <td v-if="role == 'super_admin'">{{ client.phone_number }}</td>
            <td v-if="role == 'super_admin'">{{ (client.email ? client.email : 'NA') }}</td>
            <td v-if="role == 'super_admin'">{{ (client.address ? client.address : 'NA' ) }}</td>
            <td>{{ client.gender }}</td>
            <td>{{ (client.dob ? client.dob : 'NA' ) }}</td>
            <td>{{ (client.anniversary ? client.anniversary : 'NA' ) }}</td>
             <td>{{ (client.package ? client.package : 'NA') }}</td>
            <td>{{ (client.total_debit ? client.total_debit : '0.00') }}</td>
            <td>{{ (client.total_advance ? client.total_advance : '0.00') }}</td>
            <td class="miw-200" v-if="role == 'super_admin'">
              <!-- {{-- client edit form start--}} -->
              <a :href="'clients/'+client.id+'/edit'" class="btn btn-primary btn-sm"><i class="fas fa-pencil-alt"></i>&nbsp;Edit</a>
              <div class="d-ib">
                <button type="button" class="btn btn-success btn-sm" data-client-settle-debit :client-id="client.id" :debit-amount="client.total_debit" :disabled="(client.total_debit <= 0 ? true : false)"><i class="fas fa-rupee-sign"></i>&nbsp;Settle Debit</button>
              </div>
              <form class="d-ib" :action="'clients/delete'" method="POST">
                <input type="hidden" name="client_id" :value="client.id">
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this client?'); "><i class="fas fa-trash"></i>&nbsp;Delete</button>
              </form>
              <!-- {{-- client delete form--}} -->
              <form class="d-ib" :action="'client/reset'" method="POST">
                <input type="hidden" name="client_id" :value="client.id">
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to reset this client and invoice detail?'); "><i class="fas fa-redo-alt"></i>&nbsp;Reset</button>
              </form>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- Pagination -->
	  <pagination :data="clientData" @pagination-change-page="getResults"></pagination>
	</div>
</template>

<script>
  var path = $('#base_path').val()+'/admin/';
	export default {
		props: ['role'],
		data() {
			return {
				clientData: {},
        input_search: '',
        is_search: false,
			}
		},
		mounted() {
			this.getResults();
		},
		methods: {
			getResults(page = 1) {
        let full_path = path+'clients/lvp/get?page='+page+(this.input_search ? '&type=search&term='+this.input_search : '');
				axios.get(full_path).then(response => {
					this.clientData = response.data;
				});
			},
		},
	}
</script>