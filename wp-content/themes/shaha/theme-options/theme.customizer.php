<?php
/**
 * Theme customizer
 *
 * @package WordPress
 * @subpackage Shaha
 * @since Shaha 1.0
 */


//--------------------------------------------------------------
//-- First run actions after switch theme
//--------------------------------------------------------------
if (!function_exists('shaha_customizer_action_switch_theme')) {
	add_action('after_switch_theme', 'shaha_customizer_action_switch_theme');
	function shaha_customizer_action_switch_theme() {
		// Duplshahate theme options between parent and child themes
		$duplshahate = shaha_get_theme_setting('duplshahate_options');
		if (in_array($duplshahate, array('child', 'both'))) {
			$theme_slug = get_option( 'template' );
			$theme_time = (int) get_option( "shaha_options_timestamp_{$theme_slug}" );
			$stylesheet_slug = get_option( 'stylesheet' );

			// If child-theme is activated - duplshahate options from template to the child-theme
			if ($theme_slug != $stylesheet_slug) {
				$stylesheet_time = (int) get_option( "shaha_options_timestamp_{$stylesheet_slug}" );
				if ($theme_time > $stylesheet_time) shaha_customizer_duplshahate_theme_options($theme_slug, $stylesheet_slug, $theme_time);
			
			// If main theme (template) is activated and 'duplshahate_options' == 'child'
			// (duplshahate options only from template to the child-theme) - regenerate CSS  with custom colors and fonts
			} else if ($duplshahate == 'child' && $theme_time > 0) {
				shaha_customizer_save_css();
			}
		}
	}
}


// Duplshahate theme options between template and child-theme
if (!function_exists('shaha_customizer_duplshahate_theme_options')) {
	function shaha_customizer_duplshahate_theme_options($from, $to, $timestamp = 0) {
		if ($timestamp == 0) $timestamp = get_option("shaha_options_timestamp_{$from}");
		$from = "theme_mods_{$from}";
		$from_options = get_option($from);
		$to = "theme_mods_{$to}";
		$to_options = get_option($to);
		if (is_array($from_options)) {
			if (!is_array($to_options)) $to_options = array();
			$theme_options = shaha_storage_get('options');
			foreach ($from_options as $k => $v) {
				if (isset($theme_options[$k])) $to_options[$k] = $v;
			}
			update_option($to, $to_options);
			update_option("shaha_options_timestamp_{$to}", $timestamp);
		}
	}
}


//--------------------------------------------------------------
//-- New panel in the Customizer Controls
//--------------------------------------------------------------

// Theme init priorities:
// 3 - add/remove Theme Options elements
if (!function_exists('shaha_customizer_setup3')) {
	add_action( 'after_setup_theme', 'shaha_customizer_setup3', 3 );
	function shaha_customizer_setup3() {
		shaha_storage_merge_array('options', '', array(
			'cpt' => array(
				"title" => esc_html__('Plugins settings', 'shaha'),
				"desc" => '',
				"priority" => 400,
				"type" => "panel"
				)
			)
		);
	}
}
// 3 - add/remove Theme Options elements
if (!function_exists('shaha_customizer_setup4')) {
	add_action( 'after_setup_theme', 'shaha_customizer_setup4', 4 );
	function shaha_customizer_setup4() {
		shaha_storage_merge_array('options', '', array(
			'cpt_end' => array(
				"type" => "panel_end"
				)
			)
		);
	}
}


//--------------------------------------------------------------
//-- Register Customizer Controls
//--------------------------------------------------------------

define('SHAHA_CUSTOMIZE_PRIORITY', 200);		// Start priority for the new controls

// Regoster custom controls for the customizer
if (!function_exists('shaha_customizer_custom_controls')) {
	add_action( 'customize_register', 'shaha_customizer_custom_controls' );
	function shaha_customizer_custom_controls( $wp_customize ) {
		require_once SHAHA_THEME_DIR . 'theme-options/theme.customizer.controls.php';
	}
}

