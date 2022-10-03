<template>
  <div>
    <p v-if="departures[1]?.length > 0">Departures:</p>
    <div v-if="departures[1]?.length > 0" v-for="line in departures[1]">
      <h4>{{ line.line }}:</h4> {{ line.times }}
    </div>
    <p v-if="departures[1]?.length > 0">Arrivals:</p>
    <div v-if="departures[0]?.length > 0" v-for="line in departures[0]">
      <h4>{{ line.line }}: </h4>{{ line.times }}
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
