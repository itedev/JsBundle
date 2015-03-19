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
        var flashesHeader = xhr.getResponseHeader('X-SF-Flashes');
        if (flashesHeader) {
          var flashes = $.parseJSON(flashesHeader);
          window.SF.ui.renderFlashes(flashes);
        }

        var parametersHeader = xhr.getResponseHeader('X-SF-Parameters');
        if (parametersHeader) {
          var parameters = $.parseJSON(parametersHeader);
          window.SF.parameters.add(parameters);
        }

        var ajaxBlocksHeader = xhr.getResponseHeader('X-SF-Ajax-Content');
        if (ajaxBlocksHeader) {
          settings.dataType = 'ite_content';
        }
      },
      parseSfResponse: function (rawData) {
        var data = null;

        if (typeof rawData != 'object') {
          data = $.parseJSON(rawData);
        } else {
          data = rawData;
        }

        $(document).trigger('ite-ajax-loaded.content', data);

        return data.data;
      }
    },
    callbacks: {},
    classes: {
      ParameterBag: ParameterBag,
      FlashBag: FlashBag
    },
    ui : {}
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
      settings.dataTypes.push('ite_content');
    },
    contents: {
      ite_content: /ite_content/
    },
    converters: {
      "json ite_content": window.SF.util.parseSfResponse,
      "html ite_content": window.SF.util.parseSfResponse,
      "xml ite_content": window.SF.util.parseSfResponse
    }
  });

  $(document).ajaxComplete(function(event, xhr, settings) {
    window.SF.util.processXhr(xhr, settings);
  });



})(jQuery);