<template>
	<main role="main">
	<div :class="{'logging-in login-form-fall':inPetition,'page-body login-page':2>1}">
	<div class="login-container">

		<div class="login-header login-caret">

			<div class="login-content">

				<a class="logo">
					<img :src="logo()" width="300" alt="" />
				</a>
				<p class="description">Usuario, ingresa para entrar al area de administracion!</p>

				<!-- progress bar indicator -->
				<div class="login-progressbar-indicator">
					<h3>{{ percent }}%</h3>
					<span>Ingresando...</span>
				</div>
			</div>

		</div>

		<div class="login-progressbar">
			<div :style="'width:'+percent+'%'"></div>
		</div>

		<div class="login-form">

			<div class="login-content">

				<div class="form-login-error" style="display:block;" v-show="error">
					<h3>Error</h3>
					<p>{{error}}</p>
				</div>

				<form method="post" @submit.prevent="login">

					<div class="form-group">

						<div class="input-group">
							<div class="input-group-addon">
								<i class="entypo-user"></i>
							</div>

							<input type="text" class="form-control" placeholder="Email" v-model="credentials.email">
						</div>

					</div>

					<div class="form-group">

						<div class="input-group">
							<div class="input-group-addon">
								<i class="entypo-key"></i>
							</div>

							<input type="password" class="form-control" placeholder="Password" v-model="credentials.password">
						</div>

					</div>

					<div class="form-group">
						<button type="submit" class="btn btn-primary btn-block btn-login">
							<i class="entypo-loginentypo-login"></i>
							Ingresar
						</button>
					</div>

				</form>


				<div class="login-bottom-links">

					<!-- <a class="link">Olvide mi contraseña</a> -->


				</div>

			</div>

		</div>

	</div>
	</div>
	</main>
</template>

<script type="text/javascript">
import * as moment from "moment";
export default {
    mounted() {
        console.log('Login mounted.');
    },
    methods:{
    	login(){
    		this.percent = 50;
    		this.inPetition = true;
    		axios.post(tools.url("/api/login"),this.credentials).then((response)=>{
		    	this.$parent.user = response.data.user;
		    	this.$parent.token = response.data.token;
		    	localStorage.setItem("token",this.$parent.token);
		    	this.$parent.logged = true;

				let isExecutive = false;
				for(let role of this.$parent.user.roles) {
					if(role.name == "Ejecutivo") {
						isExecutive = true;
						break;
					}
				}
				console.log(this.$route.path);
		    	this.$parent.showMessage("Bienvenido "+this.$parent.user.name);
		    	if(this.$route.path == "/login") {
					if(isExecutive) {
						axios.post(tools.url('/api/login_log'), {
								time_zone: this.getTimeZone(),
								login_date: moment().format('YYYY-MM-DD HH:mm:ss')
							}, {
							headers: {
								'Authorization': this.$parent.token
							}
						}).then(r => {
							this.$router.push('/cover');
						}).catch((error)=> {
							alert(moment().format('YYYY-MM-DD HH:mm:ss'));
					        this.error=error.response.data.error;
					        this.inPetition=false;
					    });
					}
					else
						this.$router.push('/home');
		    	}
		        this.inPetition = false;
		        this.error = false;
		    }).catch((error)=>{
		        this.error=error.response.data.error;
		        this.inPetition=false;
		    });
    	},
		logo() {
			return window.tools.url('public/images/logo.png');
		},
		getTimeZone() {
			var offset = new Date().getTimezoneOffset(), o = Math.abs(offset);
			return (offset < 0 ? "+" : "-") + ("00" + Math.floor(o / 60)).slice(-2) + ":" + ("00" + (o % 60)).slice(-2);
		}
    },
    data:function(){
    	return {
    		inPetition:false,
    		percent:0,
    		error:false,
    		credentials:{
    			email:"",
    			password:""
    		}
    	}
    }
}
</script>
<style>
.login-form{
	background-color: whitesmoke;
}
.login-header{
	background-color:silver !important;
}
.btn-primary{
	background-color: silver !important;
	color:black;
}
.login-page .login-form .form-group .input-group{
	background: whitesmoke;
}
.login-page .login-form .form-group .input-group .form-control{
	color:black;
}
</style>
