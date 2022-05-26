<template>
	<div>
		<div class="row">
			<div class="col-md-12">
				<div id="toolbar">
			        <router-link to="/laboratories/edit">
			        	<button class="btn btn-success btn-sm">
				            <i class="fa fa-plus"></i> Nuevo
				        </button>
			        </router-link>
			        
			    </div>
				<table id="laboratories"></table>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default {
		data(){
			return {
				laboratories:{},
			}
		},
		methods:{
			mounthTable(){
				jQuery('#laboratories').bootstrapTable({
					columns: [
						{
					        field: 'name',
					        title: 'Nombre',
					        sortable:true,
							switchable:true,
							
					    }, {
					        field: 'state',
					        title: 'Estado',
					        sortable:true,
							switchable:true,
							
					    },
                        {
					        field: 'town',
					        title: 'Municipio',
					        sortable:true,
							switchable:true,
							
					    }, {
					        field: 'address',
					        title: 'Direccion',
					        sortable:true,
							switchable:true,
							
					    }
					],
					//Boton de refrescar
					showRefresh:true,
		                        		
				});

				jQuery('#laboratories').on('refresh.bs.table',()=>{
					this.getLaboratories();
				});

				jQuery('#laboratories').on('click-row.bs.table',(row,data)=>{
					this.$router.push('/laboratories/edit/'+data.id);
				});

				this.getLaboratories();

			},
			getLaboratories(){
				this.$parent.inPetition=true;
				axios.get(tools.url("/api/laboratories")).then((response)=>{
			    	this.laboratories=response.data;
			    	this.laboratories.forEach((v,k)=>{
                        this.laboratories[k].state=v.state.name;
                        this.laboratories[k].town=v.town.name;
			    	});
			    	jQuery('#laboratories').bootstrapTable('removeAll');
			    	jQuery('#laboratories').bootstrapTable('append',this.laboratories);
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