// Parse Theme Options and add controls to the customizer
if (!function_exists('shaha_customizer_register_controls')) {
	add_action( 'customize_register', 'shaha_customizer_register_controls', 20);
	function shaha_customizer_register_controls( $wp_customize ) {

		$refresh_auto = shaha_get_theme_setting('custmize_refresh') != 'manual';
		
		$panels = array('');
		$p = 0;
		$sections = array('');
		$s = 0;
		
		$i = SHAHA_CUSTOMIZE_PRIORITY;

		// Reload Theme Options before create controls
		if (is_admin()) {
			shaha_storage_set('options_reloaded', true);
			shaha_load_theme_options();
		}
		$options = shaha_storage_get('options');
		
		foreach ($options as $id=>$opt) {
			
			$i = !empty($opt['priority']) 
					? $opt['priority'] 
					: (in_array($opt['type'], array('panel', 'section'))
							? SHAHA_CUSTOMIZE_PRIORITY
							: $i++
						);
			
			if (!empty($opt['hidden'])) continue;

			if (!isset($opt['title'])) $opt['title'] = '';
			if (!isset($opt['desc'])) $opt['desc'] = '';
			
			$transport = $refresh_auto && (!isset($opt['refresh']) || $opt['refresh']===true) ? 'refresh' : 'postMessage';

			if ($opt['type'] == 'panel') {

				if ($p > 0) {
					array_pop($panels);
					$p--;
				}
				if ($s > 0) {
					array_pop($sections);
					$s--;
				}

				$sec = $wp_customize->get_panel( $id );
				if ( is_object($sec) && !empty($sec->title) ) {
					$sec->title      = $opt['title'];
					$sec->description= $opt['desc'];
					if ( !empty($opt['priority']) )	$sec->priority = $opt['priority'];
					if ( !empty($opt['active_callback']) )	$sec->active_callback = $opt['active_callback'];
				} else {
					$wp_customize->add_panel( esc_attr($id) , array(
						'title'      => $opt['title'],
						'description'=> $opt['desc'],
						'priority'	 => $i,
						'active_callback' => !empty($opt['active_callback']) ? $opt['active_callback'] : ''
					) );
				}
				array_push($panels, $id);
				$p++;

			} else if ($opt['type'] == 'panel_end') {

				array_pop($panels);
				$p--;

			} else if ($opt['type'] == 'section') {

				if ($s > 0) {
					array_pop($sections);
					$s--;
				}

				$sec = $wp_customize->get_section( $id );
				if ( is_object($sec) && !empty($sec->title) ) {
					$sec->title      = $opt['title'];
					$sec->description= $opt['desc'];
					$sec->panel      = esc_attr($panels[$p]);
					if ( !empty($opt['priority']) )	$sec->priority = $opt['priority'];
					if ( !empty($opt['active_callback']) )	$sec->active_callback = $opt['active_callback'];
				} else {
					$wp_customize->add_section( esc_attr($id) , array(
						'title'      => $opt['title'],
						'description'=> $opt['desc'],
						'panel'      => esc_attr($panels[$p]),
						'priority'	 => $i,
						'active_callback' => !empty($opt['active_callback']) ? $opt['active_callback'] : ''
					) );
				}
				array_push($sections, $id);
				$s++;

			} else if ($opt['type'] == 'section_end') {

				array_pop($sections);
				$s--;

			} else if ($opt['type'] == 'select') {

				$wp_customize->add_setting( $id, array(
					'default'           => shaha_get_theme_option($id),
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $transport
				) );
			
				$wp_customize->add_control( $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority'	 => $i,
					'active_callback' => !empty($opt['active_callback']) ? $opt['active_callback'] : '',
					'type'     => 'select',
					'choices'  => apply_filters('shaha_filter_options_get_list_choises', $opt['options'], $id)
				) );

			} else if ($opt['type'] == 'radio') {

				$wp_customize->add_setting( $id, array(
					'default'           => shaha_get_theme_option($id),
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $transport
				) );
			
				$wp_customize->add_control( $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority'	 => $i,
					'active_callback' => !empty($opt['active_callback']) ? $opt['active_callback'] : '',
					'type'     => 'radio',
					'choices'  => apply_filters('shaha_filter_options_get_list_choises', $opt['options'], $id)
				) );

			} else if ($opt['type'] == 'switch') {

				$wp_customize->add_setting( $id, array(
					'default'           => shaha_get_theme_option($id),
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $transport
				) );
			
				$wp_customize->add_control( new Shaha_Customize_Switch_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority' => $i,
					'active_callback' => !empty($opt['active_callback']) ? $opt['active_callback'] : '',
					'choices'  => apply_filters('shaha_filter_options_get_list_choises', $opt['options'], $id),
					'input_attrs' => array(
						'value' => shaha_get_theme_option($id),
					)
				) ) );

			} else if ($opt['type'] == 'checkbox') {

				$wp_customize->add_setting( $id, array(
					'default'           => shaha_get_theme_option($id),
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $transport
				) );
			
				$wp_customize->add_control( $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'active_callback' => !empty($opt['active_callback']) ? $opt['active_callback'] : '',
					'priority'	 => $i,
					'type'     => 'checkbox'
				) );

			} else if ($opt['type'] == 'color') {

				$wp_customize->add_setting( $id, array(
					'default'           => shaha_get_theme_option($id),
					'sanitize_callback' => 'sanitize_hex_color',
					'transport'         => $transport
				) );
			
				$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'active_callback' => !empty($opt['active_callback']) ? $opt['active_callback'] : '',
					'priority'	 => $i,
				) ) );

			} else if ($opt['type'] == 'image') {
				$wp_customize->add_setting( $id, array(
					'default'           => shaha_remove_protocol_from_url(shaha_get_theme_option($id), false),
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $transport
				) );
			
				$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'active_callback' => !empty($opt['active_callback']) ? $opt['active_callback'] : '',
					'priority' => $i,
				) ) );

			} else if (in_array($opt['type'], array('media', 'audio', 'video'))) {
				$wp_customize->add_setting( $id, array(
					'default'           => shaha_remove_protocol_from_url(shaha_get_theme_option($id), false),
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $transport
				) );
			
				$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'active_callback' => !empty($opt['active_callback']) ? $opt['active_callback'] : '',
					'priority' => $i,
				) ) );

			} else if ($opt['type'] == 'icon') {

				$wp_customize->add_setting( $id, array(
					'default'           => shaha_remove_protocol_from_url(shaha_get_theme_option($id), false),
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $transport
				) );
			
				$wp_customize->add_control( new Shaha_Customize_Icon_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority' => $i,
					'active_callback' => !empty($opt['active_callback']) ? $opt['active_callback'] : '',
					'input_attrs' => array(
						'value' => shaha_get_theme_option($id),
					)
				) ) );

			} else if ($opt['type'] == 'checklist') {

				$wp_customize->add_setting( $id, array(
					'default'           => shaha_get_theme_option($id),
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $transport
				) );
			
				$wp_customize->add_control( new Shaha_Customize_Checklist_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority' => $i,
					'active_callback' => !empty($opt['active_callback']) ? $opt['active_callback'] : '',
					'choices' => apply_filters('shaha_filter_options_get_list_choises', $opt['options'], $id),
					'input_attrs' => array_merge($opt, array(
														'value' => shaha_get_theme_option($id),
														))
				) ) );

			} else if (in_array($opt['type'], array('slider', 'range'))) {

				$wp_customize->add_setting( $id, array(
					'default'           => shaha_get_theme_option($id),
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $transport
				) );
			
				$wp_customize->add_control( new Shaha_Customize_Range_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority' => $i,
					'active_callback' => !empty($opt['active_callback']) ? $opt['active_callback'] : '',
					'input_attrs' => array_merge($opt, array(
														'show_value' => !isset($opt['show_value']) || $opt['show_value'],
														'value' => shaha_get_theme_option($id)
														))
				) ) );

			} else if ($opt['type'] == 'scheme_editor') {

				$wp_customize->add_setting( $id, array(
					'default'           => shaha_get_theme_option($id),
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $transport
				) );
			
				$wp_customize->add_control( new Shaha_Customize_Scheme_Editor_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority' => $i,
					'active_callback' => !empty($opt['active_callback']) ? $opt['active_callback'] : '',
					'input_attrs' => array_merge($opt, array(
														'value' => shaha_get_theme_option($id),
														))
				) ) );

			} else if ($opt['type'] == 'text_editor') {

				$wp_customize->add_setting( $id, array(
					'default'           => shaha_get_theme_option($id),
					'sanitize_callback' => 'wp_kses_post',
					'transport'         => $transport
				) );

				$wp_customize->add_control( new Shaha_Customize_Text_Editor_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority' => $i,
					'active_callback' => !empty($opt['active_callback']) ? $opt['active_callback'] : '',
					'input_attrs' => array_merge($opt, array(
														'value' => shaha_get_theme_option($id),
														))
				) ) );

			} else if ($opt['type'] == 'button') {
			
				$wp_customize->add_setting( $id, array(
					'default'           => shaha_get_theme_option($id),
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $transport
				) );

				$wp_customize->add_control( new Shaha_Customize_Button_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority' => $i,
					'active_callback' => !empty($opt['active_callback']) ? $opt['active_callback'] : '',
					'input_attrs' => $opt,
				) ) );

			} else if ($opt['type'] == 'info') {
			
				$wp_customize->add_setting( $id, array(
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'postMessage'
				) );

				$wp_customize->add_control( new Shaha_Customize_Info_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority' => $i,
					'active_callback' => !empty($opt['active_callback']) ? $opt['active_callback'] : '',
				) ) );

			} else if ($opt['type'] == 'hidden') {
			
				$wp_customize->add_setting( $id, array(
					'default'           => shaha_get_theme_option($id),
					'sanitize_callback' => 'shaha_sanitize_html',
					'transport'         => 'postMessage'
				) );

				$wp_customize->add_control( new Shaha_Customize_Hidden_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority' => $i,
					'active_callback' => !empty($opt['active_callback']) ? $opt['active_callback'] : '',
				) ) );

			} else {

				if ($opt['type'] == 'text_editor') $opt['type'] = 'textarea';
				
				$wp_customize->add_setting( $id, array(
					'default'           => shaha_get_theme_option($id),
					'sanitize_callback' => !empty($opt['sanitize']) 
												? $opt['sanitize'] 
												: ($opt['type'] == 'text' 
														? 'sanitize_text_field' 
														: 'wp_kses_post'
													),
					'transport'         => $transport
				) );
			
				$wp_customize->add_control( $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority' => $i,
					'active_callback' => !empty($opt['active_callback']) ? $opt['active_callback'] : '',
					'type'     => $opt['type']
				) );
			}

			// Register Partial Refresh (if supported)
			if ($refresh_auto && isset($opt['refresh']) && is_string($opt['refresh']) 
				&& function_exists("shaha_customizer_partial_refresh_{$id}")
				&& isset($wp_customize->selective_refresh)) {
				$wp_customize->selective_refresh->add_partial($id, array(
					'selector'        => $opt['refresh'],
					'settings'        => $id,
					'render_callback' => "shaha_customizer_partial_refresh_{$id}",
					'container_inclusive' => !empty($opt['refresh_wrapper'])
				));
			}

		}


		// Setup standard WP Controls
		// ---------------------------------

		// Reorder standard WP sections
		$sec = $wp_customize->get_panel( 'nav_menus' );
		if (is_object($sec)) $sec->priority = 60;
		$sec = $wp_customize->get_panel( 'widgets' );
		if (is_object($sec)) $sec->priority = 61;
		$sec = $wp_customize->get_section( 'static_front_page' );
		if (is_object($sec)) $sec->priority = 62;
		$sec = $wp_customize->get_section( 'custom_css' );
		if (is_object($sec)) $sec->priority = 2000;
		
		// Modify standard WP controls
		$sec = $wp_customize->get_control( 'blogname' );
		if (is_object($sec))
			$sec->description = esc_html__('Use "((" and "))", "{{" and "}}" to modify style and color of parts of the text, "||" to break current line', 'shaha');
		$sec = $wp_customize->get_setting( 'blogname' );
		if (is_object($sec)) $sec->transport = 'postMessage';

		$sec = $wp_customize->get_setting( 'blogdescription' );
		if (is_object($sec)) $sec->transport = 'postMessage';

		$sec = $wp_customize->get_control( 'site_icon' );
		if (is_object($sec)) $sec->priority = 15;
		$sec = $wp_customize->get_control( 'custom_logo' );
		if (is_object($sec)) {
			$sec->priority = 50;
			$sec->description = wp_kses_data( __('Select or upload the site logo', 'shaha') );
		}

		$sec = $wp_customize->get_section( 'header_image' );
		$sec2 = $wp_customize->get_control( 'header_image_info' );
		$sec2->description = (!empty($sec2->description) ? $sec2->description . '<br>' : '') . $sec->description;

		$sec = $wp_customize->get_control( 'header_image' );
		if (is_object($sec)) {
			$sec->priority = 300;
			$sec->section = 'header';
		}
		$sec = $wp_customize->get_control( 'header_video' );
		if (is_object($sec)) {
			$sec->priority = 310;
			$sec->section = 'header';
		}
		$sec = $wp_customize->get_control( 'external_header_video' );
		if (is_object($sec)) {
			$sec->priority = 320;
			$sec->section = 'header';
		}
		
		$sec = $wp_customize->get_section( 'background_image' );
		if (is_object($sec)) {
			$sec->title = esc_html__('Background', 'shaha');
			$sec->priority = 310;
			$sec->description = esc_html__('Used only if "General settings - Body style" equal to "boxed"', 'shaha');
		}

		$sec = $wp_customize->get_control( 'background_color' );
		if (is_object($sec)) {
			$sec->priority = 10;
			$sec->section = 'background_image';
		}

		// Remove unused sections
		$wp_customize->remove_section( 'colors');
		$wp_customize->remove_section( 'header_image');
	}
}


