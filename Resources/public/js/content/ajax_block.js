/**
 * Created by sam0delkin on 19.03.2015.
 */
(function($) {
  $(document).on('ite-ajax-loaded.content', function (e, contentData) {
    var blocks = 'undefined' !== typeof contentData._sf_blocks ? contentData._sf_blocks : [];

    $.each(blocks, function(selector, blockData) {
      var $element = $(selector);

      var event = $.Event('ite-before-show.block');
      $element.trigger(event, [blockData]);
      if (false === event.result) {
        return;
      }

      var $content = $(blockData.content);
      $content.hide();

      $element
        .html($content)
        .trigger('ite-show.block', blockData)
      ;

      var afterShow = function() {
        $element.trigger('ite-shown.block', blockData);
      };

      var showAnimationLength = blockData.show_animation.length;
      switch (blockData.show_animation.type.toLowerCase()) {
        case 'fade':
          $content.fadeIn(showAnimationLength, afterShow);
          break;
        case 'slide':
          $content.slideDown(showAnimationLength, afterShow);
          break;
        case 'show':
          $content.show(showAnimationLength, afterShow);
          break;
        default:
          $content.show(null, afterShow);
          break;
      }

    });
  });
})(jQuery);