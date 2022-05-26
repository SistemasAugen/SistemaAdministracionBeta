<template>
    <div>
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-primary" data-collapsed="0">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <i class="fab fa-odnoklassniki"></i> Clientes
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" name="" id="" class="form-control" v-model="client" @keydown.enter="searchClient">
                            </div>
                            <div class="col-md-4"><button class="btn btn-success" @click="searchClient">Buscar cliente</button></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-primary" data-collapsed="0">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <i class="fas fa-clipboard"></i> RX
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" name="" id="" class="form-control" v-model="rx" @keydown.enter="searchRX">
                            </div>
                            <div class="col-md-4"><button class="btn btn-success" @click="searchRX">Buscar RX</button></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12" v-if="clients.length>0">
                    <table class="table responsive" id="clients_table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombres</th>
                                <th>Nombre comercial</th>
                                <th>Email</th>
                                <th>Celular</th>
                                <th>Domicilio</th>
                                <th>RX pendientes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="client in clients" :key="client.id">
                                <td>{{ client.id }}</td>
                                <td>{{ client.name }}</td>
                                <td>{{ client.comertial_name }}</td>
                                <td>{{ client.email }}</td>
                                <td>{{ client.celphone }}</td>
                                <td>{{ client.address }}</td>
                                <td><button class="btn btn-sm" @click="selectClient(client.id)">RX's</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div style="display:none;">
                <div id="orders_modal">
                    <h4>RX pendientes:</h4>
                    <table class="table responsive">
                        <thead>
                            <tr>
                                <th>RX</th>
                                <th>Producto</th>
                                <th>Cliente</th>
                                <th>Nombre Comercial</th>
                                <th>Antireflejante</th>
                                <th>Costo</th>
                                <th>Servicio</th>
                                <th>Subtotal</th>
                                <th>Descuentos sistema</th>
                                <th>Descuento</th>
                                <th>Descuento Promoción</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Fecha de captura</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(cart,i) in orders" :key="i">
                                <td>{{ cart.rx }}</td>
                                <td>{{ cart.product ? cart.product.name : 'N/A' }}</td>
                                <td>{{ cart.client.name }}</td>
                                <td>{{ cart.client.comertial_name }}</td>
                                <td v-if="cart.extras[0]">
                                    {{ cart.extras.map(row =>{return row.name}).join(", ") }}
                                </td>
                                <td v-else>
                                    -
                                </td>
                                <td>${{ cart.price }}</td>
                                <td>${{ cart.service }}</td>
                                <td>${{ cart.total }}</td>
                                <td>{{ cart.discount | currency }}</td>
                                <td>{{ cart.discount_admin | currency }}</td>
                                <td>
                                    <span v-if="cart.promo_discount != null">$ {{ cart.promo_discount }}</span>
                                    <span v-else>N/A</span>
                                </td>
                                <td v-if="!cart.cancelled">${{ cart.total_real }}</td>
                                <td v-else>{{ 0 | currency }} </td>
                                <button class="btn" @click="selectOrder(cart)">{{ cart.status.replace("_"," ") }}</button>
                                <td>{{ cart.created_at }}</td>
                            </tr>
                        </tbody>
                    </table>
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
                            <button class="btn btn-default btn-block" @click="changeStatus('entregado')" v-show="order.status =='terminado' || order.status == 'garantia'">Entregado</button>
                            <br>
                            <br>
                            <!-- <button class="btn btn-success btn-block" @click="changeStatus('pagado')" v-show="order.status=='terminado' || order.status=='entregado'">Pagado</button> -->
                        </div>
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
            client:"",
            rx:"",
            clients:[],
            orders:[],
            order:{}
        }
    },
    methods:{
        searchClient:function(){
            this.$parent.inPetition=true;
            axios.get(tools.url("/api/client/search/"+this.client)).then((response)=>{
                        let clients=response.data;
                        this.clients=clients;
                        this.$parent.inPetition=false;
                    })
                    .catch((err)=>{
                        this.$parent.inPetition=false;
                        this.$parent.handleErrors(err);
                    });
        },
        searchRX:function(){
            this.$parent.inPetition=true;
            axios.get(tools.url("/api/order/search/"+this.rx)).then((response)=>{
                        let orders=response.data;
                        this.orders=orders;
                        alertify.rxDialog(document.getElementById("orders_modal"));
                        this.$parent.inPetition=false;
                    })
                    .catch((err)=>{
                        this.$parent.inPetition=false;
                        this.$parent.handleErrors(err);
                    });
        },
        selectClient:function(client_id){
            this.$parent.inPetition=true;
            axios.get(tools.url("/api/order/client/"+client_id)).then((response)=>{
                        let orders=response.data;
                        this.orders=orders;
                        alertify.rxDialog(document.getElementById("orders_modal"));
                        this.$parent.inPetition=false;
                    })
                    .catch((err)=>{
                        this.$parent.inPetition=false;
                        this.$parent.handleErrors(err);
                    });
        },
        selectOrder:function(order){
            if(order.status=="en_proceso"){
                return false;
            }
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
        }
    },
    mounted(){
        alertify.rxDialog || alertify.dialog('rxDialog',function(){
            return {
                main:function(content){
                    this.setContent(content);
                    this.resizeTo("80%","70%");
                },
                setup:function(){
                    return {
                        options:{
                            basic:true,
                            maximizable:false,
                            resizable:true,
                        }
                    };
                },
                settings:{
                    selector:undefined
                }
            };
        });
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
    }
}
</script>
