(function($) {
  // ParameterBag
  var ParameterBag = function() {};
  ParameterBag.prototype.fn = ParameterBag.prototype;
  ParameterBag = new ParameterBag();

  ParameterBag.fn.parameters = {};

  ParameterBag.fn.all = function() {
    return this.parameters;
  };

  ParameterBag.fn.keys = function() {
    return _.keys(this.parameters);
  };

  ParameterBag.fn.replace = function(parameters) {
    parameters = parameters || {};
    this.parameters = parameters;
  };

  ParameterBag.fn.add = function(parameters) {
    parameters = parameters || {};
    this.parameters = $.extend(this.parameters, parameters);
  };

  ParameterBag.fn.get = function(name, defaultValue) {
    defaultValue = defaultValue || null;
    return this.has(name) ? this.parameters[name] : defaultValue;
  };

  ParameterBag.fn.set = function(key, value) {
    this.parameters[key] = value;
  };

  ParameterBag.fn.has = function(key) {
    return _.has(this.parameters, key);
  };

  ParameterBag.fn.remove = function(key) {
    return _.omit(this.parameters, key);
  };

  // FlashBag
  var FlashBag = function() {};
  FlashBag.prototype.fn = FlashBag.prototype;
  FlashBag = new FlashBag();

  FlashBag.fn.flashes = {};

  FlashBag.fn.has = function(type) {
    return _.has(this.flashes, type);
  };

  FlashBag.fn.keys = function() {
    return _.keys(this.flashes);
  };

  FlashBag.fn.add = function(type, message) {
    if (!this.has(type)) {
      this.flashes[type] = [];
    }
    this.flashes[type].push(message);
  };

  FlashBag.fn.get = function(type, defaultValue) {
    defaultValue = defaultValue || [];
    if (!this.has(type)) {
      return defaultValue;
    }
    var _return = this.flashes[type];

    delete this.flashes[type];

    return _return;
  };

  FlashBag.fn.set = function(type, messages) {
    this.flashes[type] = messages;
  };

  FlashBag.fn.setAll = function(messages) {
    this.flashes = messages;
  };

  FlashBag.fn.clear = function() {
    return this.all();
  };

  FlashBag.fn.all = function() {
    var _return = this.peekAll();
    this.flashes = {};
    return _return;
  };

  FlashBag.fn.peek = function(type, defaultValue) {
    defaultValue = defaultValue || [];
    return this.has(type) ? this.flashes[type] : defaultValue;
  };

  FlashBag.fn.peekAll = function() {
    return this.flashes;
  };

  // SF
  var SF = function() {};
  SF.prototype.fn = SF.prototype;
  SF = new SF();

  SF.fn.parameters = ParameterBag;
  SF.fn.flashes = FlashBag;

  /**
   * Render flashes
   *
   * @param selector
   * @param flashes
   */
  SF.fn.renderFlashes = function(flashes, selector) {
    selector = selector || SF.parameters.get('flashes_selector');
    flashes = flashes || this.flashes.all();
    var template = _.template(
      '<div class="sf-flash alert alert-<%= type %> fade in alert-block">' +
        '<a class="close" data-dismiss="alert" href="#">×</a>' +
        '<%= message %>' +
        '</div>'
    );
    _.each(flashes, function(messages, type) {
      _.each(messages, function(options) {
        $(selector).append(template({
          type: type,
          message: options.message
        }));
      });
    });

    $('html, body').animate({
      scrollTop: 0
    }, 1000);
  };

  /**
   * Clear flashes
   *
   * @param selector
   */
  SF.fn.clearFlashes = function(selector) {
    $('.sf-flash', selector).remove();
  };

  /**
   * Add alias for FOSJsRoutingBundle Routing.generate method
   */
  if ('undefined' !== typeof window['Routing']) {
    SF.fn.path = function(route, parameters, absolute) {
      return Routing.generate(route, parameters, absolute);
    };
  }

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

  window.SF = SF;

})(jQuery);