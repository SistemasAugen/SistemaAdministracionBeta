<template>
  <div id="new-clients">
      <div class="row">
          <div class="col-md-12">
              <div class="panel panel-default panel-shadow" data-collapsed="0">
                  <div class="panel-heading">
                      <div class="panel-title">Filtros</div>
                  </div> <!-- panel body -->
                  <div class="panel-body">
                      <div class="row filters">
                          <div class="col-md-6">
                              <p>Fecha:</p>
                              <input type="month" class="form-control" v-model="filters.month">
                          </div>
                          <div class="col-md-6">
                              <button class="btn btn-block btn-success" @click="getClients()" style="margin-top: 30px;">Buscar</button>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="col-md-12">
              <table class="table responsive">
                  <thead>
                      <tr>
                          <th>Cliente</th>
                          <th>Nombre Comercial</th>
                          <th>PDV</th>
                          <th>Total</th>
                      </tr>
                  </thead>
                  <tbody>
                      <tr v-for="client in clients" :key="client.id">
                          <td>{{ client.name }}</td>
                          <td>{{ client.comertial_name }}</td>
                          <td>{{ client.branch.name }}</td>
                          <td>{{ client.total | currency }}</td>
                      </tr>
                  </tbody>
                  <!-- <tfoot>
                      <tr>
                          <td colspan="5"><b class="pull-right">Subtotal:</b></td>
                          <td>{{ dataBranch.subtotal | currency }}</td>
                      </tr>
                      <tr>
                          <td colspan="5"><b class="pull-right">IVA:</b></td>
                          <td>{{ dataBranch.ivas | currency }}</td>
                      </tr>
                      <tr>
                          <td colspan="5"><b class="pull-right">Descuentos:</b></td>
                          <td>{{ dataBranch.discounts | currency }}</td>
                      </tr>
                      <tr>
                          <td colspan="5"><b class="pull-right">Total:</b></td>
                          <td>{{ dataBranch.total | currency }}</td>
                      </tr>
                  </tfoot> -->
              </table>
          </div>
      </div>
  </div>
</template>
<script>

export default {
    name: "",
    data: () => ({
        clients: [],
        filters: {
            month: null
        }
    }),
    methods: {
        getClients() {
            this.clients = [];
            this.$parent.inPetition = true;
            axios.post(tools.url("api/clients/news"), this.filters).then((res) => {
                this.clients = res.data.clients;
                alertify.closeAll();
                this.$parent.inPetition=false;
            }).catch((err)=>{
                this.$parent.handleErrors(err);
                this.$parent.inPetition=false;
            });
        }
    }

}
</script>
<style lang="scss" scoped>
</style>
