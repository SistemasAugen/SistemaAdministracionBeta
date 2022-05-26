import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

//Componentes
//import Login from './components/admin/Login.vue';

const page="./components/page/";

const MyRouter = new VueRouter({
  	routes:[
	    { path: '/', component: require(page+'home.vue'), meta:{title:"Home"}},
	    { path: '/instalation', component: require(page+'instalation/index.vue'), meta:{title:"Instalacion"}},
	    { path: '/backend', component: require(page+'backend/index.vue'), meta:{title:"Backend"}},
	    { path: '/frontend', component: require(page+'frontend/index.vue'), meta:{title:"Frontend"}},
	    { path: '/tutorials', component: require(page+'tutorials/index.vue'), meta:{title:"Tutoriales"}},
	    { path: '/checkout', component: require(page+'checkout.vue'), meta:{title:"Checkout"}},
	  ]
});

MyRouter.beforeEach((to, from, next) => {
	window.scrollTo(0,0);
	if(this.a.app.$refs.loadingBar){
		this.a.app.$refs.loadingBar.start();
	}
	next();
});

MyRouter.afterEach((to, from) => {	

	if(this.a.app.$refs.loadingBar){
		setTimeout(()=>{
			this.a.app.$refs.loadingBar.done();
		},500);
	}

	
});

//Titulos del website
import VueDocumentTitlePlugin from "vue-document-title-plugin";
Vue.use(VueDocumentTitlePlugin, MyRouter, 
	{ defTitle: "Augen", filter: (title)=>{ return title+" - Augen"; } }
);
	  
// export {routes};
export default MyRouter;