// Sanitize plain value - remove all tags and spaces
if (!function_exists('shaha_sanitize_value')) {
	function shaha_sanitize_value($value) {
		return empty($value) ? $value : trim(strip_tags($value));
	}
}


// Sanitize html value - keep only allowed tags
if (!function_exists('shaha_sanitize_html')) {
	function shaha_sanitize_html($value) {
		return empty($value) ? $value : wp_kses_post($value);
	}
}


// Return url to autofocus related field
if (!function_exists('shaha_customizer_get_focus_url')) {
	function shaha_customizer_get_focus_url($field) {
		return admin_url("customize.php?autofocus&#91;control&#93;={$field}");
	}
}

// Return link to autofocus related field
if (!function_exists('shaha_customizer_get_focus_link')) {
	function shaha_customizer_get_focus_link($field, $text) {
		return sprintf('<a href="%1$s" class="shaha_customizer_link">%2$s</a>',
						esc_url(shaha_customizer_get_focus_url($field)),
						$text
						);
	}
}

// Display message "Need to select widgets"
if (!function_exists('shaha_customizer_need_widgets_message')) {
	function shaha_customizer_need_widgets_message($field, $text) {
		?><div class="shaha_customizer_message"><?php
			// Translators: Add widget's name or link to focus specified section
			echo wp_kses_data(sprintf(__( 'You have to choose widget "<b>%s</b>" in this section. You can also select any other widget, and change the purpose of this section', 'shaha'),
										is_customize_preview()
											? $text
											: shaha_customizer_get_focus_link($field, $text)
							));
		?></div><?php
	}
}

