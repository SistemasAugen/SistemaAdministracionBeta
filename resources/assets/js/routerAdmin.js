import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

const page="./components/admin/";

const MyRouter = new VueRouter({
    mode: 'history',
    base: '/',
    fallback: true,
  	routes:[
	    { path: '/', redirect:"/login"},
	    { path: '/login', component: require(page+ 'login.vue'), meta:{title:"Login"}},
	    { path: '/home', component: require(page+ 'home.vue'), meta:{title:"Home"}},
	    { path: '/cover', component: require(page+ 'cover.vue'), meta:{title:"Carátula Ejecutiva"}},
	    { path: '/asistencia', component: require(page+ 'asistencia.vue'), meta:{title:"Firma de asistencia"}},
	    { path: '/profile', component: require(page+'me.vue'), meta:{title:"Mi perfil"}},
	    //Usuarios
	    { path: '/users', component: require(page+'users/index.vue'), meta:{title:"Usuarios"}},
	    { path: '/users/edit', component: require(page+'users/edit.vue'), meta:{title:"Nuevo"}},//Cuando no envian parametro
	    { path: '/users/edit/:id', component: require(page+'users/edit.vue'), meta:{title:"Editar"}},//Con parametro
	    //Roles
	    { path: '/roles', component: require(page+'configuration/roles.vue'), meta:{title:"Roles"}},
		{ path: '/roles/edit/:id', component: require(page+'configuration/permissions.vue'), meta:{title:"Editar"}},
		//Rutas de administrador
		{ path: '/branches', component: require(page+'branches/index.vue'), meta:{title:"Sucursales"}},
		{ path: '/branches/edit', component: require(page+'branches/edit.vue'), meta:{title:"Nuevo"}},
		{ path: '/branches/edit/:id', component: require(page+'branches/edit.vue'), meta:{title:"Editar"}},
		{ path: '/laboratories', component: require(page+'laboratories/index.vue'), meta:{title:"Laboratorios"}},
		{ path: '/laboratories/edit', component: require(page+'laboratories/edit.vue'), meta:{title:"Nuevo"}},
		{ path: '/laboratories/edit/:id', component: require(page+'laboratories/edit.vue'), meta:{title:"Editar"}},
		{ path: '/types', component: require(page+'types/index.vue'), meta:{title:"Diseños"}},
		{ path: '/types/edit', component: require(page+'types/edit.vue'), meta:{title:"Nuevo"}},
		{ path: '/types/edit/:id', component: require(page+'types/edit.vue'), meta:{title:"Editar"}},
		{ path: '/categories', component: require(page+'categories/index.vue'), meta:{title:"Material"}},
		{ path: '/categories/edit', component: require(page+'categories/edit.vue'), meta:{title:"Nuevo"}},
		{ path: '/categories/edit/:id', component: require(page+'categories/edit.vue'), meta:{title:"Editar"}},
		{ path: '/subcategories', component: require(page+'subcategories/index.vue'), meta:{title:"Caracteristicas"}},
		{ path: '/subcategories/edit', component: require(page+'subcategories/edit.vue'), meta:{title:"Nuevo"}},
		{ path: '/subcategories/edit/:id', component: require(page+'subcategories/edit.vue'), meta:{title:"Editar"}},
		{ path: '/products', component: require(page+'products/index.vue'), meta:{title:"Precios"}},
		{ path: '/products/edit', component: require(page+'products/edit.vue'), meta:{title:"Nuevo"}},
		{ path: '/products/edit/:id', component: require(page+'products/edit.vue'), meta:{title:"Editar"}},
		{ path: '/clients', component: require(page+'clients/index.vue'), meta:{title:"Clientes"}},
		{ path: '/clients/edit', component: require(page+'clients/edit.vue'), meta:{title:"Nuevo"}},
		{ path: '/clients/edit/:id', component: require(page+'clients/edit.vue'), meta:{title:"Editar"}},
		{ path: '/clientes-nuevos', component: require(page+'clients/news.vue'), meta:{title:"Clientes nuevos"}},
		{ path: '/extras', component: require(page+'extras/index.vue'), meta:{title:"Antireflejantes"}},
		{ path: '/extras/edit', component: require(page+'extras/edit.vue'), meta:{title:"Nuevo"}},
		{ path: '/extras/edit/:id', component: require(page+'extras/edit.vue'), meta:{title:"Editar"}},
		{ path: '/orders/new', component: require(page+'orders/new.vue'), meta:{title:"Nuevo pedido"}},
		{ path: '/search', component: require(page+'search.vue'), meta:{title:"Buscador"}},
		{ path: '/catalog', component: require(page+'orders/catalog.vue'), meta:{title:"Catalogo"}},
		{ path: '/sales', component: require(page+'orders/sales.vue'), meta:{title:"Ventas"}},
		{ path: '/orders/pending', component: require(page+'orders/pendings.vue'), meta:{title:"Pendientes"}},
		{ path: '/orders', component: require(page+'orders/all_pendings.vue'), meta:{title:"Pendientes"}},
		{ path: '/orders/payments', component: require(page+'orders/payments.vue'), meta:{title:"Cobranza"}},
		{ path: '/reports', component: require(page+'reports2.vue'), meta:{title:"Reportes"}},
        { path: '/status', component: require(page+'status.vue'), meta:{title:"Status"}},

        //  Reportes -> Cobranza

        { path: '/orders/estado-cuenta', component: require(page+'orders/account.vue'), meta:{title:"Estado de cuenta"}},
        { path: '/orders/ingresos', component: require(page+'orders/income.vue'), meta:{title:"Ingresos"}},
        { path: '/orders/deudores', component: require(page+'orders/debtors.vue'), meta:{title:"Deudores"}},

        { path: '/promociones', component: require(page+'promo.vue'), meta:{title:"Promociones"}},
	  ]
});

//Titulos del website
import VueDocumentTitlePlugin from "vue-document-title-plugin";
Vue.use(VueDocumentTitlePlugin, MyRouter,
	{ defTitle: "Augen", filter: (title)=>{ return title+" - Augen"; } }
);

// export {routes};
export default MyRouter;
