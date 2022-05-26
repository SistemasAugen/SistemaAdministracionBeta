<template>
  <div>
      <h1>Firma de Asistencia <span class="text-info">( {{ timeZone }} UTC )</span> </h1>
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
                              <input type="date" class="form-control" v-model="filters.date">
                          </div>
                          <div class="col-md-6">
                              <button class="btn btn-block btn-success" @click="getLogs()" style="margin-top: 30px;">Buscar</button>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="col-md-12">
              <table class="table responsive table-log">
                  <thead>
                      <tr>
                          <th>Nombre</th>
                          <th>Email</th>
                          <th>Hora</th>
                          <th>Zona Horaria</th>
                      </tr>
                  </thead>
                  <tbody>
                      <tr v-for="log in logs" :key="log.id">
                          <td>{{ log.name }}</td>
                          <td>{{ log.email }}</td>
                          <td>{{ log.login_time }}</td>
                          <td>{{ log.time_zone }} UTC</td>
                      </tr>
                  </tbody>
              </table>
              <p class="message">Reportado <span :class="(logs.length < maxAssistance ? 'text-danger' : 'text-success')">{{ logs.length }}</span> de <span class="text-info">{{ maxAssistance }}</span> posibles asistencias. </p>
          </div>
      </div>
  </div>
</template>
<script>
export default {
    name: "",
    data: () => ({
        logs: [],
        maxAssistance: 0,
        filters: {
            date: null,
        },
        timeZone: null,
    }),
    methods: {
        getLogs() {
            this.logs = [];
            this.$parent.inPetition = true;
            axios.post(tools.url("api/logs"), this.filters).then((res) => {
                this.logs = res.data.logs;
                this.maxAssistance = res.data.maxAssistance;
                console.log(this.logs);
                alertify.closeAll();
                this.$parent.inPetition=false;
            }).catch((err)=>{
                this.$parent.handleErrors(err);
                this.$parent.inPetition=false;
            });
        },
        getTimeZone() {
            var offset = new Date().getTimezoneOffset(), o = Math.abs(offset);
            return (offset < 0 ? "+" : "-") + ("00" + Math.floor(o / 60)).slice(-2) + ":" + ("00" + (o % 60)).slice(-2);
        }
    },
    mounted() {
        let date = new Date();
            date.setTime(new Date().getTime() - (6 * 60 * 60 * 1000));
        this.filters.date = date.toISOString().slice(0,10);
        this.getLogs();
        this.timeZone = this.getTimeZone();
    }
}
</script>
<style lang="scss" scoped>

table.table-log {
    max-width: 1000px;
    margin: 25px auto;
    border: 1px solid #ebebeb;
    -webkit-box-shadow: 0 3px 1px rgba(0,0,0,.04);
    -moz-box-shadow: 0 3px 1px rgba(0,0,0,.04);
    box-shadow: 0 3px 1px rgba(0,0,0,.04);
    // border-radius: 3px;
    background-clip: padding-box;
}

p.message {
    text-align: right;
    margin-right: 100px;
    font-size: 14px;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: #000;
}
</style>
