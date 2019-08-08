const state = {
    avatar_type: 0, // 用户选择的那啥 0 Q版 1 原版
};
const getters={
    getAvatarType(state){ //承载变化的collects
        return state.avatar_type;
    },
}; 
const mutations={
    updateType(state, val){
        state.avatar_type = val;
    },
};
const actions={
    updateAvatarType(context, val){ //触发mutations里面的pushCollects ,传入数据形参item 对应到items
        context.commit('updateType', val);
    },
};
export default {
    namespaced:true,//用于在全局引用此文件里的方法时标识这一个的文件名
    state,
    getters,
    mutations,
    actions
}