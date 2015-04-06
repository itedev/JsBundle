(function($) {
  // ParameterBag
  var ParameterBag = function() {
    this.parameters = {};
  };

  ParameterBag.prototype = {
    all: function() {
      return this.parameters;
    },

    keys: function() {
      var parameters = [];

      $.each(this.parameters, function(i, parameter) {
        parameters.push(parameter);
      });

      return parameters;
    },

    replace: function(parameters) {
      parameters = parameters || {};
      this.parameters = parameters;
    },

    add: function(parameters) {
      parameters = parameters || {};
      this.parameters = $.extend(this.parameters, parameters);
    },

    get: function(name, defaultValue) {
      defaultValue = defaultValue || null;
      return this.has(name) ? this.parameters[name] : defaultValue;
    },

    set: function(key, value) {
      this.parameters[key] = value;
    },

    has: function(key) {
      return this.parameters.hasOwnProperty(key);
    },

    remove: function(key) {
      delete this.parameters[key];
    }
  };

  ParameterBag.prototype.fn = ParameterBag.prototype;

  // FlashBag
  var FlashBag = function() {
    this.flashes = {};
  };

  FlashBag.prototype = {
    has: function(type) {
      return this.flashes.hasOwnProperty(type);
    },

    keys: function() {
      var types = [];

      $.each(this.flashes, function(i, type) {
        types.push(type);
      });

      return types;
    },

    add: function(type, message) {
      if (!this.has(type)) {
        this.flashes[type] = [];
      }
      this.flashes[type].push(message);
    },

    get: function(type, defaultValue) {
      defaultValue = defaultValue || [];
      if (!this.has(type)) {
        return defaultValue;
      }
      var _return = this.flashes[type];

      delete this.flashes[type];

      return _return;
    },

    set: function(type, messages) {
      this.flashes[type] = messages;
    },

    setAll: function(messages) {
      this.flashes = messages;
    },

    clear: function() {
      return this.all();
    },

    all: function() {
      var _return = this.peekAll();
      this.flashes = {};
      return _return;
    },

    peek: function(type, defaultValue) {
      defaultValue = defaultValue || [];
      return this.has(type) ? this.flashes[type] : defaultValue;
    },

    peekAll: function() {
      return this.flashes;
    }
  };

  FlashBag.prototype.fn = FlashBag.prototype;

  // SF
  var SF = function() {};

  SF.prototype = {
    parameters: new ParameterBag(),
    flashes: new FlashBag(),
    util: {
      processXhr: function(xhr, settings) {

        var routeName = xhr.getResponseHeader('X-SF-Route');

        if (routeName) {
          this.trigger(routeName, true);
        }
      },
      parseSfResponse: function(rawData) {
        var data = null;

        if ('object' !== typeof rawData) {
          if(rawData.indexOf('_sf_data') < 0) {
            return rawData;
          }
          data = $.parseJSON(rawData);
        } else {
          data = rawData;
        }
        if(typeof data._sf_data == 'undefined') {
          return data;
        }

        $(document).trigger('ite-ajax-loaded.content', data);

        return data._sf_data;
      }
    },
    callbacks: {},
    routeCallbacks: {},
    classes: {
      ParameterBag: ParameterBag,
      FlashBag: FlashBag
    },
    ui : {}
  };

  /**
   * Call method only if current route matches routeName. Works also for AJAX requests too.
   *
   * @param {string} routeName The Route name (can be regexp)
   * @param {function} callback The callback that will be invoked if route match
   * @param {bool} ajax Specify does this method will be called either with AJAX response.
   */
  SF.prototype.on = function (routeName, callback, ajax) {
    ajax = ajax || false;
    if (typeof this.routeCallbacks[routeName] != 'undefined') {
      this.routeCallbacks[routeName].push({callback: callback, ajax: ajax});
    } else {
      this.routeCallbacks[routeName] = [{callback: callback, ajax: ajax}];
    }
  };

  /**
   * Trigger all binded methods for route.
   *
   * @param {string} routeName
   * @param {bool} ajax
   */
  SF.prototype.trigger = function (routeName, ajax) {
    ajax = ajax || false;
    $.each(this.routeCallbacks, function (name, callbacks) {
      var routeNames = name.split(' ');

      $.each(routeNames, function (key, value) {
        var regExp = new RegExp(value);

        if (regExp.test(routeName)) {
          $.each(callbacks, function (index, callbackData) {
            if(callbackData.ajax || (!callbackData.ajax && !ajax)) {
              callbackData.callback.apply();
            }
          });
        }
      });

    });
  };

  /**
   * Add aliases for FOSJsRoutingBundle Routing.generate method
   */
  if ('undefined' !== typeof window['Routing']) {
    SF.prototype.path = function(route, parameters) {
      return Routing.generate(route, parameters, false);
    };

    SF.prototype.url = function(route, parameters) {
      return Routing.generate(route, parameters, true);
    };
  }

  SF.prototype.fn = SF.prototype;

  window.SF = new SF();

  $.ajaxSetup({
    beforeSend: function (jqXHR, settings) {
      jqXHR.setRequestHeader('X-SF-Ajax', '1');
      settings.dataTypes.push('sf_content');
    },
    contents: {
      sf_content: /sf_content/
    },
    converters: {
      "json sf_content": window.SF.util.parseSfResponse,
      "html sf_content": window.SF.util.parseSfResponse,
      "xml sf_content": window.SF.util.parseSfResponse
    }
  });

  $(document).ajaxComplete(function(event, xhr, settings) {
    window.SF.util.processXhr(xhr, settings);
  });

  $(document).ready(function () {
    if (window.SF.parameters.has('route')) {
      window.SF.trigger(window.SF.parameters.get('route'));
    }
  });



})(jQuery);