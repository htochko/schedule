<template>
  <div>
    <h2 class="center">Schedule Application</h2>
    <div class='main-wrapper'>
      <div class='row'>
        <div class='column'>
          <h4>{{ message }}</h4>
          <v-select label="name"
                    :filterable="false"
                    :options="options"
                    @search="onSearch"
                    :reduce="name => name.name"
                    @input="setSelected"
          >
            <template slot="no-options">
              type to search stops..
            </template>
            <template slot="option" slot-scope="option">
              <div class="d-center">
                {{ option.name }}
              </div>
            </template>
            <template slot="selected-option" slot-scope="option">
              <div class="selected d-center">
                {{ option.name }}
              </div>
            </template>
          </v-select>
        </div>
        <div class='column'>
          <h4 v-text="stop?.name">Nearest trips:</h4>
          <v-departures :departures="departures"></v-departures>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import _ from 'lodash';
import Vue from 'vue';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import Departures from "./Departures";
Vue.component('v-departures', Departures);
Vue.component('v-select', vSelect);

export default {
  name: 'App',
  data() {
    return {
      message: "List of stops",
      options: [],
      stopName: null,
      stop: {},
      trips:[],
      departures:[[],[]]
    };
  },
  methods: {
    onSearch(search, loading) {
      if(search.length) {
        loading(true);
        this.search(loading, search, this);
      }
    },
    getDepartures(trips) {
      const departures = [[], []];
      trips.forEach((trip) => {
        const lineName = `${trip.trip.line.name} ${trip.trip.header} `;
        if (!(lineName in departures[+trip.trip.direction])) {
          departures[+trip.trip.direction][lineName] = [];
        }
        departures[+trip.trip.direction][lineName].push(trip.departuresIn);
      });
      const objectDepartures = Object.keys(departures[1]).map(function(line) {
        return {line: line, times: departures[1][line].join(', ')};
      });
      const objectArrivals = Object.keys(departures[0]).map(function(line) {
        return {line: line, times: departures[0][line].join(', ')};
      });
      return [objectArrivals, objectDepartures];
    },
    setSelected(value) {
      // todo get value as id without additional filtering
      this.stop = this.options.find(item => item.name === value);
      // remove `trip.day` to server side as default;
      fetch(
          `/api/stop_times?page=1&stop.id=${this.stop.id}&trip.day=2&order[departure_at]=asc`
      ).then(res => {
        res.json().then(json => {
          this.trips = json['hydra:member'];
        });
      });
      this.stopName = value;
    },
    search: _.debounce((loading, search, vm) => {
      fetch(
          `/api/stops?page=1&name=${escape(search)}`
      ).then(res => {
        res.json().then(json => (vm.options = json['hydra:member']));
        loading(false);
      });
    }, 350)
  },
  watch: {
    trips: {
      handler(newTrips) {
        const departures = this.getDepartures([...newTrips]);
        this.departures = [...departures];
      },
      immediate: true
    },
  },
  computed: {

  },
  mounted() {
    // todo add initial list from real stops
  }
};
</script>

<style scoped>
.row {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  width: 100%;
  background: #ffffff;
}

.column {
  display: flex;
  flex-direction: column;
  flex-basis: 100%;
  flex: 1;
  border: 2px solid #5e2ca5;
  min-height: 40em;
  padding: 2em;
}
</style>