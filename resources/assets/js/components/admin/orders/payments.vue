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
                            <!-- <div class="col-md-2">
                                <p>Desde:</p>
                                <input type="date" class="form-control" v-model="filters.start">
                            </div>
                            <div class="col-md-2">
                                <p>Hasta:</p>
                                <input type="date" class="form-control" v-model="filters.end">
                            </div> -->
                            <div class="col-md-5">
                                <p>Cliente:</p>
                                <input type="text" name="" class="form-control" v-model="filters.client">
                            </div>
                            <div class="col-md-5">
                                <p>RX:</p>
                                <input type="text" name="" class="form-control" v-model="filters.rx">
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-block btn-success" @click="makeSearch()">Filtrar</button>
                                <button class="btn btn-block btn-danger" @click="clients = []; branchSelected = false" v-if="branchSelected != false && isAdmin" style="font-size: 11px;">Deseleccionar sucursal</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-if="isAdmin && !branchSelected">
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
                            <td><button class="btn" @click="getClients(branch.id)">{{ branch.clients_orders }}</button></td>
                            <td>{{ branch.total_payed | currency }}</td>
                            <td>{{ branch.total_debt | currency }}</td>
                            <td>{{ branch.orders.length }}</td>
                            <td><button class="btn" @click="selectPdv(branch.id)">Ver RX's</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col-md-12">
                <table class="table responsive">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Nombre Comercial</th>
                            <th>Celular</th>
                            <th>Saldo pendiente</th>
                            <th>Condicion de pago</th>
                            <th>RX's</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="client in clients" :key="client.id">
                            <td>{{ client.name }}</td>
                            <td>{{ client.comertial_name }}</td>
                            <td>{{ client.celphone }}</td>
                            <td>{{ getTotal(client.total,client.discounts, client.discounts_admin, client.ivas) | currency }}</td>
                            <td>{{ client.payment_plan }} </td>
                            <td><button class="btn" @click="selectBranch(client.orders)">Pendientes</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div style="display:none;">
            <div id="modal_rx">
                <div class="row">
                    <div class="col-md-12">
                        <h4>RX pendientes <span v-if="subtotal > 0" class="floating-span">{{ subtotal | currency }}</span> </h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table responsive">
                            <thead>
                                <tr>
                                    <th v-if="isAdmin"></th>
                                    <th>RX</th>
                                    <th>Cliente</th>
                                    <th>Fecha de Captura</th>
                                    <th>Fecha de Entrega</th>
                                    <th>Semanas restantes</th>
                                    <th>Diseño</th>
                                    <th>Material</th>
                                    <th>Caracteristica</th>
                                    <th>Antireflejante</th>
                                    <th>Plazo</th>
                                    <th>Subtotal</th>
                                    <th>Descuentos sistema</th>
                                    <th>Descuento</th>
                                    <th>Descuento Promoción</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th v-if="isAdmin">Pagar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(cart,i) in orders" :key="i">
                                    <td v-if="isAdmin"><input type="checkbox" :value="cart.id" v-model="ids" @change.prevent="recalculate($event, getTotal(cart.total,cart.discount,cart.discount_admin,cart.iva, cart.promo_discount))"></td>
                                    <td>{{ cart.rx }}</td>
                                    <td>{{ cart.client.name }}</td>
                                    <td>{{ cart.created_at }}</td>
                                    <td>{{ cart.delivered_date }}</td>
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
                                    <td>{{ cart.client.payment_plan }}</td>
                                    <td>{{ cart.total | currency }}</td>
                                    <td>{{ cart.discount | currency }}</td>
                                    <td>{{ cart.discount_admin | currency }}</td>
                                    <td>
                                        <span v-if="cart.promo_discount != null">$ {{ cart.promo_discount }}</span>
                                        <span v-else>N/A</span>
                                    </td>
                                    <td v-if="!cart.cancelled">{{ getTotal(cart.total,cart.discount,cart.discount_admin,cart.iva, cart.promo_discount) | currency }}</td>
                                    <td v-else>{{ 0 | currency }} </td>
                                    <td>
                                        <button class="btn btn-warning">{{ cart.status.replace("_"," ") }}</button>
                                    </td>
                                    <td v-if="isAdmin"><button class="btn" @click="payOrder(cart.id)" :disabled="cart.client.name == 'Cliente eliminado'">Saldar cuenta </button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row" v-if="ids.length>0 && isAdmin">
                    <div class="col-md-12"><button class="btn pull-right" @click="payManyOrders">Saldar seleccionados</button></div>
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
            discount: 0.0,
            filters:{
                start:"",
                end:"",
                client:"",
                rx:""
            },
            clients:[],
            branchSelected: false,
            data: [],
            orders:[],
            orders_ordered:[],
            client_id:"",
            ids:[],
            subtotal: 0.0,
        }
    },
    computed: {
        isAdmin: function() {
            let roles = this.$parent.user.roles;
            for(let role of roles)
                if(role.permissions.filter(p => p.name == 'cobranza_admin').length != 0)
                    return true;
            return false;
        }
    },
    methods:{
        recalculate: function(e, total) {
            // console.log(total, '<==================');
            if(e.target.checked) {
                this.subtotal += parseFloat(total);
            } else {
                this.subtotal -= parseFloat(total);
            }

            this.subtotal = Math.max(this.subtotal, 0.0);
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
        getClients:function(branch_id = null){
            this.$parent.inPetition=true;
            this.clients = [];
            this.branchSelected = branch_id;
            axios.post(tools.url("api/orders/payments" + (branch_id ? `/${ branch_id }`: '')),this.filters)
            .then((res)=>{
                this.clients=res.data;
                this.$parent.inPetition=false;
            })
            .catch((err)=>{
                this.$parent.handleErrors(err);
                this.$parent.inPetition=false;
            });
        },
        selectPdv(branch_id) {
            this.orders = [];
            this.$parent.inPetition=true;
            axios.get(tools.url("api/orders/payments_pdv/" + branch_id + `?from=${ this.filters.start }&to=${ this.filters.end }`))
            .then((res)=>{
                this.orders=res.data;
                alertify.rxDialog(document.getElementById("modal_rx"));
                this.$parent.inPetition=false;
            })
            .catch((err)=>{
                this.$parent.handleErrors(err);
                this.$parent.inPetition=false;
            });
        },
        selectOrder:function(client_id){
            this.client_id=client_id;
            this.$parent.inPetition=true;
            axios.get(tools.url("api/orders/client/"+this.client_id + ( this.branchSelected ? `/${  this.branchSelected }`: '') + `?from=${ this.filters.start }&to=${ this.filters.end }`))
            .then((res)=>{
                this.orders=res.data;
                alertify.rxDialog(document.getElementById("modal_rx"));
                this.$parent.inPetition=false;
            })
            .catch((err)=>{
                this.$parent.handleErrors(err);
                this.$parent.inPetition=false;
            });
        },
        payOrder:function(order_id){
            // this.makePdf([order_id]);
            // return;
            alertify.confirm('¿Seguro que cobrar este RX?', ()=>{
                this.$parent.inPetition=true;
                axios.post(tools.url("api/orders/pay/"+order_id))
                .then((res)=>{
                    alertify.closeAll();
                    this.$parent.showMessage(res.data.msg,"success");
                    this.$parent.inPetition=false;
                    this.getClients(this.branchSelected);
                    this.makePdf([order_id]);
                })
                .catch((err)=>{
                    this.$parent.handleErrors(err);
                    this.$parent.inPetition=false;
                });
            });
        },
        payManyOrders:function(){
            alertify.confirm('¿Seguro que cobrar '+this.ids.length+' RXs?', ()=>{
                this.$parent.inPetition=true;
                let params={ids:this.ids};
                axios.post(tools.url("api/orders/pay/many"),params)
                .then((res)=>{
                    alertify.closeAll();
                    this.$parent.showMessage(res.data.msg,"success");
                    this.$parent.inPetition=false;
                    this.getClients(this.branchSelected);
                    this.makePdf(this.ids);
                })
                .catch((err)=>{
                    this.$parent.handleErrors(err);
                    this.$parent.inPetition=false;
                });
            });
        },
        filterOrders:function(ids){
            var orders = this.orders.filter((row)=>{
                console.log("Index:"+ids.indexOf(row.id));
                if(ids.indexOf(row.id)!=-1){
                    return true;
                }
                return false;
            });
            console.log(orders);
            return orders;
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
        getReports:function(){
            this.$parent.inPetition=true;
            this.data = [];
            axios.post(tools.url("api/orders/payments"),this.filters)
            .then(res=>{
                this.data=res.data;
                this.data.forEach((v,k)=>{
                    this.data[k].total_payed=0;
                    this.data[k].total_debt=0;
                    let orders_ordered={};
                    let clients_orders=0;
                    v.orders.forEach((val,key)=>{
                        if(orders_ordered[val.client_id]){
                            orders_ordered[val.client_id].push(val);
                        }
                        else{
                            orders_ordered[val.client_id]=[val];
                            clients_orders++;

                        }
                        let iva = parseFloat(val.iva);
                        if(val.payed){
                            this.data[k].total_payed+=parseFloat((val.total-this.getDiscount(val.discount,val.discount_admin))+iva);
                        }
                        else{
                            if(val.passed <= 0)
                                this.data[k].total_debt+=parseFloat((val.total-this.getDiscount(val.discount,val.discount_admin))+iva);
                        }
                    });
                    this.data[k].clients_orders=clients_orders;
                });

                this.$parent.inPetition=false;
            })
            .catch(err=>{
                this.$parent.handleErrors(err);
                this.$parent.inPetition=false;
            });
        },
        makePdf:function(ids){
            var orders=this.filterOrders(ids);
            var doc = new jsPDF({
                format:"letter",
                unit:"cm"
            });
            doc.setFontSize(30);
            doc.setFontType("bold");
            doc.text('Augen', 11, 1.2,null,null,'center');
            doc.setLineWidth(0.1);
            doc.line(1, 3, 21, 3);
            doc.setFontSize(13);
            doc.setFontType("normal");
            doc.text('PDV: '+orders[0].branch.name.toString(), 11, 4,null,null,'center');
            if(orders[0].client.facturacion.name){
                doc.text(orders[0].client.facturacion.name.toString(), 11, 5,null,null,'center');
            }
            else{
                doc.text(orders[0].client.name.toString(), 11, 5,null,null,'center');
            }
            let date=new Date().toISOString();
            doc.text('Fecha: '+date, 11, 6,null,null,'center');
            doc.line(1, 7, 21, 7);
            doc.text('RX', 2, 7.5,null,null,'center');
            doc.text('Diseño', 5.5, 7.5,null,null,'center');
            doc.text('Material', 9, 7.5,null,null,'center');
            doc.text('Caracteristica', 12.5, 7.5,null,null,'center');
            doc.text('Precio', 16, 7.5,null,null,'center');
            doc.text('Descuento', 19.5, 7.5,null,null,'center');
            doc.line(1, 8, 21, 8);
            //Se suma 0.5cm por producto
            var x = 8;
            let total=0;
            let discounts=0;
            let iva=0;
            orders.forEach((v)=>{
                x+=0.5;
                doc.text(v.rx.toString(), 2, x,null,null,'center');
                doc.text(v.product.name.toString(), 5.5, x,null,null,'center');
                doc.text(v.product.subcategory_name.toString(), 9, x,null,null,'center');
                doc.text(v.product.type_name.toString(), 12.5, x,null,null,'center');
                doc.text("$"+v.total.toString(), 16, x,null,null,'center');
                let discount = parseFloat(v.discount)+parseFloat(v.discount_admin);
                discounts+=discount;
                discount=discount.toFixed(2);
                doc.text("$"+discount, 19.5, x,null,null,'center');
                total+=parseFloat(v.total);
                iva+=parseFloat(v.iva);
            });
            x+=1; //Donde 2 es el numero de productos.
            doc.text('Subtotal:', 14.5, x,null,null,'center');
            let subtotal=total;
            doc.text("$"+subtotal.toFixed(2), 19.5, x,null,null,'center');
            x+=0.5;
            doc.text('- Descuentos:', 14.5, x,null,null,'center');
            doc.text('$'+discounts.toFixed(2), 19.5, x,null,null,'center');
            x+=0.5;
            doc.text('IVA:', 14.5, x,null,null,'center');
            doc.text('$'+iva.toFixed(2), 19.5, x,null,null,'center');
            x+=0.5;
            doc.text('Total:', 14.5, x,null,null,'center');
            if(total-discounts>0){
                doc.text('$'+(total-discounts+iva), 19.5, x,null,null,'center');
            }
            else{
                doc.text('$0.00', 19.5, x,null,null,'center');
            }
            x+=0.5;
            doc.line(1, x, 21, x);
            x+=1;
            doc.text("Lo atendio: "+this.$parent.user.name.toString(), 1.6, x,null,null);
            x+=0.5;
            doc.text("Direccion: "+orders[0].branch.address.toString(), 1.6, x,null,null);
            x+=1;
            doc.text("Nombre: "+orders[0].client.name.toString(), 1.6, x,null,null);
            x+=0.5;
            doc.text("Cel: "+orders[0].client.celphone.toString(), 1.6, x,null,null);
            // doc.autoPrint();
            doc.save('two-by-four.pdf');

        },
        selectBranch:function(orders) {
            // orders.forEach(o => o.payment_plan = paymentPlan);
            this.orders=orders;
            alertify.rxDialog(document.getElementById('modal_rx'));
        },
        makeSearch() {
            if(this.isAdmin) {
                if(this.branchSelected) {
                    console.log('clientes');
                    this.getClients(this.branchSelected);
                }
                else {
                    console.log('pdv');
                    this.getReports();
                }
            }
            else {
                console.log('clientes');
                this.getClients(this.branchSelected);
            }
        },
    },
    mounted(){
        if(this.isAdmin)
            this.getReports();
        else
            this.getClients(this.branchSelected);

        let date = new Date();
            date.setTime(new Date().getTime() - (6 * 60 * 60 * 1000));
        this.filters.start = this.filters.end = date.toISOString().slice(0,10);

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
                            maximizable:false,
                            resizable:true,
                            onshow:function() {
                                self.subtotal = 0.0;
                            },
                            onclosing:function() {
                                $('input[type="checkbox"]:checked').click();
                            }
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
<style media="screen">
span.floating-span {
    position: fixed;
    top: 5%;
    left: calc(50% - (150px / 2));
    background: #fff;
    padding: 10px;
    border: 1px solid #eee;
    border-radius: 3px;
    width: 150px;
    text-align: center;
}
</style>
