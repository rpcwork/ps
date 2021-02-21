<template>
    <div class="container">
        <AutoCompleteDemo></AutoCompleteDemo>
    </div>
</template>

<script>
import AutoCompleteDemo from './components/AutoCompleteDemo'
export default {
    components: {
        AutoCompleteDemo
    }
}
</script>
<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
h2 {
  margin: 40px 0 0;
  font-family: Avenir, Helvetica, Arial, sans-serif;
}
</style>
