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
  var SF = function() {
    this.parameters = new ParameterBag();
    this.flashes = new FlashBag();
  };

  SF.prototype = {
    renderFlashes: function(flashes, selector) {
      selector = selector || this.parameters.get('flashes_selector');
      flashes = flashes || this.flashes.all();
      var template = _.template(
        '<div class="sf-flash alert alert-<%= type %> fade in alert-block">' +
          '<a class="close" data-dismiss="alert" href="#">×</a>' +
          '<%= message %>' +
          '</div>'
      );

      $.each(flashes, function(type, messages) {
        $.each(messages, function(i, message) {
          $(selector).append(template({
            type: type,
            message: message.message
          }));
        })
      });

      $('html, body').animate({
        scrollTop: 0
      }, 1000);
    },

    clearFlashes: function(selector) {
      $('.sf-flash', selector).remove();
    }
  };

  SF.prototype.fn = SF.prototype;

  /**
   * Add alias for FOSJsRoutingBundle Routing.generate method
   */
  if ('undefined' !== typeof window['Routing']) {
    SF.prototype.path = function(route, parameters, absolute) {
      return Routing.generate(route, parameters, absolute);
    };
  }

  window.SF = new SF();

  $.ajaxSetup({
    beforeSend: function(jqXHR, settings) {
      jqXHR.setRequestHeader('X-SF-Ajax', '1');
    }
  });

  $(document).ajaxComplete(function(event, xhr, settings) {
    var flashesHeader = xhr.getResponseHeader('X-SF-Flashes');
    if (flashesHeader) {
      var flashes = $.parseJSON(flashesHeader);
      SF.renderFlashes(flashes);
    }
  });

})(jQuery);