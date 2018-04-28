<template>
  <div id="app">
    <b-card no-body>
      <b-tabs card>
        <b-tab title="Messages" active>
          <div class="w-100 sticky-top position-fixed px-4 mt-5">
            <b-alert v-for="alert in alerts" v-bind:variant="alert.variant" dismissible show>
              {{alert.message}}
              <template slot="dismiss">
                &times;
              </template>
            </b-alert>
          </div>
          <b-navbar class="navbar-light bg-light">
            <b-nav-form>
              <b-button variant="outline-success" size="sm" v-on:click="addNotice" v-bind:disabled="isLoading">
                Add
              </b-button>
            </b-nav-form>
            <b-nav-form right>
              <b-button variant="outline-primary" size="sm" v-on:click="loadNotices" v-bind:disabled="isLoading">
                Load
              </b-button>
            </b-nav-form>
          </b-navbar>
          <b-table v-bind:items="notices" v-bind:fields="messagesFields" v-bind:busy="isLoading" hover small>
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
                      <b-button variant="outline-success" v-on:click="saveNotice(row.item)" v-if="row.item.id">
                        Save
                      </b-button>
                      <b-button variant="outline-success" v-on:click="insertNotice(row.item)" v-else>
                        Add
                      </b-button>
                      <b-button variant="outline-danger" v-on:click="resetNotice(row.item)" v-if="row.item.id">
                        Cancel
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
        </b-tab>
      </b-tabs>
    </b-card>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      messagesFields: {
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
    this.loadNotices();
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
      axios.post('ajaxMessagesDelete', { id: notice.id })
        .then(() => {
          this.cancelNotice(notice);
        })
        .catch((response) => {
          this.logCatchedNoticeResponse(notice, response, 'deleting');
        });
    },
    cancelNotice(notice) {
      this.notices.splice(this.notices.indexOf(notice), 1);
    },
    resetNotice(notice) {
      axios.get(`ajaxMessagesGet?id=${notice.id}`)
        .then((response) => {
          this.notices.splice(this.notices.indexOf(notice), 1, response.data.data);
        })
        .catch((response) => {
          this.logCatchedNoticeResponse(notice, response, 'resetting');
        });
    },
    saveNotice(notice) {
      axios.post('ajaxMessagesUpdate', notice)
        .then((response) => {
          this.notices.splice(this.notices.indexOf(notice), 1, response.data.data);
        })
        .catch((response) => {
          this.logCatchedNoticeResponse(notice, response, 'saving');
        });
    },
    insertNotice(notice) {
      axios.post('ajaxMessagesInsert', notice)
        .then((response) => {
          this.notices.splice(this.notices.indexOf(notice), 1, response.data.data);
        })
        .catch((response) => {
          this.logCatchedNoticeResponse(notice, response, 'inserting');
        });
    },
    loadNotices() {
      if (!this.isLoading) {
        this.isLoading = true;
        axios.get('ajaxMessagesList')
          .then((response) => {
            this.notices = response.data.items;
            this.isLoading = false;
          })
          .catch(() => {
            this.isLoading = false;
          });
      }
    },
    logCatchedNoticeResponse(notice, response, action) {
      this.alerts.push({
        variant: 'danger',
        message: `An error occured while ${action} ${notice.name} (${notice.id}). Check the console for further information.`,
      });
      console.log(response);
    },
  },
};
</script>

<style>
  td.table-col-minimum,
  th.table-col-minimum {
    width: 1%;
  }

  #app .card-body {
    padding: 0;
  }

  body,
  body > #app,
  body > #app > .card {
    height: 100vh;
    border: unset;
    border-radius: 0;
  }
</style>
