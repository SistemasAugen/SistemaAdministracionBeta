<template>
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
                            <!-- <p v-if="isAdmin">PDV:</p>
                            <select class="form-control" v-model="branchId" v-if="isAdmin">
                                <option :value="branch.id" v-for="branch in branches">{{ branch.name }}</option>
                            </select> -->
                            <button class="btn btn-block btn-danger" @click="clients = []; branchId = null" v-if="branchId != null && isAdmin" style="font-size: 11px;">Deseleccionar sucursal</button>
                        </div>
                        <div class="col-md-6">
                            <p>Hasta:</p>
                            <input type="date" class="form-control" v-model="filters.end">
                            <button class="btn btn-block btn-success" @click="makeSearch()">Buscar</button>
                        </div>
                        <!-- <div class="col-md-6"> -->
                        <!-- </div> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <table class="table responsive" v-if="isAdmin && !branchId">
                <thead>
                    <tr>
                        <th>PDV</th>
                        <th>Clientes</th>
                        <th>Subtotal</th>
                        <th>Descuento sistema</th>
                        <th>Descuento adicional</th>
                        <th>Total</th>
                        <th>RX</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="branch in data.branches" :key="branch.id">
                        <td>{{ branch.name }}</td>
                        <td><button class="btn" @click="getClients(branch.id)">{{ branch.clients_orders }}</button></td>
                        <td>{{ branch.subtotal | currency }}</td>
                        <td>{{ branch.descuentos | currency }}</td>
                        <td>{{ branch.descuentos_admin | currency }}</td>
                        <td>{{ branch.total | currency }}</td>
                        <td><button class="btn" @click="selectPdv(branch.id)">({{ branch.orders }})</button></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6"><b class="pull-right">Subtotal:</b></td>
                        <td>{{ data.subtotal | currency }}</td>
                    </tr>
                    <tr>
                        <td colspan="6"><b class="pull-right">Descuentos:</b></td>
                        <td>{{ data.discounts | currency }}</td>
                    </tr>
                    <tr>
                        <td colspan="6"><b class="pull-right">Total:</b></td>
                        <td>{{ data.total | currency }}</td>
                    </tr>
                    <tr>
                        <td colspan="6"><b class="pull-right">Ordenes totales:</b></td>
                        <td>{{ data.ordersCount }}</td>
                    </tr>
                    <tr>
                        <td colspan="6"><b class="pull-right">Ticket promedio:</b></td>
                        <td>{{ data.avg | currency }}</td>
                    </tr>
                </tfoot>
            </table>
            <table class="table responsive" v-else-if="(isAdmin && branchId && clientId) || isPDV">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Nombre Comercial</th>
                        <th>Celular</th>
                        <th>Condicion de pago</th>
                        <th>RX's</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="client in clients" :key="client.id">
                        <td>{{ client.name }}</td>
                        <td>{{ client.comertial_name }}</td>
                        <td>{{ client.celphone }}</td>
                        <td>{{ client.payment_plan }} semanas.</td>
                        <td><button class="btn" @click="selectBranch(client.orders)">({{ client.orders.length }})</button></td>
                        <td>{{ mgetTotal(client.total, client.discounts, client.discounts_admin, client.ivas) | currency }}</td>
                    </tr>
                </tbody>
                <tfoot>
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
                    <tr>
                        <td colspan="5"><b class="pull-right">Ordenes totales:</b></td>
                        <td>{{ dataBranch.ordersCount }}</td>
                    </tr>
                </tfoot>
            </table>
            <table class="table responsive" v-else>
                <thead>
                    <tr>
                        <th>RX</th>
                        <th>Cliente</th>
                        <th>Nombre Comercial</th>
                        <th>Fecha</th>
                        <th>Diseño</th>
                        <th>Material</th>
                        <th>Caracteristica</th>
                        <th>Antireflejante</th>
                        <th>Costo</th>
                        <th>Servicio</th>
                        <th>Subtotal</th>
                        <th>Descuentos Sistema</th>
                        <th>Descuento</th>
                        <th>Descuento promoción</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(cart,i) in orders" :key="i">
                        <td>{{ cart.rx }}</td>
                        <td>{{ cart.client.name }}</td>
                        <td>{{ cart.client.comertial_name }}</td>
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
                        <td>${{ cart.total }}</td>
                        <td>{{ cart.discount | currency }}</td>
                        <td>{{ cart.discount_admin | currency }}</td>
                        <td>
                            <span v-if="cart.promo_discount != null">$ {{ cart.promo_discount }}</span>
                            <span v-else>N/A</span>
                        </td>
                        <td>{{ mgetTotal(cart.total, cart.discount, cart.discount_admin, cart.iva) | currency }}</td>
                        <td>{{ cart.status.charAt(0).toUpperCase() + cart.status.replace('_', ' ').slice(1) }}</td>
                        <td v-if="cart.purchase.description != null">{{ cart.purchase.description }}</td>
                        <td v-else>{{ 'N/A' }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="14"><b class="pull-right">Subtotal:</b></td>
                        <td>${{ getSubtotal }}</td>
                    </tr>
                    <tr>
                        <td colspan="14"><b class="pull-right">Descuentos:</b></td>
                        <td>${{ getDiscounts }}</td>
                    </tr>
                    <tr>
                        <td colspan="14"><b class="pull-right">IVA:</b></td>
                        <td>${{ getIva }}</td>
                    </tr>
                    <tr>
                        <td colspan="14"><b class="pull-right">Total:</b></td>
                        <td>${{ getTotal }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div style="display:none;">
            <div id="modal_rx">
                <div class="row">
                    <div class="col-md-12">
                        <h4>RX</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table responsive">
                            <thead>
                                <tr>
                                    <th>RX</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Semanas restantes</th>
                                    <th>Diseño</th>
                                    <th>Material</th>
                                    <th>Caracteristica</th>
                                    <th>Antireflejante</th>
                                    <th>Subtotal</th>
                                    <th>Descuentos sistema</th>
                                    <th>Descuento</th>
                                    <th>Descuento promoción</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(cart, i) in orders" :key="i">
                                    <td>{{ cart.rx }}</td>
                                    <td>{{ cart.client.name }}</td>
                                    <td>{{ cart.created_at }}</td>
                                    <td>
                                        <p style="background-color:green;" v-if="cart.status=='pagado'">0</p>
                                        <p style="background-color:red;" v-else-if="cart.passed<0">{{ cart.passed }}</p>
                                        <p style="background-color:orange;" v-else>{{ cart.passed }}</p>
                                    </td>
                                    <td>{{ cart.product ? cart.product.name : 'NO DISPONIBLE'  }}</td>
                                    <td>{{ cart.product ? cart.product.subcategory_name : 'NO DISPONIBLE' }}</td>
                                    <td>{{ cart.product ? cart.product.type_name : 'NO DISPONIBLE' }}</td>
                                    <td v-if="cart.extras[0]">
                                        {{ cart.extras.map(row =>{return row.name}).join(", ") }}
                                    </td>
                                    <td v-else>
                                        -
                                    </td>
                                    <td>{{ cart.total | currency }}</td>
                                    <td>{{ cart.discount | currency }}</td>
                                    <td>{{ cart.discount_admin | currency }}</td>
                                    <td>
                                        <span v-if="cart.promo_discount != null">$ {{ cart.promo_discount }}</span>
                                        <span v-else>N/A</span>
                                    </td>
                                    <td>{{ mgetTotal(cart.total,cart.discount,cart.discount_admin,cart.iva, cart.promo_discount) | currency }}</td>
                                    <td>
                                        <button class="btn btn-warning">{{ cart.status.replace("_"," ") }}</button>
                                    </td>
                                    <td v-if="cart.purchase.description != null">{{ cart.purchase.description }}</td>
                                    <td v-else>{{ 'N/A' }}</td>
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
    data(){
        return {
            branchId: null,
            clientId: null,
            clients: [],
            data: {
                branches: [],
                subtotal: 0.0,
                discounts: 0.0,
                total: 0.0
            },
            dataBranch: {
                subtotal: 0.0,
                discounts: 0.0,
                total: 0.0
            },
            orders:[],
            filters: {
                start: '',
                end: '',
            }
        }
    },
    computed:{
        isAdmin: function() {
            let roles = this.$parent.user.roles;
            for(let role of roles)
                if(role.permissions.filter(p => p.name == 'cobranza_admin').length != 0)
                    return true;
            return false;
        },
        isPDV: function() {
            let roles = this.$parent.user.roles;
            for(let role of roles)
                if(role.name == 'punto de ventas' || role.name == 'Ejecutivo')
                    return true;
            return false;
        },
        getSubtotal:function(){
            let subtotal=0;
            this.orders.forEach((v)=>{
                subtotal+=parseFloat(v.total);
            });
            return parseFloat(subtotal.toFixed(2));
        },
        getIva:function(){
            let iva=0;
            this.orders.forEach((v)=>{
                iva+=parseFloat(v.iva);
            });
            return iva.toFixed(2);
        },
        getDiscounts:function(){
            let discounts=0;
            this.orders.forEach((v)=>{
                discounts += parseFloat(v.discount) + parseFloat(v.discount_admin);
            });
            return parseFloat(discounts.toFixed(2));
        },
        getTotal:function(){
            let total=0;
            this.orders.forEach((v)=>{
                total += parseFloat(v.total) - parseFloat(v.discount) - parseFloat(v.discount_admin) + parseFloat(v.iva);
            });
            return parseFloat(total.toFixed(2));
        }
    },
    methods:{
        mgetTotal:function(subtotal,dis,dis1,iva, promo = null){
            console.log(promo);
            if(promo != null)
                return promo;
            iva = parseFloat(iva);
            return Math.abs(parseFloat((parseFloat(subtotal) - this.mgetDiscount(dis,dis1)) + iva).toFixed(2));
        },
        mgetDiscount:function(dis,dis1){
            return parseFloat(dis || 0) + parseFloat(dis1 || 0);
        },
        makeSearch() {
            if(this.isAdmin) {
                if(this.branchId != null)
                    this.getClients(this.branchId);
                else
                    this.getBranches();
            } else if(this.isPDV) {
                this.getClients(this.$parent.user.branch_id);
            } else {
                this.getOrders();
            }
        },
        getBranches(){
            this.$parent.inPetition=true;
            axios.get(tools.url("/api/branches/today"  + `?from=${ this.filters.start }&to=${ this.filters.end }`)).then((response)=>{
                this.data = response.data;
                // this.branches.forEach((v,k)=>{
                    // this.branches[k].laboratory=v.laboratory.name;
                    // this.branches[k].state=v.state.name;
                    // this.branches[k].town=v.town.name;
                // });


                jQuery('#branches').bootstrapTable('removeAll');
                jQuery('#branches').bootstrapTable('append',this.branches);
                //
                //
                // this.branchId = this.branches[0].id;
                // this.getOrders();

                this.$parent.inPetition=false;
            }).catch((error)=>{
                this.$parent.handleErrors(error);
                this.$parent.inPetition=false;
            });
        },
        getOrders:function(){
            this.orders = [];
            this.$parent.inPetition=true;
            axios.get(tools.url('api/orders/today' + (this.branchId != null ? `/${ this.branchId }` : '') + `?from=${ this.filters.start }&to=${ this.filters.end }`))
            .then((res)=>{
                this.$parent.inPetition=false;
                this.orders=res.data;
            })
            .catch((err)=>{
                this.$parent.inPetition=false;
                this.$parent.handleErrors(err);
            });
        },
        getClients(branch_id = null) {
            this.$parent.inPetition=true;

            this.branchId = branch_id;
            this.dataBranch = {
                subtotal: 0.0,
                discounts: 0.0,
                ivas: 0.0,
                total: 0.0,
                ordersCount: 0,
            };

            this.clientId = true;
            this.clients = [];

            axios.get(tools.url("api/clients/today" + (branch_id ? `/${ branch_id }`: '') + `?from=${ this.filters.start }&to=${ this.filters.end }`)).then((res)=>{
                this.clients=res.data;
                this.clients.forEach(c => {
                    this.dataBranch.subtotal += parseFloat(c.total);
                    this.dataBranch.discounts += parseFloat(c.discounts_admin) + parseFloat(c.discounts);
                    this.dataBranch.ivas += parseFloat(c.ivas);
                    this.dataBranch.ordersCount += parseInt(c.orders.length);
                });

                this.dataBranch.total = this.dataBranch.subtotal + this.dataBranch.ivas - this.dataBranch.discounts;
                this.$parent.inPetition=false;
            })
            .catch((err)=>{
                this.$parent.handleErrors(err);
                this.$parent.inPetition=false;
            });
        },
        selectPdv(branch_id) {
            this.clientId = null;
            this.branchId = branch_id;
            this.getOrders();
        },
        selectBranch:function(orders) {
            this.orders=orders;
            alertify.rxDialog(document.getElementById('modal_rx'));
        },
    },
    mounted(){
        let date = new Date();
            date.setTime(new Date().getTime() - (6 * 60 * 60 * 1000));
        this.filters.start = this.filters.end = date.toISOString().slice(0,10);
        if(this.isAdmin)
            this.getBranches();
        else if(this.isPDV)
            this.getClients(this.$parent.user.branch_id);
        else
            this.getOrders();

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
<style lang="scss" scoped>

.filters {
    div {
        margin-bottom: 15px;
    }

    button {
        margin-top: 30px;
    }
}

</style>
