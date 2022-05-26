<template>
	<div>
		<div class="row">
			<div class="col-md-12">
				<div id="toolbar">
			        <router-link to="/branches/edit">
			        	<button class="btn btn-success btn-sm">
				            <i class="fa fa-plus"></i> Nuevo
				        </button>
			        </router-link>
			        
			    </div>
				<table id="branches"></table>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default {
		data(){
			return {
				branches:{},
			}
		},
		methods:{
			mounthTable(){
				jQuery('#branches').bootstrapTable({
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
							
					    },
					    {
					    	field: 'laboratory',
					    	title: 'Laboratorio',
					    }
					],
					//Boton de refrescar
					showRefresh:true,
		                        		
				});

				jQuery('#branches').on('refresh.bs.table',()=>{
					this.getBranches();
				});

				jQuery('#branches').on('click-row.bs.table',(row,data)=>{
					this.$router.push('/branches/edit/'+data.id);
				});

				this.getBranches();

			},
			getBranches(){
				this.$parent.inPetition=true;
				axios.get(tools.url("/api/branches")).then((response)=>{
			    	this.branches=response.data;
			    	this.branches.forEach((v,k)=>{
			    		this.branches[k].laboratory=v.laboratory.name;
                        this.branches[k].state=v.state.name;
                        this.branches[k].town=v.town.name;
			    	});
			    	jQuery('#branches').bootstrapTable('removeAll');
			    	jQuery('#branches').bootstrapTable('append',this.branches);
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