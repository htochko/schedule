<template>
  <div>
    <h2 class="center">Schedule Application</h2>
    {{ message }}
    <ul>
      <li :key="word.id" v-for="word in words">{{ word }}</li>
    </ul>
  </div>
</template>

<script>
export default {
  name: 'App',
  data() {
    return {
      message: "list of stops",
      words: []
    };
  },
  mounted() {

    //let el = document.querySelector("div[data-words]");
    let mywords = ['T12', '0', '1'];//el.dataset.words.split(",");

    this.words.push.apply(this.words, mywords);
  }
};
</script>

<style scoped>

</style>