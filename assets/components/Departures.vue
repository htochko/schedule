<template>
  <div>
    <h5 v-if="departures[1]?.length > 0">Departures:</h5>
    <div v-if="departures[1]?.length > 0" v-for="line in departures[1]">
    <h6>{{ line.line }}:</h6> {{ line.times }}
    </div>
    <h5 v-if="departures[1]?.length > 0">Arrivals:</h5>
    <div v-if="departures[0]?.length > 0" v-for="line in departures[0]">
    <h6>{{ line.line }}: </h6>{{ line.times }}
    </div>
  </div>
</template>

<script>
export default {
  name: "Departures",
  props: {
    departures: { required: true, type: Array },
  }
}
</script>

<style>
h4 {
  font-size: 1.4em;
}
h5 {
  font-size: 1.2em;
}
h6 {
  font-size: 1em;
}
</style>