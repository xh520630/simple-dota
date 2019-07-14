const state = {
    strength:[],
    intelligent:[],
    agile:[]
};
const getters={
    getStrength(state){ //承载变化的collects
        return state.strength;
    },
    getIntelligent(state){ //承载变化的collects
        return state.intelligent;
    },
    getAgile(state){ //承载变化的collects
        return state.agile;
    },
}; 
const mutations={
    updateStrength(state, arr){
        state.strength = arr;
    },
    updateIntelligent(state, arr){
        state.intelligent = arr;
    },
    updateAgile(state, arr){
        state.agile = arr;
    },
};
const actions={
   updateStrength(context, arr){ //触发mutations里面的pushCollects ,传入数据形参item 对应到items
        context.commit('updateStrength',arr);
   },
   updateIntelligent(context, arr){ //触发mutations里面的pushCollects ,传入数据形参item 对应到items
        context.commit('updateIntelligent',arr);
   },
   updateAgile(context, arr){ //触发mutations里面的pushCollects ,传入数据形参item 对应到items
        context.commit('updateAgile',arr);
   },
};
export default {
    namespaced:true,//用于在全局引用此文件里的方法时标识这一个的文件名
    state,
    getters,
    mutations,
    actions
}