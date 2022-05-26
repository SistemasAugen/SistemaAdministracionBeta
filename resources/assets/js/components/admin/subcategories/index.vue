<template>
	<div>
		<div class="row">
			<div class="col-md-12">
				<div id="toolbar">
			        <router-link to="/subcategories/edit">
			        	<button class="btn btn-success btn-sm">
				            <i class="fa fa-plus"></i> Nuevo
				        </button>
			        </router-link>

			    </div>
				<table id="subcategories"></table>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default {
		data(){
			return {
				subcategories:{},
			}
		},
		methods:{
			mounthTable(){
				jQuery('#subcategories').bootstrapTable({
					columns: [
						{
					        field: 'name',
					        title: 'Nombre',
					        sortable:true,
							switchable:true,

					    }, {
					        field: 'description',
					        title: 'Descripcion',
					        sortable:false,
							switchable:false,

					    },
                        {
					        field: 'categories',
					        title: 'Material',
					        sortable:false,
							switchable:false,

					    },
                        {
					        field: 'types',
					        title: 'Diseños',
					        sortable:false,
							switchable:false,

					    },
                        {
					        field: 'antireflective',
					        title: 'Antireflejante',
					        sortable:false,
							switchable:false,

						},
											{
								field: 'no_antireflective',
								title: 'No Antireflejante',
								sortable:false,
						switchable:false,

						}
					],
					//Boton de refrescar
					showRefresh:true,

				});

				jQuery('#subcategories').on('refresh.bs.table',()=>{
					this.getSubcategories();
				});

				jQuery('#subcategories').on('click-row.bs.table',(row,data)=>{
					this.$router.push('/subcategories/edit/'+data.id);
				});

				this.getSubcategories();

			},
			getSubcategories(){
				this.$parent.inPetition=true;
				axios.get(tools.url("/api/subcategories")).then((response)=>{
			    	this.subcategories=response.data;
			    	this.subcategories.forEach((v,k)=>{
                        let categories=jQuery.map(v.categories,(row)=>{
                            return row.name;
                        });
                        categories=categories.join(',');
                        this.subcategories[k].categories=categories;
						let types=jQuery.map(v.types,(row)=>{
                            return row.name;
                        });
                        types=types.join(',');
                        this.subcategories[k].types=types;
			    	});
			    	jQuery('#subcategories').bootstrapTable('removeAll');
			    	jQuery('#subcategories').bootstrapTable('append',this.subcategories);
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
