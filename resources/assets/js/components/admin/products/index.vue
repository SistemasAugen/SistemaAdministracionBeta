<template>
	<div>
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div id="toolbar">
			        <router-link to="/products/edit">
			        	<button class="btn btn-success btn-sm">
				            <i class="fa fa-plus"></i> Nuevo
				        </button>
			        </router-link>
			        
			    </div>
				<table id="products"></table>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default {
		data(){
			return {
				products:{},
			}
		},
		methods:{
			mounthTable(){
				jQuery('#products').bootstrapTable({
					columns: [
                        {
					        field: 'id',
					        title: 'ID',
					        sortable:true,
							switchable:true,
							
					    },{
					        field: 'name',
					        title: 'Nombre',
					        sortable:true,
							switchable:true,
							
					    }
					],
					//Boton de refrescar
					showRefresh:true,
		                        		
				});

				jQuery('#products').on('refresh.bs.table',()=>{
					this.getProducts();
				});

				jQuery('#products').on('click-row.bs.table',(row,data)=>{
					this.$router.push('/products/edit/'+data.id);
				});

				this.getProducts();

			},
			getProducts(){
				this.$parent.inPetition=true;
				axios.get(tools.url("/api/products")).then((response)=>{
			    	this.products=response.data;
			    	
			    	jQuery('#products').bootstrapTable('removeAll');
			    	jQuery('#products').bootstrapTable('append',this.products);
			    	this.$parent.inPetition=false;
			    }).catch((error)=>{
			        this.$parent.handleErrors(error);
			        this.$parent.inPetition=false;
			    });
			},
			
		},
        mounted() {
            this.mounthTable();
        }
    }
</script>