// Display message "Need to install plugin ThemeREX Addons"
if (!function_exists('shaha_customizer_need_trx_addons_message')) {
	function shaha_customizer_need_trx_addons_message() {
		?><div class="shaha_customizer_message"><?php
			// Translators: Add the link to install plugin and its name
			echo wp_kses_data(sprintf(__( 'You need to install the <b>%s</b> plugin to be able to add Team members, Testimonials, Services and many other widgets', 'shaha'),
								is_customize_preview()
									? __('ThemeREX Addons', 'shaha')
									// Translators: Make the tag with link to install plugin
									: sprintf('<a href="%1$s" class="shaha_customizer_link">%2$s</a>',
									  			esc_url(wp_nonce_url(
															self_admin_url('update.php?action=install-plugin&plugin=trx_addons'),
															'install-plugin_trx_addons'
														)),
											  __('ThemeREX Addons', 'shaha')
											  )
						));
			echo '<br>' . wp_kses_data(__( 'Also you can insert in this section any other widgets and to modify its purpose', 'shaha'));
		?></div><?php
	}
}


//--------------------------------------------------------------
// Save custom settings in CSS file
//--------------------------------------------------------------

// Save CSS with custom colors and fonts after save custom options
if (!function_exists('shaha_customizer_action_save_after')) {
	add_action('customize_save_after', 'shaha_customizer_action_save_after');
	function shaha_customizer_action_save_after($api=false) {

		// Get saved settings
		$settings = $api->settings();

		// Store new schemes colors
		$schemes = shaha_unserialize($settings['scheme_storage']->value());
		if (is_array($schemes) && count($schemes) > 0) 
			shaha_storage_set('schemes', $schemes);

		// Store new fonts parameters
		$fonts = shaha_get_theme_fonts();
		foreach ($fonts as $tag=>$v) {
			foreach ($v as $css_prop=>$css_value) {
				if (in_array($css_prop, array('title', 'description'))) continue;
				$fonts[$tag][$css_prop] = $settings["{$tag}_{$css_prop}"]->value();
			}
		}
		shaha_storage_set('theme_fonts', $fonts);

		// Collect options from the external storages
		$options = shaha_storage_get('options');
		$external_storages = array();
		foreach ($options as $k=>$v) {
			// Skip non-data options - sections, info, etc.
			if (!isset($v['std']) || empty($v['options_storage'])) continue;
			// Get option value from Customizer
			$value = isset($settings[$k])
							? $settings[$k]->value()
							: ($v['type']=='checkbox' ? 0 : '');
			if (!isset($external_storages[$v['options_storage']]))
				$external_storages[$v['options_storage']] = array();
			$external_storages[$v['options_storage']][$k] = $value;
		}

		// Update options in the external storages
		foreach ($external_storages as $storage_name => $storage_values) {
			$storage = get_option($storage_name, false);
			if (is_array($storage)) {
				foreach ($storage_values as $k=>$v)
					$storage[$k] = $v;
				update_option($storage_name, $storage);
			}
		}

		// Update ThemeOptions save timestamp
		$stylesheet_slug = get_option('stylesheet');
		$stylesheet_time = time();
		update_option("shaha_options_timestamp_{$stylesheet_slug}", $stylesheet_time);

		// Sinchronize theme options between child and parent themes
		if (shaha_get_theme_setting('duplshahate_options') == 'both') {
			$theme_slug = get_option('template');
			if ($theme_slug != $stylesheet_slug) {
				shaha_customizer_duplshahate_theme_options($stylesheet_slug, $theme_slug, $stylesheet_time);
			}
		}
		// Regenerate CSS with new colors
		shaha_customizer_save_css();
	}
}

