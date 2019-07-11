<template>
  <div>
    <h1>{{$t('dashboard.title')}}</h1>

    <div class="col-lg-12">

      <div v-show="!configLoaded" class="spinner spinner-lg"></div>

      <!-- Empty state -->
      <div v-show="configLoaded && showEmptyState" class="blank-slate-pf " id="">
        <div class="blank-slate-pf-icon">
          <span class="fa fa-bolt"></span>
        </div>
        <h1>
          {{$t('dashboard.ups_not_configured')}}
        </h1>
        <p>
          {{$t('dashboard.empty_state')}}.
        </p>
        <div class="blank-slate-pf-main-action">
          <a href="#/settings" class="btn btn-primary btn-lg">{{$t('settings.title')}}</a>
        </div>
      </div>

      <div v-show="configLoaded && !showEmptyState">
        <div class="row fluid divider margin-top-20">
          <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="form-horizontal">
              <div class="form-group compact">
                <label class="col-sm-3 control-label property-large">{{ $t('dashboard.model') }}</label>
                <div class="col-sm-9 adjust-li">
                  <p class="line-high property-large">
                    <span v-if="showErrorState">
                      -
                    </span>
                    <span v-else>
                      {{ status.device_model }}
                      <span class="gray">({{ status.driver_name }})</span>
                    </span>
                  </p>
                </div>
                <label class="col-sm-3 control-label property-large">{{ $t('dashboard.status') }}</label>
                <div class="col-sm-9 adjust-li">
                  <p class="line-high property-large">
                    <span v-if="showErrorState">
                      {{ $t('dashboard.offline') }}
                      <span class="fa pficon-error-circle-o"></span>
                    </span>
                    <span v-else-if="status.ups_status === 'OL'">
                      {{ $t('dashboard.online') }}
                      <span class="fa pficon-ok"></span>
                    </span>
                    <span v-else>
                      {{ status.ups_status }}
                      <span class="fa pficon-error-circle-o"></span>
                    </span>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <h3>{{$t('dashboard.status')}}</h3>

        <div class="row row-stat fluid">
          <div class="row-inline-block">
            <div class="stats-container col-xs-12 col-sm-4 col-md-4 width-30">
              <span
                class="card-pf-utilization-card-details-count stats-count"
              >
                <span>
                  <span v-if="showErrorState">-</span>
                  <span v-else>{{ parseFloat(status.input_voltage) }} V</span>
                </span>
              </span>
              <span class="card-pf-utilization-card-details-description stats-description">
                <span
                  class="card-pf-utilization-card-details-line-2 stats-text"
                >{{$t('dashboard.input_voltage')}}</span>
              </span>
            </div>
            <div class="stats-container col-xs-12 col-sm-4 col-md-4">
              <span
                class="card-pf-utilization-card-details-count stats-count"
              >
                <span>
                  <span v-if="showErrorState">-</span>
                  <span v-else>{{ parseFloat(status.output_voltage) }} V</span>
                </span>
              </span>
              <span class="card-pf-utilization-card-details-description stats-description">
                <span
                  class="card-pf-utilization-card-details-line-2 stats-text"
                >{{$t('dashboard.output_voltage')}}</span>
              </span>
            </div>
            <div class="stats-container col-xs-12 col-sm-4 col-md-4">
              <span
                class="card-pf-utilization-card-details-count stats-count"
              >
                <span>
                  <span v-if="showErrorState">-</span>
                  <span v-else>{{ parseFloat(status.ups_temperature) }} &deg;C</span>
                </span>
              </span>
              <span class="card-pf-utilization-card-details-description stats-description">
                <span
                  class="card-pf-utilization-card-details-line-2 stats-text"
                >{{$t('dashboard.temperature')}}</span>
              </span>
            </div>
          </div>
        </div>

        <div :class="['row-eq-height', { divider: !showErrorState }]">
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 resources-panel">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">
                  <span class="icon-header-panel">
                    <span class="fa pficon-rebalance right"></span>
                  </span>
                  {{$t('dashboard.load')}}
                </h3>
              </div>
              <div class="panel-body">
                <div v-if="showErrorState" class="empty-piechart">
                  <span class="fa fa-pie-chart"></span>
                  <div>{{ $t('dashboard.empty_piechart_label') }}</div>
                </div>
                <div v-else id="load-chart" class="text-center"></div>
              </div>
              <div class="panel-footer" v-show="!nutMonitorConfig.Master && !showErrorState">
                <div>
                  <span class="pficon pficon-users filter-icon"></span>
                  <span>
                    {{$t('dashboard.clients_connected')}}:
                    <span class="bold">{{ clients.length }}</span>

                    <a href="#" class="margin-left-4" data-toggle="popover" data-html="true"
                      :title="this.$i18n.t('dashboard.ups_clients')"
                      :data-content="this.clients_popover_text">{{$t('dashboard.details')}}
                    </a>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 resources-panel">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">
                  <span class="icon-header-panel">
                    <span class="fa fa-battery-full right"></span>
                  </span>
                  {{$t('dashboard.battery_charge')}}
                </h3>
              </div>
              <div class="panel-body">
                <div v-if="showErrorState" class="empty-piechart">
                  <span class="fa fa-pie-chart"></span>
                  <div>{{ $t('dashboard.empty_piechart_label') }}</div>
                </div>
                <div v-else id="battery-charge-chart" class="text-center"></div>
              </div>
              <div class="panel-footer" v-if="!showErrorState">
                <div>
                  <span class="fa fa-clock-o filter-icon"></span>
                  <span>
                    {{$t('dashboard.battery_runtime')}}:
                    <span
                      class="bold"
                    >{{ Math.round(status.battery_runtime / 60) }} {{$t('dashboard.minutes')}}</span>
                  </span>
                </div>
                <div>
                  <span class="fa fa-bolt filter-icon margin-left-4"></span>
                  <span>
                    {{$t('dashboard.battery_voltage')}}:
                    <span class="bold">{{ status.battery_voltage }} V</span>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <h3 v-if="!showErrorState">{{$t('dashboard.full_status')}}</h3>
        <div v-if="!showErrorState" class="form-group">
          <legend class="fields-section-header-pf col-sm-1" aria-expanded="true">
            <span
              :class="['fa fa-angle-right field-section-toggle-pf', showRawOutput ? 'fa-angle-down' : '']"
            ></span>
            <a
              class="field-section-toggle-pf"
              @click="toggleRawOutput()"
            >{{$t('dashboard.details')}}</a>
          </legend>
        </div>
        <div v-if="!showErrorState" class="form-group margin-top-40" v-show="showRawOutput">
          <ul>
            <li v-for="(i, ik) in status" :key="ik">
              <code>{{ ik }}</code>
              <span class="property-value">{{ i }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
var nethserver = window.nethserver;
var $ = window.$;

export default {
  name: "Users",
  props: {
    msg: String
  },
  mounted() {
    this.getUpsData();

    // Initialize Popovers
    $('[data-toggle=popover]').popovers()
      .on('hidden.bs.popover', function (e) {
        $(e.target).data('bs.popover').inState.click = false;
    });
  },
  data() {
    return {
      configLoaded: false,
      status: {},
      showRawOutput: false,
      clients: [],
      clients_popover_text: '',
      nutServerConfig: {},
      nutMonitorConfig: {},
      showEmptyState: false,
      showErrorState: false,
    };
  },
  methods: {
    getUpsData: function() {
      var ctx = this;
      nethserver.exec(
        ["nethserver-nut/dashboard/read"],
        { "app_info": "configuration" },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
            ctx.nutServerConfig = success.configuration.nut_server.props;
            ctx.nutMonitorConfig = success.configuration.nut_monitor.props;
            if (!ctx.nutServerConfig.Model && !ctx.nutMonitorConfig.Master) {
              // model and master not defined -> empty state
              ctx.showEmptyState = true;
              ctx.configLoaded = true;
            } else {
              // retrieve UPS status
              nethserver.exec(
              ["nethserver-nut/dashboard/read"],
              { "app_info": "status" },
              null,
              function(success) {
                try {
                  success = JSON.parse(success);
                  ctx.status = success.status;
                  ctx.clients = success.clients;
                  ctx.getClientsPopoverText();
                  ctx.showEmptyState = false;
                  ctx.configLoaded = true;
                  ctx.initMemoryCharts();
                } catch (e) {
                  console.error(e); /* eslint-disable-line no-console */
                }
              },
              function(error) {
                console.error(error) /* eslint-disable-line no-console */
                ctx.showErrorState = true;
                ctx.configLoaded = true;
              }
            );
            }
          } catch (e) {
            console.error(e) /* eslint-disable-line no-console */
          }
        },
        function(error) {
          console.error(error); /* eslint-disable-line no-console */
        }
      );
    },
    getClientsPopoverText() {
      this.clients_popover_text = '<ul>'
      for (var client of this.clients) {
        this.clients_popover_text += '<li>' + client + '</li>';
      }
      this.clients_popover_text += '</ul>'
    },
    toggleRawOutput() {
      this.showRawOutput = !this.showRawOutput;
    },
    initMemoryCharts() {
      var c3ChartDefaults = patternfly.c3ChartDefaults();
      var loadConfig = c3ChartDefaults.getDefaultDonutConfig("A");
      var batteryChargeConfig = c3ChartDefaults.getDefaultDonutConfig("A");
      loadConfig.bindto = "#load-chart";
      batteryChargeConfig.bindto = "#battery-charge-chart";

      loadConfig.data = {
        type: "donut",
        columns: [
          ["Used", parseFloat(this.status.ups_load)],
          ["Available", 100 - parseFloat(this.status.ups_load)]
        ],
        groups: [["used", "available"]],
        order: null
      };
      batteryChargeConfig.data = {
        type: "donut",
        columns: [
          ["Charged", parseFloat(this.status.battery_charge)],
          ["Not charged", 100 - parseFloat(this.status.battery_charge)]
        ],
        groups: [["used", "available"]],
        order: null
      };
      loadConfig.size = {
        width: 180,
        height: 180
      };
      batteryChargeConfig.size = {
        width: 180,
        height: 180
      };

      loadConfig.tooltip = {
        contents: patternfly.pfGetUtilizationDonutTooltipContentsFn("")
      };
      batteryChargeConfig.tooltip = {
        contents: patternfly.pfGetUtilizationDonutTooltipContentsFn("")
      };

      c3.generate(loadConfig);
      c3.generate(batteryChargeConfig);
      patternfly.pfSetDonutChartTitle(
        "#load-chart",
        parseFloat(this.status.ups_load)
      );
      patternfly.pfSetDonutChartTitle(
        "#battery-charge-chart",
        parseFloat(this.status.battery_charge)
      );
      var loadChartTitle = d3.select("#load-chart").select('text.c3-chart-arcs-title');
      loadChartTitle.insert('tspan').text("%").classed('donut-title-small-pf', true).attr('dy', 25).attr('x', 0);
      var batteryChartTitle = d3.select("#battery-charge-chart").select('text.c3-chart-arcs-title');
      batteryChartTitle.insert('tspan').text("%").classed('donut-title-small-pf', true).attr('dy', 25).attr('x', 0);
    }
  }
};
</script>

