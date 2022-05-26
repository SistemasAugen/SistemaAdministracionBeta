<template>
	<div class="row">
		<div class="col-md-offset-1 col-md-10">
			
			<div class="panel panel-primary" data-collapsed="0">
			
				<div class="panel-heading">
					<div class="panel-title">
						<i class="fas fa-flask"></i> Laboratorio
					</div>
					<div class="panel-options">
						<a @click="$router.push('/laboratories/')"><i class="fas fa-times"></i></a>
					</div>
				</div>
				
				<div class="panel-body">
					<form role="form" class="form-horizontal" @submit.prevent="newLaboratory($event.target)">

						<input-form name="nombre" text="Nombre" :data.sync="laboratory.name" validate="alpha_spaces|required|min:5"></input-form>
						<input-form name="address" text="Direccion" :data.sync="laboratory.address" validate="min:5|required"></input-form>
						
						<select-form text="Estado" name="state" :options="states" :data.sync="laboratory.state"></select-form>
                        <select-form text="Ciudad" name="town" :options="towns" :data.sync="laboratory.town"></select-form>
                        
						<div class="form-group">
							<div class="col-sm-12">
								<button type="button" class="btn btn-danger" @click="deleteLaboratory" v-show="$route.params.id"><i class="fa fa-trash"></i> Borrar</button>
								<button type="submit" class="btn btn-success pull-right"><i class="far fa-save"></i> Guardar</button> 
								<button type="button" class="btn btn-default pull-right" @click="$router.push('/laboratories/')">Cancelar</button>			
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
				laboratory:{
					name:"",
					address:"",
					state_id:"",
                    town_id:"",
                    
                    state:{value:"",label:"Selecciona uno."},
                    town:{value:"",label:"Selecciona uno."},
                    
				},
				
				states:{},
                towns:{},
                laboratories:{},
				id:"",
				check:false,
			}
		},
        watch:{
            'laboratory.state.value':{
                handler:function(v){
                    this.laboratory.state_id=v;
                    if(v!='' && !isNaN(v)){
                        this.getTowns();
                    }                   
                },
                deep: true,
            },
            'laboratory.town.value':{
                handler:function(v){
                    this.laboratory.town_id=v;
                },
                deep: true,
            },
        },
		methods:{
			getLaboratory(){
				this.$parent.inPetition=true;

				axios.get(tools.url("/api/laboratory/"+this.id))
				.then((response)=>{
			    	this.laboratory=response.data;
			    	this.laboratory.state={value:this.laboratory.state.id,label:this.laboratory.state.name};
                    this.laboratory.town={value:this.laboratory.town.id,label:this.laboratory.town.name};
                    
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
				axios.get(tools.url("/api/towns/"+this.laboratory.state_id))
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
			newLaboratory(form){
				this.$parent.inPetition=true;
				this.$parent.validateAll(()=>{

					var data=tools.params(form,this.laboratory);
					if(this.$route.params.id){
						axios.post(tools.url("/api/laboratory/"+this.id),data)
						.then((response)=>{
					    	this.getLaboratory();
					    	this.$parent.showMessage("Laboratorio "+this.laboratory.name+" modificado correctamente!","success");
					    	this.$parent.inPetition=false;
					    }).catch((error)=>{
					    	this.$parent.handleErrors(error);
					        this.$parent.inPetition=false;
					    });
					}
					else{
						axios.post(tools.url("/api/laboratory"),data)
						.then((response)=>{
							var laboratory=response.data;
					    	this.$parent.showMessage("Laboratorio "+laboratory.name+" agregado correctamente!","success");
					    	this.$router.push('/laboratories/edit/'+laboratory.id);
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
			deleteLaboratory:function(){
				alertify.confirm("Alerta!","¿Seguro que deseas borrar esta laboratorio?",()=>{
					this.$parent.inPetition=true;
					axios.delete(tools.url("/api/laboratory/"+this.id))
					.then((response)=>{
						this.$parent.showMessage(response.data.msg,"success");
						this.$router.push("/laboratories/");
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
				this.getLaboratory();
			}
			this.getStates();
            
            this.getLaboratories();
		}
	}
</script>