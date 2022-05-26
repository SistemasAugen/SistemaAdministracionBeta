<template>
	<div>
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div id="toolbar">
			        <router-link to="/types/edit">
			        	<button class="btn btn-success btn-sm">
				            <i class="fa fa-plus"></i> Nuevo
				        </button>
			        </router-link>
			        
			    </div>
				<table id="types"></table>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default {
		data(){
			return {
				types:{},
			}
		},
		methods:{
			mounthTable(){
				jQuery('#types').bootstrapTable({
					columns: [
						{
					        field: 'name',
					        title: 'Nombre',
					        sortable:true,
							switchable:true,
							
					    }, {
					        field: 'description',
					        title: 'Descripcion',
					        sortable:true,
							switchable:true,
							
					    }, {
					        field: 'categories',
					        title: 'Materiales',
					        sortable:false,
							switchable:false,
							
					    }
					],
					//Boton de refrescar
					showRefresh:true,
		                        		
				});

				jQuery('#types').on('refresh.bs.table',()=>{
					this.getTypes();
				});

				jQuery('#types').on('click-row.bs.table',(row,data)=>{
					this.$router.push('/types/edit/'+data.id);
				});

				this.getTypes();

			},
			getTypes(){
				this.$parent.inPetition=true;
				axios.get(tools.url("/api/types")).then((response)=>{
			    	this.types=response.data;
			    	this.types.forEach((v,k)=>{
                        this.types[k].categories=jQuery.map(this.types[k].categories,(row)=>{
                            return row.name;
                        });
                        this.types[k].categories=this.types[k].categories.join(',');
                    });
			    	jQuery('#types').bootstrapTable('removeAll');
			    	jQuery('#types').bootstrapTable('append',this.types);
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