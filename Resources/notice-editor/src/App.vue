<template>
  <div id="app" class="container-fluid pt-5">
    <b-alert v-for="alert in alerts" v-bind:variant="alert.variant" v-bind:dismissable="true">
      {{alert.message}}
    </b-alert>
    <b-navbar fixed="top" class="navbar-light bg-light">
      <b-nav-form>
        <b-button variant="outline-success" size="sm" v-on:click="addNotice" v-bind:disabled="isLoading">
          Add
        </b-button>
      </b-nav-form>
    </b-navbar>
    <b-table v-bind:items="notices" v-bind:fields="fields" v-bind:busy="isLoading" hover small>
      <template slot="actions" slot-scope="row">
        <b-button-group size="sm">
          <b-button variant="outline-secondary" v-on:click.stop="row.toggleDetails" v-bind:pressed="row.detailsShowing" v-if="row.item.id">
            Edit
          </b-button>
          <b-button variant="outline-danger" v-on:click="deleteNotice(row.item)" v-if="row.item.id">
            Delete
          </b-button>
          <b-button variant="outline-danger" v-on:click="cancelNotice(row.item)" v-else>
            Cancel
          </b-button>
        </b-button-group>
      </template>
      <template slot="row-details" slot-scope="row">
        <b-card>
          <b-form-group id="fieldName"
                        label="Name"
                        label-for="inputName"
                        v-bind:label-cols="3"
                        horizontal>
            <b-form-input id="inputName" v-model="row.item.name"></b-form-input>
          </b-form-group>
          <b-form-group id="fieldMessage"
                        v-bind:label-cols="3"
                        label="Message"
                        label-for="inputMessage"
                        horizontal>
            <b-form-input id="inputMessage" v-model="row.item.message"></b-form-input>
          </b-form-group>
          <b-row>
            <b-col class="text-right">
              <b-button-group size="sm">
                <b-button variant="outline-success" v-on:click="insertNotice(row.item)" v-if="!row.item.id">
                  Add
                </b-button>
                <b-button variant="outline-danger" v-on:click="cancelNotice(row.item)" v-if="!row.item.id">
                  Delete
                </b-button>
                <b-button variant="outline-danger" v-on:click="cancelNotice(row.item)" v-else>
                  Cancel
                </b-button>
              </b-button-group>
            </b-col>
          </b-row>
        </b-card>
      </template>
    </b-table>
    <b-navbar fixed="bottom" class="navbar-light bg-light">
      <b-nav-form>
        <b-button variant="outline-primary" size="sm" v-on:click="loadData" v-bind:disabled="isLoading">
          Load
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
        actions: {
          tdClass: 'table-col-minimum',
          thClass: 'table-col-minimum',
          label: '',
        },
      },
      alerts: [],
      notices: [],
      isLoading: false,
    };
  },
  created() {
    this.loadData();
  },
  methods: {
    addNotice() {
      this.notices.push({
        id: null,
        name: '',
        message: '',
        _showDetails: true,
      });
    },
    deleteNotice(notice) {
      axios.post('ajaxDelete', { id: notice.id })
        .then(() => {
          const notices = this.notices;
          notices.splice(this.notices.indexOf(notice), 1);
          this.notices = notices;
        })
        .catch((response) => {
          this.alerts.push({
            variant: 'danger',
            message: `An error occured while deleting ${notice.name} (${notice.id}). Check the console for further information.`,
          });
          console.log(response);
        });
    },
    cancelNotice(notice) {
      const notices = this.notices;
      notices.splice(this.notices.indexOf(notice), 1);
      this.notices = notices;
    },
    insertNotice(notice) {
      axios.post('ajaxInsert', notice)
        .then((response) => {
          this.notices[this.notices.indexOf(notice)] = response.data.data;
        })
        .catch((response) => {
          this.alerts.push({
            variant: 'danger',
            message: `An error occured while inserting ${notice.name} (${notice.id}). Check the console for further information.`,
          });
          console.log(response);
        });
    },
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

<style>
  td.table-col-minimum,
  th.table-col-minimum {
    width: 1%;
  }
</style>
