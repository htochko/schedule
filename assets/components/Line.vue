<template>
  <div>
  <h3>{{ line.id }}: {{ line.name }}</h3>
    <table>
      <tr v-for="trip in trips">
        <td>{{ trip.systemName }} {{trip.header }} ({{trip.stopTimes.length}})</td>
        <td class="time" v-for="time in trip.stopTimes">
          <span>{{ time.departure_at.substring(11, 16) }}</span>
        </td>
      </tr>
    </table>
  </div>
</template>

<script>
export default {
  name: "theLine",
  props: {
    line: { required: true, type: Object },
  },
  data() {
    return {
      trips:[],
      day: 2,
    };
  },
  computed: {

  },
  methods: {
    getTrips(){
      console.log('loading trips', this.line);
      fetch(
          `/api/trips?pagination=false&line.id=${this.line.id}&day=${this.day}`
      ).then(res => {
        res.json().then(json => {
          this.trips = json['hydra:member'];
        });
      });
    },
    getTimeDiff(start, end) {

    }
  },
  setup() {

  },
  mounted() {
    this.getTrips();
  }
}
</script>

<style scoped>
  .time {
    width: 3em;
  }
</style>