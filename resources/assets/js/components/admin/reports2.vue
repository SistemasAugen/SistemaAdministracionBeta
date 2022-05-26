<template>
    <div id="reports">
        <div class="container-fluid">
            <h1>Dashboard Clients / Ventas</h1>

            <div v-if="isAdmin" class="horizontal-form">
                <div class="form-group row">
                    <label class="control-label col-sm-2">Laboratorio:</label>
                    <div class="col-sm-10">
                        <select class="form-control" v-model="selectedUser" @change="refreshData">
                            <option value="null" selected hidden disabled>SELECCIONA UN LABORATORIO</option>
                            <option :value="user.id" v-for="user in users" :key="user.id">{{ user.branch.name }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="horizontal-form" v-if="selectedUser != null">
                <div class="row filters">
                    <div class="col-md-6">
                        <p style="font-weight: bold;">Desde:</p>
                        <input type="date" class="form-control" v-model="filters.start">
                    </div>
                    <div class="col-md-6">
                        <p style="font-weight: bold;">Hasta:</p>
                        <input type="date" class="form-control" v-model="filters.end">
                    </div>
                    <div class="col-md-6">
                        <p style="font-weight: bold;">Material:</p>
                        <select class="form-control" @change.prevent="setTypes($event)" v-model="filters.category">
                            <option value="0" disable hidden>SELECCIONA UN MATERIAL</option>
                            <option v-for="category in categoryOptions" :value="category.id">{{ category.name }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <p style="font-weight: bold;">Diseño:</p>
                        <select class="form-control" v-model="filters.type">
                            <option value="0" disable hidden>SELECCIONA UN DISEÑO</option>
                            <option v-for="type in typeOptions" :value="type.id">{{ type.name }}</option>
                        </select>
                    </div>
                    <div class="col-md-6" style="margin-top: 15px; margin-bottom: 15px;">
                        <select class="form-control" v-model="filters.mode">
                            <option value="monthly">Mensual</option>
                            <option value="weekly">Semanal</option>
                            <option value="daily">Diario</option>
                        </select>
                    </div>
                    <div class="col-md-6" style="margin-top: 15px; margin-bottom: 15px;">
                        <button class="btn btn-block btn-success" @click.prevent="setRange()">Buscar</button>
                    </div>
                </div>
            </div>
            <div class="row" v-if="selectedUser != null">
                <div class="col-xs-9">
                    <div class="small" v-if="data != null">
                        <Chart :chart-data="data"></Chart>
                    </div>
                </div>
                <div class="col-xs-3 option-container">
                    <div class="row">
                        <button type="button" v-for="option in chartRange" :class="'btn btn-default btn-block ' + (selectedOptions.includes(option) ? 'selected' : '')" @click.prevent="reloadData(option)">{{ option }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Chart from './Chart.js';
import * as moment from "moment";
export default {
    name: "",
    components: {
        Chart
    },
    data: () => ({
        users: [],
        selectedUser: null,
        data: {},
        filters: {
            start: '',
            end: '',
            mode: 'monthly',
            category: 0,
            type: 0,
        },
        chartRange: [],
        selectedOptions: [],
        categories: [],
        types: [],

        categoryOptions: [],
        typeOptions: [],
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
        setRange() {
            this.$parent.inPetition = true;
            this.chartRange = [];
            this.selectedOptions = [];
            this.data = {
                labels: [],
                datasets: []
            };

            let startDate   = new Date(this.filters.start);
            let endDate     = new Date(this.filters.end);
            let today       = new Date();

            while(startDate <= endDate && startDate <= today) {
                let step = null;
                let format = null
                switch (this.filters.mode) {
                    case 'monthly': step = 'months'; format = 'MMMM YYYY'; break;
                    case 'weekly': step = 'weeks'; format = '[Semana] ww - YYYY'; break;
                    case 'daily': step = 'days'; format = 'DD/MM/YYYY'; break;
                }

                let date = moment(startDate);
                date.add(1, 'days');
                this.chartRange.push(date.format(format));
                date.add(1, step);

                startDate = new Date(date.format('YYYY-MM-DD'));
            }

            this.$parent.inPetition = false;
        },
        refreshData(e) {
            console.log(e);
        },
        fillData (data) {
            let labels = [];
            let datasets = [];
            let optionsCount = {};

            for(let i in data) {
                labels.push(i);
            }

            this.selectedOptions.forEach(opt => {
                if(!(opt in optionsCount))
                    optionsCount[opt] = [];

                for(let i in data) {
                    optionsCount[opt].push(0);

                    if(data[i][opt])
                        optionsCount[opt].push(parseInt(data[i][opt]));
                    else
                        optionsCount[opt].push(0);
                }

                datasets.push({
                    label: opt,
                    backgroundColor: this.getRandomColor(),
                    data: optionsCount[opt]
                });
            });

            this.data = { labels, datasets };
        },
        reloadData(date) {
            if(this.selectedOptions.includes(date))
                this.selectedOptions = this.selectedOptions.filter(d => d != date);
            else
                this.selectedOptions.push(date);

            this.makeSearch();
        },
        getCategories(){
            this.$parent.inPetition=true;
            axios.get(tools.url("/api/categories")).then((response)=>{
                this.categories=response.data;
                this.categoryOptions = this.categories;

                this.$parent.inPetition=false;
            }).catch((error)=>{
                this.$parent.handleErrors(error);
                this.$parent.inPetition=false;
            });
        },
        getTypes:function(){
            this.$parent.inPetition=true;
            axios.get(tools.url("/api/types"))
            .then((response)=>{
                this.types=response.data;
                this.$parent.inPetition=false;
            })
            .catch((error)=>{
                this.$parent.handleErrors(error);
                this.$parent.inPetition=false;
            });
        },
        setTypes($event) {
            this.$parent.inPetition=true;
            let categoryId = $event.target.value;
            let category = this.categories.filter(c => c.id == categoryId).shift();

            this.filters.type = 0;
            this.typeOptions = category.types;

            this.$parent.inPetition=false;
        },
        refreshData() {
            this.filters.category = 0;
            this.filters.type = 0;
        },
        makeSearch() {
            this.$parent.inPetition = true;

            let filters = this.filters;
            let options = this.selectedOptions;
            let user    = this.selectedUser;

            axios.post('/api/report_search', { filters, options, user }).then(result => {
                this.fillData(result.data.result);
                this.$parent.inPetition = false;
            });
        },
        getRandomColor() {
            var letters = '0123456789ABCDEF';
            var color = '#';
            for (var i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }
    },
    mounted() {
        let date = new Date();
            date.setTime(new Date().getTime() - (6 * 60 * 60 * 1000));
        this.filters.start = this.filters.end = date.toISOString().slice(0,10);

        if(this.isAdmin) {
            this.getUsers();
        }

        this.getCategories();
        this.getTypes();
        // this.fillData();
    }
}
</script>
<style lang="scss" scoped>
.small {
    max-width: 600px;
    margin:  0px auto;
}

.option-container {
    max-height: 600px;
    overflow-y: scroll;

    button {
        margin-bottom: 5px;
        &.selected {
            background: #555;
            color: #fff;
        }
    }
}
</style>
