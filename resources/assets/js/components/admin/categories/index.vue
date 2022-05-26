<template>
	<div>
		<div class="row">
			<div class="col-md-12">
				<div id="toolbar">
			        <router-link to="/categories/edit">
			        	<button class="btn btn-success btn-sm">
				            <i class="fa fa-plus"></i> Nuevo
				        </button>
			        </router-link>
			        
			    </div>
				<table id="categories"></table>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
	export default {
		data(){
			return {
				categories:{},
			}
		},
		methods:{
			mounthTable(){
				jQuery('#categories').bootstrapTable({
					columns: [
						{
					        field: 'color',
					        title: 'Color',
					        sortable:false,
							switchable:false,
							
					    },
						{
					        field: 'name',
					        title: 'Nombre',
					        sortable:true,
							switchable:true,
							
					    },
                        {
					        field: 'types',
					        title: 'Diseños',
					        sortable:false,
							switchable:false,
							
					    },
                        {
					        field: 'subcategories',
					        title: 'Caractesticas',
					        sortable:false,
							switchable:false,
							
					    }
					],
					//Boton de refrescar
					showRefresh:true,
		                        		
				});

				jQuery('#categories').on('refresh.bs.table',()=>{
					this.getCategories();
				});

				jQuery('#categories').on('click-row.bs.table',(row,data)=>{
					this.$router.push('/categories/edit/'+data.id);
				});

				this.getCategories();

			},
			getCategories(){
				this.$parent.inPetition=true;
				axios.get(tools.url("/api/categories")).then((response)=>{
			    	this.categories=response.data;
			    	this.categories.forEach((v,k)=>{
			    		let types=jQuery.map(v.types,(row)=>{
                            return row.name;
                        });
                        types=types.join(',');
                        this.categories[k].types=types;
                        let subcategories=jQuery.map(v.subcategories,(row)=>{
                            return row.name;
                        });
                        subcategories=subcategories.join(',');
                        this.categories[k].subcategories=subcategories;
						this.categories[k].color='<div style="height:25px;width:25px;background-color:'+v.color+'" ></div>';
			    	});
			    	jQuery('#categories').bootstrapTable('removeAll');
			    	jQuery('#categories').bootstrapTable('append',this.categories);
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