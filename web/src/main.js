// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import Vue from 'vue'
import App from './App'
import router from './router'
import Global from './components/Global.vue'
import Axios from 'axios'
import '../static/css/index.css'
import store from './store'//引入store

Vue.prototype.GLOBAL = Global;
Vue.prototype.$axios= Axios

Vue.use(ElementUI);
Vue.config.productionTip = false;

router.beforeEach((to, from, next) => {//beforeEach是router的钩子函数，在进入路由前执行
    if (to.meta.title) //判断是否有标题
        document.title = to.meta.title
    next()//执行进入路由，如果不写就不会进入目标页
})

/* eslint-disable no-new */
new Vue({
    el: '#app',
    router,
    store,
    components: { App },
    template: '<App/>',
    render: h => h(App)
})
