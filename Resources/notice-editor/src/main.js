import axios from 'axios';
import Vue from 'vue';
import BootstrapVue from 'bootstrap-vue';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import App from './App';

Vue.use(BootstrapVue);
Vue.config.productionTip = false;

axios.defaults.baseURL = document.querySelector('base').dataset.apiHref;

/* eslint-disable no-new */
new Vue({
  el: '#app',
  template: '<App/>',
  components: { App },
});
