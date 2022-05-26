<template>
	<div class="row">

		<div class="col-md-12">

			<div class="panel panel-primary" data-collapsed="0">

				<div class="panel-heading">
					<div class="panel-title">
						<i class="fab fa-odnoklassniki"></i> Cliente
					</div>
					<div class="panel-options">
						<a @click="$router.push('/clients/')"><i class="fas fa-times"></i></a>
					</div>
				</div>

				<div class="panel-body">
					<form role="form" class="form-horizontal" @submit.prevent="newClient($event.target)">
						<div class="col-md-12">
							<div class="tabs-vertical-env">
								<ul class="nav tabs-vertical">
									<li class="active"><a href="#v-data" data-toggle="tab" aria-expanded="true">Datos</a></li>
									<li class=""><a href="#v-discounts" data-toggle="tab" aria-expanded="false">Condiciones comerciales</a></li>
									<li class=""><a href="#v-facturacion" data-toggle="tab" aria-expanded="false">Facturacion</a></li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane active" id="v-data">
										<input-form name="nombre" text="Nombre" :data.sync="client.name" validate="required"></input-form>
										<input-form name="email" text="Email" :data.sync="client.email" validate="required"></input-form>
                                        <input-form name="telefono" text="Telefono" :data.sync="client.phone"></input-form>
                                        <input-form name="celular" text="Celular" :data.sync="client.celphone"></input-form>
                                        <input-form name="nombre_comercial" text="Nombre comercial" :data.sync="client.comertial_name"></input-form>
                        				<!-- <input-form name="RFC" text="R.F.C." :data.sync="client.rfc"></input-form> -->
                                        <select-form text="Estado" name="state" :options="states" :data.sync="client.state"></select-form>
                                        <select-form text="Ciudad" name="town" :options="towns" :data.sync="client.town"></select-form>
										<input-form name="codigo_postal" text="Codigo postal" :data.sync="client.postal_code" validate="required|digits:5"></input-form>
                                        <input-form name="direccion" text="Direccion" :data.sync="client.address" validate="required"></input-form>
                                        <input-form name="colonia" text="Colonia" :data.sync="client.suburb" validate="required"></input-form>
										<select-form text="PDV" name="punto de venta" :options="branches" :data.sync="client.branch"></select-form>
										<select-form text="Estatus" name="estatus" :options="statusOp" :data.sync="client.status" @update:data="setReason"></select-form>
										<select-form text="División" name="category" :options="['DOCTORES', 'CADENAS', 'OFTALMÓLOGOS']" :data.sync="client.category"></select-form>
										<hr>
										<input-form name="contacto_nombre" text="Contacto nombre" :data.sync="client.contact_name" validate=""></input-form>
										<input-form name="contacto_email" text="Contacto email" :data.sync="client.contact_email" validate="email"></input-form>
										<input-form name="contacto_telefono" text="Contacto telefono" :data.sync="client.contact_phone"></input-form>
										<input-form name="contacto_celular" text="Origen del cliente" :data.sync="client.contact_celphone"></input-form>
									</div>
									<div class="tab-pane" id="v-discounts">
										<div class="row">
											<div class="col-md-6">
												<table class="table table-bordered">
													<thead>
														<tr>
															<th>Descuento general</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td><input type="text" v-model="discountGeneral"></td>
														</tr>
													</tbody>
												</table>
											</div>
											<div class="col-md-6">
												<table class="table table-bordered">
													<thead>
														<tr>
															<th>Plazo de pago</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td><input type="number" min="0" v-model="client.payment_plan"><small>*En semanas.</small></td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
										<table v-for="typ in types" class="table table-bordered" :key="typ.id">
											<thead>
												<tr>
													<th>{{ typ.name }}</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td><input type="text" v-model="client.discounts[searchDiscount(typ.id)].discount"></td>
												</tr>
											</tbody>
										</table>
									</div>
									<div class="tab-pane" id="v-facturacion">
										<input-form name="nombre" text="Nombre" :data.sync="client.facturacion.name"></input-form>
										<input-form name="telefono" text="Telefono" :data.sync="client.facturacion.phone"></input-form>
                                        <input-form name="celular" text="Celular" :data.sync="client.facturacion.celphone"></input-form>
										<!-- <input-form name="RFC" text="R.F.C." :data.sync="client.facturacion.rfc"></input-form> -->
										<div class="form-group">
											<label for="field-1" class="col-sm-3 control-label">R.F.C:</label>
											<div class="col-sm-7">
												<input type="text" class="form-control" name="RFC" v-model="client.facturacion.rfc" @change="checkClient($event)">
												<!-- <small class="has-error" >{{ errors.first(name) }}</small> -->
											</div>
										</div>
										<input-form name="direccion" text="Direccion" :data.sync="client.facturacion.address"></input-form>
										<input-form name="colonia" text="Colonia" :data.sync="client.facturacion.suburb"></input-form>
										<select-form text="Estado" name="state" :options="states" :data.sync="client.facturacion.state"></select-form>
                                        <select-form text="Ciudad" name="town" :options="towns_facturacion" :data.sync="client.facturacion.town"></select-form>
										<input-form name="codigo_postal" text="Codigo postal" :data.sync="client.facturacion.postal_code"></input-form>
									</div>
								</div>
							</div>
						</div>

					<div class="form-group">
						<div class="col-sm-12">
							<button type="button" class="btn btn-danger" @click="deleteClient" v-show="$route.params.id"><i class="fa fa-trash"></i> Borrar</button>

							<button type="submit" class="btn btn-success pull-right"><i class="far fa-save"></i> Guardar</button>
							<button type="button" class="btn btn-default pull-right" @click="$router.push('/clients/')">Cancelar</button>
						</div>
					</div>
					</form>
				</div>
			</div>
		</div>

	</div>
