/**
 * Created by sam0delkin on 19.03.2015.
 */
(function ($) {
  $(document).on('ite-ajax-loaded.content', function (e, contentData) {
    var blocks = 'undefined' !== typeof contentData.blocks ? contentData.blocks : [];

    $.each(blocks, function(selector, blockData) {
      var $element = $(selector);
      $element.on('ite-show.block', function() {
        $(this).html(blockData.content);
        $(this)[blockData.show_animation.type](blockData.show_animation.length);
      });

      var event = $.Event('ite-before-show.block');
      $element.trigger(event, [blockData]);
      if (false === event.result) {
        return;
      }

      $element
        .hide()
        .trigger('ite-show.block', blockData)
      ;
    });
  });
})(jQuery);