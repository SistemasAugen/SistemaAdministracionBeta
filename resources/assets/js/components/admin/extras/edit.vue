<template>
	<div class="row">
		<div class="col-md-offset-2 col-md-8">
			
			<div class="panel panel-primary" data-collapsed="0">
			
				<div class="panel-heading">
					<div class="panel-title">
						<i class="fas fa-clipboard"></i> Antireflejante
					</div>
					<div class="panel-options">
						<a @click="$router.push('/extras/')"><i class="fas fa-times"></i></a>
					</div>
				</div>
				
				<div class="panel-body">
					<form role="form" class="form-horizontal" @submit.prevent="newExtra($event.target)">

						<input-form name="nombre" text="Nombre" :data.sync="extra.name" validate="required"></input-form>
						<input-form name="descripcion" text="Descripcion" :data.sync="extra.description"></input-form>
                        <input-form name="precio" text="Precio" :data.sync="extra.price" validate="decimal|required"></input-form>
						
						<select-form text="Precio" name="precio" :options="products" :data.sync="extra.products" :multiple="true"></select-form>
						<checkbox-form text="Forma libre" :data.sync="extra.free_form" value="1" name="antireflejante" key="a"></checkbox-form>                        

						<div class="form-group">
							<div class="col-sm-12">
								<button type="button" class="btn btn-danger" @click="deleteExtra" v-show="$route.params.id"><i class="fa fa-trash"></i> Borrar</button>
								<button type="submit" class="btn btn-success pull-right"><i class="far fa-save"></i> Guardar</button> 
								<button type="button" class="btn btn-default pull-right" @click="$router.push('/extras/')">Cancelar</button>			
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
				extra:{
					name:"",
					description:"",
                    price:"",
                    products:[],
					free_form:0
				},
				products:{},
				id:"",
				check:false,
			}
		},
        watch:{
           
        },
		methods:{
			getExtra(){
				this.$parent.inPetition=true;

				axios.get(tools.url("/api/extra/"+this.id))
				.then((response)=>{
			    	this.extra=response.data;
			    	this.extra.products=jQuery.map(this.extra.products,(row)=>{
                        return {value:row.id,label:row.name};
                    });
			    	this.$parent.inPetition=false;
			    }).catch((error)=>{
			    	this.$parent.handleErrors(error);
			        this.$parent.inPetition=false;
			    });
			},
            getProducts:function(){
				this.$parent.inPetition=true;
				axios.get(tools.url("/api/products"))
				.then((response)=>{
					this.products=response.data;
					this.products=jQuery.map(this.products,(row)=>{
						return {'value':row.id,'label':row.name};
					});
					this.$parent.inPetition=false;
				})
				.catch((error)=>{
					this.$parent.handleErrors(error);
					this.$parent.inPetition=false;
				});
			},
			newExtra(form){
				this.$parent.inPetition=true;
				this.$parent.validateAll(()=>{
                    this.extra.products_id=jQuery.map(this.extra.products,(row)=>{
                        return row.value;
                    });
					var data=tools.params(form,this.extra);
					if(this.$route.params.id){
						axios.post(tools.url("/api/extra/"+this.id),data)
						.then((response)=>{
					    	this.getExtra();
					    	this.$parent.showMessage("Antireflejante "+this.extra.name+" modificado correctamente!","success");
					    	this.$parent.inPetition=false;
					    }).catch((error)=>{
					    	this.$parent.handleErrors(error);
					        this.$parent.inPetition=false;
					    });
					}
					else{
						axios.post(tools.url("/api/extra"),data)
						.then((response)=>{
							var extra=response.data;
					    	this.$parent.showMessage("Antireflejante "+extra.name+" agregado correctamente!","success");
					    	this.$router.push('/extras/edit/'+extra.id);
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
			deleteExtra:function(){
				alertify.confirm("Alerta!","¿Seguro que deseas borrar esta antireflejante?",()=>{
					this.$parent.inPetition=true;
					axios.delete(tools.url("/api/extra/"+this.id))
					.then((response)=>{
						this.$parent.showMessage(response.data.msg,"success");
						this.$router.push("/extras/");
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
				this.getExtra();
			}
			this.getProducts();
		}
	}
</script>