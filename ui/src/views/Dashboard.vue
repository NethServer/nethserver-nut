<template>
  <div class="hello">
    <h1 i18n="users">Users</h1>

    <div id="loader" class="spinner spinner-lg" v-show="showLoader"></div>
    <div id="content" v-show="showContent">
      <h3>{{$t('actions')}}</h3>
      <button id="btAddUser" class="btn btn-primary" type="submit" data-toggle="modal" data-target="#modalUserCreate"
          v-on:click="btAddUserClick">{{$t('add_user')}}</button>

      <!-- modal for user creation -->
      <div class="modal fade" id="modalUserCreate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <span class="pficon pficon-close"></span>
              </button>
              <h4 class="modal-title" id="modalUserCreateLabel">{{$t('add_user')}}</h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal">
                <div id="group-firstname-create" class="form-group" v-bind:class="{ 'has-error': showErrorFirstnameCreate }">
                  <label class="col-sm-3 control-label" for="txFirstNameCreate">{{$t('first_name')}}</label>
                  <div class="col-sm-9">
                    <input type="text" id="txFirstNameCreate" v-model="firstnameCreate" class="form-control">
                    <span id="error-firstname-create" class="help-block" v-if="showErrorFirstnameCreate">{{$t('firstname_validation')}}</span>
                  </div>
                </div>
                <div id="group-lastname-create" class="form-group" v-bind:class="{ 'has-error': showErrorLastnameCreate }">
                  <label class="col-sm-3 control-label" for="txLastNameCreate">{{$t('last_name')}}</label>
                  <div class="col-sm-9">
                    <input type="text" id="txLastNameCreate" v-model="lastnameCreate" class="form-control">
                    <span id="error-lastname-create" class="help-block" v-if="showErrorLastnameCreate">{{$t('lastname_validation')}}</span>
                  </div>
                </div>
                <div id="group-dob-create" class="form-group" v-bind:class="{ 'has-error': showErrorDobCreate }">
                  <label class="col-sm-3 control-label" for="txtxDobCreateDob">{{$t('date_of_birth')}}</label>
                  <div class="col-sm-9">
                    <input type="text" id="txDobCreate" class="form-control bootstrap-datepicker" data-date-format='dd/mm/yyyy' >
                    <span id="error-dob-create" class="help-block" v-if="showErrorDobCreate">{{$t('dob_validation')}}</span>
                  </div>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">{{$t('cancel')}}</button>
              <button type="button" class="btn btn-primary" id="btSaveCreate" v-on:click="btSaveCreateClick">{{$t('save')}}</button>
            </div>
          </div>
        </div>
      </div>

      <!-- modal for user update -->
      <div class="modal fade" id="modalUserUpdate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <span class="pficon pficon-close"></span>
              </button>
              <h4 class="modal-title" id="modalUserUpdateLabel">{{$t('edit_user')}}</h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal">
                <div id="group-firstname-update" class="form-group" v-bind:class="{ 'has-error': showErrorFirstnameUpdate }">
                  <label class="col-sm-3 control-label" for="txFirstName">{{$t('first_name')}}</label>
                  <div class="col-sm-9">
                    <input type="text" id="txFirstNameUpdate" v-model="firstnameUpdate" class="form-control">
                    <span id="error-firstname-update" class="help-block" v-if="showErrorFirstnameUpdate">{{$t('firstname_validation')}}</span>
                  </div>
                </div>
                <div id="group-lastname-update" class="form-group" v-bind:class="{ 'has-error': showErrorLastnameUpdate }">
                  <label class="col-sm-3 control-label" for="txLastName">{{$t('last_name')}}</label>
                  <div class="col-sm-9">
                    <input type="text" id="txLastNameUpdate" v-model="lastnameUpdate" class="form-control">
                    <span id="error-lastname-update" class="help-block" v-if="showErrorLastnameUpdate">{{$t('lastname_validation')}}</span>
                  </div>
                </div>
                <div id="group-dob-update" class="form-group" v-bind:class="{ 'has-error': showErrorDobUpdate }">
                  <label class="col-sm-3 control-label" for="txDob">{{$t('date_of_birth')}}</label>
                  <div class="col-sm-9">
                    <input type="text" id="txDobUpdate" class="form-control bootstrap-datepicker" data-date-format='dd/mm/yyyy' >
                    <span id="error-dob-update" class="help-block" v-if="showErrorDobUpdate">{{$t('dob_validation')}}</span>
                  </div>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">{{$t('cancel')}}</button>
              <button type="button" class="btn btn-primary" id="btSaveUpdate" v-on:click="btSaveUpdateClick">{{$t('save')}}</button>
            </div>
          </div>
        </div>
      </div>

      <!-- modal to confirm user deletion -->
      <div class="modal fade" id="modalUserDelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <span class="pficon pficon-close"></span>
              </button>
              <h4 class="modal-title" id="modalUserDeleteLabel">{{$t('delete_user')}}</h4>
            </div>
            <div class="modal-body">
              <div class="alert alert-warning alert-dismissable">
                <span class="pficon pficon-warning-triangle-o"></span>
                <strong>Warning!</strong><span class="margin-left">{{$t('delete_user_description')}}</span>.
              </div>
              <form class="form-horizontal">
                <div class="form-group">
                  <label class="col-sm-3 control-label">Are you sure?</label>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">{{$t('cancel')}}</button>
              <button type="button" class="btn btn-danger"  id="btConfirmDelete" v-on:click="btConfirmDeleteClick">{{$t('delete')}}</button>
            </div>
          </div>
        </div>
      </div>

      <div class="container-fluid">
        <div id="users" class="list-group list-view-pf list-view-pf-view">
          <div v-for="user in users" v-bind:key="user.id" class="list-group-item">
            <div class="list-view-pf-actions">
              <button class="btn btn-default" v-bind:id="'edit_' + user.id" data-toggle="modal" data-target="#modalUserUpdate"
                  v-on:click="editClick">{{$t('edit')}}</button>
              <div class="dropdown pull-right dropdown-kebab-pf">
                <button class="btn btn-link dropdown-toggle" type="button" id="dropdownKebabRight9" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="true">
                  <span class="fa fa-ellipsis-v"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownKebabRight9">
                  <li><a href="#" v-bind:id="'delete_' + user.id" data-toggle="modal" data-target="#modalUserDelete"
                      v-on:click="deleteClick">Delete</a></li>
                </ul>
              </div>
            </div>
            <div class="list-view-pf-main-info">
              <div class="list-view-pf-left">
                <span class="fa fa-linux list-view-pf-icon-sm"></span>
              </div>
              <div class="list-view-pf-body">
                <div class="list-view-pf-description">
                  <div class="list-group-item-heading">
                    {{ user.firstname }} {{ user.lastname }}
                  </div>
                  <div class="list-group-item-text">
                    {{ user.dob }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
var nethserver = window.nethserver;
var $ = window.$;

export default {
  name: 'Users',
  props: {
    msg: String
  },
  data() {
    return {
      users: [],
      current_id_user: -1,
      firstnameCreate: '',
      lastnameCreate: '',
      firstnameUpdate: '',
      lastnameUpdate: '',
      showLoader: true,
      showContent: false,
      showErrorFirstnameCreate: false,
      showErrorLastnameCreate: false,
      showErrorDobCreate: false,
      showErrorFirstnameUpdate: false,
      showErrorLastnameUpdate: false,
      showErrorDobUpdate: false
    };
  },
  methods: {
    init: function() {
      // toggle dropdown menu
      $('#pf-list-standard .list-view-pf-actions').on('show.bs.dropdown', function () {
        var $this = $(this);
        var $dropdown = $this.find('.dropdown');
        var space = $(window).height() - $dropdown[0].getBoundingClientRect().top - $this.find('.dropdown-menu').outerHeight(true);
        $dropdown.toggleClass('dropup', space < 10);
      });
      // initialize datepicker
      $('.bootstrap-datepicker').datepicker({
        autoclose: true,
        todayBtn: "linked",
        todayHighlight: true,
        dateFormat: 'dd-mm-yy'
      });
      this.getConfig()
    },
    getConfig: function() {
      var context = this;
      nethserver.exec(
        ["cockpit-test-module/read"],
        {},
        null,
        function (success) {
          try {
            success = JSON.parse(success);
            context.users = success.configuration.users
            context.showLoader = false
            context.showContent = true
          } catch (e) {
            console.error(e) /* eslint-disable-line no-console */
          }
        },
        function (error) {
          console.error(error) /* eslint-disable-line no-console */
        }
      );
    },
    btAddUserClick: function() {
      // remove validation errors from modal
      this.showErrorFirstnameCreate = false;
      this.showErrorLastnameCreate = false;
      this.showErrorDobCreate = false;
    },
    btSaveCreateClick: function() {
      // remove validation errors from modal
      this.showErrorFirstnameCreate = false;
      this.showErrorLastnameCreate = false;
      this.showErrorDobCreate = false;
      var dobCreate = $('#txDobCreate').val();

      var userObj = {
        "firstname": this.firstnameCreate,
        "lastname": this.lastnameCreate,
        "dob": dobCreate
      }
      var context = this;
      nethserver.exec(
          ["cockpit-test-module/validate"],
          userObj,
          null,
          function (success) { // eslint-disable-line no-unused-vars
              // update values
              nethserver.exec(
                  ["cockpit-test-module/create"],
                  userObj,
                  null,
                  function (success) { // eslint-disable-line no-unused-vars
                      // success - hide notifications after 3 seconds
                      parent.ns.$children[0].notifications.success.show = true
                      parent.ns.$children[0].notifications.success.message = "User created"
                      setTimeout(function () {
                          parent.ns.$children[0].notifications.success.show = false;
                      }, 3000)
                      context.getConfig();
                      $('#modalUserCreate').modal('hide')
                  },
                  function (error) {
                      // error
                      parent.ns.$children[0].notifications.error.show = true
                      parent.ns.$children[0].notifications.error.message = "Error: " + error
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
                      if (param === 'firstname') {
                        context.showErrorFirstnameCreate = true;
                      } else if (param === 'lastname') {
                        context.showErrorLastnameCreate = true;
                      } else if (param === 'dob') {
                        context.showErrorDobCreate = true;
                      }
                  }
              } catch (e) {
                  console.error(e) /* eslint-disable-line no-console */
              }
          }
      );
    },
    editClick: function(e) {
      // remove validation errors from modal
      this.showErrorFirstnameUpdate = false;
      this.showErrorLastnameUpdate = false;
      this.showErrorDobUpdate = false;

      var id_string = e.target.id.substring(5);
      var id = parseInt(id_string);
      var user_to_update = this.users.find(user => user.id === id);
      this.current_id_user = id;
      this.firstnameUpdate = user_to_update.firstname
      this.lastnameUpdate = user_to_update.lastname
      $('#txDobUpdate').val(user_to_update.dob)
    },
    btSaveUpdateClick: function() {
      // remove validation errors from modal
      this.showErrorFirstnameUpdate = false;
      this.showErrorLastnameUpdate = false;
      this.showErrorDobUpdate = false;
      var dobUpdate = $('#txDobUpdate').val();
      var userObj = {
        "id": this.current_id_user,
        "firstname": this.firstnameUpdate,
        "lastname": this.lastnameUpdate,
        "dob": dobUpdate
      }
      var context = this;
      nethserver.exec(
          ["cockpit-test-module/validate"],
          userObj,
          null,
          function (success) { // eslint-disable-line no-unused-vars
              // update values
              nethserver.exec(
                  ["cockpit-test-module/update"],
                  userObj,
                  null,
                  function (success) { // eslint-disable-line no-unused-vars
                      // success - hide notifications after 3 seconds
                      parent.ns.$children[0].notifications.success.show = true
                      parent.ns.$children[0].notifications.success.message = "User updated"
                      setTimeout(function () {
                          parent.ns.$children[0].notifications.success.show = false;
                      }, 3000)
                      context.getConfig();
                      $('#modalUserUpdate').modal('hide');
                  },
                  function (error) {
                      // error
                      parent.ns.$children[0].notifications.error.show = true
                      parent.ns.$children[0].notifications.error.message = "Error: " + error
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
                    if (param === 'firstname') {
                      context.showErrorFirstnameUpdate = true;
                    } else if (param === 'lastname') {
                      context.showErrorLastnameUpdate = true;
                    } else if (param === 'dob') {
                      context.showErrorDobUpdate = true;
                    }
                }
            } catch (e) {
                console.error(e) /* eslint-disable-line no-console */
            }
          }
      );
    },
    deleteClick: function(e) {
      var id_string = e.target.id.substring(7);
      this.current_id_user = parseInt(id_string);
    },
    btConfirmDeleteClick: function() {
      var idObj = {
        "id": this.current_id_user
      }
      var context = this;
      nethserver.exec(
          ["cockpit-test-module/delete"],
          idObj,
          null,
          function (success) { // eslint-disable-line no-unused-vars
            // success - hide notifications after 3 seconds
            parent.ns.$children[0].notifications.success.show = true
            parent.ns.$children[0].notifications.success.message = "User deleted"
            setTimeout(function () {
                parent.ns.$children[0].notifications.success.show = false;
            }, 3000)
            context.getConfig();
            $('#modalUserDelete').modal('hide');
          },
            function (error) {
            // error
            parent.ns.$children[0].notifications.error.show = true
            parent.ns.$children[0].notifications.error.message = "Error: " + error
            console.error(error); /* eslint-disable-line no-console */
      });
    }
  },
  mounted() {
    this.init()
  }
}
</script>

<style>
.margin-left {
  margin-left: 5px;
}
</style>
