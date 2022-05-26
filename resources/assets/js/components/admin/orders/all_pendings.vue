<template>
    <div class="row">
        <div class="col-md-12">
            <h3 style="display:inline-block;">RX pendientes</h3>
            <!-- <button @click="getOrders" class="btn pull-right"><i class="fas fa-sync"></i></button> -->
        </div>
        <div class="row" style="padding: 0px 20px;">
            <div class="col-md-2">
                <p>Desde:</p>
                <input type="date" class="form-control" v-model="filters.start">
            </div>
            <div class="col-md-2">
                <p>Hasta:</p>
                <input type="date" class="form-control" v-model="filters.end">
            </div>
            <div class="col-md-8">
                <div class="row"><div class="col-md-12"><p>Estado:</p></div></div>
                <div class="row" style="display: flex;">
                    <div class="col" style="flex: 1;">
                        En proceso<vSwitch name="en_proceso" :checked="true" v-model="filters.status.en_proceso"></vSwitch>
                    </div>
                    <div class="col" style="flex: 1;">
                        Terminado<vSwitch name="terminado" :checked="true" v-model="filters.status.terminado"></vSwitch>
                    </div>
                    <div class="col" style="flex: 1;">
                        Entregado<vSwitch name="entregado" :checked="true" v-model="filters.status.entregado"></vSwitch>
                    </div>
                    <div class="col" style="flex: 1;">
                        Pagado<vSwitch name="pagado" :checked="true" v-model="filters.status.pagado"></vSwitch>
                    </div>
                    <div class="col" style="flex: 1;">
                        Garantía<vSwitch name="pagado" :checked="true" v-model="filters.status.garantia"></vSwitch>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <p>RX:</p>
                <input type="text" name="" id="" class="form-control" v-model="filters.search" @keydown.enter="getOrders">
            </div>
            <div class="col-md-2">
                <button class="btn btn-block btn-success" @click="getOrders" style="margin-top: 30px;">Filtrar</button>
            </div>
        </div>
        <div class="col-md-12">
            <table class="table responsive" id="rx-table">
                <thead>
                    <tr>
                        <th>RX</th>
                        <th>Cliente</th>
                        <th>Nombre Comercial</th>
                        <th>Laboratorio</th>
                        <th>Fecha</th>
                        <th>Diseño</th>
                        <th>Material</th>
                        <th>Caracteristica</th>
                        <th>Antireflejante</th>
                        <th>Costo</th>
                        <th>Servicio</th>
                        <th>Subtotal</th>
                        <th>Descuento sistema</th>
                        <th>Descuento</th>
                        <th>Descuento promoción</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Acciones</th>
                        <!-- <th>Semanas Restantes</th> -->
                    </tr>
                </thead>
                <tbody v-if="orders.length > 0">
                    <tr v-for="(cart,i) in orders" :key="i">
                        <td>{{ cart.rx }}</td>
                        <td>{{ cart.client.name }}</td>
                        <td>{{ cart.client.comertial_name }}</td>
                        <td>{{ cart.branch.name }}</td>
                        <td>{{ cart.created_at }}</td>
                        <td>{{ cart.product.name }}</td>
                        <td>{{ cart.product.subcategory_name }}</td>
                        <td>{{ cart.product.type_name }}</td>
                        <td v-if="cart.extras[0]">
                            {{ cart.extras.map(row =>{return row.name}).join(", ") }}
                        </td>
                        <td v-else>
                            -
                        </td>
                        <td>${{ cart.price }}</td>
                        <td>${{ cart.service }}</td>
                        <td>{{ getTotal(cart.price, 0, 0,cart.service) | currency }}</td>
                        <td>${{ cart.discount }}</td>
                        <td><button class="btn" @click="selectDiscount(cart.id)">${{ cart.discount_admin }}</button></td>
                        <td>
                            <span v-if="cart.promo_discount != null">$ {{ cart.promo_discount }}</span>
                            <span v-else>N/A</span>
                        </td>
                        <td v-if="cart.status=='en_proceso'">
                            <button class="btn btn-success" @click="selectOrder(cart)">{{ cart.status.replace("_"," ") }}</button>
                        </td>
                        <td v-else-if="cart.status == 'cancelado'">
                            <button class="btn btn-danger">{{ cart.status.replace("_"," ") }}</button>
                        </td>
                        <td v-else>
                            <button class="btn btn-warning" @click="selectOrder(cart)">{{ cart.status.replace("_"," ") }}</button>
                        </td>
                        <td v-if="!cart.cancelled">{{ getTotal(cart.total,cart.discount,cart.discount_admin,cart.iva, cart.promo_discount) | currency }}</td>
                        <td v-else>{{ 0 | currency }} </td>
                        <td>
                            <div v-if="!cart.cancelled && !cart.payed" style="width: 120px;">
                                <!-- <button class="btn btn-primary" @click.prevent="applyCourtesy(cart.rx, cart.id, cart)"><i class="fas fa-gift"></i></button> -->
                                <button class="btn btn-danger" @click.prevent="applyCancelled(cart.rx, cart.id, cart)" v-if="!cart.cancelled"><i class="fas fa-times"></i></button>
                                <button class="btn btn-info" @click.prevent="applyPayment(cart.rx, cart.id, cart)" v-if="!cart.payed"><i class="fas fa-money-bill-alt"></i></button>
                            </div>
                            <div v-else style="width: 120px; text-align: center;">
                                -
                            </div>
                        </td>
                        <!-- <td>{{ '0' }}</td> -->
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="display:none;">
            <div id="discount_table">
                <div class="row">
                    <div class="col-md-12">
                        <h3>Ingresa el nuevo descuento:</h3>
                    </div>
                    <form action="" @submit.prevent="setDiscount">
                        <div class="col-md-9">
                            $<input type="number" step=".01" class="" min="0" v-model="discount" >
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-success">Aplicar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div id="status_table">
                <div class="row">
                    <div class="col-md-12">
                        <h3>Cambio de estatus:</h3>
                    </div>
                    <div class="col-md-6 col-md-offset-3">
                        <br>
                        <br>
                        <button class="btn btn-warning btn-block" @click="changeStatus('garantia')" v-show="order.status != 'en_proceso' && order.status != 'garantia'">Garantia</button>
                        <br>
                        <br>
                        <button class="btn btn-success btn-block" @click="changeStatus('terminado')" v-show="order.status == 'en_proceso'">Terminado</button>
                        <br>
                        <br>
                        <button class="btn btn-default btn-block" @click="changeStatus('entregado')" v-show="order.status =='terminado' || order.status == 'garantia'">Entregado</button>
                        <!-- <button class="btn btn-success btn-block" @click="changeStatus('pagado')" v-show="order.status=='terminado' || order.status=='entregado'">Pagado</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data(){
        return {
            filters:{
                start: "",
                end: "",
                status:{
                    en_proceso:true,
                    terminado:true,
                    entregado:true,
                    pagado:true,
                    garantia: true
                },
                search: '',
            },
            orders:[],
            order: {},
            discount:0,
            order_id:"",
        }
    },
    methods:{
        applyPayment(rx, id, order) {
            if(!confirm(`¿Estás seguro que deseas marcar la RX: ${ rx } como pagada?`))
                return;

            this.$parent.inPetition=true;
            axios.post('/api/orders/' + id + '/payed').then(result => {
                order.payed = 1;
                this.$parent.inPetition = false;
                this.$parent.showMessage(result.data.msg,"success");
            }).catch((err)=>{
                this.$parent.inPetition = false;
                this.$parent.handleErrors(err);
            });
        },
        applyCancelled(rx, id, order) {
            if(!confirm(`¿Estás seguro que deseas cancelar la RX: ${ rx }?`))
                return;

            this.$parent.inPetition=true;
            axios.post('/api/orders/' + id + '/cancel').then(result => {
                order.cancelled = 1;
                order.status = 'cancelado';
                this.$parent.inPetition = false;
                this.$parent.showMessage(result.data.msg,"success");
            }).catch((err)=>{
                this.$parent.inPetition = false;
                this.$parent.handleErrors(err);
            });
        },
        getTotal:function(subtotal,dis,dis1,iva, promo = null){
            console.log(promo);
            if(promo != null)
                return promo;
            iva = parseFloat(iva);
            return Math.abs(parseFloat((parseFloat(subtotal) - this.getDiscount(dis,dis1)) + iva).toFixed(2));
        },
        getDiscount:function(dis,dis1){
            return parseFloat(dis || 0) + parseFloat(dis1 || 0);
        },
        getOrders:function(){
            this.orders = [];
            this.$parent.inPetition=true;
            axios.post(tools.url('api/orders'), this.filters)
            .then((res)=>{
                this.$parent.inPetition=false;
                this.orders=res.data;
                console.log(this.orders.length);
                // setTimeout(()=>{
                //     this.mounthTable();
                // }, 1000);
            })
            .catch((err)=>{
                this.$parent.inPetition=false;
                this.$parent.handleErrors(err);
            });
        },
        selectDiscount:function(id){
            this.order_id=id;
            alertify.discountDialog(document.getElementById("discount_table"));
        },
        setDiscount:function(){
            this.$parent.inPetition=true;
            let params={
                discount:this.discount
            }
            axios.post(tools.url('api/orders/discount/'+this.order_id),params)
            .then((res)=>{
                this.$parent.inPetition=false;
                alertify.closeAll();
                this.$parent.showMessage(res.data.msg,"success");
                this.getOrders();
            })
            .catch((err)=>{
                this.$parent.inPetition=false;
                this.$parent.handleErrors(err);
            });
        },
        selectOrder:function(order){
            // if(order.status=="en_proceso"){
            //     return false;
            // }
            this.order=order;
            alertify.statusDialog(document.getElementById('status_table'));
        },
        changeStatus:function(status){
            alertify.confirm('¿Seguro que deseas cambiar el estatus de este RX?', ()=>{
                this.$parent.inPetition=true;
                let params={
                    status:status
                };
                axios.post(tools.url("api/orders/status/"+this.order.id),params)
                .then((res)=>{
                    this.$parent.inPetition=false;
                    this.$parent.showMessage(res.data.msg,"success");
                    this.clients=[];
                    alertify.closeAll();
                })
                .catch((err)=>{
                    this.$parent.inPetition=false;
                    this.$parent.handleErrors(err);
                });
            });
        },
        mounthTable(){
            // jQuery('#rx-table').bootstrapTable('removeAll');
            // jQuery('#rx-table').bootstrapTable('destroy');

            jQuery('#rx-table').bootstrapTable({
                //Boton de refrescar x
                exportTypes: ['excel'],
                exportDataType: 'all',
                showRefresh:true,
                showExport: true,
                showToggle: true,
                pagination: true,
                search: false,
                exportOptions: {
                    fileName: 'rx-pendientes' + this.filters.start + '_' + this.filters.end
                }
            });

            jQuery('#rx-table').on('refresh.bs.table',()=>{
                this.getOrders();
            });
            // this.getClients();
        },
    },
    mounted() {
        let date = new Date();
            date.setTime(new Date().getTime() - (6 * 60 * 60 * 1000));
        this.filters.start = this.filters.end = date.toISOString().slice(0,10);

        this.getOrders();
        alertify.statusDialog || alertify.dialog('statusDialog',function(){
            return {
                main:function(content){
                    this.setContent(content);
                },
                setup:function(){
                    return {
                        options:{
                            basic:true,
                            maximizable:false,
                            resizable:false,
                        }
                    };
                },
                settings:{
                    selector:undefined
                }
            };
        });
        alertify.discountDialog || alertify.dialog('discountDialog',function(){
            return {
                main:function(content){
                    this.setContent(content);
                },
                setup:function(){
                    return {
                        options:{
                            basic:true,
                            maximizable:true,
                            resizable:false,
                        }
                    };
                },
                settings:{
                    selector:undefined
                }
            };
        });
    }
}
</script>
<style lang="scss">
.col {
    label.switch {
        display: block;
    }
}
</style>
