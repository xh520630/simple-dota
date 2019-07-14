const state = {
    testNum : 233,
}
const getters = {
    getNum(state){
        return state.testNum;
    }
}
const mutations = {
    changeNum(state, num){
        state.testNum += num;
    }
}
const actions = {
    updateNum(context, num){
        context.commit('changeNum', num);
    }
}
export default {
    namespaced: true, //用于在全局引用此文里的方法时标识这一个的文件名
    state,
    getters,
    mutations,
    actions
}