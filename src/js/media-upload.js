/* global wp */
const ucfSectionMediaUpload = ($) => {
  let cssFrame;
  let jsFrame;

  const $metaBox     = $('#ucf_section_metabox');
  const $addCssLink  = $metaBox.find('.css-upload');
  const $addJsLink   = $metaBox.find('.js-upload');
  const $cssInput    = $metaBox.find('#ucf_section_stylesheet');
  const $cssFilename = $metaBox.find('#ucf_section_css_filename');
  const $cssPreview  = $metaBox.find('.css-preview');
  const $delCssLink  = $metaBox.find('.css-remove');
  const $delJsLink   = $metaBox.find('.js-remove');
  const $jsInput     = $metaBox.find('#ucf_section_javascript');
  const $jsFilename  = $metaBox.find('#ucf_section_js_filename');
  const $jsPreview   = $metaBox.find('.js-preview');

  const addCss = (e) => {
    e.preventDefault();

    if (cssFrame) {
      cssFrame.open();
      return;
    }

    cssFrame = wp.media({
      title: 'Select or upload the stylesheet for this section.',
      button: {
        text: 'Use this stylesheet'
      },
      library: {
        text: 'text/css'
      },
      multiple: false
    });

    cssFrame.on('select', () => {
      const attachment = cssFrame.state().get('selection').first().toJSON();
      $cssPreview.removeClass('hidden');
      $cssInput.val(attachment.id);
      $cssFilename.text(attachment.filename);
      $addCssLink.addClass('hidden');
      $delCssLink.removeClass('hidden');
    });

    cssFrame.open();
  };

  const addJs = (e) => {
    e.preventDefault();

    if (jsFrame) {
      jsFrame.open();
      return;
    }

    jsFrame = wp.media({
      title: 'Select or upload the JavaScript for this section.',
      button: {
        text: 'Use this JavaScript'
      },
      library: {
        type: 'application/javascript'
      },
      multiple: false
    });

    jsFrame.on('select', () => {
      const attachment = jsFrame.state().get('selection').first().toJSON();
      $jsPreview.removeClass('hidden');
      $jsInput.val(attachment.id);
      $jsFilename.text(attachment.filename);
      $addJsLink.addClass('hidden');
      $delJsLink.removeClass('hidden');
    });

    jsFrame.open();
  };

  const removeMedia = (e) => {
    e.preventDefault();

    const $target = $(e.target);
    const isCss = $target.hasClass('css-remove');

    if (isCss) {
      $cssPreview.addClass('hidden');
      $addCssLink.removeClass('hidden');
      $delCssLink.addClass('hidden');
      $cssInput.val('');
      $cssFilename.text('');
    } else {
      $jsPreview.addClass('hidden');
      $addJsLink.removeClass('hidden');
      $delJsLink.addClass('hidden');
      $jsInput.val('');
      $jsFilename.text('');
    }
  };

  $addCssLink.on('click', addCss);
  $addJsLink.on('click', addJs);
  $delCssLink.on('click', removeMedia);
  $delJsLink.on('click', removeMedia);
};

if (typeof jQuery !== 'undefined') {
  jQuery(document).ready(($) => {
    ucfSectionMediaUpload($);
  });
}
