<template>
	<div>
		<div class="row">
			<div class="col-md-12">
				<div id="toolbar">
			        <router-link to="/extras/edit">
			        	<button class="btn btn-success btn-sm">
				            <i class="fa fa-plus"></i> Nuevo
				        </button>
			        </router-link>
			        
			    </div>
				<table id="extras"></table>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default {
		data(){
			return {
				extras:{},
			}
		},
		methods:{
			mounthTable(){
				jQuery('#extras').bootstrapTable({
					columns: [
						{
					        field: 'name',
					        title: 'Nombre',
					        sortable:true,
							switchable:true,
							
					    },
						{
					        field: 'description',
					        title: 'Descripcion',
					        sortable:true,
							switchable:true,
							
					    },
                        {
					        field: 'price',
					        title: 'Precio',
					        sortable:true,
							switchable:true,
							
					    },
                        {
					        field: 'products',
					        title: 'Productos',
					        sortable:false,
							switchable:false,
							
					    }
					],
					//Boton de refrescar
					showRefresh:true,
		                        		
				});

				jQuery('#extras').on('refresh.bs.table',()=>{
					this.getExtras();
				});

				jQuery('#extras').on('click-row.bs.table',(row,data)=>{
					this.$router.push('/extras/edit/'+data.id);
				});

				this.getExtras();

			},
			getExtras(){
				this.$parent.inPetition=true;
				axios.get(tools.url("/api/extras")).then((response)=>{
			    	this.extras=response.data;
			    	this.extras.forEach((v,k)=>{
			    		let products=jQuery.map(v.products,(row)=>{
                            return row.name;
                        });
                        products=products.join(',');
                        this.extras[k].products=products;
			    	});
			    	jQuery('#extras').bootstrapTable('removeAll');
			    	jQuery('#extras').bootstrapTable('append',this.extras);
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