<template>
	<div class="row">

		<div class="col-md-12">

			<div class="panel panel-primary" data-collapsed="0">

				<div class="panel-heading">
					<div class="panel-title">
						<i class="fas fa-clipboard"></i> Producto
					</div>
					<div class="panel-options">
						<a @click="$router.push('/products/')"><i class="fas fa-times"></i></a>
					</div>
				</div>

				<div class="panel-body">
					<form role="form" class="form-horizontal" @submit.prevent="newProduct($event.target)">
						<div class="col-md-12">
							<div class="tabs-vertical-env">
								<ul class="nav tabs-vertical">
									<li class="active"><a href="#v-data" data-toggle="tab" aria-expanded="true">Datos</a></li>
									<li class=""><a href="#v-prices" data-toggle="tab" aria-expanded="false">Precios</a></li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane active" id="v-data">
										<!-- <input-form name="codigo" text="Codigo" :data.sync="product.code" validate="required"></input-form>
                        				<input-form name="material" text="Material" :data.sync="product.material"></input-form>
										<input-form name="descripcion" text="Descripcion" :data.sync="product.description"></input-form>
										<input-form name="entrega" text="Tiempo de entrega" :data.sync="product.delivery"></input-form> -->
										<select-form text="Diseño" name="diseño" :options="types" :data.sync="product.type"></select-form>
										<input-form name="version" text="Version" :data.sync="product.name" validate="required"></input-form>
									</div>
									<div class="tab-pane" id="v-prices">
										<table v-for="category in categories" class="table table-bordered" :key="category.id">
											<thead>
												<tr>
													<th :colspan="category.subcategories.length" :style="{backgroundColor:category.color}" style="color:black !important;">{{ category.name }}</th>
												</tr>
												<tr>
													<td v-for="subcategory in category.subcategories" :key="subcategory.id" :style="{backgroundColor:category.color}" style="color:black !important;">{{ subcategory.name }}</td>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td v-for="subcategory in category.subcategories" :key="subcategory.id"><input type="text" v-model="product.prices[searchPrice(category.id,subcategory.id)].price"></td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>

					<div class="form-group">
						<div class="col-sm-12">
							<button type="button" class="btn btn-danger" @click="deleteProduct" v-show="$route.params.id"><i class="fa fa-trash"></i> Borrar</button>

							<button type="submit" class="btn btn-success pull-right"><i class="far fa-save"></i> Guardar</button>
							<button type="button" class="btn btn-default pull-right" @click="$router.push('/products/')">Cancelar</button>
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
				product:{
					name:"",
					code:"",
                    material:"",
                    description:"",
                    delivery:"",
					type_id:"",
					type:{value:"",label:"Selecciona uno..."},
					prices:[],
				},
				categories:[],
				prices:[],
				types:{},
				id:"",
				check:false,
			}
		},
        watch:{
           'product.type':{
			   handler:function(v){
				   	if(v.value!='' && !isNaN(v.value)){
					   if(v.value!=this.product.type_id){
						   this.product.prices=[];
					   }
					   this.product.type_id=v.value;
					   this.getSubcategories(v.value);
					   this.$forceUpdate();
				   	}

			   },
			   deep:true
		   }
        },
		methods:{
			searchPrice:function(category_id,subcategory_id){
				let index=this.product.prices.findIndex((row)=>{
					if(row.category_id==category_id && row.subcategory_id==subcategory_id){
						return true;
					}
				});
				if(index==-1){
					this.product.prices.push({price:0,type_id:this.product.type_id,category_id:category_id,subcategory_id:subcategory_id});
					return this.product.prices.length-1;
				}
				return index;

			},
			getProduct(){
				this.$parent.inPetition=true;

				axios.get(tools.url("/api/product/"+this.id))
				.then((response)=>{
			    	this.product=response.data;
			    	this.product.type={value:this.product.type.id,label:this.product.type.name};
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
						this.categories.push(v);

					});

					this.$parent.inPetition=false;
				})
				.catch((error)=>{
					this.$parent.handleErrors(error);
					this.$parent.inPetition=false;
				});
			},
            newProduct(form){
				this.$parent.inPetition=true;
				this.$parent.validateAll(()=>{

					var data=this.product;
					if(this.$route.params.id){
						axios.post(tools.url("/api/product/"+this.id),data)
						.then((response)=> {
					    	this.getProduct();
					    	this.$parent.showMessage("Producto "+this.product.name+" modificado correctamente!","success");
					    	this.$parent.inPetition=false;
					    }).catch((error)=>{
					    	this.$parent.handleErrors(error);
					        this.$parent.inPetition=false;
					    });
					}
					else{
						axios.post(tools.url("/api/product"),data)
						.then((response)=>{
							var product=response.data;
					    	this.$parent.showMessage("Producto "+product.name+" agregado correctamente!","success");
					    	this.$router.push('/products/edit/'+product.id);
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
			deleteProduct:function(){
				alertify.confirm("Alerta!","¿Seguro que deseas borrar este producto?",()=>{
					this.$parent.inPetition=true;
					axios.delete(tools.url("/api/product/"+this.id))
					.then((response)=>{
						this.$parent.showMessage(response.data.msg,"success");
						this.$router.push("/products/");
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
				this.getProduct();
			}
			this.getTypes();

		}
	}
</script>
