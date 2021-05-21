(function($) {
  // ParameterBag
  var ParameterBag = function (parameters) {
    parameters = parameters || {};

    this.parameters = parameters;
  };

  ParameterBag.prototype = {
    all: function () {
      return this.parameters;
    },

    keys: function () {
      var parameters = [];

      $.each(this.parameters, function (i, parameter) {
        parameters.push(parameter);
      });

      return parameters;
    },

    replace: function (parameters) {
      parameters = parameters || {};
      this.parameters = parameters;

      return this;
    },

    add: function (parameters) {
      parameters = parameters || {};
      $.extend(this.parameters, parameters);

      return this;
    },

    get: function (name, defaultValue) {
      defaultValue = defaultValue || null;

      return this.has(name) ? this.parameters[name] : defaultValue;
    },

    set: function (key, value) {
      this.parameters[key] = value;

      return this;
    },

    has: function (key) {
      return this.parameters.hasOwnProperty(key);
    },

    remove: function (key) {
      delete this.parameters[key];

      return this;
    }
  };

  ParameterBag.prototype.fn = ParameterBag.prototype;

  var ServiceContainer = function () {
    this.services = {};
  };

  ServiceContainer.prototype = {
    has: function (id) {
      return this.services.hasOwnProperty(id);
    },

    get: function (id) {
      if (!this.has(id)) {
        // error
      }

      return this.services[id];
    },

    set: function (id, service) {
      this.services[id] = service;

      return this;
    }
  };

  ServiceContainer.prototype.fn = ServiceContainer.prototype;

  // AjaxData
  var AjaxData = function (data) {
    this.data = data;
  };

  AjaxData.prototype = {
    has: function (name) {
      return this.data.hasOwnProperty(name);
    },

    get: function (name, defaultValue) {
      defaultValue = defaultValue || null;

      return this.has(name) ? this.data[name] : defaultValue;
    },

    all: function () {
      return this.data;
    }
  };

  // AjaxDataBag
  var AjaxDataBag = function () {
    this.requests = {};
  };

  AjaxDataBag.prototype = {
    add: function (id, data) {
      this.requests[id] = new AjaxData(data);

      return this.requests[id];
    },

    has: function (id) {
      return this.requests.hasOwnProperty(id);
    },

    get: function (id, defaultValue) {
      defaultValue = defaultValue || null;

      return this.has(id) ? this.requests[id] : defaultValue;
    },

    remove: function (id) {
      delete this.requests[id];

      return this;
    }
  };

  AjaxDataBag.prototype.fn = AjaxDataBag.prototype;

  // SF
  var SF = function () {
    this.services = new ServiceContainer();
  };

  SF.prototype = {
    parameters: new ParameterBag(),
    ajaxRequests: new AjaxDataBag(),
    ajaxParameters: function (jqXHR) {
      var id = jqXHR.sfId;

      var ajaxData = this.ajaxRequests.get(id);
      var parameters = ajaxData.get('ajax_parameters', {});

      return new ParameterBag(parameters);
    },
    initialize: function () {},
    util: {
      extend: function (Child, Parent, methods) {
        methods = methods || {};

        Child.prototype = Object.create(Parent.prototype);
        Child.prototype.constructor = Child;
        Child.superclass = Parent.prototype;

        $.each(methods, function (name, method) {
          Child.prototype[name] = method;
        });

        return Child;
      }
    },
    callbacks: {
      convert: function (response) {
        var parentArguments = arguments.callee.caller.arguments;

        var jqXHR = parentArguments[2];
        var id = jqXHR.sfId;

        var headerData = {};
        var headers = {};
        var match;
        var rx = /^X-SF-(.*?):[ \t]*([^\r\n]*)$/mgi;
        var responseHeadersString = jqXHR.getAllResponseHeaders();
        while ((match = rx.exec(responseHeadersString))) {
          headers[match[1].toLowerCase()] = match[2];
        }

        $.each(headers, function (name, value) {
          name = name.replace(/-+/g, '_');

          headerData[name] = $.parseJSON(value);
        });

        var rawBodyData = {};
        var hasBodyData = null !== jqXHR.getResponseHeader('X-SF-Body-Data');
        if (hasBodyData) {
          if ('string' === typeof response) {
            // html/text content type
            rawBodyData = $.parseJSON(response);
            response = rawBodyData['_sf_data'];
            delete rawBodyData['_sf_data'];
          } else if ('object' === typeof response) {
            // json content type
            rawBodyData = response;
            response = response['_sf_data'];
            delete rawBodyData['_sf_data'];
          }
        }
        var bodyData = {};
        $.each(rawBodyData, function (name, value) {
          name = name.replace(/^_sf_/, '');

          bodyData[name] = value;
        });

        var data = $.extend(true, {}, headerData, bodyData);

        _SF.ajaxRequests.add(id, data);

        $(document).trigger('ite-pre-ajax-complete', data);

        return response;
      }
    },
    routeCallbacks: {},
    classes: {
      ParameterBag: ParameterBag,
      ServiceContainer: ServiceContainer,
      AjaxDataBag: AjaxDataBag,
      AjaxData: AjaxData
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
    routeName = '*' === routeName ? '.+' : routeName;

    if ('undefined' !== typeof this.routeCallbacks[routeName]) {
      this.routeCallbacks[routeName].push({
        callback: callback,
        ajax: ajax
      });
    } else {
      this.routeCallbacks[routeName] = [{
        callback: callback,
        ajax: ajax
      }];
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
            if (callbackData.ajax || (!callbackData.ajax && !ajax)) {
              callbackData.callback.apply(window, [routeName]);
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
    SF.prototype.path = function (route, parameters) {
      return Routing.generate(route, parameters, false);
    };

    SF.prototype.url = function (route, parameters) {
      return Routing.generate(route, parameters, true);
    };
  }

  SF.prototype.fn = SF.prototype;

  var _SF = window.SF = new SF();

  var jqXhrId = 0;
  $.ajaxSetup({
    contents: {
      sf: /sf/
    },
    beforeSend: function (jqXHR, settings) {
      // add custom sf ajax header
      jqXHR.setRequestHeader('X-SF-Ajax', '1');
      // add custom content type
      settings.dataTypes.push('sf');
      // save xhr id
      jqXHR.sfId = jqXhrId++;
    },
    converters: {
      'html sf': _SF.callbacks.convert,
      'text sf': _SF.callbacks.convert,
      'json sf': _SF.callbacks.convert
    }
  });
  
  $(document)
    .ajaxComplete(function(e, jqXHR, options) {
      var id = jqXHR.sfId;
      var request = _SF.ajaxRequests.get(id);

      if (request) {
        var data = request.all();

        $(document).trigger('ite-post-ajax-complete', data);

        _SF.ajaxRequests.remove(id);
      }
    })
    .on('ite-pre-ajax-complete', function (e, data) {
      if (data.hasOwnProperty('redirect')) {
        if (window.location.origin + data['redirect'] !== window.location.href) {
          window.location.href = data['redirect'];
        } else {
          window.location.reload();
        }
      }
      if (data.hasOwnProperty('parameters')) {
        _SF.parameters.add(data['parameters']);
      }
    })
    .on('ite-post-ajax-complete', function (e, data) {
      if (data.hasOwnProperty('route')) {
        _SF.trigger(data['route'], true);
      }
    })
  ;

  $(function() {
    if (_SF.parameters.has('route')) {
      _SF.trigger(_SF.parameters.get('route'));
    }
    _SF.initialize();
  });

})(jQuery);