// Save CSS with custom colors and fonts into custom.css
if (!function_exists('shaha_customizer_save_css')) {
	add_action('trx_addons_action_save_options', 'shaha_customizer_save_css');
	function shaha_customizer_save_css() {
		$msg = 	'/* ' . esc_html__("ATTENTION! This file was generated automatshahally! Don't change it!!!", 'shaha') 
				. "\n----------------------------------------------------------------------- */\n";

		// Save CSS with custom colors and fonts into custom.css
		$css = shaha_customizer_get_css();
		$file = shaha_get_file_dir('css/__colors.css');
		if (file_exists($file)) shaha_fpc($file, $msg . $css );

		// Merge stylesheets
		$list = apply_filters( 'shaha_filter_merge_styles', array() );
		$css = '';
		foreach ($list as $f) {
			$css .= shaha_fgc(shaha_get_file_dir($f));
		}
		if ( $css != '') {
			shaha_fpc( shaha_get_file_dir('css/__styles.css'), $msg . apply_filters( 'shaha_filter_prepare_css', $css, true ) );
		}

		// Merge scripts
		$list = apply_filters( 'shaha_filter_merge_scripts', array(
																	'js/skip-link-focus.js',
																	'js/bideo.js',
																	'js/jquery.tubular.js',
																	'js/_utils.js',
																	'js/_init.js'
																	)
							);
		$js = '';
		foreach ($list as $f) {
			$js .= shaha_fgc(shaha_get_file_dir($f));
		}
		if ( $js != '') {
			shaha_fpc( shaha_get_file_dir('js/__scripts.js'), $msg . apply_filters( 'shaha_filter_prepare_js', $js, true ) );
		}
	}
}


