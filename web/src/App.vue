<template>
    <div id="app">
      <Search></Search>
      <router-view/>
      <Footer></Footer>
    </div>
</template>

<script>
import Search from './components/Search';
import Footer from './components/Footer';

export default {
  name: 'App',
  data() {
    return {
      confirmText: ['目前手机端适配较差，小程序正在开发中。', '是否继续浏览？'],
    }
  },
  components:{
    'Search' : Search,
    'Footer' : Footer
  },
  methods:{
    _isMobile() {
      let flag = navigator.userAgent.match(/(phone|pod|iPhone|ios|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i)
      return flag;
    }, // 检测是否手机端
    checkBrowse() {
      const newDatas = [] 
      const h = this.$createElement
      for (const i in this.confirmText) { 
          newDatas.push(h('p', null, this.confirmText[i])) 
      } 
      this.$confirm(h('div', null, newDatas), '提示', {
        confirmButtonText: '算了算了，溜了溜了。',
        cancelButtonText: '朕知道了，无妨。',
        type: 'warning',
        center: true
      }).then(() => {
        history.go(-1);
      }).catch(() => {
      });
    }, // 手机端弹框
  },
  created(){
    if (this._isMobile()) 
      this.checkBrowse();
  }
}
</script>