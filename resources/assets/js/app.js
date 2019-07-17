//require('./bootstrap');

window.Vue = require('vue');

import App from './app.vue'
import store from './store'
import i18n from './lang'
import {router} from './router'
import iView from 'iview'
import 'iview/dist/styles/iview.css'

Vue.use(iView);

Vue.prototype.$Message.config({
  duration: 5
});

let _hmt = _hmt || [];
window._hmt = _hmt;
(function () {
  let hm = document.createElement("script");
  hm.src = "https://hm.baidu.com/hm.js?3868fd88eff3a2efc2c0746310ad9b7c";
  let s = document.getElementsByTagName("script")[0];
  s.parentNode.insertBefore(hm, s);
})();

const app = new Vue({
  el: '#app',
  i18n,
  store,
  router,
  render: h => h(App)
});
