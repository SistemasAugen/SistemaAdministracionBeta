var router = new VueRouter({
    mode: 'history',
    routes: [],
});

var app = new Vue({
    router,
    el: '#app',
    data: () => ({
        data: [],
        timer: null
    }),
    computed: {
        id: function() {
            return this.$route.query.lab;
        }
    },
    methods: {
        getOrders() {
            swal.fire('Espera un momento', 'Se está actualizando la información');
            swal.showLoading();
            this.timer = null;
            this.data = [];
            axios.get(`/api/v1/orders?lab=${ this.id }`).then(result => {
                try {
                    this.data = result.data.pages;
                } catch (e) {
                    this.orders = [];
                } finally {
                    swal.close();
                    if(this.timer == null) {
                        this.timer = setTimeout(this.getOrders, 1000 * 60);
                    }
                }
            });
        }
    },
    mounted() {
        this.getOrders();
    }
})
