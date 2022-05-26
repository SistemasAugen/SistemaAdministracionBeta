<template>
    <div class="row">
        <div class="col-md-12">
            <h3 style="display:inline-block;">RX pendientes.</h3>
            <button @click="getOrders" class="btn pull-right"><i class="fas fa-sync"></i></button>
        </div>
        <div class="col-md-12">
            <table class="table responsive">
                <thead>
                    <tr>
                        <th>RX</th>
                        <th>Cliente</th>
                        <th>Origen</th>
                        <th>Fecha</th>
                        <th>Diseño</th>
                        <th>Material</th>
                        <th>Caracteristica</th>
                        <th>Antireflejante</th>
                        <th>Costo</th>
                        <th>Servicio</th>
                        <th>Descuento promoción</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Mover</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(cart,i) in orders" :key="i">
                        <td>{{ cart.rx }}</td>
                        <td>{{ cart.client.name }}</td>
                        <td>{{ filterBranch(cart.branch_id).name }}</td>
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
                        <td>
                            <span v-if="cart.promo_discount != null">$ {{ cart.promo_discount }}</span>
                            <span v-else>N/A</span>
                        </td>
                        <td>{{ getTotal(cart.total,cart.discount,cart.discount_admin,cart.iva, cart.promo_discount) | currency }}</td>
                        <td v-if="cart.status=='en_proceso'">
                            <button class="btn btn-success" @click="finishOrder(cart.id)">{{ cart.status.replace("_"," ") }}</button>
                        </td>
                        <td v-else>
                            <button class="btn btn-warning" @click="finishOrder(cart.id)">{{ cart.status.replace("_"," ") }}</button>
                        </td>
                        <td><button class="btn" @click="selectLaboratory(cart.id)"> <i class="fas fa-arrows-alt"></i> </button></td>
                    </tr>
                </tbody>

            </table>
        </div>
        <div style="display:none;">
            <div id="laboratories_table">
                <div class="row">
                    <div class="col-md-12">
                        <h3>Selecciona un laboratorio:</h3>
                    </div>
                    <div class="col-md-6 col-md-offset-3" v-for="laboratory in laboratories" :key="laboratory.id">
                        <br>
                        <br>
                        <button class="btn btn-default btn-block" @click="moveOrder(laboratory.id)">{{ laboratory.name }}</button>
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
            orders:[],
            laboratories:[],
            order_id:"",
            branches:[]
        }
    },
    methods:{
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
            this.$parent.inPetition=true;
            axios.get(tools.url('api/orders/pending'))
            .then((res)=>{
                this.$parent.inPetition=false;
                this.orders=res.data;
            })
            .catch((err)=>{
                this.$parent.inPetition=false;
                this.$parent.handleErrors(err);
            });
        },
        getLaboratories:function(){
            axios.get(tools.url("api/laboratories"))
            .then((response)=>{
                this.laboratories=response.data;
            })
            .catch((err)=>{
                this.$parent.handleErrors(err);
            });
        },
        finishOrder:function(id){
            alertify.confirm('¿Deseas marcar como terminado este RX?', ()=>{
                this.$parent.inPetition=true;
                let params={
                    status:"terminado"
                }
                axios.post(tools.url("api/orders/status/"+id),params)
                .then((res)=>{
                    this.$parent.inPetition=false;
                    this.$parent.showMessage(res.data.msg,"success");
                    this.getOrders();
                })
                .catch((err)=>{
                    this.$parent.inPetition=false;
                    this.$parent.handleErrors(err);
                });
            });
        },
        selectLaboratory:function(id){
            this.order_id=id;
            alertify.laboratoriesDialog(document.getElementById("laboratories_table"));
        },
        moveOrder:function(id){
            alertify.confirm('¿Seguro que deseas mover este RX?', ()=>{
                this.$parent.inPetition=true;
                let params={
                    laboratory_id:id
                }
                axios.post(tools.url("api/orders/laboratory/"+this.order_id),params)
                .then((res)=>{
                    this.$parent.inPetition=false;
                    this.$parent.showMessage(res.data.msg,"warning");
                    this.order_id="";
                    alertify.closeAll();
                    this.getOrders();
                })
                .catch((err)=>{
                    this.$parent.inPetition=false;
                    this.$parent.handleErrors(err);
                });
            });
        },
        getBranches(){
            this.$parent.inPetition=true;
            axios.get(tools.url("/api/branches")).then((response)=>{
                this.branches=response.data;
                this.branches.forEach((v,k)=>{
                    this.branches[k].laboratory=v.laboratory.name;
                    this.branches[k].state=v.state.name;
                    this.branches[k].town=v.town.name;
                });

                this.$parent.inPetition=false;
            }).catch((error)=>{
                this.$parent.handleErrors(error);
                this.$parent.inPetition=false;
            });
        },
        filterBranch:function(branch_id){
            let branch = this.branches.find(row=>{
                return row.id==branch_id;
            });

            return branch;
        }
    },
    mounted(){
        this.getBranches();
        this.getLaboratories();
        setTimeout(()=>{
            this.getOrders();
        },1000);
        alertify.laboratoriesDialog || alertify.dialog('laboratoriesDialog',function(){
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
