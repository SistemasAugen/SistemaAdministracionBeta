<template>
    <div>
        <div id="types_table" v-if="isSelectingType">
            <div class="row">
                <div class="col-md-12">
                    <h3>Elige un diseño:</h3>
                </div>
                <div class="col-md-6 col-md-offset-3" v-for="type in types" :key="type.id">
                    <!-- <br> -->
                    <!-- <br> -->
                    <button class="btn btn-default btn-block" @click="selectProduct(type)" v-if="type.canSee">{{ type.name }}</button>
                </div>
            </div>
        </div>
        <div id="products_table" v-else>
            <div class="row">
                <div class="col-md-12">
                    <h3 style="display:inline-block">Versiones:</h3>
                    <button class="btn pull-right" @click="isSelectingType=true">Atras</button>
                </div>
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th rowspan="2" width="15%" style="color:black;">{{ type.name }}</th>
                                <th v-for="category in categories" :key="category.id" :colspan="category.subcategories.length" :style="{color:'black', backgroundColor:category.color}">{{ category.name }}</th>
                            </tr>
                            <tr>
                                <template v-for="category in categories">
                                    <th v-for="subcategory in category.subcategories" :key="subcategory.id+category.name" :style="{color:'black', backgroundColor:category.color}" :title="subcategory.description">
                                        {{ subcategory.name }}
                                    </th>
                                </template>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(product, i) in products" :key="product.id + i">
                                <td>{{ product.name }}</td>
                                <template v-for="category in categories">
                                    <td v-for="subcategory in category.subcategories" :key="subcategory.id+category.name" :title="subcategory.description">
                                        <button class="btn btn-sm btn-block" v-if="filterProduct(product,category.id,subcategory.id).price!=0" @click="addProduct(filterProduct(product,category.id,subcategory.id))">${{ filterProduct(product,category.id,subcategory.id).price }}</button>
                                        <p v-else> - </p>
                                    </td>
                                </template>

                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</template>
<script>
export default {
    data(){
        return {
            types:[],
            subcategories:[],
            categories:[],
            products:[],
            type:{},
            isSelectingType:true,
        }
    },
    methods:{
        getTypes:function(){
            this.$parent.inPetition=true;
            axios.get(tools.url("/api/types"))
            .then((response)=>{
                let types=response.data;
                const CHAPULTEPEC_LAB = 46;
                let isPDV = false;
                for(let role of this.$parent.user.roles)
                    if(role.name == 'punto de ventas') {
                        isPDV = true;
                        break;
                    }

                for(let i = 0; i < types.length; i++) {
                    if(!isPDV) {
                        types[i].canSee = true;
                        continue;
                    }
                    if([25, 34, 46].includes(this.$parent.user.id)) {
                        types[i].canSee = true;
                    } else {
                        if(types[i].id == 10)
                            types[i].canSee = false;
                        else
                            types[i].canSee = true;
                    }
                }

                this.types = types;
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

                    sub.categories.forEach((cat,k)=> {

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
                    v.subcategories.sort((a,b)=>{
                        return a.id-b.id;
                    });

                    this.categories.push(v);
                });

                this.categories.sort((a,b)=>{
                    return a.id-b.id;
                });

                this.$parent.inPetition=false;
            })
            .catch((error)=>{
                this.$parent.handleErrors(error);
                this.$parent.inPetition=false;
            });
        },
        getProducts:function(type_id){
            axios.get(tools.url("/api/product/type/"+type_id)).then((response)=>{
                this.products=response.data;
                this.products.forEach((v,k)=>{
                    this.products[k].prices.sort((a,b)=>{
                        return a.id-b.id;
                    });
                });

                for(let category of this.categories) {
                    var category_id = category.id;

                    category.subcategories.forEach((subcategory, idx) => {
                        let isAllInZero = true;

                        for(let product of this.products) {
                            let data = this.filterProduct(product, category_id, subcategory.id);
                            let finalPrice = parseFloat(data.price);
                            // console.log(finalPrice > 0);
                            if(finalPrice > 0) {
                                isAllInZero = false;
                                break;
                            }
                        }

                        if(isAllInZero) {
                            // console.log(category.subcategories.splice(idx, 1));
                            category.subcategories.splice(idx, 1);
                        }
                    });
                }

                this.categories.sort((a, b) => {
                    if(a.id == 8)
                        return 1;
                    if(b.id == 8)
                        return -1;
                    return 0;
                })
            });
        },
        selectType:function(){
            alertify.prompt( 'Augen', 'Escribe el RX.', ''
               , (evt, value) => {
                    this.rx=value;
                    // alertify.typesDialog(document.getElementById('types_table'));
               }, function() {}).set('labels', {ok:'Aceptar', cancel:'Cancelar'});

        },
        selectProduct:function(type){
            this.getSubcategories(type.id);
            this.getProducts(type.id);
            this.type=type;
            this.isSelectingType=false;
            // alertify.productsDialog(document.getElementById('products_table'));
        },
        filterProduct:function(product,category_id,subcategory_id){
            let data = product.prices.find((r)=>{
                if(r.category_id==category_id && r.subcategory_id==subcategory_id){
                    return true;
                }
            });
            if(data)
                return data;
            else
                return { price: 0 }
        },
        addProduct:function(price){
            alertify.prompt( 'Augen', 'Escribe el RX.', ''
               , (evt, value) => {

                    let product=this.products.find((r)=>{
                    if(r.id==price.product_id)
                        return true;
                    });
                    let order={
                            product_has_subcategory_id:price.id,
                            price:price.price,
                            discount:0,
                            total:price.price,
                            service:0,
                            extras:[],
                            product:product,
                            rx:value,
                        };

                    alertify.closeAll();
                    this.$parent.sale.cart.push(order);
                    this.$parent.showMessage("Añadido al pedido.");
                    // alertify.typesDialog(document.getElementById('types_table'));
               }, function() {}).set('labels', {ok:'Aceptar', cancel:'Cancelar'});


        },
    },
    mounted(){
        this.getTypes();
    }
}
</script>
<style media="screen">
div#types_table button {
    margin-top: 35px;
}
</style>
