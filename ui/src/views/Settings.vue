<template>
  <div>
    <h1>{{$t('settings.title')}}</h1>
    <div v-if="!configLoaded" class="spinner spinner-lg"></div>
    <div v-if="configLoaded">
      <form class="form-horizontal" v-on:submit.prevent="btSaveClick">
        <!-- Enable NUT UPS -->
        <div class="form-group">
          <label
            class="col-sm-2 control-label"
            for="textInput-modal-markup"
          >{{$t('settings.enable_nut_ups')}}</label>
          <div class="col-sm-5">
            <toggle-button
              class="min-toggle"
              :width="40"
              :height="20"
              :color="{checked: '#0088ce', unchecked: '#bbbbbb'}"
              :value="viewConfig.enableNutUps"
              :sync="true"
              @change="toggleEnableNut()"
            />
          </div>
        </div>
        <div v-if="viewConfig.enableNutUps">
          <!-- Mode -->
          <div class="form-group">
            <label
              class="col-sm-2 control-label"
              for="textInput-modal-markup"
            >{{$t('settings.mode')}}</label>
            <div class="col-sm-5">
              <select required type="text" class="combobox form-control" v-model="viewConfig.mode">
                <option value="server">{{$t('settings.server')}}</option>
                <option value="client">{{$t('settings.client')}}</option>
              </select>
            </div>
          </div>
          <div v-if="viewConfig.mode === 'server'">
            <!-- Model -->
            <div class="form-group" v-if="showModel">
              <label
                class="col-sm-2 control-label"
                for="textInput-modal-markup"
              >{{$t('settings.model')}}
                <doc-info
                  :placement="'top'"
                  :title="$t('settings.model')"
                  :chapter="'model'"
                  :inline="true"
                ></doc-info>
              </label>
              <div class="col-sm-5">
                <suggestions
                  v-model="viewConfig.model"
                  :options="autoOptions"
                  :onInputChange="filterModel"
                  :onItemSelected="selectModel"
                >
                  <div slot="item" slot-scope="props" class="single-item">
                    <span>
                      {{props.item.name}}
                      <b class="mg-left-5">{{props.item.manufacturer}}</b>
                      -
                      {{props.item.model_name}}
                      <i
                        class="mg-left-5"
                      >
                        (
                        <span v-for="i in parseInt(props.item.support_level)" v-bind:key="i">*</span>)
                      </i>
                    </span>
                  </div>
                </suggestions>
              </div>
            </div>
            <!-- Driver -->
            <div class="form-group" :class="{ 'has-error': showErrorDriver }">
              <label
                class="col-sm-2 control-label"
                for="textInput-modal-markup"
              >{{$t('settings.driver')}}</label>
              <div class="col-sm-5">
                <select
                  required
                  type="text"
                  class="combobox form-control"
                  v-model="viewConfig.driver"
                  v-if="modelTyped"
                >
                  <option
                    v-for="(driver, index) in driversForModel"
                    v-bind:key="index"
                    :value="driver"
                  >{{ driver }}</option>
                </select>
                <input
                  type="input"
                  class="form-control"
                  v-model="viewConfig.driver"
                  v-if="!modelTyped"
                  :disabled="!modelTyped"
                  required
                >
                <span class="help-block" v-if="showErrorDriver">{{$t('settings.driver_validation')}}</span>
              </div>
              <!-- Edit driver -->
              <div class="col-sm-2 adjust-index" v-if="!showModel">
                <button
                  type="button"
                  class="btn btn-primary"
                  @click="editDriver()"
                >{{$t('edit')}}</button>
              </div>
            </div>
            <!-- Device -->
            <div class="form-group">
              <label
                class="col-sm-2 control-label"
                for="textInput-modal-markup"
              >{{$t('settings.device')}}</label>
              <div class="col-sm-5">
                <select
                  required
                  type="text"
                  class="combobox form-control"
                  v-model="viewConfig.device"
                >
                  <option value="auto">{{$t('settings.device_usb_auto')}}</option>
                  <option value="/dev/ttyS0">{{$t('settings.device_ttyS0')}}</option>
                  <option value="/dev/ttyS1">{{$t('settings.device_ttyS1')}}</option>
                  <option value="/dev/ttyS2">{{$t('settings.device_ttyS2')}}</option>
                  <option value="/dev/ttyUSB0">{{$t('settings.device_ttyUSB0')}}</option>
                  <option value="/dev/ttyUSB1">{{$t('settings.device_ttyUSB1')}}</option>
                </select>
              </div>
            </div>

            <!-- UPS credentials -->
            <div class="divider"></div>
            <h3>{{$t('settings.ups_credentials')}}</h3>

            <!-- UPS name -->
            <div class="form-group">
              <label
                class="col-sm-2 control-label"
                for="textInput-modal-markup"
              >{{$t('settings.ups_name')}}</label>
              <div class="col-sm-5">
                <input type="input" class="form-control" v-model="viewConfig.upsName" disabled>
              </div>
              <!-- Copy UPS name -->
              <div class="col-sm-2 adjust-index">
                <button
                  type="button"
                  class="btn btn-primary"
                  v-clipboard:copy="viewConfig.upsName"
                  v-clipboard:success="onCopyUpsNameSuccess"
                >{{$t('copy')}}</button>
                <span v-if="upsNameCopied" class="fa fa-check green copy-success"></span>
              </div>
            </div>
            <!-- UPS user -->
            <div class="form-group">
              <label
                class="col-sm-2 control-label"
                for="textInput-modal-markup"
              >{{$t('settings.ups_user')}}</label>
              <div class="col-sm-5">
                <input type="input" class="form-control" v-model="viewConfig.upsUser" disabled>
              </div>
              <!-- Copy UPS user -->
              <div class="col-sm-2 adjust-index">
                <button
                  type="button"
                  class="btn btn-primary"
                  v-clipboard:copy="viewConfig.upsUser"
                  v-clipboard:success="onCopyUpsUserSuccess"
                >{{$t('copy')}}</button>
                <span v-if="upsUserCopied" class="fa fa-check green copy-success"></span>
              </div>
            </div>
            <!-- Password for slaves -->
            <div class="form-group">
              <label
                class="col-sm-2 control-label"
                for="textInput-modal-markup"
              >{{$t('settings.password_for_clients')}}</label>
              <div class="col-sm-5">
                <input type="input" class="form-control" v-model="viewConfig.password" disabled>
              </div>
              <!-- Copy password for slaves -->
              <div class="col-sm-2 adjust-index">
                <button
                  type="button"
                  class="btn btn-primary"
                  v-clipboard:copy="viewConfig.password"
                  v-clipboard:success="onCopyPasswordForSlavesSuccess"
                >{{$t('copy')}}</button>
                <span v-if="passwordForSlavesCopied" class="fa fa-check green copy-success"></span>
              </div>
            </div>
          </div>
          <div v-if="viewConfig.mode === 'client'">
            <!-- Server address -->
            <div class="form-group" :class="{ 'has-error': showErrorMaster }">
              <label
                class="col-sm-2 control-label"
                for="textInput-modal-markup"
              >{{$t('settings.server_address')}}</label>
              <div class="col-sm-5">
                <input 
                  type="input" 
                  class="form-control" 
                  v-model="viewConfig.master" 
                  required 
                  :placeholder="$t('settings.hostname_or_ip_address')"
                >
                <span class="help-block" v-if="showErrorMaster">{{$t('settings.master_validation')}}</span>
              </div>
            </div>
            <!-- Password -->
            <div class="form-group" :class="{ 'has-error': showErrorPassword }">
              <label
                class="col-sm-2 control-label"
                for="textInput-modal-markup"
              >{{$t('settings.password')}}</label>
              <div class="col-sm-5">
                <input
                  :type="passwordVisible ? 'text' : 'password'"
                  class="form-control"
                  v-model="viewConfig.password"
                >
                <span
                  class="help-block"
                  v-if="showErrorPassword"
                >{{$t('settings.password_validation')}}</span>
              </div>
              <!-- Toggle password visibility -->
              <div class="col-sm-2 adjust-index">
                <button
                  tabindex="-1"
                  type="button"
                  class="btn btn-primary"
                  @click="togglePasswordVisibility()"
                >
                  <span :class="[!passwordVisible ? 'fa fa-eye' : 'fa fa-eye-slash']"></span>
                </button>
              </div>
            </div>

            <!-- Advanced options -->
            <div class="form-group">
              <legend class="fields-section-header-pf col-sm-1" aria-expanded="true">
                <span
                  :class="['fa fa-angle-right field-section-toggle-pf', showAdvancedOptions ? 'fa-angle-down' : '']"
                ></span>
                <a
                  class="field-section-toggle-pf"
                  @click="toggleAdvancedOptions()"
                >{{$t('advanced_options')}}</a>
              </legend>
            </div>
            <div class="form-group" v-if="showAdvancedOptions">
              <!-- UPS name -->
              <div class="form-group" v-bind:class="{ 'has-error': showErrorUpsName }">
                <label
                  class="col-sm-2 control-label"
                  for="textInput-modal-markup"
                >{{$t('settings.ups_name')}}</label>
                <div class="col-sm-5">
                  <input type="input" class="form-control" v-model="viewConfig.upsName">
                  <span
                    class="help-block"
                    v-if="showErrorUpsName"
                  >{{$t('settings.ups_name_validation')}}</span>
                </div>
              </div>
              <!-- UPS user -->
              <div class="form-group" v-bind:class="{ 'has-error': showErrorUpsUser }">
                <label
                  class="col-sm-2 control-label"
                  for="textInput-modal-markup"
                >{{$t('settings.ups_user')}}</label>
                <div class="col-sm-5">
                  <input type="input" class="form-control" v-model="viewConfig.upsUser">
                  <span
                    class="help-block"
                    v-if="showErrorUpsUser"
                  >{{$t('settings.ups_user_validation')}}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Save -->
        <div class="form-group">
          <label class="col-sm-2 control-label" for="textInput-modal-markup"></label>
          <div class="col-sm-5">
            <button class="btn btn-primary" type="submit">{{$t('save')}}</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
