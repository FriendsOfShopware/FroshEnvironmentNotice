<template>
  <div id="app" class="container-fluid">
    <b-table v-bind:items="notices" v-bind:fields="fields" v-bind:busy="isLoading" hover small/>
    <b-navbar fixed="bottom" class="navbar-light">
      <b-nav-form>
        <b-button variant="outline-primary" v-on:click="loadData" v-bind:disabled="isLoading">
          Neuladen
        </b-button>
      </b-nav-form>
    </b-navbar>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      fields: {
        name: {
          sortable: true,
        },
        message: {
          sortable: true,
        },
      },
      notices: [],
      isLoading: false,
    };
  },
  created() {
    this.loadData();
  },
  methods: {
    loadData() {
      if (!this.isLoading) {
        this.isLoading = true;
        axios.get('ajaxList')
          .then((response) => {
            this.notices = response.data.items;
            this.isLoading = false;
          })
          .catch(() => {
            this.isLoading = false;
          });
      }
    },
  },
};
</script>
