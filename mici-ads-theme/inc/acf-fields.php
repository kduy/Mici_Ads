<?php
/**
 * Mici Ads Theme — ACF Local Field Groups
 *
 * Registers all ACF field groups for the front page.
 * Field names MUST match what template-parts/landing-*.php reads via get_field().
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
// 1. Hero Section
// -------------------------------------------------------------------------
acf_add_local_field_group(
	array(
		'key'      => 'group_mici_hero',
		'title'    => '① Hero Section',
		'location' => $front_page_location,
		'menu_order' => 0,
		'fields'   => array(
			array( 'key' => 'field_hero_proof_text',         'name' => 'proof_text',         'label' => 'Proof Text (e.g. "500+ khách hàng hài lòng")',    'type' => 'text' ),
			array( 'key' => 'field_hero_title',              'name' => 'hero_title',         'label' => 'Hero Title (supports <br> <em>)',                  'type' => 'textarea', 'rows' => 3 ),
			array( 'key' => 'field_hero_subtitle',           'name' => 'hero_subtitle',      'label' => 'Hero Subtitle',                                   'type' => 'textarea', 'rows' => 3 ),
			array( 'key' => 'field_hero_cta_primary_text',   'name' => 'cta_primary_text',   'label' => 'CTA Primary — Button Text',  'type' => 'text' ),
			array( 'key' => 'field_hero_cta_primary_url',    'name' => 'cta_primary_url',    'label' => 'CTA Primary — URL',          'type' => 'url' ),
			array( 'key' => 'field_hero_cta_secondary_text', 'name' => 'cta_secondary_text', 'label' => 'CTA Secondary — Button Text', 'type' => 'text' ),
			array( 'key' => 'field_hero_cta_secondary_url',  'name' => 'cta_secondary_url',  'label' => 'CTA Secondary — URL',         'type' => 'url' ),
			array(
				'key'        => 'field_hero_gallery_repeater',
				'name'       => 'hero_gallery',
				'label'      => 'Gallery Preview (4 items)',
				'type'       => 'repeater',
				'layout'     => 'block',
				'sub_fields' => array(
					array( 'key' => 'field_hg_image',      'name' => 'image',      'label' => 'Image (leave empty for text card)', 'type' => 'image', 'return_format' => 'array' ),
					array( 'key' => 'field_hg_label',      'name' => 'label',      'label' => 'Card Label (e.g. "OLIVIA")',         'type' => 'text' ),
					array( 'key' => 'field_hg_sublabel',   'name' => 'sublabel',   'label' => 'Card Sublabel',                     'type' => 'text' ),
					array( 'key' => 'field_hg_background', 'name' => 'background', 'label' => 'Card Background CSS',               'type' => 'text', 'placeholder' => 'linear-gradient(135deg, #1a1a2e, #16213e)' ),
					array( 'key' => 'field_hg_text_color', 'name' => 'text_color', 'label' => 'Card Text Color',                   'type' => 'text', 'placeholder' => '#f4c2c2' ),
				),
			),
		),
	)
);

// -------------------------------------------------------------------------
// 2. Trust Bar
// -------------------------------------------------------------------------
acf_add_local_field_group(
	array(
		'key'        => 'group_mici_trust',
		'title'      => '② Trust Bar',
		'location'   => $front_page_location,
		'menu_order' => 1,
		'fields'     => array(
			array( 'key' => 'field_trust_text', 'name' => 'trust_text', 'label' => 'Trust Statement', 'type' => 'text' ),
			array(
				'key'        => 'field_trust_clients_repeater',
				'name'       => 'trust_clients',
				'label'      => 'Client Names',
				'type'       => 'repeater',
				'layout'     => 'table',
				'sub_fields' => array(
					array( 'key' => 'field_tc_name', 'name' => 'name', 'label' => 'Client Name', 'type' => 'text' ),
				),
			),
		),
	)
);

// -------------------------------------------------------------------------
// 3. Services
// -------------------------------------------------------------------------
acf_add_local_field_group(
	array(
		'key'        => 'group_mici_services',
		'title'      => '③ Services Section',
		'location'   => $front_page_location,
		'menu_order' => 2,
		'fields'     => array(
			array( 'key' => 'field_services_section_title', 'name' => 'section_title', 'label' => 'Section Title', 'type' => 'text' ),
			array(
				'key'        => 'field_services_repeater',
				'name'       => 'services',
				'label'      => 'Services',
				'type'       => 'repeater',
				'layout'     => 'block',
				'sub_fields' => array(
					array( 'key' => 'field_service_image_url',   'name' => 'image_url',   'label' => 'Image URL',                   'type' => 'url' ),
					array( 'key' => 'field_service_title',       'name' => 'title',       'label' => 'Title',                       'type' => 'text' ),
					array( 'key' => 'field_service_description', 'name' => 'description', 'label' => 'Description',                 'type' => 'textarea', 'rows' => 3 ),
					array( 'key' => 'field_service_tags',        'name' => 'tags',        'label' => 'Tags (comma separated)',       'type' => 'text' ),
				),
			),
		),
	)
);

// -------------------------------------------------------------------------
// 4. Tagline
// -------------------------------------------------------------------------
acf_add_local_field_group(
	array(
		'key'        => 'group_mici_tagline',
		'title'      => '④ Tagline',
		'location'   => $front_page_location,
		'menu_order' => 3,
		'fields'     => array(
			array( 'key' => 'field_tagline_text', 'name' => 'tagline_text', 'label' => 'Tagline (supports <em>)', 'type' => 'text' ),
		),
	)
);

// -------------------------------------------------------------------------
// 5. Portfolio Marquee
// -------------------------------------------------------------------------
acf_add_local_field_group(
	array(
		'key'        => 'group_mici_portfolio',
		'title'      => '⑤ Portfolio Marquee',
		'location'   => $front_page_location,
		'menu_order' => 4,
		'fields'     => array(
			array(
				'key'           => 'field_portfolio_images',
				'name'          => 'portfolio_images',
				'label'         => 'Portfolio Images (auto-split into 3 columns)',
				'type'          => 'gallery',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'instructions'  => 'Upload 9-12 images. They will be auto-distributed across 3 scrolling columns.',
			),
		),
	)
);

// -------------------------------------------------------------------------
// 6. Benefits
// -------------------------------------------------------------------------
acf_add_local_field_group(
	array(
		'key'        => 'group_mici_benefits',
		'title'      => '⑥ Benefits Section',
		'location'   => $front_page_location,
		'menu_order' => 5,
		'fields'     => array(
			array( 'key' => 'field_benefits_section_title', 'name' => 'benefits_section_title', 'label' => 'Section Title', 'type' => 'text' ),
			array(
				'key'        => 'field_benefits_repeater',
				'name'       => 'benefits',
				'label'      => 'Benefits',
				'type'       => 'repeater',
				'layout'     => 'block',
				'sub_fields' => array(
					array( 'key' => 'field_benefit_icon',        'name' => 'icon',        'label' => 'Icon (SVG code)', 'type' => 'textarea', 'rows' => 2 ),
					array( 'key' => 'field_benefit_title',       'name' => 'title',       'label' => 'Title',           'type' => 'text' ),
					array( 'key' => 'field_benefit_description', 'name' => 'description', 'label' => 'Description',     'type' => 'textarea', 'rows' => 3 ),
				),
			),
		),
	)
);

// -------------------------------------------------------------------------
// 7. Testimonials
// -------------------------------------------------------------------------
acf_add_local_field_group(
	array(
		'key'        => 'group_mici_testimonials',
		'title'      => '⑦ Testimonials Section',
		'location'   => $front_page_location,
		'menu_order' => 6,
		'fields'     => array(
			array( 'key' => 'field_testimonials_section_title', 'name' => 'testimonials_section_title', 'label' => 'Section Title', 'type' => 'text' ),
			array(
				'key'        => 'field_testimonials_repeater',
				'name'       => 'testimonials',
				'label'      => 'Testimonials',
				'type'       => 'repeater',
				'layout'     => 'block',
				'sub_fields' => array(
					array( 'key' => 'field_testimonial_quote',          'name' => 'quote',          'label' => 'Quote',                    'type' => 'textarea', 'rows' => 4 ),
					array( 'key' => 'field_testimonial_name',           'name' => 'name',           'label' => 'Name',                     'type' => 'text' ),
					array( 'key' => 'field_testimonial_role',           'name' => 'role',           'label' => 'Role / Location',           'type' => 'text' ),
					array( 'key' => 'field_testimonial_avatar_initial', 'name' => 'avatar_initial', 'label' => 'Avatar Initial (1 letter)', 'type' => 'text' ),
					array(
						'key'           => 'field_testimonial_rating',
						'name'          => 'rating',
						'label'         => 'Rating (1-5)',
						'type'          => 'number',
						'min'           => 1,
						'max'           => 5,
						'default_value' => 5,
					),
				),
			),
		),
	)
);

// -------------------------------------------------------------------------
// 8. FAQ
// -------------------------------------------------------------------------
acf_add_local_field_group(
	array(
		'key'        => 'group_mici_faq',
		'title'      => '⑧ FAQ Section',
		'location'   => $front_page_location,
		'menu_order' => 7,
		'fields'     => array(
			array( 'key' => 'field_faq_section_title', 'name' => 'faq_section_title', 'label' => 'Section Title', 'type' => 'text' ),
			array(
				'key'        => 'field_faq_repeater',
				'name'       => 'faq_items',
				'label'      => 'FAQ Items',
				'type'       => 'repeater',
				'layout'     => 'block',
				'sub_fields' => array(
					array( 'key' => 'field_faq_question', 'name' => 'question', 'label' => 'Question', 'type' => 'text' ),
					array( 'key' => 'field_faq_answer',   'name' => 'answer',   'label' => 'Answer',   'type' => 'textarea', 'rows' => 4 ),
				),
			),
		),
	)
);

// -------------------------------------------------------------------------
// 9. CTA Footer
// -------------------------------------------------------------------------
acf_add_local_field_group(
	array(
		'key'        => 'group_mici_cta_footer',
		'title'      => '⑨ CTA Footer Section',
		'location'   => $front_page_location,
		'menu_order' => 8,
		'fields'     => array(
			array( 'key' => 'field_cta_footer_title',    'name' => 'cta_title',    'label' => 'CTA Title (supports <em> <br>)', 'type' => 'text' ),
			array( 'key' => 'field_cta_footer_subtitle', 'name' => 'cta_subtitle', 'label' => 'CTA Button Text',               'type' => 'text' ),
		),
	)
);

// -------------------------------------------------------------------------
// 10. Contact
// -------------------------------------------------------------------------
acf_add_local_field_group(
	array(
		'key'        => 'group_mici_contact',
		'title'      => '⑩ Contact Section',
		'location'   => $front_page_location,
		'menu_order' => 9,
		'fields'     => array(
			array( 'key' => 'field_contact_title',      'name' => 'contact_title',    'label' => 'Contact Title',    'type' => 'text' ),
			array( 'key' => 'field_contact_subtitle',   'name' => 'contact_subtitle', 'label' => 'Contact Subtitle', 'type' => 'text' ),
			array( 'key' => 'field_contact_wpforms_id', 'name' => 'wpforms_id',       'label' => 'WPForms ID (leave empty for default form)', 'type' => 'number' ),
		),
	)
);
