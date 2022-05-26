<template>
	<div class="row">
		<div class="col-md-offset-1 col-md-10">
			
			<div class="panel panel-primary" data-collapsed="0">
			
				<div class="panel-heading">
					<div class="panel-title">
						<i class="fas fa-home"></i> Sucursal
					</div>
					<div class="panel-options">
						<a @click="$router.push('/branches/')"><i class="fas fa-times"></i></a>
					</div>
				</div>
				
				<div class="panel-body">
					<form role="form" class="form-horizontal" @submit.prevent="newBranch($event.target)">

						<input-form name="nombre" text="Nombre" :data.sync="branch.name" validate="required"></input-form>
						<input-form name="address" text="Direccion" :data.sync="branch.address" validate="min:5|required"></input-form>
						
						<select-form text="Estado" name="state" :options="states" :data.sync="branch.state"></select-form>
                        <select-form text="Ciudad" name="town" :options="towns" :data.sync="branch.town"></select-form>
                        <select-form text="Laboratorio" name="laboratory" :options="laboratories" :data.sync="branch.laboratory"></select-form>

						<div class="form-group">
							<div class="col-sm-12">
								<button type="button" class="btn btn-danger" @click="deleteBranch" v-show="$route.params.id"><i class="fa fa-trash"></i> Borrar</button>
								<button type="submit" class="btn btn-success pull-right"><i class="far fa-save"></i> Guardar</button> 
								<button type="button" class="btn btn-default pull-right" @click="$router.push('/branches/')">Cancelar</button>			
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
				branch:{
					name:"",
					address:"",
					state_id:"",
                    town_id:"",
                    laboratory_id:"",
                    state:{value:"",label:"Selecciona uno."},
                    town:{value:"",label:"Selecciona uno."},
                    laboratory:{value:"",label:"Selecciona uno."}
				},
				
				states:{},
                towns:{},
                laboratories:{},
				id:"",
				check:false,
			}
		},
        watch:{
            'branch.state.value':{
                handler:function(v){
                    this.branch.state_id=v;
                    if(v!='' && !isNaN(v)){
                        this.getTowns();
                    }                   
                },
                deep: true,
            },
            'branch.town.value':{
                handler:function(v){
                    this.branch.town_id=v;
                },
                deep: true,
            },
            'branch.laboratory.value':{
                handler:function(v){
                    this.branch.laboratory_id=v;
                },
                deep: true,
            }
        },
		methods:{
			getBranch(){
				this.$parent.inPetition=true;

				axios.get(tools.url("/api/branch/"+this.id))
				.then((response)=>{
			    	this.branch=response.data;
			    	this.branch.state={value:this.branch.state.id,label:this.branch.state.name};
                    this.branch.town={value:this.branch.town.id,label:this.branch.town.name};
                    this.branch.laboratory={value:this.branch.laboratory.id,label:this.branch.laboratory.name};
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
				axios.get(tools.url("/api/towns/"+this.branch.state_id))
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
            getLaboratories:function(){
				this.$parent.inPetition=true;
				axios.get(tools.url("/api/laboratories"))
				.then((response)=>{
					this.laboratories=response.data;
					this.laboratories=jQuery.map(this.laboratories,(row)=>{
						return {'value':row.id,'label':row.name};
					});
					this.$parent.inPetition=false;
				})
				.catch((error)=>{
					this.$parent.handleErrors(error);
					this.$parent.inPetition=false;
				});
			},
			newBranch(form){
				this.$parent.inPetition=true;
				this.$parent.validateAll(()=>{

					var data=tools.params(form,this.branch);
					if(this.$route.params.id){
						axios.post(tools.url("/api/branch/"+this.id),data)
						.then((response)=>{
					    	this.getBranch();
					    	this.$parent.showMessage("Sucursal "+this.branch.name+" modificado correctamente!","success");
					    	this.$parent.inPetition=false;
					    }).catch((error)=>{
					    	this.$parent.handleErrors(error);
					        this.$parent.inPetition=false;
					    });
					}
					else{
						axios.post(tools.url("/api/branch"),data)
						.then((response)=>{
							var branch=response.data;
					    	this.$parent.showMessage("Sucursal "+branch.name+" agregado correctamente!","success");
					    	this.$router.push('/branches/edit/'+branch.id);
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
			deleteBranch:function(){
				alertify.confirm("Alerta!","¿Seguro que deseas borrar esta sucursal?",()=>{
					this.$parent.inPetition=true;
					axios.delete(tools.url("/api/branch/"+this.id))
					.then((response)=>{
						this.$parent.showMessage(response.data.msg,"success");
						this.$router.push("/branches/");
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

		},
		mounted(){
			if(this.$route.params.id){
				this.id=this.$route.params.id;
				this.getBranch();
			}
			this.getStates();
            
            this.getLaboratories();
		}
	}
</script>