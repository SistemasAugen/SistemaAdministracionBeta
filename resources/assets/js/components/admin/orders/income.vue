<template>
  <div id="">
      <div class="row">
          <div class="col-md-12">
              <div class="panel panel-default panel-shadow" data-collapsed="0">
                  <div class="panel-heading">
                      <div class="panel-title">Filtros</div>
                  </div> <!-- panel body -->
                  <div class="panel-body">
                      <div class="row filters">
                          <div class="col-md-6">
                              <p>Desde:</p>
                              <input type="date" class="form-control" v-model="filters.start">
                          </div>
                          <div class="col-md-6">
                              <p>Hasta:</p>
                              <input type="date" class="form-control" v-model="filters.end">
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
      <div class="row">
          <div class="col-md-12">
              <h1>Ingresos</h1>
              <div class="table-responsive">
                  <table class="table responsive table-striped response">
                      <tr>
                          <td>ESTADO DE CUENTA</td>
                          <td><a href="#" @click="buildUrl('EDO_CUENTA')" class="btn btn-info">Generar reporte <i class="fas fa-file-excel"></i></a></td>
                      </tr>
                      <tr>
                          <td>COBRANZA</td>
                          <td><a href="#" @click="buildUrl('COBRANZA')" class="btn btn-info">Generar reporte <i class="fas fa-file-excel"></i></a></td>
                      </tr>
                      <tr>
                          <td>VENCIDO</td>
                          <td><a href="#" @click="buildUrl('VENCIDO')" class="btn btn-info">Generar reporte <i class="fas fa-file-excel"></i></a></td>
                      </tr>
                      <tr>
                          <td>CONTADO</td>
                          <td><a href="#" @click="buildUrl('CONTADO')" class="btn btn-info">Generar reporte <i class="fas fa-file-excel"></i></a></td>
                      </tr>
                      <tr>
                          <td>ANTICIPO</td>
                          <td><a href="#" @click="buildUrl('ANTICIPO')" class="btn btn-info">Generar reporte <i class="fas fa-file-excel"></i></a></td>
                      </tr>
                  </table>
              </div>
          </div>
      </div>
  </div>
</template>
<script>
export default {
    name: "",
    data: () => ({
        filters: {
            start: '',
            end: ''
        },
    }),
    methods: {
        buildUrl(param) {
            let url = `/generate/income?type=${ param }&start_date=${ this.filters.start }&end_date=${ this.filters.end }`;


            var element = document.createElement('a');
            element.setAttribute('href', url);
            element.setAttribute('target', '_blank');
            // element.setAttribute('download', filename);

            element.style.display = 'none';
            document.body.appendChild(element);

            element.click();

            document.body.removeChild(element);
        }
    },
    mounted() {
        let date = new Date();
            date.setTime(new Date().getTime() - (6 * 60 * 60 * 1000));
        this.filters.date = this.filters.start = this.filters.end = date.toISOString().slice(0,10);
    }
}
</script>
<style lang="scss" scoped>


table {
    thead {
        th {
            text-transform: uppercase;
        }
    }
}

table.response {
    max-width: 600px;
    margin: 25px auto;
    border: 1px solid #ebebeb;
    -webkit-box-shadow: 0 3px 1px rgba(0,0,0,.04);
    -moz-box-shadow: 0 3px 1px rgba(0,0,0,.04);
    box-shadow: 0 3px 1px rgba(0,0,0,.04);
    // border-radius: 3px;
    background-clip: padding-box;

    tr {
        td {
            width: 50%;
            font-size: 16px;
            padding: 10px;
            text-align: center;
            border-bottom: 3px solid #f0f0f1;

            &:first-child {
                font-weight: bold;
                // padding-left: 10px;
                background-color: #f0f0f1;
                color: #373e4a;
                border-color: #ebebeb;
            }
        }
    }
}

</style>
