<?php

if (!class_exists('WP_Sheet_Editor_ACF')) {

	class WP_Sheet_Editor_ACF {

		static private $instance = false;
		static $checkbox_keys = array();
		static $map_keys = array();
		var $gallery_field_keys = array();
		var $repeater_keys = array();
		var $excluded_serialized_keys = array();

		private function __construct() {
			
		}

		function init() {

			// exit if acf plugin is not active
			if (!$this->is_acf_plugin_active()) {
				return;
			}

			add_action('vg_sheet_editor/editor/before_init', array($this, 'register_columns'));

			// Dates
			add_filter('vg_sheet_editor/load_rows/get_cell_data', array($this, 'filter_dates_display'), 10, 4);
			add_filter('vg_sheet_editor/save_rows/incoming_data', array($this, 'filter_dates_saving'), 10, 2);

			// Checkbox
			add_filter('vg_sheet_editor/serialized_addon/column_settings', array($this, 'filter_checkbox_column_settings'), 20, 6);
			add_filter('vg_sheet_editor/serialized_addon/save_cell', array($this, 'filter_save_checkbox_from_serialized_class'), 10, 7);

			// Map
			add_filter('vg_sheet_editor/provider/post/update_item_meta', array($this, 'filter_map_data_for_saving'), 10, 3);
			add_filter('vg_sheet_editor/provider/user/update_item_meta', array($this, 'filter_map_data_for_saving'), 10, 3);

			// Gallery
			add_filter('vg_sheet_editor/provider/post/update_item_meta', array($this, 'filter_gallery_data_for_saving'), 10, 3);
			add_filter('vg_sheet_editor/provider/user/update_item_meta', array($this, 'filter_gallery_data_for_saving'), 10, 3);

			// Save ACF field key
			add_action('vg_sheet_editor/save_rows/before_saving_cell', array($this, 'save_acf_field_key'), 10, 6);
			add_action('vg_sheet_editor/formulas/execute_formula/after_sql_execution', array($this, 'save_acf_field_key_after_sql_formula'), 10, 5);

			// Repeater fields
			add_filter('vg_sheet_editor/provider/post/update_item_meta', array($this, 'sync_repeater_main_field_count'), 10, 3);
			add_filter('vg_sheet_editor/provider/user/update_item_meta', array($this, 'sync_repeater_main_field_count'), 10, 3);


			add_filter('vg_sheet_editor/serialized_addon/column_settings', array($this, 'exclude_keys_from_serialized_columns'), 10, 5);
		}

		function sync_repeater_main_field_count($value, $id, $key) {
			global $wpdb;

			if (empty($this->repeater_keys) || strpos($key, '_') === 0) {
				return $value;
			}

			$repeater_key = null;
			$regex = null;
			foreach ($this->repeater_keys as $raw_repeater_key => $subfields) {
				foreach ($subfields as $repeater_key_regex) {
					if (preg_match($repeater_key_regex, $key)) {
						$repeater_key = $raw_repeater_key;
						$regex = $repeater_key_regex;
						break;
					}
				}
				if ($repeater_key) {
					break;
				}
			}
			if (empty($repeater_key)) {
				return $value;
			}

			$mysql_regex = str_replace(array('/', '\d'), array('', '[0-9]'), $regex);
			$meta_table_name = VGSE()->helpers->get_current_provider()->get_meta_table_name();
			$meta_table_id_column = VGSE()->helpers->get_current_provider()->get_meta_table_post_id_key();
			$sql = "SELECT meta_key FROM $meta_table_name WHERE meta_key RLIKE '" . $mysql_regex . "' AND " . esc_sql($meta_table_id_column) . " = " . (int) $id . " ORDER BY meta_key DESC LIMIT 1";
			$highest_key = $wpdb->get_var($sql);

			if (empty($highest_key)) {
				$highest_key = $key;
			}

			$count_regex = str_replace('\d+', '(\d+)', $regex);
			$repeater_count = (int) preg_replace($count_regex, '$1', $highest_key);
			$key_index_count = (int) preg_replace($count_regex, '$1', $key);
			if ($repeater_count < $key_index_count) {
				$repeater_count = $key_index_count;
			}


			// Subfields index starts from 0, but the parent count starts from 1
			$repeater_count++;

			remove_filter('vg_sheet_editor/provider/post/update_item_meta', array($this, 'sync_repeater_main_field_count'), 10);
			remove_filter('vg_sheet_editor/provider/user/update_item_meta', array($this, 'sync_repeater_main_field_count'), 10);

			VGSE()->helpers->get_current_provider()->update_item_meta($id, $repeater_key, $repeater_count);

			add_filter('vg_sheet_editor/provider/post/update_item_meta', array($this, 'sync_repeater_main_field_count'), 10, 3);
			add_filter('vg_sheet_editor/provider/user/update_item_meta', array($this, 'sync_repeater_main_field_count'), 10, 3);


			return $value;
		}

		function filter_save_checkbox_from_serialized_class($post_criterias, $post_id, $settings, $item, $post_type, $column_settings, $key) {
			if (empty($settings['is_acf_checkbox'])) {
				return $post_criterias;
			}

			$value = $item[$key];

			// Allow to save field with the acf choice key, 1, yes, true, or check
			if (in_array($value, array('1', 'yes', 'true', 'check', $column_settings['formatted']['checkedTemplate']), true)) {
				$value = $column_settings['formatted']['checkedTemplate'];
			} else {
				$value = '';
			}

			$criteria_parts = explode('_i_', str_replace($settings['sample_field_key'] . '_', '', $key));

			if (is_numeric(current($criteria_parts))) {
				$criteria_key = end($criteria_parts);
				$criteria_index = current($criteria_parts);
			} else {
				$criteria_key = current($criteria_parts);
				$criteria_index = end($criteria_parts);
			}


			if (is_numeric($criteria_index)) {
				$criteria_index = (int) $criteria_index;
			}

			$post_criterias = maybe_unserialize(VGSE()->helpers->get_current_provider()->get_item_meta($post_id, $settings['sample_field_key'], true, 'save', true));


			if (empty($post_criterias) || !is_array($post_criterias)) {
				$post_criterias = array();
			}
			$post_criterias[] = $value;
			$post_criterias = array_filter(array_unique($post_criterias));

			if (empty($value)) {
				$index = array_search($column_settings['formatted']['checkedTemplate'], $post_criterias);
				if ($index !== false) {
					unset($post_criterias[$index]);
				}
			}

			return $post_criterias;
		}

		function save_acf_field_key_after_sql_formula($column, $formula, $post_type, $spreadsheet_columns, $post_ids) {
			$column_settings = $spreadsheet_columns[$column];
			if (empty($column_settings['acf_field']) || empty($column_settings['acf_field']['key'])) {
				return;
			}
			$column_settings['key_for_formulas'] = '_' . $column_settings['key_for_formulas'];
			$formula = '=REPLACE(""$current_value$"",""' . $column_settings['acf_field']['key'] . '"")';
			WP_Sheet_Editor_Formulas::get_instance()->execute_formula_as_sql($post_ids, $formula, $column_settings, $post_type);


			if (!empty($column_settings['acf_field']['parent'])) {
				$column_settings['key_for_formulas'] = '_' . $column_settings['acf_field']['parent']['name'];
				$formula = '=REPLACE(""$current_value$"",""' . $column_settings['acf_field']['parent']['key'] . '"")';
				WP_Sheet_Editor_Formulas::get_instance()->execute_formula_as_sql($post_ids, $formula, $column_settings, $post_type);
			}
		}

		function save_acf_field_key($item, $post_type, $column_settings, $key, $spreadsheet_columns, $post_id) {
			if (empty($column_settings['acf_field']) || empty($column_settings['acf_field']['key'])) {
				return;
			}

			$real_key = preg_replace('/_\d+_i_\d+$/', '', $key);
			VGSE()->helpers->get_current_provider()->update_item_meta($post_id, '_' . $real_key, $column_settings['acf_field']['key']);

			if (!empty($column_settings['acf_field']['parent']) && is_array($column_settings['acf_field']['parent'])) {
				VGSE()->helpers->get_current_provider()->update_item_meta($post_id, '_' . $column_settings['acf_field']['parent']['name'], $column_settings['acf_field']['parent']['key']);
			}
		}

		function exclude_keys_from_serialized_columns($column_settings, $first_set_keys, $field, $key, $post_type) {
			if (!isset($this->excluded_serialized_keys[$post_type])) {
				return $column_settings;
			}
			foreach ($this->excluded_serialized_keys[$post_type] as $field_key) {
				if (!empty($column_settings['serialized_field_original_key']) && $column_settings['serialized_field_original_key'] === $field_key) {
					$column_settings = array();
				}
			}

			return $column_settings;
		}

		function filter_map_data_for_saving($new_value, $id, $key) {
			$real_key = preg_replace('/_\d+_i_\d+$/', '', $key);
			if (!isset(WP_Sheet_Editor_ACF::$map_keys[$real_key])) {
				return $new_value;
			}

			$current_address_data = VGSE()->helpers->get_current_provider()->get_item_meta($id, $real_key, true, 'save', true);
			if ((empty($current_address_data) && !empty($new_value['address'])) || (!empty($current_address_data['address']) && $current_address_data['address'] !== $new_value['address'] )) {
				$geo_response = wp_remote_get('https://maps.googleapis.com/maps/api/geocode/json?key=' . acf_get_setting('google_api_key') . '&language=en&address=' . urlencode($new_value['address']) . '&sensor=false');
				$geo_json = wp_remote_retrieve_body($geo_response);

				$geo = json_decode($geo_json, true);
				if ($geo['status'] === 'OK') {
					$new_value['lat'] = $geo['results'][0]['geometry']['location']['lat'];
					$new_value['lng'] = $geo['results'][0]['geometry']['location']['lng'];
				}
			}

			return $new_value;
		}

		function prepare_gallery_value_for_display($value, $post, $key, $column_settings) {
			$post_type = VGSE()->helpers->get_provider_from_query_string();
			if (!isset($this->gallery_field_keys[$post_type]) || !in_array($key, $this->gallery_field_keys[$post_type])) {
				return $value;
			}

			if (!empty($value) && is_array($value)) {
				$value = implode(',', $value);
			}

			return $value;
		}

		function prepare_checkbox_value_for_display($value, $post, $key, $column_settings) {
			$real_key = preg_replace('/_\d+_i_\d+$/', '', $key);
			if ($key === $real_key || !isset(WP_Sheet_Editor_ACF::$checkbox_keys[$real_key])) {
				return $value;
			}
			$post_id = $post->ID;

			$raw_value = VGSE()->helpers->get_current_provider()->get_item_meta($post_id, $real_key, true, 'read');
			if (empty($raw_value) || !is_array($raw_value)) {
				return $value;
			}
			$index = (int) str_replace(array($real_key . '_', '_i_0'), '', $key);
			$accepted_values = array_keys(WP_Sheet_Editor_ACF::$checkbox_keys[$real_key]['choices']);
			$expected_value = $accepted_values[$index];

			$value = ( in_array($expected_value, $raw_value)) ? $expected_value : '';
			return $value;
		}

		function filter_gallery_data_for_saving($value, $id, $key) {
			$post_type = VGSE()->helpers->get_provider_from_query_string();
			if (!isset($this->gallery_field_keys[$post_type]) || !in_array($key, $this->gallery_field_keys[$post_type])) {
				return $value;
			}

			if (!empty($value) && is_string($value)) {
				$value = explode(',', $value);
			}

			return $value;
		}

		function filter_checkbox_column_settings($column_settings, $first_set_keys, $field, $key, $post_type, $settings) {
			// If this serialized field is not an acf checkbox but uses a key known as 
			// acf checkbox, return empty to not register the column
			if (empty($settings['is_acf_checkbox']) && in_array($settings['sample_field_key'], array_keys(WP_Sheet_Editor_ACF::$checkbox_keys))) {
				return array();
			}

			if (empty($settings['is_acf_checkbox'])) {
				return $column_settings;
			}

			$choices_values = array_keys($settings['acf_choices']);
			$column_settings['formatted']['type'] = 'checkbox';
			$column_settings['formatted']['checkedTemplate'] = $choices_values[$field];
			$column_settings['formatted']['uncheckedTemplate'] = '';
			$column_settings['formatted']['default_value'] = $column_settings['default_value'];
			$column_settings['title'] = $settings['column_title_prefix'] . ': ' . $settings['acf_choices'][$choices_values[$field]];

			// We ignore the default value set in ACF because it causes issues.
			// If we show the checkbox with the default value (i.e. checked), it will ignore it as checked when saving
			// because it would have the same value as initially loaded
			$column_settings['default_value'] = '';

			return $column_settings;
		}

		function filter_dates_saving($rows, $settings) {

			$post_type = sanitize_text_field($settings['post_type']);
			$spreadsheet_columns = VGSE()->helpers->get_provider_columns($post_type);

			$date_keys = array();
			foreach ($spreadsheet_columns as $key => $column_settings) {
				if ($column_settings['data_type'] !== 'meta_data') {
					continue;
				}

				if (empty($column_settings['acf_field'])) {
					continue;
				}
				if (empty($column_settings['unformatted']['type']) || empty($column_settings['formatted']['type'])) {
					continue;
				}

				if ($column_settings['unformatted']['type'] === 'date' || $column_settings['formatted']['type'] === 'date') {
					$date_keys[] = $key;
				}
			}

			foreach ($rows as $row_index => $row) {
				foreach ($row as $column_key => $column_value) {
					if (in_array($column_key, $date_keys) && !empty($column_value)) {

						$date_parts = explode('/', str_replace('-', '/', $column_value));

						$rows[$row_index][$column_key] = date('Ymd', strtotime(implode('-', array($date_parts[2], $date_parts[0], $date_parts[1]))));
					}
				}
			}

			return $rows;
		}

		function filter_dates_display($value, $post, $field_key, $column_settings) {

			if (!empty($column_settings['acf_field']) && $column_settings['data_type'] === 'meta_data' && ((!empty($column_settings['unformatted']['type']) && $column_settings['unformatted']['type'] === 'date' ) || (!empty($column_settings['formatted']['type']) && $column_settings['formatted']['type'] === 'date' ) )) {
				$raw_value = get_post_meta($post->ID, $field_key, true);

				if (!empty($raw_value)) {
					$value = date('m-d-Y', strtotime($raw_value));
				}
			}

			return $value;
		}

		/**
		 * Get fields registered in Advanced Custom Fields for a specific post type
		 * @param str $post_type
		 * @return boolean|array
		 */
		function get_acf_fields_objects_by_post_type($post_type = 'post', $editor) {
			// get field groups
			if ($editor->provider->key === 'user') {
				$filter = array(
					'user_form' => 'edit'
				);
			} else {
				$filter = array();
			}
			// get field groups
			$acfs = acf_get_field_groups($filter);
			$fields = array();

			if ($acfs) {
				foreach ($acfs as $acf) {
					$post_type_fields = false;
					$location = serialize($acf['location']);
					if ($editor->provider->is_post_type) {
						if ($post_type === 'post' && preg_match('/"(category|post_tag):/', $location)) {
							$post_type_fields = true;
						} elseif (strpos($location, '"post_type"') !== false && strpos($location, '"' . $post_type . '"') !== false) {
							$post_type_fields = true;
						} else {
							$post_type_fields = array_merge(wp_list_filter($acf['location'][0], array(
								'param' => 'post_type',
								'operator' => '==',
								'value' => $post_type,
									)), wp_list_filter($acf['location'][0], array(
								'param' => 'post_type',
								'operator' => '==',
								'value' => 'all',
							)));
						}
					} elseif ($editor->provider->key === 'term') {
						$post_type_fields = array_merge(wp_list_filter($acf['location'][0], array(
							'param' => 'taxonomy',
							'operator' => '==',
							'value' => $post_type,
								)), wp_list_filter($acf['location'][0], array(
							'param' => 'taxonomy',
							'operator' => '==',
							'value' => 'all',
						)));
					} else {
						$post_type_fields = true;
					}

					if (!empty($post_type_fields)) {
						$fields[] = acf_get_fields($acf);
					}
				}
			}

			return apply_filters('vg_sheet_editor/acf/fields', $fields, $post_type, $acfs);
		}

		/**
		 * Is acf plugin active
		 * @return boolean
		 */
		function is_acf_plugin_active() {
			return function_exists('acf_get_field_groups');
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if (null == WP_Sheet_Editor_ACF::$instance) {
				WP_Sheet_Editor_ACF::$instance = new WP_Sheet_Editor_ACF();
				WP_Sheet_Editor_ACF::$instance->init();
			}
			return WP_Sheet_Editor_ACF::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

		/**
		 * Register columns in the spreadsheet
		 * @return null
		 */
		function register_columns($editor) {


			if ($editor->provider->key === 'user') {
				$post_types = array(
					'user'
				);
			} else {
				$post_types = $editor->args['enabled_post_types'];
			}

			if (empty($post_types)) {
				return;
			}


			$columns = array();
			foreach ($post_types as $post_type) {
				$acf_post_type_groups = $this->get_acf_fields_objects_by_post_type($post_type, $editor);
				if (empty($acf_post_type_groups)) {
					continue;
				}

				if (!isset($this->gallery_field_keys[$post_type])) {
					$this->gallery_field_keys[$post_type] = array();
				}
				if (!isset($this->excluded_serialized_keys[$post_type])) {
					$this->excluded_serialized_keys[$post_type] = array();
				}
				foreach ($acf_post_type_groups as $acf_group_index => $acf_group) {
					if (empty($acf_group)) {
						continue;
					}
					$columns = array_merge($columns, $this->_acf_fields_to_columns_args($acf_group, $post_type, $editor));
				}
			}

			$this->_register_columns($columns, $editor);
		}

		function _acf_fields_to_columns_args($acf_group, $post_type, $editor) {
			$column_defaults = array(
				'name' => '',
				'key' => '',
				'data_source' => 'meta_data',
				'post_types' => 'post',
				'read_only' => 'no',
				'allow_formulas' => 'yes',
				'allow_hide' => 'yes',
				'allow_rename' => 'yes',
				'plain_renderer' => 'text',
				'formatted_renderer' => 'text',
				'width' => '150',
				'cell_type' => '',
			);
			$columns = array();
			foreach ($acf_group as $acf_field_index => $acf_field) {
				// We don't register the text fields and unsupported fields because
				// they will appear automatically. The custom columns module registers
				// all custom fields as plain text. We only register fields with special format here.

				if (in_array($acf_field['type'], array('image', 'file'))) {
					$columns[] = wp_parse_args(array(
						'name' => $acf_field['label'],
						'key' => $acf_field['name'],
						'post_types' => $post_type,
						'cell_type' => 'boton_gallery',
						'acf_field' => $acf_field,
							), $column_defaults);
				} elseif (in_array($acf_field['type'], array('text', 'textarea', 'number', 'email', 'url', 'password', 'oembed'))) {
					$columns[] = wp_parse_args(array(
						'acf_field' => $acf_field,
						'name' => $acf_field['label'],
						'key' => $acf_field['name'],
						'post_types' => $post_type,
						'plain_renderer' => 'text',
						'formatted_renderer' => 'text',
							), $column_defaults);
				} elseif (in_array($acf_field['type'], array('relationship'))) {
					$this->excluded_serialized_keys[$post_type][] = $acf_field['name'];
					$editor->args['columns']->register_item($acf_field['name'], $post_type, array(
						'data_type' => 'meta_data',
						'column_width' => 200,
						'title' => $acf_field['label'],
						'type' => '',
						'supports_formulas' => true,
						'supports_sql_formulas' => false,
						'allow_to_hide' => true,
						'allow_to_rename' => true,
						'allow_plain_text' => true,
						'prepare_value_for_display' => array($this, 'prepare_relationship_for_display'),
						'save_value_callback' => array($this, 'update_relationship_for_cell'),
						'acf_field' => $acf_field,
						'list_separation_character' => ',',
					));
				} elseif (in_array($acf_field['type'], array('wysiwyg'))) {
					$columns[] = wp_parse_args(array(
						'acf_field' => $acf_field,
						'name' => $acf_field['label'],
						'key' => $acf_field['name'],
						'post_types' => $post_type,
						'cell_type' => 'boton_tiny',
							), $column_defaults);
				} elseif (in_array($acf_field['type'], array('radio')) || ($acf_field['type'] === 'select' && !$acf_field['multiple'] )) {
					$columns[] = wp_parse_args(array(
						'acf_field' => $acf_field,
						'name' => $acf_field['label'],
						'key' => $acf_field['name'],
						'post_types' => $post_type,
						'plain_renderer' => 'text',
						'formatted_renderer' => 'select',
						'selectOptions' => $acf_field['choices'],
						'default_value' => $acf_field['default_value'],
							), $column_defaults);
				} elseif ($acf_field['type'] === 'select' && $acf_field['multiple']) {
					$this->excluded_serialized_keys[$post_type][] = $acf_field['name'];
					$editor->args['columns']->register_item($acf_field['name'], $post_type, array(
						'data_type' => 'meta_data',
						'column_width' => 200,
						'title' => $acf_field['label'],
						'type' => '',
						'supports_formulas' => true,
						'supports_sql_formulas' => false,
						'allow_to_hide' => true,
						'allow_to_rename' => true,
						'allow_plain_text' => true,
						'prepare_value_for_display' => array($this, 'prepare_multi_select_for_display'),
						'save_value_callback' => array($this, 'update_multi_select_for_cell'),
						'acf_field' => $acf_field,
						'list_separation_character' => ',',
					));
				} elseif (in_array($acf_field['type'], array('true_false'))) {
					$columns[] = wp_parse_args(array(
						'acf_field' => $acf_field,
						'name' => $acf_field['label'],
						'key' => $acf_field['name'],
						'post_types' => $post_type,
						'plain_renderer' => 'text',
						'formatted_renderer' => 'checkbox',
						'checkedTemplate' => 1,
						'uncheckedTemplate' => 0,
						'default_value' => 0
							), $column_defaults);
				} elseif (in_array($acf_field['type'], array('gallery'))) {
					$this->gallery_field_keys[$post_type][] = $acf_field['name'];
					$this->excluded_serialized_keys[$post_type][] = $acf_field['name'];

					$columns[] = wp_parse_args(array(
						'name' => $acf_field['label'],
						'key' => $acf_field['name'],
						'post_types' => $post_type,
						'cell_type' => 'boton_gallery_multiple',
						'acf_field' => $acf_field,
						'prepare_value_for_display' => array($this, 'prepare_gallery_value_for_display')
							), $column_defaults);
				} elseif (in_array($acf_field['type'], array('checkbox'))) {
					$sample_field = array();
					$choice_index = 0;
					foreach ($acf_field['choices'] as $choice_key => $choice_label) {
						$sample_field[] = ( is_array($acf_field['default_value']) && isset($acf_field['default_value'][$choice_index])) ? $acf_field['default_value'][$choice_index] : '';
						$choice_index++;
					}

					new WP_Sheet_Editor_Serialized_Field(array(
						'sample_field_key' => $acf_field['name'],
						'sample_field' => $sample_field,
						'column_width' => 150,
						'column_title_prefix' => $acf_field['label'], // to remove the field key from the column title
						'level' => 1,
						'allowed_post_types' => array($post_type),
						'is_single_level' => true,
						'allow_in_wc_product_variations' => false,
						'is_acf_checkbox' => true,
						'acf_choices' => $acf_field['choices'],
						'column_settings' => array(
							'acf_field' => $acf_field,
							'prepare_value_for_display' => array($this, 'prepare_checkbox_value_for_display')
						)
					));
					WP_Sheet_Editor_ACF::$checkbox_keys[$acf_field['name']] = $acf_field;
				} elseif (in_array($acf_field['type'], array('google_map'))) {
					new WP_Sheet_Editor_Serialized_Field(array(
						'sample_field_key' => $acf_field['name'],
						'sample_field' => array(
							'address' => '',
							'lat' => '',
							'lng' => '',
						),
						'column_width' => 150,
						'column_title_prefix' => $acf_field['label'], // to remove the field key from the column title
						'level' => 1,
						'allowed_post_types' => array($post_type),
						'is_single_level' => true,
						'allow_in_wc_product_variations' => false,
						'is_acf_map' => true,
						'column_settings' => array(
							'acf_field' => $acf_field,
						)
					));
					WP_Sheet_Editor_ACF::$map_keys[$acf_field['name']] = $acf_field;
				} elseif (in_array($acf_field['type'], array('repeater'))) {
					$this->repeater_keys[$acf_field['name']] = array();

					// The parent repeater is not editable, it's used internally to keep count of internal rows
					$editor->args['columns']->remove_item($acf_field['name'], $post_type);

					$repeater_count_values = $this->_get_repeater_count_values($acf_field['name'], $post_type, $editor);

					$highest_count = ( empty($repeater_count_values) || empty($repeater_count_values[0])) ? 3 : (int) $repeater_count_values[0];

					// Save the subfield keys for processing the values during saving/reading
					foreach ($acf_field['sub_fields'] as $subfield) {
						$this->repeater_keys[$acf_field['name']][] = '/' . $acf_field['name'] . '_\d+_' . $subfield['name'] . '$/';
					}

					// Register columns for each subfield
					for ($i = 0; $i < $highest_count; $i++) {
						$repeater_field_group = array();
						foreach ($acf_field['sub_fields'] as $subfield) {
							$subfield['parent'] = array(
								'name' => $acf_field['name'],
								'label' => $acf_field['label'],
								'key' => $acf_field['key'],
							);
							$subfield['name'] = $acf_field['name'] . '_' . $i . '_' . $subfield['name'];
							$subfield['label'] = implode(' : ', array($acf_field['label'], $i + 1, $subfield['label']));
							$repeater_field_group[] = $subfield;
						}
						$repeater_columns = $this->_acf_fields_to_columns_args($repeater_field_group, $post_type, $editor);
						$columns = array_merge($columns, $repeater_columns);
					}
				}
			}
			return $columns;
		}

		function prepare_relationship_for_display($value, $post, $column_key, $column_settings) {
			global $wpdb;
			$titles = '';
			if (is_array($value) && !empty($value)) {
				$raw_titles = array_unique($wpdb->get_col("SELECT post_title FROM $wpdb->posts WHERE ID IN (" . implode(',', array_map('intval', $value)) . ")"));
				$titles = implode(', ', $raw_titles);
			}
			return $titles;
		}

		function update_relationship_for_cell($post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns) {
			global $wpdb;
			$titles = array_map('trim', explode(',', $data_to_save));
			$ids = '';
			if (!empty($titles)) {
				$sql = "SELECT ID FROM $wpdb->posts WHERE post_title IN ('" . implode("','", array_map('esc_sql', $titles)) . "') ";
				if (!empty($cell_args['acf_field']['post_type'])) {
					$sql .= " AND post_type IN ('" . implode("','", array_map('esc_sql', $cell_args['acf_field']['post_type'])) . "') ";
				}
				$ids = array_unique($wpdb->get_col($sql));
			}
			VGSE()->helpers->get_current_provider()->update_item_meta($post_id, $cell_key, $ids);
		}

		function prepare_multi_select_for_display($value, $post, $column_key, $column_settings) {
			$titles = '';
			if (is_array($value) && !empty($value)) {
				$raw_titles = array();
				foreach ($value as $key) {
					if ($column_settings['acf_field']['choices'][$key]) {
						$raw_titles[] = $column_settings['acf_field']['choices'][$key];
					}
				}

				$titles = implode(', ', $raw_titles);
			}
			return $titles;
		}

		function update_multi_select_for_cell($post_id, $cell_key, $data_to_save, $post_type, $column_settings, $spreadsheet_columns) {
			$titles = array_map('trim', explode(',', $data_to_save));
			$ids = '';
			if (!empty($titles)) {
				$ids = array();
				foreach ($titles as $title) {
					$key = array_search($title, $column_settings['acf_field']['choices']);
					if (isset($column_settings['acf_field']['choices'][$title])) {
						$ids[] = $title;
					} elseif ($key !== false) {
						$ids[] = $key;
					} else {
						continue;
					}
				}
				$ids = array_unique($ids);
			}
			if (empty($ids)) {
				$ids = '';
			}
			VGSE()->helpers->get_current_provider()->update_item_meta($post_id, $cell_key, $ids);
		}

		function _get_repeater_count_values($key, $post_type, $editor) {
			$cache_key = 'vgse_acf_repeater_values' . $key . $post_type;
			$repeater_count_values = get_transient($cache_key);
			if (method_exists(VGSE()->helpers, 'can_rescan_db_fields') && VGSE()->helpers->can_rescan_db_fields($post_type)) {
				$repeater_count_values = false;
			}

			if (!$repeater_count_values) {
				$repeater_count_values = array_filter(array_map('maybe_unserialize', $editor->provider->get_meta_field_unique_values($key, $post_type)));
				set_transient($cache_key, $repeater_count_values, DAY_IN_SECONDS);
			}
			return $repeater_count_values;
		}

		/**
		 * Helper: Convert the advanced custom fields, fields objects to the structure 
		 * required by the WP Sheet Editor Columns API.
		 * @param array $columns
		 * @return null
		 */
		function _register_columns($columns, $editor) {

			if (empty($columns)) {
				return;
			}

			foreach ($columns as $column_index => $column_settings) {

				if (!is_array($column_settings['post_types'])) {
					$column_settings['post_types'] = array($column_settings['post_types']);
				}
				foreach ($column_settings['post_types'] as $post_type) {
					if (!empty($column_settings['cell_type'])) {
						$column_settings['read_only'] = true;
						$column_settings['plain_renderer'] = 'html';
						$column_settings['formatted_renderer'] = 'html';
					}

					if (($column_settings['cell_type'] === 'boton_gallery' || $column_settings['cell_type'] === 'boton_gallery_multiple' ) && $column_settings['width'] < 280) {
						$column_settings['width'] = 300;
					}
					if ($column_settings['data_source'] === 'post_terms') {
						if (!in_array($column_settings['formatted_renderer'], array('text', 'taxonomy_dropdown'))) {
							$column_settings['formatted_renderer'] = 'text';
						} elseif (!in_array($column_settings['plain_renderer'], array('text', 'taxonomy_dropdown'))) {
							$column_settings['plain_renderer'] = 'text';
						}
					}

					$column_args = array(
						'acf_field' => isset($column_settings['acf_field']) ? $column_settings['acf_field'] : array(),
						'data_type' => $column_settings['data_source'], //String (post_data,post_meta|meta_data)	
						'unformatted' => array(
							'data' => $column_settings['key'],
							'readOnly' => ( $column_settings['read_only'] === 'yes') ? true : false,
						), //Array (Valores admitidos por el plugin de handsontable)
						'column_width' => $column_settings['width'], //int (Ancho de la columna)
						'title' => $column_settings['name'], //String (Titulo de la columna)
						'type' => $column_settings['cell_type'], // String (Es para saber si será un boton que abre popup, si no dejar vacio) boton_tiny|boton_gallery|boton_gallery_multiple|(vacio)
						'supports_formulas' => ( $column_settings['allow_formulas'] === 'yes') ? true : false,
						'allow_to_hide' => ( $column_settings['allow_hide'] === 'yes') ? true : false,
						'allow_to_save' => ( $column_settings['read_only'] === 'yes' && !in_array($column_settings['cell_type'], array('boton_gallery', 'boton_gallery_multiple'))) ? false : true,
						'allow_to_rename' => ( $column_settings['allow_rename'] === 'yes') ? true : false,
						'formatted' => array(
							'data' => $column_settings['key'],
							'readOnly' => ( $column_settings['read_only'] === 'yes') ? true : false,
						),
					);

					if (in_array($column_settings['plain_renderer'], array('html', 'text',))) {
						$column_args['unformatted']['renderer'] = $column_settings['plain_renderer'];
					}
					if (in_array($column_settings['formatted_renderer'], array('html', 'text',))) {
						$column_args['formatted']['renderer'] = $column_settings['formatted_renderer'];
					}

					if ($column_settings['plain_renderer'] === 'checkbox') {
						$column_args['unformatted']['type'] = 'checkbox';
						$column_args['unformatted']['checkedTemplate'] = $column_settings['checkedTemplate'];
						$column_args['unformatted']['uncheckedTemplate'] = $column_settings['uncheckedTemplate'];
						$column_args['default_value'] = $column_settings['default_value'];
					}
					if ($column_settings['formatted_renderer'] === 'checkbox') {
						$column_args['formatted']['type'] = 'checkbox';
						$column_args['formatted']['checkedTemplate'] = $column_settings['checkedTemplate'];
						$column_args['formatted']['uncheckedTemplate'] = $column_settings['uncheckedTemplate'];
						$column_args['default_value'] = $column_settings['default_value'];
					}
					if ($column_settings['plain_renderer'] === 'select') {
						$column_args['unformatted']['editor'] = 'select';
						$column_args['unformatted']['selectOptions'] = $column_settings['selectOptions'];
						$column_args['default_value'] = $column_settings['default_value'];
					}
					if ($column_settings['formatted_renderer'] === 'select') {
						$column_args['formatted']['editor'] = 'select';
						$column_args['formatted']['selectOptions'] = $column_settings['selectOptions'];
						$column_args['default_value'] = $column_settings['default_value'];
					}
					if ($column_settings['plain_renderer'] === 'date') {
						$column_args['unformatted'] = array_merge($column_args['unformatted'], array('type' => 'date', 'dateFormat' => 'MM-DD-YYYY', 'correctFormat' => true, 'defaultDate' => date('m-d-Y'), 'datePickerConfig' => array('firstDay' => 0, 'showWeekNumber' => true, 'numberOfMonths' => 1)));
						unset($column_args['unformatted']['renderer']);
					}
					if ($column_settings['formatted_renderer'] === 'date') {
						$column_args['formatted'] = array_merge($column_args['formatted'], array('type' => 'date', 'dateFormat' => 'MM-DD-YYYY', 'correctFormat' => true, 'defaultDate' => date('m-d-Y'), 'datePickerConfig' => array('firstDay' => 0, 'showWeekNumber' => true, 'numberOfMonths' => 1,)));
						unset($column_args['formatted']['renderer']);
					}
					if ($column_settings['data_source'] === 'post_terms') {
						if ($column_settings['plain_renderer'] === 'taxonomy_dropdown') {
							$column_args['unformatted'] = array_merge($column_args['unformatted'], array('type' => 'autocomplete', 'source' => 'loadTaxonomyTerms'));
						} elseif ($column_settings['formatted_renderer'] === 'taxonomy_dropdown') {
							$column_args['formatted'] = array_merge($column_args['formatted'], array('type' => 'autocomplete', 'source' => 'loadTaxonomyTerms'));
						}
					}

					if ($column_settings['cell_type'] === 'metabox') {
						$column_args = array_merge($column_args, $column_settings);
					}

					$editor->args['columns']->register_item($column_settings['key'], $post_type, $column_args);
				}
			}
		}

	}

}

if (!function_exists('WP_Sheet_Editor_ACF_Obj')) {

	function WP_Sheet_Editor_ACF_Obj() {
		return WP_Sheet_Editor_ACF::get_instance();
	}

}


add_action('vg_sheet_editor/initialized', 'WP_Sheet_Editor_ACF_Obj');
