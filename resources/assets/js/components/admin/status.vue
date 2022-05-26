<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default panel-shadow" data-collapsed="0">
                    <div class="panel-heading">
                        <div class="panel-title">Filtros</div>
                    </div> <!-- panel body -->
                    <div class="panel-body">
                        <div class="row filters">
                            <!-- <div class="col-md-6">
                                <p>Fecha:</p>
                                <input type="date" class="form-control" v-model="filters.date">
                            </div> -->
                            <div class="col-md-6">
                                <p>Desde:</p>
                                <input type="date" class="form-control" v-model="filters.start">
                            </div>
                            <div class="col-md-6">
                                <p>Hasta:</p>
                                <input type="date" class="form-control" v-model="filters.end">
                            </div>
                            <!-- <div class="col-md-6">
                                <p>Laboratorio de Origen:</p>
                                <select class="form-control" v-model="filters.lab">
                                    <option :value="branch.id" v-for="branch in branches">{{ branch.name }}</option>
                                </select>
                            </div> -->
                            <div class="col-md-6">
                                <p>Rx:</p>
                                <input type="text" class="form-control" v-model="filters.rx">
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-block btn-success" @click="getOrders()">Buscar</button>
                            </div>
                            <!-- <div class="col-md-12">
                                <div class="row"><div class="col-md-12"><p>Estado:</p></div></div>
                                <div class="row">
                                    <div class="col-md-3">
                                        En proceso<vSwitch name="en_proceso" :checked="true" v-model="filters.status.en_proceso"></vSwitch>
                                    </div>
                                    <div class="col-md-3">
                                        Terminado<vSwitch name="terminado" :checked="true" v-model="filters.status.terminado"></vSwitch>
                                    </div>
                                    <div class="col-md-3">
                                        Entregado<vSwitch name="entregado" :checked="true" v-model="filters.status.entregado"></vSwitch>
                                    </div>
                                    <div class="col-md-3">
                                        Pagado<vSwitch name="pagado" :checked="true" v-model="filters.status.pagado"></vSwitch>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h1>Cálculos globales</h1>
                <div class="table-responsive">
                    <table class="table responsive table-striped response">
                        <tr>
                            <td>EN PROCESO <i class="fas fa-arrow-right"></i> TERMINADO</td>
                            <td><button class="btn btn-default" @click="filter('finish_date')" style="width: 80px !important; margin-right: 5px;">{{ data.terminado }}</button> <a @click.prevent="downloadExcel('finish_date')" class="btn btn-success" v-if="data.terminado > 0"><i class="fas fa-file-excel"></i></a></td>
                        </tr>
                        <tr>
                            <td>TERMINADO <i class="fas fa-arrow-right"></i> ENTREGADO</td>
                            <td><button class="btn btn-default" @click="filter('delivered_date')" style="width: 80px !important; margin-right: 5px;">{{ data.entregado }}</button> <a @click.prevent="downloadExcel('delivered_date')" class="btn btn-success" v-if="data.entregado > 0"><i class="fas fa-file-excel"></i></a></td>
                        </tr>
                        <tr>
                            <td>ENTREGADO <i class="fas fa-arrow-right"></i> PAGADO</td>
                            <td><button class="btn btn-default" @click="filter('payment_date')" style="width: 80px !important; margin-right: 5px;">{{ data.pagado }}</button> <a @click.prevent="downloadExcel('payment_date')" class="btn btn-success" v-if="data.pagado > 0"><i class="fas fa-file-excel"></i></a></td>
                        </tr>
                        <tr>
                            <td>GARANTÍA <i class="fas fa-arrow-right"></i> ENTREGADO</td>
                            <td><button class="btn btn-default" @click="filter('delivered_date2')" style="width: 80px !important; margin-right: 5px;">{{ data.garantia }}</button> <a @click.prevent="downloadExcel('delivered_date2')" class="btn btn-success" v-if="data.garantia > 0"><i class="fas fa-file-excel"></i></a></td>
                        </tr>
                    </table>
                    <!-- <table class="table responsive"> -->
                        <!-- RX | FECHA CAPTURA |	LAB DESTINO	 | DISEÑO |	MATERIAL  |	AR | STATUS | CONT. DIAS | FECHA TERMINADO| FECHA ENTREGADO | FECHA PAGADO -->
                        <!-- <thead> -->
                            <!-- <tr> -->
                                <!-- <th>RX</th> -->
                                <!-- <th>NOMBRE COMERCIAL</th> -->
                                <!-- <th>FECHA CAPTURA</th> -->
                                <!-- <th>LAB DESTINO</th> -->
                                <!-- <th>DISEÑO</th> -->
                                <!-- <th>MATERIAL</th> -->
                                <!-- <th>AR</th> -->
                                <!-- <th>STATUS</th> -->
                                <!-- <th>CONT. DÍAS</th> -->
                                <!-- <th>FECHA TERMINADO</th> -->
                                <!-- <th>FECHA ENTREGADO</th> -->
                                <!-- <th>FECHA PAGADO</th> -->
                            <!-- </tr> -->
                        <!-- </thead> -->
                        <!-- <tbody> -->
                            <!-- <tr v-for="order in orders" :key="order.id"> -->
                                <!-- <td>{{ order.rx }} </td> -->
                                <!-- <td>{{ order.client.comertial_name }} </td> -->
                                <!-- <td>{{ order.created_at }} </td> -->
                                <!-- <td>{{ order.laboratory.name }} </td> -->
                                <!-- <td>{{ order.product ? order.product.name : 'NO DISPONIBLE'  }}</td> -->
                                <!-- <td>{{ order.product ? order.product.subcategory_name : 'NO DISPONIBLE' }}</td> -->
                                <!-- <td>{{ order.product ? order.product.type_name : 'NO DISPONIBLE' }}</td> -->
                                <!-- <td>{{ order.status.charAt(0).toUpperCase() + order.status.replace('_', ' ').slice(1) }}</td> -->
                                <!-- <td>{{ order.cont_dias }}</td> -->
                                <!-- <td>{{ order.finish_date ? order.finish_date : '-'  }}</td> -->
                                <!-- <td>{{ order.delivered_date ? order.delivered_date : '-'  }}</td> -->
                                <!-- <td>{{ order.payment_date ? order.payment_date : '-'  }}</td> -->
                            <!-- </tr> -->
                        <!-- </tbody> -->
                    <!-- </table> -->
                </div>
            </div>
        </div>
        <div style="display:none;">
            <div id="modal_rx">
                <div class="row">
                    <div class="col-md-12">
                        <h4>RX pendientes:</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table responsive">
                            <thead>
                                <tr>
                                    <th>RX</th>
                                    <th>Fecha de captura</th>
                                    <th v-if="filter.rx != ''">Fecha de terminado</th>
                                    <th v-if="filter.rx != ''">Fecha de entregado</th>
                                    <th v-if="filter.rx != ''">Fecha de pago</th>
                                    <th>Lab Origen</th>
                                    <th>Lab Destino</th>
                                    <th>Cliente</th>
                                    <th>Diseño</th>
                                    <th>Material</th>
                                    <th>Caracteristica</th>
                                    <th>Antireflejante</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Cont. Días</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(cart,i) in orders" :key="i">
                                    <td>{{ cart.rx }}</td>
                                    <td>{{ cart.created_at }}</td>
                                    <td v-if="filter.rx != ''">{{ cart.finish_date }}</td>
                                    <td v-if="filter.rx != ''">{{ cart.delivered_date }}</td>
                                    <td v-if="filter.rx != ''">{{ cart.payment_date }}</td>
                                    <td>{{ cart.branch.name }}</td>
                                    <td>{{ cart.laboratory.name }}</td>
                                    <td>{{ cart.client.name }}</td>
                                    <td>{{ cart.product ? cart.product.name : 'NO DISPONIBLE'  }}</td>
                                    <td>{{ cart.product ? cart.product.subcategory_name : 'NO DISPONIBLE' }}</td>
                                    <td>{{ cart.product ? cart.product.type_name : 'NO DISPONIBLE' }}</td>
                                    <td v-if="cart.extras[0]">
                                        {{ cart.extras.map(row =>{return row.name}).join(", ") }}
                                    </td>
                                    <td v-else>
                                        -
                                    </td>
                                    <td>{{ getTotal(cart.total, cart.discount, cart.discount_admin, cart.iva) | currency }}</td>
                                    <td>
                                        <button class="btn btn-warning">{{ cart.status.replace("_"," ") }}</button>
                                    </td>
                                    <td>{{ cart.cont_dias }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "",
    data: () => ({
        branches: [],
        orders: [],
        data: {
            terminado: 0,
            entregado: 0,
            pagado: 0,
            garantia: 0,
        },
        filters: {
            rx: '',
            date: '',
            start: '',
            end: '',
            lab: '',
            status:{
                en_proceso:true,
                terminado:true,
                entregado:true,
                pagado:true
            },
            user_id: null,
        }
    }),
    methods: {
        getBranches(){
            this.$parent.inPetition=true;
            axios.get(tools.url("/api/branches")).then((response)=>{
                this.branches=response.data;
                this.branches.forEach((v,k)=>{
                    this.branches[k].laboratory=v.laboratory.name;
                    this.branches[k].state=v.state.name;
                    this.branches[k].town=v.town.name;
                });

                jQuery('#branches').bootstrapTable('removeAll');
                jQuery('#branches').bootstrapTable('append',this.branches);

                this.filters.lab = this.branches[0].id;
                this.getOrders();

                this.$parent.inPetition=false;
            }).catch((error)=>{
                this.$parent.handleErrors(error);
                this.$parent.inPetition=false;
            });
        },
        getOrders() {
            if(this.filters.rx !== '') {
                this.filter('rx');
                return;
            }
            // this.orders = [];
            this.$parent.inPetition=true;
            axios.post('/api/orders/status', this.filters).then(result => {
                // this.orders = result.data;
                this.data = result.data;
                this.$parent.inPetition=false;
            }).catch((error)=>{
                this.$parent.handleErrors(error);
                this.$parent.inPetition=false;
            });
        },
        getTotal:function(subtotal,dis,dis1,iva){
            iva = parseFloat(iva);
            return parseFloat((parseFloat(subtotal) - this.getDiscount(dis,dis1)) + iva);
        },
        getDiscount:function(dis,dis1){
            return parseFloat(dis || 0) + parseFloat(dis1 || 0);
        },
        filter(type) {
            this.$parent.inPetition=true;
            axios.post('/api/orders/status?q=' + type, this.filters).then(result => {
                this.orders = result.data;
                alertify.rxDialog(document.getElementById("modal_rx"));
                this.$parent.inPetition=false;
            }).catch((error)=>{
                this.$parent.handleErrors(error);
                this.$parent.inPetition=false;
            });
        },
        downloadExcel(type) {
            let urlParams = Object.keys(this.filters).map(k => `${encodeURIComponent(k)}=${encodeURIComponent(this.filters[k])}`).join('&');
            let url = `/v1/status?q=${ type }&` + urlParams;

            var element = document.createElement('a');
            element.setAttribute('href', url);
            // element.setAttribute('target', '_blank');
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
        this.filters.user_id = this.$parent.user.id;
        // this.getBranches();

        var self = this;

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
                            maximizable:true,
                            resizable:true,
                            onclose: function() {
                                self.filters.rx = '';
                            }
                        },
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
<style lang="scss" scoped>

.filters {
    div {
        margin-bottom: 15px;
    }

    button {
        margin-top: 30px;
    }
}

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
