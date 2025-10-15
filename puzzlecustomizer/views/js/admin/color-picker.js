(function ($) {
  'use strict';
  $(document).ready(function () {
    $('.puzzle-color-input').spectrum && $('.puzzle-color-input').spectrum({
      preferredFormat: 'hex',
      showInput: true
    });
  });
})(jQuery);
