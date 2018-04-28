<template>
  <div id="app">
    <div class="w-100 sticky-top position-fixed px-4 mt-5">
      <b-alert v-for="alert in alerts" v-bind:variant="alert.variant" dismissible show>
        {{alert.message}}
        <template slot="dismiss">
          &times;
        </template>
      </b-alert>
    </div>
    <b-card no-body>
      <b-tabs card>
        <b-tab title="Messages" active>
          <collection-editor v-bind:fields="messagesFields" api-key="Messages" v-bind:default-item="defaultMessage" v-on:error="addAlert">
            <template slot="detail" slot-scope="{ item }">
              <b-form-group id="fieldName"
                            label="Name"
                            label-for="inputName"
                            v-bind:label-cols="3"
                            horizontal>
                <b-form-input id="inputName" v-model="item.name"/>
              </b-form-group>
              <b-form-group id="fieldMessage"
                            v-bind:label-cols="3"
                            label="Message"
                            label-for="inputMessage"
                            horizontal>
                <b-form-input id="inputMessage" v-model="item.message"/>
              </b-form-group>
            </template>
          </collection-editor>
        </b-tab>
        <b-tab title="Slots">
          <collection-editor v-bind:fields="slotsFields" api-key="Slots" v-bind:default-item="defaultSlot" v-on:error="addAlert">
            <b-form-group id="fieldName"
                          label="Name"
                          label-for="inputName"
                          v-bind:label-cols="3"
                          slot-scope="{ item }"
                          slot="detail"
                          horizontal>
              <b-form-input id="inputName" v-model="item.name"/>
            </b-form-group>
          </collection-editor>
        </b-tab>
      </b-tabs>
    </b-card>
  </div>
</template>

<script>
import CollectionEditor from './components/CollectionEditor';

export default {
  components: {
    CollectionEditor,
  },
  data() {
    return {
      messagesFields: {
        name: {
          sortable: true,
        },
        message: {
          sortable: true,
        },
      },
      slotsFields: {
        name: {
          sortable: true,
        },
      },
      defaultSlot: {
        name: '',
        style: {},
      },
      defaultMessage: {
        name: '',
        message: '',
      },
      alerts: [],
    };
  },
  methods: {
    addAlert(alertData) {
      this.alerts.push(alertData);
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
