/**
 * Admin Image Processing Tools for Design CPT.
 * Handles tab switching, media selection, AJAX processing,
 * result display, and gallery integration.
 */
jQuery(function($) {
	var cfg = window.miciImageProc || {};

	// --- Tab switching ---
	$('.mici-proc__tab').on('click', function() {
		var panel = $(this).data('panel');
		$('.mici-proc__tab').removeClass('mici-proc__tab--active');
		$(this).addClass('mici-proc__tab--active');
		$('.mici-proc__panel').removeClass('mici-proc__panel--active');
		$('.mici-proc__panel[data-panel="' + panel + '"]').addClass('mici-proc__panel--active');
	});

	// --- Range slider value display ---
	$('.mici-proc__range').on('input', function() {
		$(this).siblings('.mici-proc__range-val').text(this.value + '%');
	});

	// --- Watermark source toggle ---
	$('[data-target="wm-source"]').on('change', function() {
		$('.mici-proc__wm-custom').toggle($(this).val() === 'custom');
	});

	// --- Media picker ---
	$('.mici-proc__pick').on('click', function(e) {
		e.preventDefault();
		var target = $(this).data('target');
		var type = $(this).data('type');
		var frame = wp.media({
			title: type === 'application/pdf' ? cfg.i18n.selectPdf : cfg.i18n.selectImage,
			button: { text: type === 'application/pdf' ? cfg.i18n.selectPdf : cfg.i18n.selectImage },
			multiple: false,
			library: { type: type }
		});

		frame.on('select', function() {
			var att = frame.state().get('selection').first().toJSON();
			$('.mici-proc__input-id[data-target="' + target + '"]').val(att.id);
			$('.mici-proc__filename[data-target="' + target + '"]').text(att.filename);
			var $preview = $('.mici-proc__preview[data-target="' + target + '"]');
			if ($preview.length && att.type === 'image') {
				var thumb = (att.sizes && att.sizes.thumbnail) ? att.sizes.thumbnail.url : att.url;
				$preview.html('<img src="' + thumb + '">');
			}
		});

		frame.open();
	});

	// --- AJAX processing ---
	$('.mici-proc__run').on('click', function() {
		var $btn = $(this);
		var action = $btn.data('action');
		var panel = $btn.closest('.mici-proc__panel').data('panel');
		var $status = $('.mici-proc__status[data-target="' + panel + '"]');
		var $result = $('.mici-proc__result[data-target="' + panel + '"]');

		// Gather input ID.
		var attachId = parseInt($('.mici-proc__input-id[data-target="' + panel + '"]').val(), 10);
		if (!attachId) {
			$status.text(cfg.i18n.selectImage).addClass('mici-proc__status--error');
			return;
		}

		// Build AJAX data per action.
		var data = {
			action: action,
			_ajax_nonce: cfg.nonce,
			post_id: cfg.postId,
			attachment_id: attachId
		};

		if (action === 'mici_compress_image') {
			data.quality = parseInt($('[data-target="compress-quality"]').val(), 10);
			data.format = $('[data-target="compress-format"]').val();
		} else if (action === 'mici_pdf_to_jpeg') {
			if (!cfg.hasImagick) {
				$status.text(cfg.i18n.noImagick).addClass('mici-proc__status--error');
				return;
			}
			data.dpi = parseInt($('[data-target="pdf-dpi"]').val(), 10);
			data.quality = parseInt($('[data-target="pdf-quality"]').val(), 10);
		} else if (action === 'mici_apply_watermark') {
			data.opacity = parseInt($('[data-target="wm-opacity"]').val(), 10);
			data.position = $('[data-target="wm-position"]').val();
			var wmSrc = $('[data-target="wm-source"]').val();
			data.watermark_id = wmSrc === 'custom'
				? parseInt($('.mici-proc__input-id[data-target="wm-custom"]').val(), 10) || 0
				: 0;
		}

		// UI feedback.
		$btn.prop('disabled', true);
		$status.removeClass('mici-proc__status--error')
			.html('<span class="mici-proc__spinner"></span>' + cfg.i18n.processing);
		$result.empty();

		$.post(cfg.ajaxUrl, data, function(res) {
			$btn.prop('disabled', false);
			$status.text('');

			if (!res.success) {
				$status.text(cfg.i18n.error + ' ' + (res.data || 'Unknown'))
					.addClass('mici-proc__status--error');
				return;
			}

			renderResult(panel, res.data, $result);
		}).fail(function() {
			$btn.prop('disabled', false);
			$status.text(cfg.i18n.error + ' Network error').addClass('mici-proc__status--error');
		});
	});

	// --- Render result cards ---
	function renderResult(panel, data, $result) {
		if (panel === 'pdf' && data.pages) {
			// Multiple page results.
			var ids = [];
			data.pages.forEach(function(pg) {
				ids.push(pg.attachment_id);
				$result.append(buildCard(pg.url, 'Page ' + pg.page_num, pg.attachment_id));
			});
			// "Add all to gallery" button.
			$result.append(
				'<div style="width:100%;margin-top:6px;">' +
					'<button type="button" class="button mici-proc__add-all" data-ids="' + ids.join(',') + '">' +
						cfg.i18n.addAllGallery +
					'</button>' +
				'</div>'
			);
		} else {
			// Single result (compress / watermark).
			var stats = '';
			if (data.original_size && data.new_size) {
				stats = formatBytes(data.original_size) + ' → ' + formatBytes(data.new_size) +
					' <span class="mici-proc__savings">(-' + data.savings_pct + '%)</span>';
			}
			$result.append(buildCard(data.url, stats, data.attachment_id));
		}
	}

	function buildCard(url, info, attachId) {
		return '<div class="mici-proc__result-card">' +
			'<img src="' + url + '">' +
			(info ? '<div class="mici-proc__stats">' + info + '</div>' : '') +
			'<button type="button" class="button mici-proc__add-gallery" data-id="' + attachId + '">' +
				cfg.i18n.addToGallery +
			'</button>' +
		'</div>';
	}

	// --- Gallery integration ---
	$(document).on('click', '.mici-proc__add-gallery', function() {
		addToGallery([$(this).data('id')]);
		$(this).text(cfg.i18n.saved).prop('disabled', true);
	});

	$(document).on('click', '.mici-proc__add-all', function() {
		var ids = String($(this).data('ids')).split(',').map(Number);
		addToGallery(ids);
		$(this).text(cfg.i18n.saved).prop('disabled', true);
		// Disable individual buttons too.
		$(this).closest('.mici-proc__result').find('.mici-proc__add-gallery').text(cfg.i18n.saved).prop('disabled', true);
	});

	function addToGallery(newIds) {
		var $input = $('#mici-gallery-ids');
		var $container = $('#mici-gallery-images');
		if (!$input.length) return;

		var current = $input.val() ? $input.val().split(',').filter(Boolean) : [];

		newIds.forEach(function(id) {
			id = String(id);
			if (current.length >= 30 || current.indexOf(id) !== -1) return;
			current.push(id);

			// Fetch thumbnail URL from WP API.
			wp.media.attachment(parseInt(id, 10)).fetch().then(function(att) {
				var thumb = (att.sizes && att.sizes.thumbnail) ? att.sizes.thumbnail.url : att.url;
				$container.append(
					'<div class="mici-gallery-item" data-id="' + id + '" style="position:relative;">' +
						'<img src="' + thumb + '" style="width:80px;height:80px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">' +
						'<button type="button" class="mici-gallery-remove" style="position:absolute;top:-6px;right:-6px;background:#e00;color:#fff;border:none;border-radius:50%;width:20px;height:20px;font-size:12px;cursor:pointer;line-height:1;">&times;</button>' +
					'</div>'
				);
			});
		});

		$input.val(current.join(','));
	}

	// --- Helpers ---
	function formatBytes(bytes) {
		if (bytes < 1024) return bytes + ' B';
		if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
		return (bytes / 1048576).toFixed(2) + ' MB';
	}
});