var nethserver = window.nethserver;

export default {
  name: "Settings",
  mounted() {
    this.init();
  },
  data() {
    return {
      configLoaded: false,
      models: [],
      driversForModel: [],
      nutServerConfig: null,
      nutMonitorConfig: null,
      showAdvancedOptions: false,
      passwordVisible: false,
      matchingModels: [],
      modelTyped: false,
      autoOptions: {
        inputClass: "form-control",
        placeholder: this.$i18n.t('settings.search_for_model')
      },
      showErrorMaster: false,
      showErrorPassword: false,
      showErrorUpsName: false,
      showErrorUpsUser: false,
      showErrorDriver: false,
      upsNameCopied: false,
      upsUserCopied: false,
      passwordForSlavesCopied: false,
      showModel: false,
      viewConfig: {
        enableNutUps: false,
        mode: '',
        model: '',
        driver: '',
        device: '',
        upsName: '',
        upsUser: '',
        password: '',
        master: ''
      }
    }
  },
  methods: {
    init: function() {
      this.getConfig()
    },
    getConfig: function() {
      var ctx = this;
      nethserver.exec(
        ["nethserver-nut/settings/read"],
        { "app_info": "configuration" },
        null,
        function (success) {
          try {
            success = JSON.parse(success);
            ctx.nutServerConfig = success.configuration.nut_server.props;
            ctx.nutMonitorConfig = success.configuration.nut_monitor.props;
            nethserver.exec(
              ["nethserver-nut/settings/read"],
              { "app_info": "models" },
              null,
              function (success) {
                try {
                  success = JSON.parse(success);
                  ctx.models = success.models;
                  // update view settings
                  ctx.viewConfig.enableNutUps = ctx.nutMonitorConfig.status === 'enabled' ? true : false;
                  ctx.viewConfig.master = ctx.nutMonitorConfig.Master;
                  
                  if (ctx.viewConfig.master) {
                    ctx.viewConfig.mode = 'client';
                  } else {
                    ctx.viewConfig.mode = 'server';
                  }
                  ctx.viewConfig.driver = ctx.nutServerConfig.Model;
                  ctx.viewConfig.device = ctx.nutServerConfig.Device;
                  ctx.viewConfig.upsName = ctx.nutServerConfig.Ups;
                  ctx.viewConfig.upsUser = ctx.nutServerConfig.User;
                  ctx.viewConfig.password = ctx.nutServerConfig.Password;
                  if (ctx.viewConfig.driver) {
                    ctx.showModel = false;
                  } else {
                    ctx.showModel = true;
                  }
                  ctx.configLoaded = true;
                  ctx.modelTyped = false;
                } catch (e) {
                  console.error(e) /* eslint-disable-line no-console */
                }
              },
              function (error) {
                console.error(error) /* eslint-disable-line no-console */
              }
            );
          } catch (e) {
            console.error(e) /* eslint-disable-line no-console */
          }
        },
        function (error) {
          console.error(error) /* eslint-disable-line no-console */
        }
      );
    },
    toggleEnableNut() {
      this.viewConfig.enableNutUps = !this.viewConfig.enableNutUps;
    },
    toggleAdvancedOptions() {
      this.showAdvancedOptions = !this.showAdvancedOptions;
    },
    togglePasswordVisibility() {
      this.passwordVisible = !this.passwordVisible;
    },
    filterModel(query) {
      if (query.trim().length === 0) {
        return null;
      }
      return this.models.filter(function(model) {
        return (model.description.toLowerCase().includes(query.toLowerCase()));
      });
    },
    selectModel(item) {
      this.viewConfig.model = item.description
      this.driversForModel = item.drivers;
      if (this.driversForModel.length > 0) {
        this.viewConfig.driver = this.driversForModel[0];
        this.showErrorDriver = false;
      }
      this.modelTyped = true;
    },
    btSaveClick() {
      this.showErrorMaster = false;
      this.showErrorPassword = false;
      this.showErrorUpsName = false;
      this.showErrorUpsUser = false;
      var enableNutServer = this.nutServerConfig.status;
      var enableNutUps = this.viewConfig.enableNutUps;

      if (!this.viewConfig.enableNutUps) {
        // if nut-monitor is turned off, then disable nut-server too
        enableNutServer = "disabled";
      } else if (this.viewConfig.mode === 'server') {
        // if mode = server, then enable nut-server
        enableNutServer = "enabled";
      } else {
        // if mode = client, then disable nut-server
        enableNutServer = "disabled";
      }

      var master;
      if (this.viewConfig.mode === 'client') {
        master = this.viewConfig.master;
      } else {
        master = '';
      }

      var configObj = {
        "configuration": {
          "nut_server": {
            "props": {
              "status": enableNutServer,
              "access": this.nutServerConfig.access,
              "User": enableNutUps ? this.viewConfig.upsUser : this.nutServerConfig.User,
              "TCPPort": this.nutServerConfig.TCPPort,
              "Device": enableNutUps ? this.viewConfig.device : this.nutServerConfig.Device,
              "Model": enableNutUps ? this.viewConfig.driver : this.nutServerConfig.Model,
              "Password": enableNutUps ? this.viewConfig.password : this.nutServerConfig.Password,
              "Ups": enableNutUps ? this.viewConfig.upsName : this.nutServerConfig.Ups
            }
          },
          "nut_monitor": {
            "props": {
              "status": enableNutUps ? "enabled" : "disabled",
              "Master": enableNutUps ? master : this.nutMonitorConfig.Master,
              "Notify": this.nutMonitorConfig.Notify
            }
          }
        }
      }
      var context = this;
      nethserver.exec(
          ["nethserver-nut/settings/validate"],
          configObj,
          null,
          function () {
            // success
            nethserver.notifications.success = context.$i18n.t(
              "settings.configuration_saved"
            );
            nethserver.notifications.error = context.$i18n.t(
              "settings.configuration_failed"
            );
            // update values
            nethserver.exec(
              ["nethserver-nut/settings/update"],
              configObj,
              function(stream) {
                console.info("nut-update", stream); /* eslint-disable-line no-console */
              },
              function () {
                // success
                context.getConfig();
              },
              function (error) {
                console.error(error); /* eslint-disable-line no-console */
              }
            );
          },
          function (error, data) {
            try {
              var errorData = JSON.parse(data);
              for (var e in errorData.attributes) {
                var attr = errorData.attributes[e]
                var param = attr.parameter;
                if (param === 'Master') {
                  context.showErrorMaster = true;
                } else if (param === 'Password') {
                  context.showErrorPassword = true;
                } else if (param === 'Ups') {
                  context.showErrorUpsName = true;
                } else if (param === 'User') {
                  context.showErrorUpsUser = true;
                } else if (param === 'Model') {
                  context.showErrorDriver = true;
                }
              }
            } catch (e) {
              console.error(e) /* eslint-disable-line no-console */
            }
          }
      );
    },
    onCopyUpsNameSuccess: function (e) {
      this.upsNameCopied = true;
      var ctx = this;
      setTimeout(function() {
        ctx.upsNameCopied = false;
      }, 2000);
    },
    onCopyUpsUserSuccess: function (e) {
      this.upsUserCopied = true;
      var ctx = this;
      setTimeout(function() {
        ctx.upsUserCopied = false;
      }, 2000);
    },
    onCopyPasswordForSlavesSuccess: function (e) {
      this.passwordForSlavesCopied = true;
      var ctx = this;
      setTimeout(function() {
        ctx.passwordForSlavesCopied = false;
      }, 2000);
    },
    editDriver() {
      this.viewConfig.driver = '';
      this.showModel = true;
    }
  }
};
</script>

<style scoped>
.copy-success {
  margin-left: 10px;
}

.green {
  color: #3f9c35;
}
</style>
