<template>
  <div>
    <h2 class="center">{{ message }}</h2>
    <div class='main-wrapper'>
          Type to choose line to configure
          <v-select label="name"
                    :filterable="false"
                    :options="options"
                    @search="onSearch"
                    :reduce="name => name.name"
                    @input="setSelected"
          >
            <template slot="no-options">
              type to search line..
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
      </div>
</template>

<script>
import _ from 'lodash';
import Vue from 'vue';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

Vue.component('v-select', vSelect);

export default {
  name: 'Admin',
  data() {
    return {
      message: "Welcome on Admin widget",
      options: [],
      lineName: null,
      line: {},
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
      this.line = this.options.find(item => item.name === value);
      this.lineName = value;
      fetch(
          `/api/stop_times?page=1&trip.line.name=${this.lineName}&trip.day=2`
      ).then(res => {
        res.json().then(json => {
          this.trips = json['hydra:member'];
        });
      });

    },
    search: _.debounce((loading, search, vm) => {
      fetch(
          `/api/lines?page=1&name=${escape(search)}`
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