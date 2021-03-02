require('./bootstrap');
require('jquery.cookie');

window.Vue = require('vue');

export const eventBus = new Vue();

import Vuex from 'vuex';
import VueRouter from 'vue-router'

import {state} from './vuex/state.js'
import {getters} from './vuex/getters.js'
import {mutations} from './vuex/mutations.js'
import {actions} from './vuex/actions.js'
//

Vue.use(VueRouter);
Vue.use(Vuex);

Vue.component('filter-index', require('./components/Filter/Index.vue').default);
Vue.component('catalog-index', require('./components/Catalog/Index.vue').default);

const store = new Vuex.Store({
    state,
    mutations,
    getters,
    actions
});

const app = new Vue({
    el: '#app',
    data: ()=>{
        return {
            config: {}
        }
    },
    store,
    beforeMount() {
        let token;
        let arr = document.cookie.split(';').forEach(e => {
           if(e.includes('pc-constructor')) {
               token = JSON.parse(decodeURIComponent(e.split('=')[1])).token_config;
           }
        });

        this.$store.commit("add_token_config", token);
    },
});

require('./main.js');
