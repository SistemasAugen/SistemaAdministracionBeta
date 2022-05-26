<template>
    <div>
        <form-wizard @on-complete="onComplete" ref="form" :start-index="index" color="gray" shape="circle" title="Nuevo pedido"
            subtitle="Sigue los pasos marcados" nextButtonText="Siguiente" backButtonText="Atras" finishButtonText="Finalizar">
            <tab-content title="Cliente" icon="fa fa-user" :beforeChange="saveClient" ref="client">
                <div class="panel panel-primary" data-collapsed="0">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <i class="fab fa-odnoklassniki"></i> Cliente
                        </div>
                    </div>

                    <div class="panel-body">
                        <form role="form" class="form-horizontal">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-secondary pull-right" style="margin-bottom:10px;" @click="cleanClient"><i></i> Limpiar</button>
                                    <button type="button" class="btn btn-info pull-right" style="margin-bottom:10px;" @click="searchClient"><i class="fa fa-search"></i> Buscar cliente</button>
                                </div>
                            </div>
                            <hr>
                            <div class="col-md-12">
                                <div class="tabs-vertical-env">
                                    <ul class="nav tabs-vertical">
                                        <li class="active"><a href="#v-data" data-toggle="tab" aria-expanded="true">Datos</a></li>
                                        <li class=""><a href="#v-contact" data-toggle="tab" aria-expanded="false">Contacto</a></li>
                                        <li class=""><a href="#v-facturacion" data-toggle="tab" aria-expanded="false">Facturacion</a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="v-data">
                                            <input-form name="nombre" :disabled="true" text="Nombre" :data.sync="client.name" validate="required"></input-form>
                                            <input-form name="email" :disabled="true" text="Email" :data.sync="client.email" validate="required"></input-form>
                                            <input-form name="telefono" :disabled="true" text="Telefono" :data.sync="client.phone" validate="digits:8"></input-form>
                                            <input-form name="celular" :disabled="true" text="Celular" :data.sync="client.celphone" validate="digits:10"></input-form>
                                            <input-form name="nombre_comercial" :disabled="true" text="Nombre comercial" :data.sync="client.comertial_name"></input-form>
                                            <!-- <input-form name="RFC" :disabled="true" text="R.F.C." :data.sync="client.rfc"></input-form> -->
                                            <select-form text="Estado" name="state" :options="states" :data.sync="client.state"></select-form>
                                            <select-form text="Ciudad" name="town" :options="towns" :data.sync="client.town"></select-form>
                                            <input-form name="codigo_postal" :disabled="true" text="Codigo postal" :data.sync="client.postal_code" validate="required|digits:5"></input-form>
                                            <input-form name="direccion" :disabled="true" text="Direccion" :data.sync="client.address" validate="required"></input-form>
                                            <input-form name="colonia" :disabled="true" text="Colonia" :data.sync="client.suburb" validate="required"></input-form>
                                        </div>
                                        <div class="tab-pane" id="v-contact">
                                            <input-form name="contacto_nombre" :disabled="true" text="Contacto nombre" :data.sync="client.contact_name" validate=""></input-form>
                                            <input-form name="contacto_email" :disabled="true" text="Contacto email" :data.sync="client.contact_email" validate="email"></input-form>
                                            <input-form name="contacto_telefono" :disabled="true" text="Contacto telefono" :data.sync="client.contact_phone" validate="digits:8"></input-form>
                                            <input-form name="contacto_celular" :disabled="true" text="Contacto celular" :data.sync="client.contact_celphone" validate="digits:10"></input-form>
                                        </div>
                                        <div class="tab-pane" id="v-facturacion">
                                            <input-form name="nombre" :disabled="true" text="Nombre" :data.sync="client.facturacion.name"></input-form>
                                            <input-form name="telefono" :disabled="true" text="Telefono" :data.sync="client.facturacion.phone" validate="digits:8"></input-form>
                                            <input-form name="celular" :disabled="true" text="Celular" :data.sync="client.facturacion.celphone" validate="digits:10"></input-form>
                                            <input-form name="RFC" :disabled="true" text="R.F.C." :data.sync="client.facturacion.rfc"></input-form>
                                            <input-form name="direccion" :disabled="true" text="Direccion" :data.sync="client.facturacion.address"></input-form>
                                            <input-form name="colonia" :disabled="true" text="Colonia" :data.sync="client.facturacion.suburb"></input-form>
                                            <select-form text="Estado" name="state" :options="states" :data.sync="client.facturacion.state"></select-form>
                                            <select-form text="Ciudad" name="town" :options="towns" :data.sync="client.facturacion.town"></select-form>
                                            <input-form name="codigo_postal" :disabled="true" text="Codigo postal" :data.sync="client.facturacion.postal_code"></input-form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </tab-content>
            <tab-content title="Pedido" icon="fas fa-shopping-cart">
                <div class="panel panel-primary" data-collapsed="0">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <i class="fas fa-shopping-cart"></i> Pedido
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="col-md-10">
                            <table class="table responsive">
                                <thead>
                                    <tr>
                                        <th>RX</th>
                                        <th>Diseño</th>
                                        <th>Material</th>
                                        <th>Caracteristica</th>
                                        <th v-if="!isAugenLabs">Antireflejante</th>
                                        <th v-else>Cantidad</th>
                                        <th>Costo</th>
                                        <th v-if="!isAugenLabs">Servicio</th>
                                        <th>Subtotal</th>
                                        <th>Eliminar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(cart,i) in $parent.sale.cart" :key="i">
                                        <td>{{ cart.rx }}</td>
                                        <td>{{ cart.product.name }}</td>
                                        <td>{{ cart.product.category_name }}</td>
                                        <td>{{ cart.product.type_name }}</td>
                                        <td v-if="!isAugenLabs && cart.extras[0]">
                                            {{ cart.extras.map(row =>{return row.name}).join(", ") }} <button class="btn btn-sm btn-warning" @click="selectOrder(cart)"><i class="fa fa-plus"></i></button>
                                        </td>
                                        <!-- <td v-else>
                                            <button class="btn btn-sm btn-warning" @click="selectOrder(cart.product)">
                                                <i class="fa fa-plus" v-show="cart.product.extras[0]"></i>
                                            </button>
                                        </td>
										El sig bloque else-if, else-if, valida la presencia del campo No AR, si esta presente, no permite agregar ningun antireflejante,
										corrobar el v-else anterior si es necesario tenerlo presente o escribrilo despues de esta validacion. -->
										<td v-else-if="!isAugenLabs && (cart.not_ar != 1 || cart.ar != 1)">
                                            <button class="btn btn-sm btn-warning" @click="selectOrder(cart)"><i class="fa fa-plus"></i></button>
                                        </td>
                                        <td v-else-if="!isAugenLabs && (cart.not_ar == 1 && cart.ar == 1)">
                                            No AR activo.
                                        </td>
                                        <td v-else><input type="number" class="form-control" v-model="cart.service" @change="getDiscounts" min="0"></td>
                                        <td>${{ cart.price }}</td>
                                        <td v-if="!isAugenLabs"><input type="number" v-model="cart.service" @change="getDiscounts" accept="any" min="0" v-validate="'decimal:2'" width="50px"></td>
                                        <td>${{ cart.total }}</td>
                                        <td><button class="btn btn-danger btn-sm" @click="deleteProduct(i)">Borrar</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-success" @click="selectType"><i class="fa fa-search"></i> Buscar producto</button>
                        </div>
                    </div>
                </div>
            </tab-content>
            <tab-content title="Checkout" icon="fas fa-check">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-primary" data-collapsed="0">
                            <div class="panel-heading">
                                <div class="panel-title">
                                    <i class="fab fa-odnoklassniki"></i> Cliente
                                </div>
                            </div>

                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-4">Nombre</div>
                                    <div class="col-md-8">{{ client.name }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">Email:</div>
                                    <div class="col-md-8">{{ client.email }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">Celular:</div>
                                    <div class="col-md-8">{{ client.celphone }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">Nombre comercial:</div>
                                    <div class="col-md-8">{{ client.comertial_name }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">Domicilio:</div>
                                    <div class="col-md-8">{{ client.address }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-121">
                        <div class="panel panel-primary" data-collapsed="0">
                            <div class="panel-heading">
                                <div class="panel-title">
                                    <i class="fas fa-shopping-cart"></i> Pedido
                                </div>
                            </div>

                            <div class="panel-body">
                                <table class="table responsive">
                                    <thead>
                                        <tr>
                                            <th>RX</th>
                                            <th>Diseño</th>
                                            <th>Material</th>
                                            <th>Caracteristica</th>
                                            <th>Antireflejante</th>
                                            <th>Costo</th>
                                            <th v-if="!isAugenLabs">Servicio</th>
                                            <th v-else>Cantidad</th>
                                            <th>Descuento</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(cart,i) in $parent.sale.cart" :key="i">
                                            <td>{{ cart.rx }}</td>
                                            <td>{{ cart.product.name }}</td>
                                            <td>{{ cart.product.category_name }}</td>
                                            <td>{{ cart.product.type_name }}</td>
                                            <td v-if="cart.extras[0]">{{ cart.extras.map(row =>{return row.name}).join(", ") }}</td><td v-else> - </td>
                                            <td>${{ cart.price }}</td>
                                            <td v-if="!isAugenLabs">${{ cart.service }}</td>
                                            <td v-else>{{ cart.service }}</td>
                                            <td>{{ cart.percent_discount }}%</td>
                                            <td>${{ parseFloat(cart.total-cart.discount) }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="8" class="text-right">Descuentos:</td>
                                            <td>${{ $parent.sale.discounts }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="8" class="text-right">IVA:</td>
                                            <td>${{ $parent.sale.ivas }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="8" class="text-right" style="font-size: 27px;letter-spacing: 2px;">Total:</td>
                                            <td style="font-size: 28px;position: absolute;font-weight: bold;margin-left: -12px;" v-if="isAugenLabs">${{ (Math.ceil(($parent.sale.total-$parent.sale.discounts)+parseFloat($parent.sale.ivas))).toFixed(2) }}</td>
                                            <td style="font-size: 28px;position: absolute;font-weight: bold;margin-left: -12px;" v-else>${{ (($parent.sale.total-$parent.sale.discounts)+parseFloat($parent.sale.ivas)).toFixed(2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>

                            </div>
                        </div>
                        <h4>Observaciones</h4>
                        <textarea name="" id="" cols="30" rows="6" class="form-control" v-model="$parent.sale.observation"></textarea>
                    </div>
                </div>
            </tab-content>
        </form-wizard>
        <div style="display:none;">
            <table class="table responsive" id="clients_table">
                <thead>
                    <tr>
                        <th>#</th> <th>Nombres</th> <th>Nombre comercial</th> <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="client in clients" :key="client.id" @click="selectClient(client.id)">
                        <td>{{ client.id }}</td>
                        <td>{{ client.name }}</td>
                        <td>{{ client.comertial_name }}</td>
                        <td><button class="btn btn-sm"><i class="fa fa-plus"></i></button></td>
                    </tr>
                </tbody>
            </table>
            <div id="types_table">
                <div class="row">
                    <div class="col-md-12">
                        <h3>Elige un tipo de producto:</h3>
                    </div>
                    <div class="col-md-6 col-md-offset-3" v-for="type in types" :key="type.id">
                        <!-- <br> -->
                        <!-- <br> -->
                        <button class="btn btn-default btn-block" @click="selectProduct(type)" v-if="type.canSee">{{ type.name }}</button>
                    </div>
                </div>
            </div>
            <div id="extras_table">
                <div class="row">
                    <div class="col-md-12">
                        <h3>Seleccionar antireflejante:</h3>
                    </div>
                    <div class="col-md-6 col-md-offset-3" v-for="extra in extras" :key="extra.id">
                        <br>
                        <br>
                        <button class="btn btn-default btn-block" @click="addExtra(extra)">{{ extra.name }} - ${{ extra.price }}</button>
                    </div>
                    <div class="col-md-6 col-md-offset-3">
                        <br>
                        <br>
                        <button class="btn btn-warning btn-block" @click="addExtra()">Omitir</button>
                    </div>
                </div>
            </div>
            <div id="products_table">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2" width="15%" style="color:black;">{{ type.name }}</th>
                            <th v-for="category in categories" :key="category.id" :colspan="category.subcategories.length" :style="{color:'black', backgroundColor:category.color}">{{ category.name }}</th>
                        </tr>
                        <tr>
                            <template v-for="category in categories">
                                <th v-for="subcategory in category.subcategories" :key="subcategory.id+category.name" :style="{color:'black', backgroundColor:category.color}" :title="subcategory.description">
                                    {{ subcategory.name }}
                                </th>
                            </template>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(product, i) in products" :key="product.id + i">
                            <td>{{ product.name }}</td>
                            <template v-for="category in categories">
                                <td v-for="subcategory in category.subcategories" :key="subcategory.id+category.name" :title="subcategory.description">
                                    <button class="btn btn-sm btn-block" v-if="filterProduct(product,category.id,subcategory.id).price!=0" @click="addProduct(filterProduct(product,category.id,subcategory.id),category,subcategory)">${{ filterProduct(product,category.id,subcategory.id).price }}</button>
                                    <p v-else> - </p>
                                </td>
                            </template>

                        </tr>
                    </tbody>
                </table>
                <small>*Precios sin I.V.A.</small>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data(){
        return {
            index:0,
            client:{
                name:"",
                emal:"",
                phone:"",
                celphone:"",
                comertial_name:"",
                // rfc:"",
                address:"",
                suburb:"",
                state_id:"",
                state:{value:'',label:'Selecciona..'},
                town_id:"",
                town:{value:'',label:'Selecciona..'},
                postal_code:"",
                discounts:[],
                branch_id:"",
                branch:{value:'',label:'Selecciona..'},
                payment_plan:"",
                facturacion:{
                    name:"",
                    phone:"",
                    celphone:"",
                    rfc:"",
                    address:"",
                    suburb:"",
                    state_id:"",
                    state:{value:'',label:'Selecciona..'},
                    town_id:"",
                    town:{value:'',label:'Selecciona..'},
                    postal_code:""
                }
            },
            type:{},
            states:[],
            towns:[],
            clients:[],
            types:[],
            categories:[],
            products:[],
            extras:[],
            rx:"",
            order:{},
            orderInProcess: false,
        }
    },
    computed: {
        isAugenLabs: function() {
            return this.client.id == 1862;
        },
        isPaqueteria: function() {
            return this.client.id == 1901;
        }
    },
    watch:{
        'client.state.value':{
            handler:function(v){
                this.client.state_id=v;
                if(v!='' && !isNaN(v)){
                    this.getTowns();
                }
            },
            deep: true,
        },
        'client.town.value':{
            handler:function(v){
                this.client.town_id=v;
            },
            deep: true,
        },
        '$parent.sale.cart':{
            handler:function(v){
                this.getDiscounts();
            },
            deep:false
        },
        'client.discounts':{
            handler:function(){
                this.getDiscounts();
            },
            deep:true
        }
    },
    methods:{
        reset:function(){
            this.cleanClient();
            this.$parent.sale={
                cart:[],
                client_id:"",
                total:0,
                observation:"",
                discounts:0
            }

        },
        onComplete:function() {
            if(!this.orderInProcess) {
                this.orderInProcess = true;
                alertify.confirm('¿Seguro que deseas realizar esta venta?', ()=>{
                    this.$parent.inPetition=true;
                    let params=window._.cloneDeep(this.$parent.sale);

                    params.cart=params.cart.map((row)=>{
                        let extras=row.extras.map((r)=>{
                            return {extra_id:r.id,price:r.price};
                        });
                        return {
                                product_has_subcategory_id:row.product_has_subcategory_id,
                                price:row.price,
                                discount:row.discount,
                                service:row.service,
                                total:row.total,
                                iva:row.iva,
                                rx:row.rx,
                                percent_discount:row.percent_discount,
                                extras:extras
                            };
                    });
                    axios.post(tools.url('/api/order'),params)
                    .then((response)=>{
                        this.$refs.form.changeTab(2,0);
                        this.reset();
                        this.$parent.inPetition=false;
                        this.$parent.showMessage("Pedido realizado correctamente.","success");
                        this.orderInProcess = false;
                    })
                    .catch((err)=>{
                        this.$parent.inPetition=false;
                        this.$parent.handleErrors(err);
                    });
                });
            }
        },
        getStates:function(){
            this.$parent.inPetition=true;
            axios.get(tools.url("/api/states"))
            .then((response)=>{
                this.states=response.data;
                this.states=jQuery.map(this.states,(row)=>{
                    return {'value':row.id,'label':row.name};
                });
                this.$parent.inPetition=false;
            })
            .catch((error)=>{
                this.$parent.handleErrors(error);
                this.$parent.inPetition=false;
            });
        },
        getTowns:function(){
            this.$parent.inPetition=true;
            axios.get(tools.url("/api/towns/"+this.client.state_id))
            .then((response)=>{
                this.towns=response.data;
                this.towns=jQuery.map(this.towns,(row)=>{
                    return {'value':row.id,'label':row.name};
                });
                this.$parent.inPetition=false;
            })
            .catch((error)=>{
                this.$parent.handleErrors(error);
                this.$parent.inPetition=false;
            });
        },
        getTypes:function(){
            this.$parent.inPetition=true;
            axios.get(tools.url("/api/types"))
            .then((response)=>{
                let types=response.data;
                const CHAPULTEPEC_LAB = 46;
                let isPDV = false;
                for(let role of this.$parent.user.roles)
                    if(role.name == 'punto de ventas') {
                        isPDV = true;
                        break;
                    }

                for(let i = 0; i < types.length; i++) {
                    if(!isPDV) {
                        types[i].canSee = true;
                        continue;
                    }
                    if([25, 34, 46].includes(this.$parent.user.id)) {
                        types[i].canSee = true;
                    } else {
                        if(types[i].id == 10)
                        types[i].canSee = false;
                        else
                        types[i].canSee = true;
                    }
                }

                this.types = types;
                this.$parent.inPetition=false;
            })
            .catch((error)=>{
                this.$parent.handleErrors(error);
                this.$parent.inPetition=false;
            });
        },
        getSubcategories:function(v){
            this.categories=[];
            this.$parent.inPetition=true;
            axios.get(tools.url("/api/subcategories/type/"+v))
            .then((response)=>{
                let subcategories=response.data;
                let categories={};

                subcategories.forEach((sub,key)=>{

                    sub.categories.forEach((cat,k)=>{
                        if(categories[cat.name]){
                            categories[cat.name].subcategories.push(sub);
                        }
                        else{
                            categories[cat.name]=cat;

                            categories[cat.name].subcategories=[sub];
                        }
                    });
                });

                jQuery.each(categories,(k,v)=>{
                    v.subcategories.sort((a,b)=>{
                        return a.id-b.id;
                    });
                    this.categories.push(v);

                });

                this.categories.sort((a,b)=>{
                    return a.id-b.id;
                });

                this.$parent.inPetition=false;
            })
            .catch((error)=>{
                this.$parent.handleErrors(error);
                this.$parent.inPetition=false;
            });
        },
        getProducts:function(type_id){
            axios.get(tools.url("/api/product/type/"+type_id)).then((response)=>{
                this.products=response.data;
                this.products.forEach((v,k)=>{
                    this.products[k].prices.sort((a,b)=>{
                        return a.id-b.id;
                    });
                });
            });
        },
        searchClient:function(){
            alertify.prompt( 'Augen', 'Escribe algun nombre de cliente.', ''
               , (evt, value) => {
                    axios.get(tools.url("/api/client/search/"+value)).then((response)=>{
                        let clients=response.data;
                        this.clients=clients;
                        alertify.clientsDialog(document.getElementById('clients_table'));
                    });
               }, function() {}).set('labels', {ok:'Aceptar', cancel:'Cancelar'});;
        },
        selectClient:function(client_id){
            this.$parent.sale.client_id=client_id;
            this.getClient();
            alertify.clientsDialog().close();
        },
        selectType: async function() {
            // COVID-19 Augen Mask patch
            if(this.isAugenLabs) {// Augen Labs Client
                this.rx = (await axios.get('/api/generateRx')).data;

                for(let i = 0; i < this.types.length; i++) {
                    this.types[i].canSee = this.types[i].id == 12;
                }
                alertify.typesDialog(document.getElementById('types_table'));
            } else if(this.isPaqueteria) {
                this.rx = (await axios.get('/api/generateRx?paqueteria')).data;

                for(let i = 0; i < this.types.length; i++) {
                    this.types[i].canSee = this.types[i].id == 7;
                }
                alertify.typesDialog(document.getElementById('types_table'));
            } else {
                this.getTypes();
                alertify.prompt( 'Augen', 'Escribe el RX.', ''
                   , async (evt, value) => {
                        this.rx = value;
                        this.$parent.inPetition = true;
                        let alreadyExist = (await axios.get(`/api/orders/check_duplicates/${ value }`)).data;
                        this.$parent.inPetition = false;

                        console.log(alreadyExist);

                        if(alreadyExist) {
                            alertify.confirm('Augen', 'Ya hay una receta ingresada con ese folio, ¿quieres continuar?', () => {
                                alertify.typesDialog(document.getElementById('types_table'));
                            }, () => {
                                this.rx = null;
                            }).set('labels', { ok:'Sí', cancel:'No'});
                        } else {
                            alertify.typesDialog(document.getElementById('types_table'));
                        }
                   }, function() {}).set('labels', {ok:'Aceptar', cancel:'Cancelar'});
            }
        },
        selectProduct:function(type) {
            this.getSubcategories(type.id);
            this.getProducts(type.id);
            this.type=type;
            alertify.productsDialog(document.getElementById('products_table'));
        },
        getDiscounts:function(event){
            // if('target' in event) {
            //     let value = parseFloat(event.target.value);
            //     if(value < 0) {
            //
            //     }
            // }
            let total=0;
            let discounts=0;
            let ivas=0;

            if(this.isAugenLabs) {
                // In this case v.service will be used as qty option.
                this.$parent.sale.cart.forEach((v,k)=> {
                    this.$parent.sale.cart[k].percent_discount = 0;
                    this.$parent.sale.cart[k].total = (window.parseFloat(v.price) * window.parseFloat(v.service)).toFixed(2);
                    this.$parent.sale.cart[k].discount = 0;
                    this.$parent.sale.cart[k].iva = window.parseFloat(this.$parent.sale.cart[k].total * 0.16);
                });
            } else {
                this.$parent.sale.cart.forEach((v,k)=>{
                    let discount=this.client.discounts.find((row)=>{
                        return row.type_id==v.product.type_id;
                    });

                    if(discount){
                        this.$parent.sale.cart[k].percent_discount=discount.discount;
                        this.$parent.sale.cart[k].total=(window.parseFloat(v.price) + window.parseFloat(v.service)).toFixed(2);;
                        this.$parent.sale.cart[k].discount=window.parseFloat((discount.discount/100)*this.$parent.sale.cart[k].total);
                        this.$parent.sale.cart[k].iva=window.parseFloat((this.$parent.sale.cart[k].total-this.$parent.sale.cart[k].discount)*.16);
                    }else{
                        this.$parent.sale.cart[k].percent_discount=0;
                        this.$parent.sale.cart[k].total=(window.parseFloat(v.price) + window.parseFloat(v.service)).toFixed(2);;
                        this.$parent.sale.cart[k].discount=window.parseFloat((0/100)*this.$parent.sale.cart[k].total);
                        this.$parent.sale.cart[k].iva=window.parseFloat((this.$parent.sale.cart[k].total-this.$parent.sale.cart[k].discount)*.16);
                    }
                });

            }

            this.$parent.sale.cart.forEach((v,k)=>{
                total += window.parseFloat(v.total);
                discounts += window.parseFloat(v.discount);
                ivas += window.parseFloat(v.iva);
            });

            this.$parent.sale.total=total.toFixed(2);
            this.$parent.sale.discounts=discounts.toFixed(2);
            this.$parent.sale.ivas=ivas.toFixed(2);
        },
        addProduct:function(price,category,subcategory){
            var no_AR = 0;
            var ar=0;
            let product=this.products.find((r)=>{
                if(r.id==price.product_id)
                    return true;
            });
            product.category_name = category.name;
            product.type_name = subcategory.name;
            let order={
                    product_has_subcategory_id:price.id,
                    price:price.price,
                    discount:0,
                    total:price.price,
                    service:0,
                    extras:[],
                    product:product,
                    rx:this.rx,
                    not_ar:0,
                };

            if(this.isAugenLabs)
                order.service = 1;

            product.subcategories.forEach((subcategory)=>{
              if(subcategory.id == price.subcategory_id){
                no_AR = subcategory.no_antireflective;
                ar = subcategory.antireflective;
              }
            });

            order.not_ar = no_AR;
            order.ar = ar;

            if(product.extras.length==0 || no_AR == 1 && ar==1){
                alertify.closeAll();
                this.$parent.sale.cart.push(order);
                this.getDiscounts();
                this.rx="";
            }
            else if(product.extras.length!=0 && ar==1){
                this.order=order;
                this.extras=product.extras.filter(row=>{
                    return row.free_form==1;
                });
                alertify.extrasDialog(document.getElementById('extras_table'));
            }
            else if(product.extras.length!=0 && no_AR==1){
                this.order=order;
                this.extras=product.extras.filter(row=>{
                    return row.free_form==1;
                });
                alertify.extrasDialog(document.getElementById('extras_table'));
            }
            else{
                this.order=order;
                this.extras=product.extras;
                alertify.extrasDialog(document.getElementById('extras_table'));
            }

        },
        addExtra:function(extra){
			var flag = 1;

            if(extra){
				this.order.extras.forEach((tempExtra)=>{
					if(extra.id == tempExtra.id){
						flag = 0;
                    }
                    if(extra.free_form == tempExtra.free_form){
                        flag = 0;
                    }
		        });
				if(flag == 1){
					this.order.extras.push(extra);
					this.order.price=window.parseFloat(this.order.price)+window.parseFloat(extra.price);
				}
				else{
					this.$parent.showMessage("No se puede agregar otro Antireflejante de este tipo.", "warning");
				}
            }
            this.$parent.sale.cart.push(this.order);
            this.order={};
            alertify.closeAll();
        },
        selectOrder:function(cart){
            let product=cart.product;
            let no_AR=cart.not_ar;
            let ar=cart.ar;
            let indexProduct = this.$parent.sale.cart.findIndex(row=>{
                if(row.product_has_subcategory_id==cart.product_has_subcategory_id)
                    return true;
            });
            let order = this.$parent.sale.cart[indexProduct];
            this.$parent.sale.cart.splice(indexProduct,1);

            if(product.extras.length==0 || no_AR == 1 && ar==1){
                alertify.closeAll();
                this.$parent.sale.cart.push(order);
                this.getDiscounts();
                this.rx="";
            }
            else if(product.extras.length!=0 && ar==1){
                this.order=order;
                this.extras=product.extras.filter(row=>{
                    return row.free_form==1;
                });
                alertify.extrasDialog(document.getElementById('extras_table'));
            }
            else if(product.extras.length!=0 && no_AR==1){
                this.order=order;
                this.extras=product.extras.filter(row=>{
                    return row.free_form==1;
                });
                alertify.extrasDialog(document.getElementById('extras_table'));
            }
            else{
                this.order=order;
                this.extras=product.extras;
                alertify.extrasDialog(document.getElementById('extras_table'));
            }
            // if(cart.product.extras.length==0){
            //     this.$parent.showMessage("No puedes agregar antireflejante a este diseño.");
            // }
            // else{
            //     let indexProduct = this.$parent.sale.cart.findIndex(row=>{
            //         if(row.product_has_subcategory_id==cart.product_has_subcategory_id)
            //             return true;
            //     });
            //     this.order = this.$parent.sale.cart[indexProduct];
            //     this.$parent.sale.cart.splice(indexProduct,1);
            //     this.extras=cart.product.extras;
            //     alertify.extrasDialog(document.getElementById('extras_table'));
            // }
        },
        deleteProduct:function(index){
            this.$parent.sale.cart.splice(index,1);
        },
        saveClient:function(){
            //this.$parent.inPetition=true;
            this.$parent.validateAll(()=>{
                var data=this.client;
                if(this.$parent.sale.client_id!=""){
                    // axios.post(tools.url("/api/client/"+this.client.id),data)
                    // .then((response)=>{
                    //     this.$parent.sale.client_id=response.data.id;
                    //     this.$parent.inPetition=false;
                    //     this.$refs.form.changeTab(0,1);
                    // }).catch((error)=>{
                    //     this.$parent.handleErrors(error);
                    //     this.$parent.inPetition=false;
                    // });
                    this.$refs.form.changeTab(0,1);
                    return true;
                }
                else{
                    // axios.post(tools.url("/api/client"),data)
                    // .then((response)=>{
                    //     this.$parent.sale.client_id=response.data.id;
                    //     this.$parent.inPetition=false;
                    //     this.$refs.form.changeTab(0,1);
                    // }).catch((error)=>{
                    //     this.$parent.handleErrors(error);
                    //     this.$parent.inPetition=false;
                    // });
                    this.$parent.showMessage("Primero busca un cliente.");
                    return false;
                }
            },(e)=>{
                console.log(e);
                this.$parent.inPetition=false;
                return false;
            },this.$refs.client);

        },
        getClient(){
            this.$parent.inPetition=true;

            axios.get(tools.url("/api/client/"+this.$parent.sale.client_id))
            .then((response)=>{
                this.client=response.data;

                this.client.state={value:this.client.state.id,label:this.client.state.name};
                this.client.town={value:this.client.town.id,label:this.client.town.name};
                if(this.client.facturacion.state != null){
                  this.client.facturacion.state={value:this.client.facturacion.state.id,label:this.client.facturacion.state.name};
                }
                if(this.client.facturacion.town != null){
                  this.client.facturacion.town={value:this.client.facturacion.town.id,label:this.client.facturacion.town.name};
                }
                this.$parent.inPetition=false;

            }).catch((error)=>{
                this.$parent.handleErrors(error);
                this.$parent.inPetition=false;
            });

        },
        cleanClient:function(){
            this.client={
                    name:"",
                    emal:"",
                    phone:"",
                    celphone:"",
                    comertial_name:"",
                    address:"",
                    suburb:"",
                    state_id:"",
                    state:{value:'',label:'Selecciona..'},
                    town_id:"",
                    town:{value:'',label:'Selecciona..'},
                    postal_code:"",
                    discounts:[],
                    branch_id:"",
                    branch:{value:'',label:'Selecciona..'},
                    payment_plan:"",
                    facturacion:{
                        name:"",
                        phone:"",
                        celphone:"",
                        rfc:"",
                        address:"",
                        suburb:"",
                        state_id:"",
                        state:{value:'',label:'Selecciona..'},
                        town_id:"",
                        town:{value:'',label:'Selecciona..'},
                        postal_code:""
                    }
                };
            this.$parent.sale.client_id="";
        },
        filterProduct:function(product,category_id,subcategory_id){
            let data= product.prices.find((r)=>{
                if(r.category_id==category_id && r.subcategory_id==subcategory_id){
                    return true;
                }
            });

            if(data)
                return data;
            else
                return { price: 0 }
        },
    },
    mounted(){
        this.getStates();
        this.getTypes();
        if(this.$parent.sale.client_id!="")
            this.getClient();

        alertify.clientsDialog || alertify.dialog('clientsDialog',function(){
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

        alertify.typesDialog || alertify.dialog('typesDialog',function(){
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

        alertify.productsDialog || alertify.dialog('productsDialog',function(){
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

        alertify.extrasDialog || alertify.dialog('extrasDialog',function(){
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
                            closable:false
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
div#types_table button {
    margin-top: 35px;
}
</style>
