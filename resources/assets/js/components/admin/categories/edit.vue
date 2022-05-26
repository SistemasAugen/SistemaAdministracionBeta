<template>
	<div class="row">
		<div class="col-md-offset-2 col-md-8">
			
			<div class="panel panel-primary" data-collapsed="0">
			
				<div class="panel-heading">
					<div class="panel-title">
						<i class="fas fa-clipboard"></i> Material
					</div>
					<div class="panel-options">
						<a @click="$router.push('/categories/')"><i class="fas fa-times"></i></a>
					</div>
				</div>
				
				<div class="panel-body">
					<form role="form" class="form-horizontal" @submit.prevent="newCategory($event.target)">

						<input-form name="nombre" text="Nombre" :data.sync="category.name" validate="required"></input-form>
						<input-form name="color" text="Color" :data.sync="category.color" :type="'color'"></input-form>
						
						<select-form text="Diseño" name="diseño" :options="types" :data.sync="category.types" :multiple="true"></select-form>
                        
						<div class="form-group">
							<div class="col-sm-12">
								<button type="button" class="btn btn-danger" @click="deletecategory" v-show="$route.params.id"><i class="fa fa-trash"></i> Borrar</button>
								<button type="submit" class="btn btn-success pull-right"><i class="far fa-save"></i> Guardar</button> 
								<button type="button" class="btn btn-default pull-right" @click="$router.push('/categories/')">Cancelar</button>			
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
				category:{
					name:"",
					color:"",
					types:[],
                    
				},
				types:{},               
				id:"",
				check:false,
			}
		},
        watch:{
           
        },
		methods:{
			getCategory(){
				this.$parent.inPetition=true;

				axios.get(tools.url("/api/category/"+this.id))
				.then((response)=>{
			    	this.category=response.data;
			    	this.category.types=jQuery.map(this.category.types,(row)=>{
                        return {value:row.id,label:row.name};
                    });
			    	this.$parent.inPetition=false;
			    }).catch((error)=>{
			    	this.$parent.handleErrors(error);
			        this.$parent.inPetition=false;
			    });
			},
            getTypes:function(){
				this.$parent.inPetition=true;
				axios.get(tools.url("/api/types"))
				.then((response)=>{
					this.types=response.data;
					this.types=jQuery.map(this.types,(row)=>{
						return {'value':row.id,'label':row.name};
					});
					this.$parent.inPetition=false;
				})
				.catch((error)=>{
					this.$parent.handleErrors(error);
					this.$parent.inPetition=false;
				});
			},
			newCategory(form){
				this.$parent.inPetition=true;
				this.$parent.validateAll(()=>{
                    this.category.types_id=jQuery.map(this.category.types,(row)=>{
                        return row.value;
                    });
					var data=tools.params(form,this.category);
					if(this.$route.params.id){
						axios.post(tools.url("/api/category/"+this.id),data)
						.then((response)=>{
					    	this.getCategory();
					    	this.$parent.showMessage("Categoria "+this.category.name+" modificado correctamente!","success");
					    	this.$parent.inPetition=false;
					    }).catch((error)=>{
					    	this.$parent.handleErrors(error);
					        this.$parent.inPetition=false;
					    });
					}
					else{
						axios.post(tools.url("/api/category"),data)
						.then((response)=>{
							var category=response.data;
					    	this.$parent.showMessage("Categoria "+category.name+" agregado correctamente!","success");
					    	this.$router.push('/categories/edit/'+category.id);
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
			deletecategory:function(){
				alertify.confirm("Alerta!","¿Seguro que deseas borrar esta categoria?",()=>{
					this.$parent.inPetition=true;
					axios.delete(tools.url("/api/category/"+this.id))
					.then((response)=>{
						this.$parent.showMessage(response.data.msg,"success");
						this.$router.push("/categories/");
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
				this.getCategory();
			}
			this.getTypes();
		}
	}
</script>