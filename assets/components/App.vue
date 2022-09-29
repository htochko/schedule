<template>
  <div>
    <h2 class="center">Schedule Application</h2>
    <div class='main-wrapper'>
      <div class='row'>
        <div class='column'>
          {{ message }}
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
          <h2 v-text="stopName"></h2>
          Nearest trips: <!-- Selected  -->
          <!-- table of Nearest routes -->
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
Vue.component('v-select', vSelect);

export default {
  name: 'App',
  data() {
    return {
      message: "list of stops",
      options: [],
      stopName: null,
      stop: {},
      trips:[],
    };
  },
  methods: {
    onSearch(search, loading) {
      if(search.length) {
        loading(true);
        this.search(loading, search, this);
      }
    },
    setSelected(value) {
      console.log(value);
      // todo get value as id without addtional viltering
      this.stopName = value;
      let stop = this.options.find(item => item.name = value);
      // todo add filters to by day, order by nearest time etc;
      fetch(
          `/api/stops/${stop.id}`
      ).then(res => {
        res.json().then(json => (this.trips = json['hydra:member']));
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
}

.column {
  display: flex;
  flex-direction: column;
  flex-basis: 100%;
  flex: 1;
  border: 2px solid #5e2ca5;
}
</style>