</template>
<script type="text/javascript">
	export default {
		data(){
			return {
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
					status:{value:'',label:'Selecciona..'},
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
					},
					category: 'DOCTORES',
					reason: null,
				},
				discountGeneral:0,
				states:[],
				towns:[],
				towns_facturacion:[],
				types:[],
				branches:[],
				statusOp:["Activo", "Inactivo"],
				id:"",
				check:false,
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
			'client.facturacion.state.value':{
                handler:function(v){
                    this.client.facturacion.state_id=v;
                    if(v!='' && !isNaN(v)){
                        this.getTownsFacturacion();
                    }
                },
                deep: true,
            },
			'client.facturacion.town.value':{
                handler:function(v){
                    this.client.facturacion.town_id=v;
                },
                deep: true,
			},
			'client.branch':{
				handler:function(v){
					this.client.branch_id=v.value;
				},
				deep:true
			},
			discountGeneral:function(v){
				this.client.discounts.forEach((val,k)=>{
					this.client.discounts[k].discount=v;
				});
			}
        },
		methods:{
            searchDiscount:function(type_id){
				let index=this.client.discounts.findIndex((row)=>{
					if(row.type_id==type_id){
						return true;
					}
				});
				if(index==-1){
					this.client.discounts.push({discount:0,type_id:type_id});
					return this.client.discounts.length-1;
				}
				return index;

			},
			getClient(){
				this.$parent.inPetition=true;

				axios.get(tools.url("/api/client/"+this.id))
				.then((response)=>{
			    	this.client=response.data;
                    this.client.state={value:this.client.state.id,label:this.client.state.name};
					this.client.town={value:this.client.town.id,label:this.client.town.name};
					this.client.branch={value:this.client.branch.id,label:this.client.branch.name};
					if(this.client.facturacion.town){
						this.client.facturacion.state={value:this.client.facturacion.state.id,label:this.client.facturacion.state.name};
						this.client.facturacion.town={value:this.client.facturacion.town.id,label:this.client.facturacion.town.name};
					}else{
						this.client.facturacion.state={value:"",label:"Selecciona uno.."};
						this.client.facturacion.town={value:"",label:"Selecciona uno.."};
					}
					this.$parent.inPetition=false;

			    }).catch((error)=>{
			    	this.$parent.handleErrors(error);
			        this.$parent.inPetition=false;
				});

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
			getTownsFacturacion:function(){
				this.$parent.inPetition=true;
				axios.get(tools.url("/api/towns/"+this.client.facturacion.state_id))
				.then((response)=>{
					this.towns_facturacion=response.data;
					this.towns_facturacion=jQuery.map(this.towns_facturacion,(row)=>{
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
					this.types=response.data;

					this.$parent.inPetition=false;
				})
				.catch((error)=>{
					this.$parent.handleErrors(error);
					this.$parent.inPetition=false;
				});
			},
			getbranches:function(){
				this.$parent.inPetition=true;
				axios.get(tools.url("/api/branches"))
				.then((response)=>{
					this.branches=response.data;
					this.branches=jQuery.map(this.branches,(row)=>{
						return {'value':row.id,'label':row.name};
					});
					this.$parent.inPetition=false;
				})
				.catch((error)=>{
					this.$parent.handleErrors(error);
					this.$parent.inPetition=false;
				});
			},
            newClient(form){
				this.$parent.inPetition=true;
				this.$parent.validateAll(()=>{

					var data=this.client;
					if(this.$route.params.id){
						axios.post(tools.url("/api/client/"+this.id),data)
						.then((response)=>{
					    	this.getClient();
					    	this.$parent.showMessage("Cliente "+this.client.name+" modificado correctamente!","success");
					    	this.$parent.inPetition=false;
					    }).catch((error)=>{
					    	this.$parent.handleErrors(error);
					        this.$parent.inPetition=false;
					    });
					}
					else{
						axios.post(tools.url("/api/client"),data)
						.then((response)=>{
							var client=response.data;
					    	this.$parent.showMessage("Cliente "+client.name+" agregado correctamente!","success");
					    	this.$router.push('/clients/edit/'+client.id);
					    	this.$parent.inPetition=false;
					    }).catch((error)=>{
					    	this.$parent.handleErrors(error);
					        this.$parent.inPetition=false;
					    });
					}
				},(e)=>{
					console.log(e);
					this.$parent.inPetition=false;
				});
			},
			deleteClient:function(){
				alertify.confirm("Alerta!","¿Seguro que deseas borrar este cliento?",()=>{
					this.$parent.inPetition=true;
					axios.delete(tools.url("/api/client/"+this.id))
					.then((response)=>{
						this.$parent.showMessage(response.data.msg,"success");
						this.$router.push("/clients/");
						this.$parent.inPetition=false;
					})
					.catch((error)=>{
						this.$parent.handleErrors(error);
				        this.$parent.inPetition=false;
					});
				},
				()=>{

				});
			},
			checkClient(event) {
				this.$parent.inPetition=true;
				let rfc = event.target.value;

				axios.post(tools.url("/api/clients/client_check"), { rfc: rfc }).then(result => {
					if(!result.data.status) {
						alertify.error(result.data.msg);
					}
					this.$parent.inPetition = false;
				}).catch((error)=>{
					this.$parent.handleErrors(error);
					this.$parent.inPetition = false;
				});
				if(false && true)
					alert(`Ya hay un cliente con el RFC: ${rfc}`);
			},
			setReason(value) {
				if(typeof value == 'string') {
					if(value == 'Activo') {
						this.client.reason = null;
					} else {
						var self = this;
						alertify.prompt('¿Por qué se está inhabilitando?', 'Razón', this.client.reason,
										function(evt, value) { self.client.reason = value;  alertify.success('Gracias por los comentarios') },
										function() { });
					}
				}
			}
		},
		mounted(){

			if(this.$route.params.id){
				this.id=this.$route.params.id;
				this.getClient();
			}
            this.getTypes();
			this.getStates();
			this.getbranches();
		}
	}
</script>