//--------------------------------------------------------------
// Customizer JS and CSS
//--------------------------------------------------------------

// Binds JS listener to Customizer controls.
if ( !function_exists( 'shaha_customizer_control_js' ) ) {
	add_action( 'customize_controls_enqueue_scripts', 'shaha_customizer_control_js' );
	function shaha_customizer_control_js() {
		wp_enqueue_style( 'shaha-customizer', shaha_get_file_url('theme-options/theme.customizer.css'), array(), null );
		wp_enqueue_script( 'shaha-customizer',
									shaha_get_file_url('theme-options/theme.customizer.js'),
									array( 'customize-controls', 'iris', 'underscore', 'wp-util' ), null, true );
		wp_enqueue_script( 'colorpicker-colors', shaha_get_file_url('js/colorpicker/colors.js'), array('jquery'), null, true );
		wp_enqueue_script( 'jquery-colorpicker', shaha_get_file_url('js/colorpicker/jqColorPicker.js'), array('jquery'), null, true );
		wp_localize_script( 'shaha-customizer', 'shaha_color_schemes', shaha_storage_get('schemes') );
		wp_localize_script( 'shaha-customizer', 'shaha_simple_schemes', shaha_storage_get('schemes_simple') );
		wp_localize_script( 'shaha-customizer', 'shaha_theme_fonts', shaha_storage_get('theme_fonts') );
		wp_localize_script( 'shaha-customizer', 'shaha_customizer_vars', array(
			'max_load_fonts' => shaha_get_theme_setting('max_load_fonts'),
			'msg_refresh' => esc_html__('Refresh', 'shaha'),
			'msg_reset' => esc_html__('Reset', 'shaha'),
			'msg_reset_confirm' => esc_html__('Are you sure you want to reset all Theme Options?', 'shaha'),
			) );
		wp_localize_script( 'shaha-customizer', 'shaha_dependencies', shaha_get_theme_dependencies() );
	}
}


