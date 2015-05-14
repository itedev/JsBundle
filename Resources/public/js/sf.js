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

  // SF
  var SF = function() {};

  SF.prototype = {
    parameters: new ParameterBag(),
    util: {
      processXhr: function(xhr, settings) {
        var routeName = xhr.getResponseHeader('X-SF-Route');
        var parameters = xhr.getResponseHeader('X-SF-Parameters');

        if (routeName) {
          window.SF.trigger(routeName, true);
        }

        if (parameters) {
          parameters = $.parseJSON(parameters);
          window.SF.parameters.add(parameters);
        }

      }
    },
    callbacks: {},
    routeCallbacks: {},
    classes: {
      ParameterBag: ParameterBag
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
  SF.prototype.on = function(routeName, callback, ajax) {
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
  SF.prototype.trigger = function(routeName, ajax) {
    ajax = ajax || false;
    $.each(this.routeCallbacks, function(name, callbacks) {
      var routeNames = name.split(' ');

      $.each(routeNames, function(key, value) {
        var regExp = new RegExp(value);

        if (regExp.test(routeName)) {
          $.each(callbacks, function(index, callbackData) {
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
    beforeSend: function(jqXHR, settings) {
      jqXHR.setRequestHeader('X-SF-Ajax', '1');
    }
  });

  $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
    // @todo: maybe, another dataType option my exist?
    var dataType = options.dataTypes[0];

    /**
     * Injects processor into jQuery ajax functions.
     *
     * @param object
     * @param callbackName
     */
    function injectProcessor(object, callbackName) {
      var originalCallback = object[callbackName];
      if (!originalCallback) {
        return;
      }

      object[callbackName] = function() {

        var redirect = jqXHR.getResponseHeader('X-SF-Redirect');

        if (redirect) {
          location.href = redirect;
          return;
        }

        var originalArguments = $.merge([], arguments);

        if (jqXHR.getResponseHeader('X-SF-Data')) {
          var data = null;

          if ('undefined' !== typeof arguments[0] ) {
            data = arguments[0].promise ? null : arguments[0];
          }
          if (!data) {
            data = jqXHR.responseText;
          }
          if ('json' !== dataType || 'string' === typeof data) {
            data = $.parseJSON(data);
          }

          $(document).trigger('ite-ajax-loaded.content', data);
          originalArguments[0] = data['_sf_data'];
        }
        originalCallback.apply(this, originalArguments);
        if (jqXHR.getResponseHeader('X-SF-Data')) {
          $(document).trigger('ite-ajax-after-load.content', data);
        }
      }
    }

    injectProcessor(options, 'success');
    injectProcessor(options, 'error');
    injectProcessor(jqXHR, 'done');
    injectProcessor(jqXHR, 'fail');
    injectProcessor(jqXHR, 'always');

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