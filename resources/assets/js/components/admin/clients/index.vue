<template>
	<div>
		<div class="row">
			<div class="col-md-12">
				<div id="toolbar">
			        <router-link to="/clients/edit">
			        	<button class="btn btn-success btn-sm">
				            <i class="fa fa-plus"></i> Nuevo
				        </button>
			        </router-link>

			    </div>
				<table id="clients"></table>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default {
		data(){
			return {
				clients:[],
				branches:[]
			}
		},
		methods:{
			mounthTable(){
				jQuery('#clients').bootstrapTable({
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

					    },
					    {
					        field: 'comertial_name',
					        title: 'Nombre comercial',
					        sortable:true,
							switchable:true,

					    },
					    {
					        field: 'branch_name',
					        title: 'Sucursal',
					        sortable:true,
							switchable:true,

					    },
                        {
					        field: 'email',
					        title: 'Email',
					        sortable:true,
							switchable:true,

					    },
                        {
					        field: 'celphone',
					        title: 'Telefono',
					        sortable:true,
							switchable:true,

					    },
                        {
					        field: 'state',
					        title: 'Estado',
					        sortable:true,
							switchable:true,

					    },
                        {
					        field: 'town',
					        title: 'Ciudad',
					        sortable:true,
							switchable:true,

					    },
                        {
					        field: 'status',
					        title: 'Estatus',
					        sortable:true,
							switchable:true,

					    },
                        {
					        field: 'postal_code',
					        title: 'CP',
					        sortable:true,
							switchable:true,

					    },
                        {
					        field: 'facturacion.postal_code',
					        title: 'CP Fiscal',
					        sortable:true,
							switchable:true,

					    },
						{
					        field: 'contact_celphone',
					        title: 'Origen del cliente',
					        sortable:true,
							switchable:true,

					    }
					],
					//Boton de refrescar x
					exportTypes: ['excel'],
					exportDataType: 'all',
					showRefresh:true,
					showExport: true,
					showToggle: true,
					pagination: true,
					search: true,
					exportOptions: {
						fileName: 'clientes'
					}
				});

				jQuery('#clients').on('refresh.bs.table',()=>{
					this.getClients();
				});

				jQuery('#clients').on('click-row.bs.table',(row,data)=>{
					this.$router.push('/clients/edit/'+data.id);
				});

				this.getClients();

			},
			getClients(){
				this.$parent.inPetition=true;
				axios.get(tools.url("/api/clients")).then((response)=>{
			    	this.clients=response.data;
			    	this.clients.forEach((v,k) => {
                        this.clients[k].state=v.state.name;
						this.clients[k].town=v.town.name;
						this.clients[k].branch_name=this.filterBranch(v.branch_id).name;
                    });
			    	jQuery('#clients').bootstrapTable('removeAll');
			    	jQuery('#clients').bootstrapTable('append',this.clients);
			    	this.$parent.inPetition=false;
			    }).catch((error)=>{
			        this.$parent.handleErrors(error);
			        this.$parent.inPetition=false;
			    });
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

			    	this.$parent.inPetition=false;
			    }).catch((error)=>{
			        this.$parent.handleErrors(error);
			        this.$parent.inPetition=false;
			    });
			},
			filterBranch:function(branch_id){

				let branch = " - ";

				this.branches.forEach(row=>{
					if(row.id==branch_id)
						branch=row;
				});

				return branch;
			}
		},
        mounted() {
			this.getBranches();
			setTimeout(()=>{
				this.mounthTable();
			}, 1000);
        }
    }
</script>
