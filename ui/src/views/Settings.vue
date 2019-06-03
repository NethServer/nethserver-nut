<template>
  <div>
    <h2>{{$t('settings.title')}}</h2>
    <div v-if="!configLoaded" class="spinner spinner-lg"></div>
    <div v-if="configLoaded">
      <form class="form-horizontal">
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
              <select
                required
                type="text"
                class="combobox form-control"
                v-model="viewConfig.mode"
              >
                <option value="server">{{$t('settings.server')}}</option>
                <option value="client">{{$t('settings.client')}}</option>
              </select>
            </div>
          </div>
          <div v-if="viewConfig.mode === 'server'">
            <!-- Model -->
            <div class="form-group">
              <label 
                class="col-sm-2 control-label" 
                for="textInput-modal-markup"
              >{{$t('settings.model')}}</label>
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
                      <i class="mg-left-5">({{props.item.support_level}}*)</i>
                    </span>
                  </div>
                </suggestions>
                <!-- <input 
                  type="text" 
                  v-model="viewConfig.model" 
                  class="combobox form-control"
                  :placeholder="$t('search')+'...'"
                  @change="modelSelected()"
                  list="models"
                >
                <datalist id="models">
                  <option v-for="(model, index) in matchingModels" v-bind:key="index" :value="model.description">
                    {{ model.description }}
                  </option>
                </datalist> -->
              </div>
            </div>
            <!-- Driver -->
            <div class="form-group">
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
                >
                  <option v-for="(driver, index) in driversForModel" v-bind:key="index" :value="driver">
                    {{ driver }}
                  </option>
                </select>
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
                  <option value="auto">{{$t('settings.device_auto')}}</option>
                  <option value="ttyS0">{{$t('settings.device_ttyS0')}}</option>
                  <option value="ttyS1">{{$t('settings.device_ttyS1')}}</option>
                  <option value="ttyS2">{{$t('settings.device_ttyS2')}}</option>
                  <option value="ttyUSB0">{{$t('settings.device_ttyUSB0')}}</option>
                  <option value="ttyUSB1">{{$t('settings.device_ttyUSB1')}}</option>
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
                <input 
                  type="input" 
                  class="form-control"
                  v-model="viewConfig.upsName"
                  disabled
                >
              </div>
            </div>
            <!-- UPS user -->
            <div class="form-group">
              <label 
                class="col-sm-2 control-label" 
                for="textInput-modal-markup"
              >{{$t('settings.ups_user')}}</label>
              <div class="col-sm-5">
                <input 
                  type="input" 
                  class="form-control"
                  v-model="viewConfig.upsUser"
                  disabled
                >
              </div>
            </div>
            <!-- Password for slaves -->
            <div class="form-group">
              <label 
                class="col-sm-2 control-label" 
                for="textInput-modal-markup"
              >{{$t('settings.password_for_slaves')}}</label>
              <div class="col-sm-5">
                <input 
                  type="input" 
                  class="form-control"
                  v-model="viewConfig.password"
                  disabled
                >
              </div>
            </div>
          </div>
          <div v-if="viewConfig.mode === 'client'">
            <!-- Master server address -->
            <div class="form-group">
              <label 
                class="col-sm-2 control-label" 
                for="textInput-modal-markup"
              >{{$t('settings.master_server_address')}}</label>
              <div class="col-sm-5">
                <input 
                  type="input" 
                  class="form-control"
                  v-model="viewConfig.master"
                >
              </div>
            </div>
            <!-- Password -->
            <div class="form-group">
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
              </div>
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
            <!-- Toggle password visibility -->
            <div class="form-group">
              <legend
                class="fields-section-header-pf col-sm-1"
                aria-expanded="true"
              >
                <span
                  :class="['fa fa-angle-right field-section-toggle-pf', showAdvancedOptions ? 'fa-angle-down' : '']"
                ></span>
                <a
                  class="field-section-toggle-pf"
                  @click="toggleAdvancedOptions()"
                >{{$t('advanced_options')}}</a>
              </legend>
            </div>

            <!-- Advanced options -->
            <div
              class="form-group"
              v-if="showAdvancedOptions"
            >
              <!-- UPS name -->
              <div class="form-group">
                <label 
                  class="col-sm-2 control-label" 
                  for="textInput-modal-markup"
                >{{$t('settings.ups_name')}}</label>
                <div class="col-sm-5">
                  <input 
                    type="input" 
                    class="form-control"
                    v-model="viewConfig.upsName"
                  >
                </div>
              </div>
              <!-- UPS user -->
              <div class="form-group">
                <label 
                  class="col-sm-2 control-label" 
                  for="textInput-modal-markup"
                >{{$t('settings.ups_user')}}</label>
                <div class="col-sm-5">
                  <input 
                    type="input" 
                    class="form-control"
                    v-model="viewConfig.upsUser"
                  >
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Save -->
        <div class="form-group">
          <label 
            class="col-sm-2 control-label" 
            for="textInput-modal-markup"
          ></label>
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
      // cboDriverDisabled: true, // todo useless?
      models: [],
      driversForModel: [],
      nutServerConfig: null,
      nutMonitorConfig: null,
      showAdvancedOptions: false,
      passwordVisible: false,
      matchingModels: [],
      autoOptions: {
        inputClass: "form-control"
      },
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
        ["nethserver-nut/read"],
        {},
        null,
        function (success) {
          try {
            success = JSON.parse(success);
            ctx.models = success.configuration.models;
            ctx.nutServerConfig = success.configuration.nut_server.props;
            ctx.nutMonitorConfig = success.configuration.nut_monitor.props;

            // update view settings
            ctx.viewConfig.enableNutUps = ctx.nutMonitorConfig.Status;
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
            ctx.configLoaded = true
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

  //   modelSelected() { // todo remove
  //     this.driversForModel = [];
  //     // var driverString = this.models.find(model => model.description === this.viewConfig.model); //// todo
  //     // var driverString = 'driver1 or driver2'; //// todo
  //     var driverString = this.models_drivers[this.viewConfig.model];
  //     if (typeof driverString !== 'undefined') {
  //       if (driverString.toLowerCase().indexOf(' or ') == -1) {
  //         // there's only one driver for the model selected
  //         this.driversForModel.push(driverString);
  //       } else {
  //         // multiple drivers for the model selected
  //         var drivers = driverString.split(' or ');
  //         for (let driver of drivers) {
  //           this.driversForModel.push(driver);
  //         }
  //       }
  //       if (this.driversForModel.length > 0) {
  //         this.driver = this.driversForModel[0];
  //       }
  //     }
  //   },
  //   searchModel: _.debounce(function(searchValue) {
  //     this.matchingModels = [];
  //     if (searchValue.length > 0) {
  //       this.matchingModels = this.models.filter(function(model) {
  //         return (model.description.toLowerCase().indexOf(searchValue.toLowerCase()) !== -1);
  //       });
  //       // this.matchingModels = [this.models[1], this.models[2]]; //// todo
  //     }
  //     // console.log(this.matchingModels); // todo 
  //     // this.matchingModels.$forceUpdate();
  //   }, 200)
  // },

    filterModel(query) {
      if (query.trim().length === 0) {
        return null;
      }
      return this.models.filter(function(model) {
        return (model.description.toLowerCase().includes(query.toLowerCase()));
      });
    },
    selectModel(item) {
      this.driversForModel = item.driver; // todo item.driverS
      if (this.driversForModel.length > 0) {
          this.driver = this.driversForModel[0];
        }
    },
  }
  // watch: { // todo remove
  //   // 'viewConfig.model': this.searchModel(searchValue)
  //   'viewConfig.model'(searchValue){
  //     this.searchModel(searchValue);
  //   }
  // }
};
</script>

<style>
</style>