<style>
.right {
  float: right;
}
.line-high {
  line-height: 25px;
}
.property-large {
  font-size: 14px;
  padding-top: 3px;
}
.gray {
  color: #8b8d8f;
}
.width-30 {
  width: 30%;
}
.margin-top-20 {
  margin-top: 20px;
}
.padding-bottom-20 {
  padding-bottom: 20px;
}
.margin-top-40 {
  margin-top: 40px;
}
.property-value {
  font-weight: bold;
  margin-left: 10px;
}
.bold {
  font-weight: bold;
}
.filter-icon {
  margin-right: 10px;
  font-size: 20px;
}
.row-eq-height {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  flex-flow: row wrap;
}
.row-eq-height > div {
  margin: 20px 0;
}
.row-eq-height .panel {
  min-height: 100%;
  display: flex;
  flex-direction: column;
}
.panel-body {
  flex-grow: 1;
}
.panel-body [class^="pficon-"] {
  vertical-align: middle;
  margin-right: 0.5ex;
}
.icon-header-panel .fa {
  float: right;
}
.stats-count {
  font-size: 26px;
  font-weight: 300;
  margin-right: 10px;
  line-height: 1;
}
.stats-container {
  padding: 20px !important;
  text-align: center;
}
.margin-left-4 {
  margin-left: 4px;
}
.empty-piechart {
  margin-top: 2em;
  text-align: center;
  color: #9c9c9c;
}
.empty-piechart .fa {
  font-size: 92px;
  color: #bbbbbb;
}
</style>
