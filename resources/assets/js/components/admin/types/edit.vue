<template>
	<div class="row">
		<div class="col-md-offset-2 col-md-8">
			
			<div class="panel panel-primary" data-collapsed="0">
			
				<div class="panel-heading">
					<div class="panel-title">
						<i class="fas fa-clipboard"></i> Diseño
					</div>
					<div class="panel-options">
						<a @click="$router.push('/types/')"><i class="fas fa-times"></i></a>
					</div>
				</div>
				
				<div class="panel-body">
					<form role="form" class="form-horizontal" @submit.prevent="newType($event.target)">

						<input-form name="nombre" text="Nombre" :data.sync="type.name" validate="required"></input-form>
						<input-form name="observacion" text="Observacion" :data.sync="type.description" validate="min:5"></input-form>
				
						<div class="form-group">
							<div class="col-sm-12">
								<button type="button" class="btn btn-danger" @click="deleteType" v-show="$route.params.id"><i class="fa fa-trash"></i> Borrar</button>
								<button type="submit" class="btn btn-success pull-right"><i class="far fa-save"></i> Guardar</button> 
								<button type="button" class="btn btn-default pull-right" @click="$router.push('/types/')">Cancelar</button>			
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
				type:{
					name:"",
					description:"",
					
				},
				id:"",
				check:false,
			}
		},
        methods:{
			getType(){
				this.$parent.inPetition=true;

				axios.get(tools.url("/api/type/"+this.id))
				.then((response)=>{
			    	this.type=response.data;
			    	
			    	this.$parent.inPetition=false;
			    }).catch((error)=>{
			    	this.$parent.handleErrors(error);
			        this.$parent.inPetition=false;
			    });
			},
			newType(form){
				this.$parent.inPetition=true;
				this.$parent.validateAll(()=>{

					var data=tools.params(form,this.type);
					if(this.$route.params.id){
						axios.post(tools.url("/api/type/"+this.id),data)
						.then((response)=>{
					    	this.getType();
					    	this.$parent.showMessage("Tipo "+this.type.name+" modificado correctamente!","success");
					    	this.$parent.inPetition=false;
					    }).catch((error)=>{
					    	this.$parent.handleErrors(error);
					        this.$parent.inPetition=false;
					    });
					}
					else{
						axios.post(tools.url("/api/type"),data)
						.then((response)=>{
							var type=response.data;
					    	this.$parent.showMessage("Tipo "+type.name+" agregado correctamente!","success");
					    	this.$router.push('/types/edit/'+type.id);
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
			deleteType:function(){
				alertify.confirm("Alerta!","¿Seguro que deseas borrar esta tipo?",()=>{
					this.$parent.inPetition=true;
					axios.delete(tools.url("/api/type/"+this.id))
					.then((response)=>{
						this.$parent.showMessage(response.data.msg,"success");
						this.$router.push("/types/");
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
				this.getType();
			}
		}
	}
</script>