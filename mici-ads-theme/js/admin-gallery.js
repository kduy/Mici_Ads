/**
 * Admin gallery uploader for Design CPT.
 * Opens WP Media Library in multi-select mode, stores comma-separated attachment IDs.
 * Max 30 images per design.
 */
jQuery(function($) {
  var $container = $('#mici-gallery-images');
  var $input = $('#mici-gallery-ids');
  var maxImages = 30;

  // Open media library to add images
  $('#mici-gallery-add').on('click', function(e) {
    e.preventDefault();
    var frame = wp.media({
      title: 'Select Gallery Images',
      button: { text: 'Add to Gallery' },
      multiple: true,
      library: { type: 'image' }
    });

    frame.on('select', function() {
      var currentIds = $input.val() ? $input.val().split(',').filter(Boolean) : [];
      frame.state().get('selection').each(function(attachment) {
        var id = String(attachment.id);
        if (currentIds.length >= maxImages || currentIds.indexOf(id) !== -1) return;
        currentIds.push(id);
        var thumb = (attachment.attributes.sizes && attachment.attributes.sizes.thumbnail)
          ? attachment.attributes.sizes.thumbnail.url
          : attachment.attributes.url;
        $container.append(
          '<div class="mici-gallery-item" data-id="' + id + '" style="position:relative;">' +
            '<img src="' + thumb + '" style="width:80px;height:80px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">' +
            '<button type="button" class="mici-gallery-remove" style="position:absolute;top:-6px;right:-6px;background:#e00;color:#fff;border:none;border-radius:50%;width:20px;height:20px;font-size:12px;cursor:pointer;line-height:1;">&times;</button>' +
          '</div>'
        );
      });
      $input.val(currentIds.join(','));
    });

    frame.open();
  });

  // Remove a single image from gallery
  $container.on('click', '.mici-gallery-remove', function() {
    var $item = $(this).closest('.mici-gallery-item');
    var removeId = String($item.data('id'));
    $item.remove();
    var ids = $input.val().split(',').filter(function(v) { return v && v !== removeId; });
    $input.val(ids.join(','));
  });
});