// Binds JS handlers to make the Customizer preview reload changes asynchronously.
if ( !function_exists( 'shaha_customizer_preview_js' ) ) {
	add_action( 'customize_preview_init', 'shaha_customizer_preview_js' );
	function shaha_customizer_preview_js() {
		wp_enqueue_script( 'shaha-customizer-preview',
							shaha_get_file_url('theme-options/theme.customizer.preview.js'), 
							array( 'customize-preview' ), null, true );
	}
}

// Output an Underscore template for generating CSS for the color scheme.
// The template generates the css dynamshahally for instant display in the Customizer preview.
if ( !function_exists( 'shaha_customizer_css_template' ) ) {
	add_action( 'customize_controls_print_footer_scripts', 'shaha_customizer_css_template' );
	function shaha_customizer_css_template() {
		$colors = array();
		foreach (shaha_get_scheme_colors() as $k=>$v)
			$colors[$k] = '{{ data.'.esc_attr($k).' }}';

		$tmpl_holder = 'script';

		$schemes = array_keys(shaha_get_list_schemes());
		if (count($schemes) > 0) {
			foreach ($schemes as $scheme) {
				shaha_show_layout(shaha_customizer_get_css($colors, false, false, $scheme),
									'<' . esc_attr($tmpl_holder) . ' type="text/html" id="tmpl-shaha-color-scheme-'.esc_attr($scheme).'">',
									'</' . esc_attr($tmpl_holder) . '>');
			}
		}


		// Fonts
		$fonts = shaha_get_theme_fonts();
		if (is_array($fonts) && count($fonts) > 0) {
			foreach ($fonts as $tag => $font) {
				$fonts[$tag]['font-family']		= '{{ data["'.$tag.'"]["font-family"] }}';
				$fonts[$tag]['font-size']		= '{{ data["'.$tag.'"]["font-size"] }}';
				$fonts[$tag]['line-height']		= '{{ data["'.$tag.'"]["line-height"] }}';
				$fonts[$tag]['font-weight']		= '{{ data["'.$tag.'"]["font-weight"] }}';
				$fonts[$tag]['font-style']		= '{{ data["'.$tag.'"]["font-style"] }}';
				$fonts[$tag]['text-decoration']	= '{{ data["'.$tag.'"]["text-decoration"] }}';
				$fonts[$tag]['text-transform']	= '{{ data["'.$tag.'"]["text-transform"] }}';
				$fonts[$tag]['letter-spacing']	= '{{ data["'.$tag.'"]["letter-spacing"] }}';
				$fonts[$tag]['margin-top']		= '{{ data["'.$tag.'"]["margin-top"] }}';
				$fonts[$tag]['margin-bottom']	= '{{ data["'.$tag.'"]["margin-bottom"] }}';
			}
			shaha_show_layout(shaha_customizer_get_css(false, $fonts, false, false),
								'<' . esc_attr($tmpl_holder) . ' type="text/html" id="tmpl-shaha-fonts">',
								'</' . esc_attr($tmpl_holder) . '>');
		}

	}
}


