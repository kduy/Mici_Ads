<?php
/**
 * Mici Ads Theme — ACF Local Field Groups (Free ACF Compatible)
 *
 * Uses only field types available in free ACF: text, textarea, url, number, image, group.
 * NO repeater, gallery, or flexible content (those require ACF PRO).
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	return;
}

// Shared location rule: front page.
$front_page_location = array(
	array(
		array(
			'param'    => 'page_type',
			'operator' => '==',
			'value'    => 'front_page',
		),
	),
);

// -------------------------------------------------------------------------
// ① Hero Section
// -------------------------------------------------------------------------
$hero_gallery_fields = array();
for ( $i = 1; $i <= 4; $i++ ) {
	$hero_gallery_fields[] = array(
		'key'        => 'field_hero_item_' . $i,
		'name'       => 'hero_item_' . $i,
		'label'      => 'Gallery Item ' . $i,
		'type'       => 'group',
		'layout'     => 'block',
		'sub_fields' => array(
			array( 'key' => 'field_hi' . $i . '_image',      'name' => 'image',      'label' => 'Image (leave empty for text card)', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),
			array( 'key' => 'field_hi' . $i . '_label',      'name' => 'label',      'label' => 'Card Label (e.g. "OLIVIA")',         'type' => 'text' ),
			array( 'key' => 'field_hi' . $i . '_sublabel',   'name' => 'sublabel',   'label' => 'Card Sublabel',                     'type' => 'text' ),
			array( 'key' => 'field_hi' . $i . '_background', 'name' => 'background', 'label' => 'Card Background CSS',               'type' => 'text', 'placeholder' => 'linear-gradient(135deg, #1a1a2e, #16213e)' ),
			array( 'key' => 'field_hi' . $i . '_text_color', 'name' => 'text_color', 'label' => 'Card Text Color',                   'type' => 'text', 'placeholder' => '#f4c2c2' ),
		),
	);
}

acf_add_local_field_group(
	array(
		'key'        => 'group_mici_hero',
		'title'      => '① Hero Section',
		'location'   => $front_page_location,
		'menu_order' => 0,
		'fields'     => array_merge(
			array(
				array( 'key' => 'field_hero_proof_text',         'name' => 'proof_text',         'label' => 'Proof Text (e.g. "500+ khách hàng hài lòng")',   'type' => 'text' ),
				array( 'key' => 'field_hero_title',              'name' => 'hero_title',         'label' => 'Hero Title (supports &lt;br&gt; &lt;em&gt;)',    'type' => 'textarea', 'rows' => 3 ),
				array( 'key' => 'field_hero_subtitle',           'name' => 'hero_subtitle',      'label' => 'Hero Subtitle',                                  'type' => 'textarea', 'rows' => 3 ),
				array( 'key' => 'field_hero_cta_primary_text',   'name' => 'cta_primary_text',   'label' => 'CTA Primary — Button Text',  'type' => 'text' ),
				array( 'key' => 'field_hero_cta_primary_url',    'name' => 'cta_primary_url',    'label' => 'CTA Primary — URL',          'type' => 'url' ),
				array( 'key' => 'field_hero_cta_secondary_text', 'name' => 'cta_secondary_text', 'label' => 'CTA Secondary — Button Text', 'type' => 'text' ),
				array( 'key' => 'field_hero_cta_secondary_url',  'name' => 'cta_secondary_url',  'label' => 'CTA Secondary — URL',         'type' => 'url' ),
			),
			$hero_gallery_fields
		),
	)
);

// -------------------------------------------------------------------------
// ② Trust Bar
// -------------------------------------------------------------------------
acf_add_local_field_group(
	array(
		'key'        => 'group_mici_trust',
		'title'      => '② Trust Bar',
		'location'   => $front_page_location,
		'menu_order' => 1,
		'fields'     => array(
			array( 'key' => 'field_trust_text',         'name' => 'trust_text',         'label' => 'Trust Statement',                          'type' => 'text' ),
			array( 'key' => 'field_trust_clients_list', 'name' => 'trust_clients_list', 'label' => 'Client Names (one per line)',               'type' => 'textarea', 'rows' => 6, 'instructions' => 'Enter one client name per line. e.g.:\nOlivia Nails\nPho Ha Noi\nBREW Coffee' ),
		),
	)
);

// -------------------------------------------------------------------------
// ③ Services (4 groups)
// -------------------------------------------------------------------------
$service_fields = array(
	array( 'key' => 'field_services_section_title', 'name' => 'section_title', 'label' => 'Section Title (supports &lt;em&gt;)', 'type' => 'text' ),
);
for ( $i = 1; $i <= 4; $i++ ) {
	$service_fields[] = array(
		'key'        => 'field_service_' . $i,
		'name'       => 'service_' . $i,
		'label'      => 'Service ' . $i,
		'type'       => 'group',
		'layout'     => 'block',
		'sub_fields' => array(
			array( 'key' => 'field_s' . $i . '_image',       'name' => 'image',       'label' => 'Image',                    'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),
			array( 'key' => 'field_s' . $i . '_title',       'name' => 'title',       'label' => 'Title',                    'type' => 'text' ),
			array( 'key' => 'field_s' . $i . '_description', 'name' => 'description', 'label' => 'Description',              'type' => 'textarea', 'rows' => 3 ),
			array( 'key' => 'field_s' . $i . '_tags',        'name' => 'tags',        'label' => 'Tags (comma separated)',    'type' => 'text' ),
		),
	);
}
acf_add_local_field_group(
	array(
		'key'        => 'group_mici_services',
		'title'      => '③ Services Section',
		'location'   => $front_page_location,
		'menu_order' => 2,
		'fields'     => $service_fields,
	)
);

// -------------------------------------------------------------------------
// ④ Tagline
// -------------------------------------------------------------------------
acf_add_local_field_group(
	array(
		'key'        => 'group_mici_tagline',
		'title'      => '④ Tagline',
		'location'   => $front_page_location,
		'menu_order' => 3,
		'fields'     => array(
			array( 'key' => 'field_tagline_text', 'name' => 'tagline_text', 'label' => 'Tagline (supports &lt;em&gt;)', 'type' => 'text' ),
		),
	)
);

// -------------------------------------------------------------------------
// ⑤ Portfolio Marquee (12 individual image fields)
// -------------------------------------------------------------------------
$portfolio_fields = array();
for ( $i = 1; $i <= 12; $i++ ) {
	$col = ( ( $i - 1 ) % 3 ) + 1;
	$portfolio_fields[] = array(
		'key'           => 'field_portfolio_image_' . $i,
		'name'          => 'portfolio_image_' . $i,
		'label'         => 'Image ' . $i . ' (Column ' . $col . ')',
		'type'          => 'image',
		'return_format' => 'array',
		'preview_size'  => 'medium',
		'instructions'  => $i <= 9 ? '' : 'Optional — leave empty if not needed.',
	);
}
acf_add_local_field_group(
	array(
		'key'        => 'group_mici_portfolio',
		'title'      => '⑤ Portfolio Marquee',
		'location'   => $front_page_location,
		'menu_order' => 4,
		'fields'     => $portfolio_fields,
	)
);

// -------------------------------------------------------------------------
// ⑥ Benefits (6 groups)
// -------------------------------------------------------------------------
$benefit_fields = array(
	array( 'key' => 'field_benefits_section_title', 'name' => 'benefits_section_title', 'label' => 'Section Title (supports &lt;em&gt;)', 'type' => 'text' ),
);
for ( $i = 1; $i <= 6; $i++ ) {
	$benefit_fields[] = array(
		'key'        => 'field_benefit_' . $i,
		'name'       => 'benefit_' . $i,
		'label'      => 'Benefit ' . $i,
		'type'       => 'group',
		'layout'     => 'block',
		'sub_fields' => array(
			array( 'key' => 'field_b' . $i . '_title',       'name' => 'title',       'label' => 'Title',       'type' => 'text' ),
			array( 'key' => 'field_b' . $i . '_description', 'name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 3 ),
		),
	);
}
acf_add_local_field_group(
	array(
		'key'        => 'group_mici_benefits',
		'title'      => '⑥ Benefits Section',
		'location'   => $front_page_location,
		'menu_order' => 5,
		'fields'     => $benefit_fields,
	)
);

// -------------------------------------------------------------------------
// ⑦ Testimonials (4 groups)
// -------------------------------------------------------------------------
$testimonial_fields = array(
	array( 'key' => 'field_testimonials_section_title', 'name' => 'testimonials_section_title', 'label' => 'Section Title (supports &lt;em&gt;)', 'type' => 'text' ),
);
for ( $i = 1; $i <= 4; $i++ ) {
	$testimonial_fields[] = array(
		'key'        => 'field_testimonial_' . $i,
		'name'       => 'testimonial_' . $i,
		'label'      => 'Testimonial ' . $i,
		'type'       => 'group',
		'layout'     => 'block',
		'sub_fields' => array(
			array( 'key' => 'field_t' . $i . '_quote',          'name' => 'quote',          'label' => 'Quote',                    'type' => 'textarea', 'rows' => 4 ),
			array( 'key' => 'field_t' . $i . '_name',           'name' => 'name',           'label' => 'Name',                     'type' => 'text' ),
			array( 'key' => 'field_t' . $i . '_role',           'name' => 'role',           'label' => 'Role / Location',           'type' => 'text' ),
			array( 'key' => 'field_t' . $i . '_avatar_initial', 'name' => 'avatar_initial', 'label' => 'Avatar Initial (1 letter)', 'type' => 'text' ),
			array( 'key' => 'field_t' . $i . '_rating',         'name' => 'rating',         'label' => 'Rating (1-5)',              'type' => 'number', 'min' => 1, 'max' => 5, 'default_value' => 5 ),
		),
	);
}
acf_add_local_field_group(
	array(
		'key'        => 'group_mici_testimonials',
		'title'      => '⑦ Testimonials Section',
		'location'   => $front_page_location,
		'menu_order' => 6,
		'fields'     => $testimonial_fields,
	)
);

// -------------------------------------------------------------------------
// ⑧ FAQ (8 groups)
// -------------------------------------------------------------------------
$faq_fields = array(
	array( 'key' => 'field_faq_section_title', 'name' => 'faq_section_title', 'label' => 'Section Title (supports &lt;em&gt;)', 'type' => 'text' ),
);
for ( $i = 1; $i <= 8; $i++ ) {
	$faq_fields[] = array(
		'key'        => 'field_faq_' . $i,
		'name'       => 'faq_' . $i,
		'label'      => 'FAQ ' . $i . ( $i > 5 ? ' (optional)' : '' ),
		'type'       => 'group',
		'layout'     => 'block',
		'sub_fields' => array(
			array( 'key' => 'field_f' . $i . '_question', 'name' => 'question', 'label' => 'Question', 'type' => 'text' ),
			array( 'key' => 'field_f' . $i . '_answer',   'name' => 'answer',   'label' => 'Answer',   'type' => 'textarea', 'rows' => 4 ),
		),
	);
}
acf_add_local_field_group(
	array(
		'key'        => 'group_mici_faq',
		'title'      => '⑧ FAQ Section',
		'location'   => $front_page_location,
		'menu_order' => 7,
		'fields'     => $faq_fields,
	)
);

// -------------------------------------------------------------------------
// ⑨ CTA Footer
// -------------------------------------------------------------------------
acf_add_local_field_group(
	array(
		'key'        => 'group_mici_cta_footer',
		'title'      => '⑨ CTA Footer Section',
		'location'   => $front_page_location,
		'menu_order' => 8,
		'fields'     => array(
			array( 'key' => 'field_cta_footer_title',    'name' => 'cta_title',    'label' => 'CTA Title (supports &lt;em&gt; &lt;br&gt;)', 'type' => 'text' ),
			array( 'key' => 'field_cta_footer_subtitle', 'name' => 'cta_subtitle', 'label' => 'CTA Button Text',                            'type' => 'text' ),
		),
	)
);

// -------------------------------------------------------------------------
// ⑩ Contact
// -------------------------------------------------------------------------
acf_add_local_field_group(
	array(
		'key'        => 'group_mici_contact',
		'title'      => '⑩ Contact Section',
		'location'   => $front_page_location,
		'menu_order' => 9,
		'fields'     => array(
			array( 'key' => 'field_contact_title',      'name' => 'contact_title',    'label' => 'Contact Title',                            'type' => 'text' ),
			array( 'key' => 'field_contact_subtitle',   'name' => 'contact_subtitle', 'label' => 'Contact Subtitle',                         'type' => 'text' ),
			array( 'key' => 'field_contact_wpforms_id', 'name' => 'wpforms_id',       'label' => 'WPForms ID (leave empty for default form)', 'type' => 'number' ),
		),
	)
);
