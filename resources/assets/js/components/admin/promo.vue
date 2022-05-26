<template>
  <div id="" class="container">
      <h1> Promociones </h1>
      <table class="table">
          <thead>
              <tr>
                  <th>Nombre</th>
                  <th>Tipo</th>
                  <th>Cantidad en %</th>
                  <th>Empieza</th>
                  <th>Termina</th>
              </tr>
          </thead>
          <tbody>
              <tr v-for="promo in promos" :key="promo.id">
                <td>{{ promo.name }}</td>
                <td>{{ promo.type }}</td>
                <td>{{ promo.amount }} %</td>
                <td>{{ promo.starts_at }}</td>
                <td>{{ promo.ends_at }}</td>
              </tr>
          </tbody>
      </table>
  </div>
</template>
<script>
export default {
    data: () => ({
        promos: [],
    }),
    methods: {
        getPromos() {
            this.$parent.inPetition=true;
            axios.get('/api/promos').then(result => {
                this.promos = result.data;
                console.log(result.data);
                this.$parent.inPetition=false;
            }).catch((error)=>{
                this.$parent.handleErrors(error);
                this.$parent.inPetition=false;
            });
        }
    },
    mounted() {
        this.getPromos();
    }
}
</script>
<style lang="scss" scoped>
</style>
