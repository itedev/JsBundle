/**
 * Created by sam0delkin on 19.03.2015.
 */
(function ($) {
  $(document).on('ite-ajax-loaded.content', function (e, contentData) {
    var blocks = typeof contentData.blocks != undefined ? contentData.blocks : [];

    $.each(blocks, function(selector, blockData) {
      var $selector = $(selector);
      $selector.on('ite-show.block', function() {
        $(this).html(blockData.block_data);
        $(this)[blockData.show_animation](blockData.length);
      });

      var event = $.Event('ite-before-show.block');
      $selector.trigger(event, [blockData]);

      if(false === event.result) {
        return;
      }

      $selector.hide().trigger('ite-show.block', blockData);
    });
  });
})(jQuery);