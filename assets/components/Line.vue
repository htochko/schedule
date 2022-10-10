<template>
  <div>
  <h3>{{ line }}:</h3>
  </div>
</template>

<script>
export default {
  name: "Line",
  props: {
    line: { required: true, type: Number },
  },
  data() {
    return {
      trips:[],
      day: 6,
    };
  },
  computed: {

  },
  methods: {
    getTrips(){
      console.log('loading trips', this.line);
      fetch(
          `/api/trips?pagination=false&line.id=${this.line}&day=${this.day}`
      ).then(res => {
        res.json().then(json => {
          this.lines = json['hydra:member'];
        });
      });
    }
  },
  mounted() {
    this.getTrips();
  }
}
</script>

<style scoped>

</style>