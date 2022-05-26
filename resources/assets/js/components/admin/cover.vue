<template>
    <div id="ejecutivo">
        <div v-if="isAdmin" class="horizontal-form" style="height: 50px;">
            <div class="form-group">
                <label class="control-label col-sm-2">Laboratorio:</label>
                <div class="col-sm-10">
                    <select class="form-control" v-model="selectedUser" @change="refreshData">
                        <option value="null" selected hidden disabled>SELECCIONA UN LABORATORIO</option>
                        <option :value="user.id" v-for="user in users" :key="user.id">{{ user.branch.name }}</option>
                    </select>
                </div>
            </div>
            <hr/>
        </div>
        <button type="button" name="button" class="btn btn-success" style="position: absolute;right: 25px;" @click="getClients()">Actualizar <i class="fas fa-sync"></i> </button>
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a :class="'nav-link ' + (selectedCategory == 'DOCTORES' ? 'active' : '')" href="#" @click.prevent="setInfo('DOCTORES')">DOCTORES</a>
            </li>
            <li class="nav-item">
                <a :class="'nav-link ' + (selectedCategory == 'CADENAS' ? 'active' : '')" href="#" @click.prevent="setInfo('CADENAS')">CADENAS</a>
            </li>
        </ul>
        <div class="row no-gutters">
            <h2 class="jumbotron">{{ selectedCategory }}</h2>
            <div class="col-sm-2">
                <table class="table table-customers table-header">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Nombre comercial</th>
                        </tr>
                    </thead>
                </table>
                <div class="table-container">
                    <table class="table table-customers table-body">
                        <tbody>
                            <tr v-for="(client, idx) in clients">
                                <td>{{ idx + 1 }}</td>
                                <td class="name" @click="openModal(client.id)">{{ client.comertial_name }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-sm-10">
                <div class="table-responsive">
                    <table id="table1" class="table table-rx">
                        <thead>
                            <tr>
                                <th colspan="7" v-for="(d, i) in dates" :key="i" style="border-right: 2px solid #ddd;">
                                    De <span>{{ d.monday }}</span> a <span>{{ d.saturday }}</span>
                                </th>
                            </tr>
                            <tr>
                                <th>Lu</th>
                                <th>Ma</th>
                                <th>Mi</th>
                                <th>Ju</th>
                                <th>Vi</th>
                                <th>Sá</th>
                                <th style="border-right: 2px solid #ddd;">TOTAL</th>
                                <th>Lu</th>
                                <th>Ma</th>
                                <th>Mi</th>
                                <th>Ju</th>
                                <th>Vi</th>
                                <th>Sá</th>
                                <th style="border-right: 2px solid #ddd;">TOTAL</th>
                                <th>Lu</th>
                                <th>Ma</th>
                                <th>Mi</th>
                                <th>Ju</th>
                                <th>Vi</th>
                                <th>Sá</th>
                                <th style="border-right: 2px solid #ddd;">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="client in clients">
                                <tr>
                                    <td v-for="(r, i) in client.rx" :class="{'text-danger': r == '0', 'border': [6, 13, 20].includes(i) }" @click="openModal(client.id, i)">{{ r }}</td>
                                </tr>
                                <tr>
                                    <td v-for="(r, i) in client.cash" :class="{'text-danger': r == '0', 'border': [6, 13, 20].includes(i) }" @click="openModal(client.id, i)">
                                        <span v-if="r != '-'" style="font-size: 11px;">{{ r | currency }}</span>
                                        <span v-else>-</span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div>
                    <br/>
                    <button class="btn btn-primary btn-lg" v-if="selectedCategory == 'DOCTORES' && canLoadMoreDoctors" @click.prevent="loadMoreDoctors">Cargar 15 más <i class="fas fa-info-circle"></i></button>
                    <button class="btn btn-primary btn-lg" v-if="selectedCategory == 'CADENAS' && canLoadMoreBranches" @click.prevent="loadMoreBranches">Cargar 15 más <i class="fas fa-info-circle"></i></button>
                </div>
            </div>
        </div>
        <div style="display:none;">
            <div id="modal_rx">
                <div class="row" v-if="selectedClient != null">
                    <table class="table table-client">
                        <tbody>
                            <tr>
                                <td>{{ selectedClient.name }}</td>
                                <td>{{ selectedClient.phone }}</td>
                                <td>MON: {{ selectedClient.monofocalDiscount + ' %' }}</td>
                                <td>PAQ: {{ selectedClient.packagesDiscount + ' %' }}</td>
                            </tr>
                            <tr>
                                <td>{{ selectedClient.comertial_name }}</td>
                                <td>{{ selectedClient.email }}</td>
                                <td>BI: {{ selectedClient.bifocalDiscount + ' %' }}</td>
                                <td>TER: {{ selectedClient.finishedDiscount + ' %' }}</td>
                            </tr>
                            <tr>
                                <td>{{ selectedClient.branch.name }}</td>
                                <td>PLAZO: {{ selectedClient.payment_plan }} SEMANAS</td>
                                <td>PRO: {{ selectedClient.progresiveDiscount + ' %' }}</td>
                                <td>
                                    <a :href="'/generate/account/' + selectedClient.id + '/client'" class="btn btn-success">ADEDUO <i class="fas fa-file-excel"></i></a>
                                    <button type="button" name="button" class="btn btn-danger" @click="makePdf()">PDF <i class="fas fa-file-pdf"></i> </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table responsive">
                            <thead>
                                <tr>
                                    <th>RX</th>
                                    <th>Cliente</th>
                                    <th>Fecha de Captura</th>
                                    <th>Fecha de Entrega</th>
                                    <th>Semanas restantes</th>
                                    <th>Diseño</th>
                                    <th>Material</th>
                                    <th>Caracteristica</th>
                                    <th>Antireflejante</th>
                                    <th>Plazo</th>
                                    <th>Subtotal</th>
                                    <th>Descuentos sistema</th>
                                    <th>Descuento</th>
                                    <th>Descuento Promoción</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(cart,i) in orders" :key="i">
                                    <td>{{ cart.rx }}</td>
                                    <td>{{ cart.client.name }}</td>
                                    <td>{{ cart.created_at }}</td>
                                    <td>{{ cart.delivered_date }}</td>
                                    <td>
                                        <p style="background-color:green;" v-if="cart.status=='pagado'">0</p>
                                        <p style="background-color:red;" v-else-if="cart.passed<0">{{ cart.passed }}</p>
                                        <p style="background-color:orange;" v-else>{{ cart.passed }}</p>
                                    </td>
                                    <td>{{ cart.product ? cart.product.name : 'NO DISPONIBLE'  }}</td>
                                    <td>{{ cart.product ? cart.product.subcategory_name : 'NO DISPONIBLE' }}</td>
                                    <td>{{ cart.product ? cart.product.type_name : 'NO DISPONIBLE' }}</td>
                                    <td v-if="cart.extras[0]">
                                        {{ cart.extras.map(row =>{return row.name}).join(", ") }}
                                    </td>
                                    <td v-else>
                                        -
                                    </td>
                                    <td>{{ cart.client.payment_plan }}</td>
                                    <td>{{ cart.total | currency }}</td>
                                    <td>{{ cart.discount | currency }}</td>
                                    <td>{{ cart.discount_admin | currency }}</td>
                                    <td>
                                        <span v-if="cart.promo_discount != null">$ {{ cart.promo_discount }}</span>
                                        <span v-else>N/A</span>
                                    </td>
                                    <td v-if="!cart.cancelled">{{ getTotal(cart.total,cart.discount,cart.discount_admin,cart.iva, cart.promo_discount) | currency }}</td>
                                    <td v-else>{{ 0 | currency }} </td>
                                    <td>
                                        <button class="btn btn-warning">{{ cart.status.replace("_"," ") }}</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import * as moment from "moment";
export default {
    name: "",
    data: () => ({
        clients: [],
        selectedClient: null,
        doctors: [],
        branches: [],
        dates:[],
        formatedDates:[],
        selectedCategory: 'DOCTORES',
        orders: [],
        data: null,
        // pagesIndicators
        pagesDoctorsLoaded: 0,
        pagesBranchesLoaded: 0,
        canLoadMoreDoctors: true,
        canLoadMoreBranches: true,
        windowPrompt: null,
        users: [],
        selectedUser: null
    }),
    computed: {
        isAdmin: function() {
            let roles = this.$parent.user.roles;
            for(let role of roles)
                if(role.name == 'Administrador Sr' || role.name == 'Administrador Jr')
                    return true;
            return false;
        }
    },
    methods: {
        getClients() {
            this.$parent.inPetition = true;
            this.clients = [];
            this.dates = [];

            axios.get('/api/clients/cover' + (this.selectedUser != null ? '/' + this.selectedUser : '')).then(result => {
                let clients = result.data.clients;

                this.doctors = result.data.doctors;
                this.clients = this.doctors;
                this.pagesDoctorsLoaded++;
                this.canLoadMoreDoctors = this.pagesDoctorsLoaded < result.data.maxPages;

                for(let d of result.data.dates)
                    this.dates.push({
                        monday: moment(d.Monday).locale('es').format('D/MMMM/YYYY'),
                        saturday: moment(d.Sunday).locale('es').format('D/MMMM/YYYY'),
                    });

                this.formatedDates = result.data.dates;

                this.$parent.inPetition = false;
                this.$parent.showMessage(result.data.msg,"success");
            }).catch((err)=>{
                this.$parent.inPetition = false;
                this.$parent.handleErrors(err);
            });
        },
        loadMoreDoctors() {
            this.$parent.inPetition = true;
            axios.get(`/api/clients/cover${ this.selectedUser != null ? '/' + this.selectedUser : '' }?p=${ this.pagesDoctorsLoaded }&c=DOCTORES`).then(result => {
                if(!Array.isArray(result.data.doctors))
                    result.data.doctors = [ result.data.doctors ];
                this.doctors = [...this.doctors, ...result.data.doctors];
                this.pagesDoctorsLoaded++;
                this.canLoadMoreDoctors = this.pagesDoctorsLoaded < result.data.maxPages;
                this.clients = this.doctors;

                this.$parent.inPetition = false;
                this.$parent.showMessage(result.data.msg,"success");
            }).catch((err)=>{
                this.$parent.inPetition = false;
                this.$parent.handleErrors(err);
            });
        },
        loadMoreBranches() {
            this.$parent.inPetition = true;
            axios.get(`/api/clients/cover${ this.selectedUser != null ? '/' + this.selectedUser : '' }?p=${ this.pagesBranchesLoaded }&c=CADENAS`).then(result => {
                if(!Array.isArray(result.data.branches))
                    result.data.branches = [ result.data.branches ];
                this.branches = [...this.branches, ...result.data.branches];
                this.pagesBranchesLoaded++;
                this.canLoadMoreBranches = this.pagesBranchesLoaded < result.data.maxPages;
                this.clients = this.branches;

                this.$parent.inPetition = false;
                this.$parent.showMessage(result.data.msg,"success");
            }).catch((err)=>{
                this.$parent.inPetition = false;
                this.$parent.handleErrors(err);
            });
        },
        setInfo(c) {
            this.selectedCategory = c;

            if(this.selectedCategory == 'DOCTORES')
                this.clients = this.doctors;
            else {
                if(this.pagesBranchesLoaded == 0)
                    this.loadMoreBranches();
                else
                    this.clients = this.branches;
            }

        },
        openModal(id, idx = null) {
            this.orders = [];
            this.selectedClient = null;
            this.$parent.inPetition = true;

            let data = {
                client_id: id,
                start_date: null,
                end_date: null,
            };

            this.selectedClient = this.clients.filter(c => c.id == id).shift();
            this.selectedClient.monofocalDiscount       = this.selectedClient.discounts.filter(d => d.type_id == 1).shift().discount;
            this.selectedClient.bifocalDiscount         = this.selectedClient.discounts.filter(d => d.type_id == 2).shift().discount;
            this.selectedClient.progresiveDiscount      = this.selectedClient.discounts.filter(d => d.type_id == 3).shift().discount;
            this.selectedClient.packagesDiscount        = this.selectedClient.discounts.filter(d => d.type_id == 5).shift().discount;
            this.selectedClient.finishedDiscount        = this.selectedClient.discounts.filter(d => d.type_id == 4).shift().discount;

            if(idx == null) {
                data.start_date = this.formatedDates[2].Monday;
                data.end_date   = this.formatedDates[0].Sunday;
            } else {
                let week = parseInt(idx / 7);
                idx = idx % 7;
                let startDate = moment(this.formatedDates[week].Monday).add('days', idx).format('YYYY-MM-DD');

                if(startDate == this.formatedDates[week].Sunday) {
                    // Then it must show the whole week
                    data.start_date = this.formatedDates[week].Monday;
                    data.end_date   = this.formatedDates[week].Sunday;
                } else {
                    data.start_date = startDate;
                    data.end_date   = startDate;
                    if(moment(startDate).isAfter(moment().format('YYYY-MM-DD'))) {
                        this.$parent.inPetition = false;
                        return;
                    }
                }

            }

            this.data = data;

            axios.post('/api/clients/cover', data).then(result => {
                this.orders = result.data.orders;

                alertify.rxDialog(document.getElementById('modal_rx'));
                this.$parent.inPetition = false;
            }).catch((err)=>{
                this.$parent.inPetition = false;
                this.$parent.handleErrors(err);
            });
        },
        getTotal:function(subtotal,dis,dis1,iva, promo = null){
            if(promo != null)
                return promo;
            iva = parseFloat(iva);
            return Math.abs(parseFloat((parseFloat(subtotal) - this.getDiscount(dis,dis1)) + iva).toFixed(2));
        },
        getDiscount:function(dis,dis1){
            return parseFloat(dis || 0) + parseFloat(dis1 || 0);
        },
        makePdf() {
            if(this.windowPrompt)
                this.windowPrompt.close();

            // var w = window.innerWidth;
            var w = 1013;
            var h = window.innerHeight;
            // var h = 1232;
            let urlParams = Object.keys(this.data).map(k => `${encodeURIComponent(k)}=${encodeURIComponent(this.data[k])}`).join('&');
            let url = '/generate/pdf?' + urlParams;
            let code = '<iframe src="' + url + '" width="100%" height="100%" scrolling="auto"></iframe>';

            this.windowPrompt = window.open("", "Augen Labs", `width=${ w },height=${ h }`);
            this.windowPrompt.document.open("application/pdf", "replace");
            this.windowPrompt.document.write(code);
        },
        getUsers() {
            this.$parent.inPetition = true;
            axios.get('/api/users/excecutive').then(result => {
                this.users = result.data.users;
                this.$parent.inPetition = false;
            }).catch((err)=>{
                this.$parent.inPetition = false;
                this.$parent.handleErrors(err);
            });
        },
        refreshData() {
            this.clients = [];
            this.selectedClient = null;
            this.doctors = [];
            this.branches = [];
            this.dates =[];
            this.formatedDates =[];
            this.selectedCategory = 'DOCTORES';
            this.orders = [];
            this.data = null;
            // pagesIndicators
            this.pagesDoctorsLoaded = 0;
            this.pagesBranchesLoaded = 0;
            this.canLoadMoreDoctors = true;
            this.canLoadMoreBranches = true;
            this.windowPrompt = null;

            this.getClients();
        }
    },
    mounted() {
        if(this.isAdmin) {
            this.getUsers();
        } else {
            this.getClients();
        }

        alertify.rxDialog || alertify.dialog('rxDialog',function(){
            return {
                main:function(content){
                    this.setContent(content);
                    this.resizeTo("80%","70%");
                },
                setup:function(){
                    return {
                        options:{
                            basic:true,
                            maximizable:false,
                            resizable:true
                        }
                    };
                },
                settings:{
                    selector:undefined
                }
            };
        });

        $('#table1').on('scroll', function() {
            $("#" + this.id + " > *").width($(this).width() + $(this).scrollLeft());
        });

        $('#table1 tbody').on('scroll', function() {
             $("div.table-container").scrollTop($(this).scrollTop());
        });

        $("div.table-container").on('scroll', function() {
             $('#table1 tbody').scrollTop($(this).scrollTop());
        });

        if(!$('.page-container').hasClass('sidebar-collapsed')) {
            $('.page-container').addClass('sidebar-collapsed');
        }
    }
}
</script>
<style lang="scss" scoped>
$mexican--red: #CE1126;
$cell-height: 39px;

$cell-width: 80px;

.no-gutters {
    margin-right: 0;
    margin-left: 0;

    > .col,
    > [class*="col-"] {
        padding-right: 0;
        padding-left: 0;
    }
}

.table-customers {
    background: $mexican--red;
    border-bottom: 2px solid #ddd;
    margin-bottom: 0px !important;

    thead {
        th {
            height: calc(#{ $cell-height } * 2);
            vertical-align: middle;
            text-transform: uppercase;
            font-weight: bold;
            color: #fff;
            border-left: 2px solid #ddd;
            border-right: 2px solid #ddd;
        }
    }

    tbody {
        td {
            height: calc(#{ $cell-height } * 2);
            vertical-align: middle;
            text-transform: uppercase;
            font-weight: bold;
            color: #fff;
            border-left: 2px solid #ddd;
            border-right: 2px solid #ddd;
            &.name {
                transition: all 0.5s;
                font-size: 12px;

                &:hover {
                    background: darken($mexican--red, 20%);
                    transform: scale(1,1);
                    -webkit-transform: scale(1,1);
                    -moz-transform: scale(1,1);
                    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                    -webkit-box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                    -moz-box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                    cursor: pointer;
                }
            }
        }
    }

}

.table-rx {
    border-bottom: 2px solid #ddd;
    margin-bottom: 0px !important;

    thead {
        background: $mexican--red;
        tr {
            &:first-child {
                span {
                    color: #80000F;
                    text-transform: uppercase;
                    letter-spacing: 3px;
                    margin: 0 3px;
                }
            }
            th {
                font-weight: bold;
                color: #fff;
                text-transform: uppercase;
            }
        }
    }
    th, td {
        text-align: center;
        min-width: 80px;
        color: #000;
        height: $cell-height !important;

        &.text-danger {
            font-weight: bold;
            color: #a94442 !important;
            cursor: not-allowed;
            pointer-events: none;
        }

        &.border {
            border-right: 2px solid #ddd;
        }
    }

    tbody{
        tr {
            transition: all 0.5s;
            &:hover {
                background: #F0F8FD;
                transform: scale(1,1);
                -webkit-transform: scale(1,1);
                -moz-transform: scale(1,1);
                box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                -webkit-box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                -moz-box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                cursor: pointer;
            }
        }
    }
}

.table-client {
    border: 3px solid #000;
    tr {
        td:not(:last-child) {
            border-right: 2px solid #ddd;
        }
    }
}

.table-container {
    height: calc(#{ $cell-height } * 2 * 10) !important;
    overflow: scroll;
}

#table1 {
    overflow-x: scroll;
    display: block;

    thead,
    tbody {
        display: block;
    }

    tbody {
        overflow-y: scroll;
        overflow-x: hidden;
        height: calc(#{ $cell-height } * 2 * 10) !important;
    }

    td,
    th {
      min-width: 80px;
      overflow: hidden;
      max-width: 80px;
    }
}

</style>
