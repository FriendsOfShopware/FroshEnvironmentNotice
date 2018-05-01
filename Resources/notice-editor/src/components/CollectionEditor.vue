<template>
  <div>
    <b-navbar class="navbar-light bg-light">
      <b-nav-form>
        <b-button variant="outline-success" size="sm" v-on:click="addItem" v-bind:disabled="isLoading">
          Add
        </b-button>
      </b-nav-form>
      <b-nav-form right>
        <b-button variant="outline-primary" size="sm" v-on:click="loadItems" v-bind:disabled="isLoading">
          Load
        </b-button>
      </b-nav-form>
    </b-navbar>
    <b-table v-bind:items="items" v-bind:fields="tableFields" v-bind:busy="isLoading" hover small>
      <template slot="actions" slot-scope="row">
        <b-button-group size="sm">
          <b-button variant="outline-secondary" v-on:click.stop="row.toggleDetails" v-bind:pressed="row.detailsShowing" v-if="row.item.id">
            Edit
          </b-button>
          <b-button variant="outline-danger" v-on:click="deleteItem(row.item)" v-if="row.item.id">
            Delete
          </b-button>
          <b-button variant="outline-danger" v-on:click="cancelItem(row.item)" v-else>
            Cancel
          </b-button>
        </b-button-group>
      </template>
      <template slot="row-details" slot-scope="row">
        <b-card>
          <slot name="detail" v-bind:item="row.item" v-bind:row="row"></slot>
          <b-row>
            <b-col class="text-right">
              <b-button-group size="sm">
                <b-button variant="outline-success" v-on:click="saveItem(row.item)" v-if="row.item.id">
                  Save
                </b-button>
                <b-button variant="outline-success" v-on:click="insertItem(row.item)" v-else>
                  Add
                </b-button>
                <b-button variant="outline-danger" v-on:click="resetItem(row.item)" v-if="row.item.id">
                  Cancel
                </b-button>
                <b-button variant="outline-danger" v-on:click="cancelItem(row.item)" v-else>
                  Cancel
                </b-button>
              </b-button-group>
            </b-col>
          </b-row>
        </b-card>
      </template>
    </b-table>
  </div>
</template>

<script>
  import axios from 'axios';

  export default {
    props: {
      apiKey: {
        type: String,
        required: true,
      },
      fields: {
        type: Object,
        required: true,
      },
      defaultItem: {
        type: Object,
        required: true,
      },
    },
    data() {
      return {
        items: [],
        isLoading: false,
        tableFields: {
          ...this.fields,
          actions: {
            tdClass: 'table-col-minimum',
            thClass: 'table-col-minimum',
            label: '',
          },
        },
      };
    },
    created() {
      this.loadItems();
    },
    methods: {
      throwException(exception) {
        this.$emit('error', exception);
      },
      loadItems() {
        if (!this.isLoading) {
          this.isLoading = true;
          axios.get(`ajax${this.apiKey}List`)
            .then((response) => {
              this.items = response.data.items;
              this.isLoading = false;
            })
            .catch(() => {
              this.isLoading = false;
            });
        }
      },
      addItem() {
        this.items.push({
          ...this.defaultItem,
          id: null,
          _showDetails: true,
        });
      },
      deleteItem(item) {
        axios.post(`ajax${this.apiKey}Delete`, { id: item.id })
          .then(() => {
            this.cancelItem(item);
          })
          .catch((response) => {
            this.logCatchedItemResponse(item, response, 'deleting');
          });
      },
      cancelItem(item) {
        this.items.splice(this.items.indexOf(item), 1);
      },
      resetItem(item) {
        axios.get(`ajax${this.apiKey}Get?id=${item.id}`)
          .then((response) => {
            this.items.splice(this.items.indexOf(item), 1, response.data.data);
          })
          .catch((response) => {
            this.logCatchedItemResponse(item, response, 'resetting');
          });
      },
      saveItem(item) {
        const specificItem = ['id', ...Object.keys(this.defaultItem)]
          .reduce((result, key) => ({ ...result, [key]: item[key] }), {});
        axios.post(`ajax${this.apiKey}Update`, specificItem)
          .then((response) => {
            this.items.splice(this.items.indexOf(item), 1, response.data.data);
          })
          .catch((response) => {
            this.logCatchedItemResponse(item, response, 'saving');
          });
      },
      insertItem(item) {
        axios.post(`ajax${this.apiKey}Insert`, item)
          .then((response) => {
            this.items.splice(this.items.indexOf(item), 1, response.data.data);
          })
          .catch((response) => {
            this.logCatchedItemResponse(item, response, 'inserting');
          });
      },
      logCatchedItemResponse(item, response, action) {
        this.throwException({
          variant: 'danger',
          message: `An error occured while ${action} ${item.name} (${item.id}). Check the console for further information.`,
        });
        console.log(item);
      },
    },
  };
</script>
