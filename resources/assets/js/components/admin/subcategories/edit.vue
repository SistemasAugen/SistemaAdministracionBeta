<template>
	<div class="row">
		<div class="col-md-offset-2 col-md-8">

			<div class="panel panel-primary" data-collapsed="0">

				<div class="panel-heading">
					<div class="panel-title">
						<i class="fas fa-clipboard"></i> Caracteristica
					</div>
					<div class="panel-options">
						<a @click="$router.push('/subcategories/')"><i class="fas fa-times"></i></a>
					</div>
				</div>

				<div class="panel-body">
					<form role="form" class="form-horizontal" @submit.prevent="newSubcategory($event.target)">

						<input-form name="nombre" text="Nombre" :data.sync="subcategory.name" validate="required"></input-form>
						<input-form name="observacion" text="Observacion" :data.sync="subcategory.description" validate="min:5"></input-form>

						<select-form text="Material" name="material" :options="categories" :data.sync="subcategory.categories" :multiple="true"></select-form>
						<select-form text="Diseño" name="diseño" :options="types" :data.sync="subcategory.types" :multiple="true"></select-form>
						<checkbox-form text="Antireflejante" :data.sync="subcategory.antireflective" value="1" name="antireflejante" key="a"></checkbox-form>
						<checkbox-form text="No Antireflejante" :data.sync="subcategory.no_antireflective" value="1" name="no antireflejante" key="a"></checkbox-form>

						<div class="form-group">
							<div class="col-sm-12">
								<button type="button" class="btn btn-danger" @click="deleteSubcategory" v-show="$route.params.id"><i class="fa fa-trash"></i> Borrar</button>
								<button type="submit" class="btn btn-success pull-right"><i class="far fa-save"></i> Guardar</button>
								<button type="button" class="btn btn-default pull-right" @click="$router.push('/subcategories/')">Cancelar</button>
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
				subcategory:{
					name:"",
					description:"",
					categories:[],
                    types:[],
					antireflective:0,
					no_antireflective:0,
				},
				categories:{},
				types:{},
				id:"",
				check:false,
			}
		},
        watch:{

        },
		methods:{
			getSubcategory(){
				this.$parent.inPetition=true;

				axios.get(tools.url("/api/subcategory/"+this.id))
				.then((response)=>{
			    	this.subcategory=response.data;
			    	this.subcategory.categories=jQuery.map(this.subcategory.categories,(row)=>{
                        return {value:row.id,label:row.name};
                    });
					this.subcategory.types=jQuery.map(this.subcategory.types,(row)=>{
                        return {value:row.id,label:row.name};
                    });
			    	this.$parent.inPetition=false;
			    }).catch((error)=>{
			    	this.$parent.handleErrors(error);
			        this.$parent.inPetition=false;
			    });
			},
            getCategories:function(){
				this.$parent.inPetition=true;
				axios.get(tools.url("/api/categories"))
				.then((response)=>{
					this.categories=response.data;
					this.categories=jQuery.map(this.categories,(row)=>{
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
			newSubcategory(form){
				this.$parent.inPetition=true;
				this.$parent.validateAll(()=>{
                    this.subcategory.categories_id=jQuery.map(this.subcategory.categories,(row)=>{
                        return row.value;
                    });
					this.subcategory.types_id=jQuery.map(this.subcategory.types,(row)=>{
                        return row.value;
                    });
					var data=tools.params(form,this.subcategory);
					if(this.$route.params.id){
						axios.post(tools.url("/api/subcategory/"+this.id),data)
						.then((response)=>{
					    	this.getSubcategory();
					    	this.$parent.showMessage("Subcategoria "+this.subcategory.name+" modificado correctamente!","success");
					    	this.$parent.inPetition=false;
					    }).catch((error)=>{
					    	this.$parent.handleErrors(error);
					        this.$parent.inPetition=false;
					    });
					}
					else{
						axios.post(tools.url("/api/subcategory"),data)
						.then((response)=>{
							var subcategory=response.data;
					    	this.$parent.showMessage("Subcategoria "+subcategory.name+" agregado correctamente!","success");
					    	this.$router.push('/subcategories/edit/'+subcategory.id);
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
			deleteSubcategory:function(){
				alertify.confirm("Alerta!","¿Seguro que deseas borrar esta subcategoria?",()=>{
					this.$parent.inPetition=true;
					axios.delete(tools.url("/api/subcategory/"+this.id))
					.then((response)=>{
						this.$parent.showMessage(response.data.msg,"success");
						this.$router.push("/subcategories/");
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
				this.getSubcategory();
			}
			this.getCategories();
			this.getTypes();
		}
	}
</script>
