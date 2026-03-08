<?php
/**
 * Mici Ads Theme — ACF Local Field Groups
 *
 * Registers all ACF field groups for the front page using local JSON.
 * Groups: Hero, Services, Benefits, Testimonials, FAQ, CTA Footer, Contact.
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	return;
}

// -------------------------------------------------------------------------
// Shared location rule: front page
// -------------------------------------------------------------------------
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
// Hero
// -------------------------------------------------------------------------
acf_add_local_field_group(
	array(
		'key'      => 'group_mici_hero',
		'title'    => 'Hero Section',
		'location' => $front_page_location,
		'fields'   => array(
			array( 'key' => 'field_hero_proof_text',        'name' => 'proof_text',        'label' => 'Proof Text',           'type' => 'text' ),
			array( 'key' => 'field_hero_title',             'name' => 'title',             'label' => 'Title',                'type' => 'textarea', 'rows' => 3 ),
			array( 'key' => 'field_hero_subtitle',          'name' => 'subtitle',          'label' => 'Subtitle',             'type' => 'textarea', 'rows' => 3 ),
			array( 'key' => 'field_hero_cta_primary_text',  'name' => 'cta_primary_text',  'label' => 'CTA Primary Text',     'type' => 'text' ),
			array( 'key' => 'field_hero_cta_primary_url',   'name' => 'cta_primary_url',   'label' => 'CTA Primary URL',      'type' => 'url' ),
			array( 'key' => 'field_hero_cta_secondary_text','name' => 'cta_secondary_text','label' => 'CTA Secondary Text',   'type' => 'text' ),
			array( 'key' => 'field_hero_cta_secondary_url', 'name' => 'cta_secondary_url', 'label' => 'CTA Secondary URL',    'type' => 'url' ),
		),
	)
);

// -------------------------------------------------------------------------
// Services
// -------------------------------------------------------------------------
acf_add_local_field_group(
	array(
		'key'      => 'group_mici_services',
		'title'    => 'Services Section',
		'location' => $front_page_location,
		'fields'   => array(
			array( 'key' => 'field_services_section_title', 'name' => 'section_title', 'label' => 'Section Title', 'type' => 'text' ),
			array(
				'key'        => 'field_services_repeater',
				'name'       => 'services',
				'label'      => 'Services',
				'type'       => 'repeater',
				'layout'     => 'block',
				'sub_fields' => array(
					array( 'key' => 'field_service_image',       'name' => 'image',       'label' => 'Image',       'type' => 'image',    'return_format' => 'array' ),
					array( 'key' => 'field_service_title',       'name' => 'title',       'label' => 'Title',       'type' => 'text' ),
					array( 'key' => 'field_service_description', 'name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 3 ),
					array( 'key' => 'field_service_tags',        'name' => 'tags',        'label' => 'Tags (comma separated)', 'type' => 'text' ),
				),
			),
		),
	)
);

// -------------------------------------------------------------------------
// Benefits
// -------------------------------------------------------------------------
acf_add_local_field_group(
	array(
		'key'      => 'group_mici_benefits',
		'title'    => 'Benefits Section',
		'location' => $front_page_location,
		'fields'   => array(
			array( 'key' => 'field_benefits_section_title', 'name' => 'section_title', 'label' => 'Section Title', 'type' => 'text' ),
			array(
				'key'        => 'field_benefits_repeater',
				'name'       => 'benefits',
				'label'      => 'Benefits',
				'type'       => 'repeater',
				'layout'     => 'block',
				'sub_fields' => array(
					array( 'key' => 'field_benefit_icon',        'name' => 'icon',        'label' => 'Icon (SVG/class)', 'type' => 'text' ),
					array( 'key' => 'field_benefit_title',       'name' => 'title',       'label' => 'Title',           'type' => 'text' ),
					array( 'key' => 'field_benefit_description', 'name' => 'description', 'label' => 'Description',     'type' => 'textarea', 'rows' => 3 ),
				),
			),
		),
	)
);

// -------------------------------------------------------------------------
// Testimonials
// -------------------------------------------------------------------------
acf_add_local_field_group(
	array(
		'key'      => 'group_mici_testimonials',
		'title'    => 'Testimonials Section',
		'location' => $front_page_location,
		'fields'   => array(
			array( 'key' => 'field_testimonials_section_title', 'name' => 'section_title', 'label' => 'Section Title', 'type' => 'text' ),
			array(
				'key'        => 'field_testimonials_repeater',
				'name'       => 'testimonials',
				'label'      => 'Testimonials',
				'type'       => 'repeater',
				'layout'     => 'block',
				'sub_fields' => array(
					array( 'key' => 'field_testimonial_quote',          'name' => 'quote',          'label' => 'Quote',          'type' => 'textarea', 'rows' => 4 ),
					array( 'key' => 'field_testimonial_name',           'name' => 'name',           'label' => 'Name',           'type' => 'text' ),
					array( 'key' => 'field_testimonial_role',           'name' => 'role',           'label' => 'Role',           'type' => 'text' ),
					array( 'key' => 'field_testimonial_location',       'name' => 'location',       'label' => 'Location',       'type' => 'text' ),
					array( 'key' => 'field_testimonial_avatar_initial', 'name' => 'avatar_initial', 'label' => 'Avatar Initial', 'type' => 'text' ),
					array(
						'key'       => 'field_testimonial_rating',
						'name'      => 'rating',
						'label'     => 'Rating',
						'type'      => 'number',
						'min'       => 1,
						'max'       => 5,
						'default_value' => 5,
					),
				),
			),
		),
	)
);

// -------------------------------------------------------------------------
// FAQ
// -------------------------------------------------------------------------
acf_add_local_field_group(
	array(
		'key'      => 'group_mici_faq',
		'title'    => 'FAQ Section',
		'location' => $front_page_location,
		'fields'   => array(
			array( 'key' => 'field_faq_section_title', 'name' => 'section_title', 'label' => 'Section Title', 'type' => 'text' ),
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
// CTA Footer
// -------------------------------------------------------------------------
acf_add_local_field_group(
	array(
		'key'      => 'group_mici_cta_footer',
		'title'    => 'CTA Footer Section',
		'location' => $front_page_location,
		'fields'   => array(
			array( 'key' => 'field_cta_footer_title',    'name' => 'cta_title',    'label' => 'CTA Title',    'type' => 'text' ),
			array( 'key' => 'field_cta_footer_subtitle', 'name' => 'cta_subtitle', 'label' => 'CTA Subtitle', 'type' => 'text' ),
		),
	)
);

// -------------------------------------------------------------------------
// Contact
// -------------------------------------------------------------------------
acf_add_local_field_group(
	array(
		'key'      => 'group_mici_contact',
		'title'    => 'Contact Section',
		'location' => $front_page_location,
		'fields'   => array(
			array( 'key' => 'field_contact_title',     'name' => 'contact_title',    'label' => 'Contact Title',    'type' => 'text' ),
			array( 'key' => 'field_contact_subtitle',  'name' => 'contact_subtitle', 'label' => 'Contact Subtitle', 'type' => 'text' ),
			array( 'key' => 'field_contact_wpforms_id','name' => 'wpforms_id',       'label' => 'WPForms ID',       'type' => 'number' ),
		),
	)
);
