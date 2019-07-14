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
  components:{
    'Search' : Search,
    'Footer' : Footer
  }
}
</script>