// Add scheme name in each selector in the CSS (priority 100 - after complete css)
if (!function_exists('shaha_customizer_add_scheme_in_css')) {
	add_action( 'shaha_filter_get_css', 'shaha_customizer_add_scheme_in_css', 100, 4 );
	function shaha_customizer_add_scheme_in_css($css, $colors, $fonts, $scheme) {
		if ($colors && !empty($css['colors'])) {
			$rez = '';
			$in_comment = $in_rule = false;
			$allow = true;
			$scheme_class = sprintf('.scheme_%s ', $scheme);
			$self_class = '.scheme_self';
			$self_class_len = strlen($self_class);
			$css_str = str_replace(array('{{', '}}'), array('[[',']]'), $css['colors']);
			for ($i=0; $i<strlen($css_str); $i++) {
				$ch = $css_str[$i];
				if ($in_comment) {
					$rez .= $ch;
					if ($ch=='/' && $css_str[$i-1]=='*') {
						$in_comment = false;
						$allow = !$in_rule;
					}
				} else if ($in_rule) {
					$rez .= $ch;
					if ($ch=='}') {
						$in_rule = false;
						$allow = !$in_comment;
					}
				} else {
					if ($ch=='/' && $css_str[$i+1]=='*') {
						$rez .= $ch;
						$in_comment = true;
					} else if ($ch=='{') {
						$rez .= $ch;
						$in_rule = true;
					} else if ($ch==',') {
						$rez .= $ch;
						$allow = true;
					} else if (strpos(" \t\r\n", $ch)===false) {
						if ($allow) {
							$pos_comma = strpos($css_str, ',', $i+1);
							$pos_bracket = strpos($css_str, '{', $i+1);
							$pos = $pos_comma === false
										? $pos_bracket
										: ($pos_bracket === false
												? $pos_comma
												: min($pos_comma, $pos_bracket)
											);
							$selector = $pos > 0 ? substr($css_str, $i, $pos-$i) : '';
							if (strpos($selector, $self_class) !== false) {
								$rez .= str_replace($self_class, trim($scheme_class), $selector);
								$i += strlen($selector) - 1;
							} else {
								$rez .= $scheme_class . trim($ch);
							}
							$allow = false;
						} else
							$rez .= $ch;
					} else {
						$rez .= $ch;
					}
				}
			}
			$rez = str_replace(array('[[',']]'), array('{{', '}}'), $rez);
			$css['colors'] = $rez;
		}
		return $css;
	}
}


//----------------------------------------------------------------------------------------------
// Add fix to allow theme-specific sidebars in Customizer (if is_customize_preview() mode)
//----------------------------------------------------------------------------------------------
if (!function_exists('shaha_customizer_fix_sidebars') && is_customize_preview() && is_front_page()) {
	add_action('wp_footer', 'shaha_customizer_fix_sidebars');
	function shaha_customizer_fix_sidebars() {
		$sidebars = shaha_get_sidebars();
		if (is_array($sidebars)) {
			foreach ($sidebars as $sb=>$params) {
				if (!empty($params['front_page_section']) && is_active_sidebar($sb)) {
					?><div class="hidden"><?php dynamic_sidebar($sb); ?></div><?php
				}
			}
		}
	}
}

// Load theme options and styles
require_once SHAHA_THEME_DIR . 'theme-specific/theme.setup.php';
require_once SHAHA_THEME_DIR . 'theme-specific/theme.styles.php';
require_once SHAHA_THEME_DIR . 'theme-options/theme.options.php';
require_once SHAHA_THEME_DIR . 'theme-options/theme.override-options.php';
?>