import Vue from 'vue';
import Vuex from 'vuex';
import heroStatus from './modules/heroStatus'
import ornamentStatus from './modules/ornamentStatus'
Vue.use(Vuex);

export default new Vuex.Store({
    modules:{
        heroStatus,
        ornamentStatus
    }
});

// import Vue from 'vue';
// import Vuex from 'vuex';
// Vue.use(Vuex);
// const state = {
//     showNum: 12,
//     heroList:[],
// }
// const getters = {   //实时监听state值的变化(最新状态)
//     getShowNum(state) {  //方法名随意,主要是来承载变化的showFooter的值
//         return state.showNum
//     },
//     getHeroes(){  //方法名随意,主要是用来承载变化的changableNum的值
//         return state.heroList
//     }
// };
// const mutations = {
//     newNum(state,sum){ //同上，这里面的参数除了state之外还传了需要增加的值sum
//         state.showNum += sum;
//     },
//     changeList(state, list){
//         state.heroList = list;
//     }
// }
// const actions = {
//     updateNewNum(context,num){   //同上注释，num为要变化的形参
//         context.commit('newNum',num)
//     }
// };
// const store = new Vuex.Store({
//        state,
//        getters,
//        mutations,
//        actions
//     });
 
// export default store;