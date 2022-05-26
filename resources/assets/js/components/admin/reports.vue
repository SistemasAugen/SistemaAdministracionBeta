<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default panel-shadow" data-collapsed="0">
                    <div class="panel-heading">
                        <div class="panel-title">Filtros</div>
                    </div> <!-- panel body -->
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-2">
                                <p>Desde:</p>
                                <input type="date" class="form-control" v-model="filters.start">
                            </div>
                            <div class="col-md-2">
                                <p>Hasta:</p>
                                <input type="date" class="form-control" v-model="filters.end">
                            </div>
                            <div class="col-md-6">
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
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-block btn-success" @click="getReports">Filtrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table responsive">
                    <thead>
                        <tr>
                            <th>PDV</th>
                            <th>Clientes</th>
                            <th>Pagado</th>
                            <th>Pendiente</th>
                            <th>Total de RX</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="branch in data" :key="branch.id">
                            <td>{{ branch.name }}</td>
                            <td><button class="btn" @click="selectBranchClients(branch.orders_ordered)">{{ branch.clients_orders }}</button></td>
                            <td>${{ toLocal(branch.total_payed) }}</td>
                            <td>${{ toLocal(branch.total_debt) }}</td>
                            <td>{{ branch.orders.length }}</td>
                            <td><button class="btn" @click="selectBranch(branch.orders)">Ver RX's</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div style="display:none;">
            <div class="row" id="modal_rx">
                <div class="col-md-12">
                    <h4>Sucursal :</h4>
                </div>
                <div class="col-md-12">
                    <table class="table responsive">
                        <thead>
                            <tr>
                                <th>RX</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Semanas credito</th>
                                <th>Semanas restantes</th>
                                <th>Diseño</th>
                                <th>Material</th>
                                <th>Caracteristica</th>
                                <th>Antireflejante</th>
                                <th>Subtotal</th>
                                <th>Descuento Sistema</th>
                                <th>Descuento</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(cart,i) in orders" :key="i">
                                <td>{{ cart.rx }}</td>
                                <td>{{ cart.client ? cart.client.name : 'CLIENTE ELIMINADO' }}</td>
                                <td>{{ cart.created_at }}</td>
                                <td>{{ cart.client ? cart.client.payment_plan : 'SIN DEFINIR' }}</td>
                                <td>
                                    <p style="background-color:green;" v-if="cart.status=='pagado'">0</p>
                                    <p style="background-color:red;" v-else-if="cart.pased<0">{{ cart.pased }}</p>
                                    <p style="background-color:orange;" v-else>{{ cart.pased }}</p>
                                </td>
                                <td>{{ cart.product.name }}</td>
                                <td>{{ cart.product.subcategory_name }}</td>
                                <td>{{ cart.product.type_name }}</td>
                                <td v-if="cart.extras[0]">
                                    {{ cart.extras.map(row =>{return row.name}).join(", ") }}
                                </td>
                                <td v-else>
                                    -
                                </td>
                                <td>${{ toLocal(cart.total) }}</td>
                                <td>{{ cart.discount  | currency }}</td>
                                <td>{{ cart.discount_admin | currency }}</td>
                                <td>{{ getTotal(cart.total, cart.discount, cart.discount_admin, cart.iva) | currency }}</td>
                                <td>
                                    <button class="btn btn-warning">{{ cart.status.replace("_"," ") }}</button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="11"><b class="pull-right">Descuentos:</b></td>
                                <td>{{ getDiscounts(orders) }}</td>
                            </tr>
                            <tr>
                                <td colspan="11"><b class="pull-right">IVA:</b></td>
                                <td>{{ getIvas(orders) }}</td>
                            </tr>
                            <tr>
                                <td colspan="11"><b class="pull-right">Total:</b></td>
                                <td>{{ getSum(orders) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="row" id="modal_clients">
                <div class="col-md-12">
                    <h4>Sucursal :</h4>
                </div>
                <div class="col-md-12">
                    <table class="table responsive">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Nombre comercial</th>
                                <th>Saldo pagado</th>
                                <th>Saldo vencido</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(client,i) in orders_ordered" :key="i">
                                <td>{{ client[0].client.name }}</td>
                                <td>{{ client[0].client.comertial_name }}</td>
                                <td>{{ filterPayed(client,1) }}</td>
                                <td>{{ filterPayed(client,0) }}</td>
                                <td>
                                    <button class="btn" @click="selectBranch(client)">Ver RX's</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import * as moment from "moment";
export default {
    data(){
        return {
            filters:{
                start:"",
                end:"",
                status:{
                    en_proceso:true,
                    terminado:true,
                    entregado:true,
                    pagado:true
                }
            },
            data:[],
            orders:[],
            orders_ordered:[],
        }
    },
    methods:{
        filterPayed:function(orders,status){
            let total=0;
            orders.forEach((v)=>{
                if(v.payed==status){
                    total+=parseFloat(this.getTotal(v.total,v.discount,v.discount_admin,v.iva));
                }
            });
            return total.toFixed(2);
        },
        getSum:function(orders){
            let total=0;
            orders.forEach((v)=>{
                total+=parseFloat(this.getTotal(v.total,v.discount,v.discount_admin,v.iva));
            });

            return total.toFixed(2);
        },
        getDiscounts:function(orders){
            let total=0;
            orders.forEach((v)=>{
                total+=parseFloat(this.getDiscount(v.discount,v.discount_admin));
            });

            return total.toFixed(2);
        },
        getIvas:function(orders){
            let total=0;
            orders.forEach((v)=>{
                total+=parseFloat(v.iva);
            });

            return total.toFixed(2);
        },
        getTotal:function(subtotal,dis,dis1,iva){
            iva = parseFloat(iva);
            return (parseFloat(subtotal) - this.getDiscount(dis,dis1) + iva).toFixed(2);
        },
        getDiscount:function(dis,dis1){
            return parseFloat(dis || 0) + parseFloat(dis1 || 0);
        },
        getReports:function(){
            this.$parent.inPetition=true;
            axios.post(tools.url("api/reports"),this.filters)
            .then(res=>{
                this.data=res.data;

                this.data.forEach((v,k)=>{
                    this.data[k].total_payed=0;
                    this.data[k].total_debt=0;
                    let orders_ordered={};
                    let clients_orders=0;
                    v.orders.forEach((val,key)=>{
                        let limit = moment(val.created_at).add(parseInt(val.client ? val.client.payment_plan : 0),"week");
                        // console.log("Inicio:"+moment(val.created_at));
                        // console.log("Limite:"+limit);
                        let pased = moment(limit).diff(moment(),"week");
                        val.pased=pased;
                        if(orders_ordered[val.client_id]){
                            orders_ordered[val.client_id].push(val);
                        }
                        else{
                            orders_ordered[val.client_id]=[val];
                            clients_orders++;

                        }
                        this.data[k].orders[key].pased=pased;
                        let iva = parseFloat(val.iva);
                        if(val.payed){
                            this.data[k].total_payed+=parseFloat((val.total-this.getDiscount(val.discount,val.discount_admin))+iva);
                        }
                        else{
                            this.data[k].total_debt+=parseFloat((val.total-this.getDiscount(val.discount,val.discount_admin))+iva);
                        }
                    });
                    this.data[k].orders_ordered=orders_ordered;
                    this.data[k].clients_orders=clients_orders;
                    this.data[k].total_payed=this.data[k].total_payed.toFixed(2);
                    this.data[k].total_debt=this.data[k].total_debt.toFixed(2);


                });

                this.$parent.inPetition=false;
            })
            .catch(err=>{
                this.$parent.handleErrors(err);
                this.$parent.inPetition=false;
            });
        },
        selectBranch:function(orders){
            this.orders=orders;
            alertify.rxDialog(document.getElementById('modal_rx'));
        },
        selectBranchClients:function(orders_ordered){
            this.orders_ordered=orders_ordered;
            alertify.clientsDialog(document.getElementById('modal_clients'));
        },
        toLocal:function(str){
            let num = parseFloat(str);
            if(num.toLocaleString){
                return num.toLocaleString("en");
            }
            return 0;
        }
    },
    mounted(){
        this.getReports();
        let date = new Date();
            date.setTime(new Date().getTime() - (6 * 60 * 60 * 1000));
        this.filters.start = this.filters.end = date.toISOString().slice(0,10);

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
        alertify.clientsDialog || alertify.dialog('clientsDialog',function(){
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
