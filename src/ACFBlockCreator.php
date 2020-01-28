<?php
/**
 * ACF Block Creator
 *
 * @package micropackage/acf-block-creator
 *
 * phpcs:disable WordPress.WP.AlternativeFunctions
 */

namespace Micropackage\ACFBlockCreator;

use Micropackage\DocHooks\Helper;
use Micropackage\Singleton\Singleton;

/**
 * ACFBlockCreator class
 */
class ACFBlockCreator extends Singleton {

	/**
	 * Initiates ACF Block Creator
	 *
	 * @since  1.0.0
	 * @param  array $config Configuration array.
	 * @return ACFBlockCreator
	 */
	public static function init( $config = [] ) {
		return self::get( $config );
	}

	/**
	 * Configuration array
	 *
	 * @var array
	 */
	private $config;

	/**
	 * Constructor
	 *
	 * @param array $config Config array.
	 */
	protected function __construct( $config ) {
		$this->config = wp_parse_args( $config, [
			'blocks_dir'            => 'blocks',
			'scss_dir'              => false,
			'default_category'      => 'common',
			'block_container_class' => 'block-inner',
			'package'               => true,
			'license'               => 'GPL-3.0-or-later',
		] );

		Helper::hook( $this );
	}

	/**
	 * Initiates Block Loader
	 *
	 * @action acf/render_field_group_settings
	 *
	 * @since  1.0.0
	 * @param  array $field_group Field group params.
	 * @return void
	 */
	public function render_field_group_settings( $field_group ) {

		// If fields has been saved, don't show them again.
		if ( isset( $field_group['create_gutenberg_block'] ) && $field_group['create_gutenberg_block'] ) {
			return;
		}

		acf_render_field_wrap( [
			'label'        => 'Create Gutenberg block',
			'instructions' => 'Group location will be automatically set to freshly created block',
			'type'         => 'true_false',
			'name'         => 'create_gutenberg_block',
			'prefix'       => 'acf_field_group',
			'value'        => false,
			'ui'           => true,
		] );

		acf_render_field_wrap( [
			'label'        => 'Block name',
			'instructions' => '',
			'type'         => 'text',
			'name'         => 'block_name',
			'prefix'       => 'acf_field_group',
			'value'        => isset( $field_group['block_name'] ) ? $field_group['block_name'] : '',
		] );

		acf_render_field_wrap( [
			'label'        => 'Block slug',
			'instructions' => '',
			'type'         => 'text',
			'name'         => 'block_slug',
			'prefix'       => 'acf_field_group',
			'value'        => isset( $field_group['block_slug'] ) ? $field_group['block_slug'] : '',
		] );

		$default_category = apply_filters( 'micropackage/acf-block-creator/block-category', $this->config['default_category'] );

		acf_render_field_wrap( [
			'label'        => 'Block category',
			'instructions' => '',
			'type'         => 'select',
			'name'         => 'block_category',
			'prefix'       => 'acf_field_group',
			'value'        => isset( $field_group['block_category'] ) ? $field_group['block_category'] : $default_category,
			'choices'      => array_reduce( get_block_categories( null ), function( $result, $item ) {
				if ( 'reusable' !== $item['slug'] ) {
					$result[ $item['slug'] ] = $item['title'];
				}
				return $result;
			}, [] ),
		] );

		acf_render_field_wrap( [
			'label'        => 'Block align',
			'instructions' => '',
			'type'         => 'select',
			'name'         => 'block_align',
			'prefix'       => 'acf_field_group',
			'value'        => isset( $field_group['block_align'] ) ? $field_group['block_align'] : 'full',
			'choices'      => [
				'full'   => 'full',
				'center' => 'center',
				'wide'   => 'wide',
				'left'   => 'left',
				'right'  => 'right',
			],
		] );

		$container_class = apply_filters( 'micropackage/acf-block-creator/block-container-class', $this->config['block_container_class'] );

		acf_render_field_wrap( [
			'label'        => 'Block container class',
			'instructions' => '',
			'type'         => 'text',
			'name'         => 'block_container_class',
			'prefix'       => 'acf_field_group',
			'value'        => isset( $field_group['block_container_class'] ) ? $field_group['block_container_class'] : $container_class,
		] );
	}

	/**
	 * Initiates Block Loader
	 *
	 * @action acf/update_field_group 5
	 *
	 * @since  1.0.0
	 * @param  array $field_group Field group params.
	 * @return void
	 */
	public function update_field_group( $field_group ) {
		if ( ! $field_group['create_gutenberg_block'] || empty( $field_group['block_slug'] ) ) {
			return;
		}

		$blocks_dir = get_stylesheet_directory() . '/' . trim( $this->config['blocks_dir'], '/' );

		if ( ! $this->dir_exists( $blocks_dir ) ) {
			return;
		}

		// Populate fields.
		$field_group['fields'] = acf_get_fields( $field_group );

		$name       = $field_group['block_name'];
		$slug       = $field_group['block_slug'];
		$category   = $field_group['block_category'];
		$align      = $field_group['block_align'];
		$textdomain = wp_get_theme()->get( 'TextDomain' );

		// Group location.
		$field_group['location'] = [
			[
				[
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'acf/' . $field_group['block_slug'],
				],
			],
		];

		$comment = [];

		if ( $this->config['package'] ) {
			$package = $this->config['package'];

			if ( true === $package ) {
				$package = get_bloginfo( 'name' );
			}

			$comment[] = " * @package {$package}";
		}

		if ( is_string( $this->config['license'] ) ) {
			$comment[] = " * @license {$this->config['license']}";
		}

		if ( $comment ) {
			$comment[] = ' *';
		}

		$block_config = [
			'Block Name' => $name,
			'Category'   => $category,
			'Align'      => $align,
		];

		foreach ( $block_config as $key => $value ) {
			$comment[] = " * {$key}: $value";
		}

		$comment = substr( implode( "\n", $comment ), 3 );

		// Create block template file.
		$fields_markup = [];
		foreach ( $field_group['fields'] as $field ) {
			$fields_markup[] = $this->get_field_markup( $field );
		}

		$class = $field_group['block_container_class'] ? " class=\"{$field_group['block_container_class']}\"" : null;

		$template = file_get_contents( dirname( __FILE__ ) . '/block.php' );
		$template = str_replace(
			[
				'{COMMENT}',
				'{FIELDS}',
				'{CLASS}',
			],
			[
				$comment,
				implode( "\n", $fields_markup ),
				$class,
			],
			$template
		);

		file_put_contents( $blocks_dir . "/{$slug}.php", $template );

		// Create block scss partial.
		if ( is_string( $this->config['scss_dir'] ) ) {
			$scss_dir = get_stylesheet_directory() . '/' . trim( $this->config['scss_dir'], '/' );
			$scss     = ".block.$slug {\n\n}";

			if ( $this->dir_exists( $scss_dir ) ) {
				file_put_contents( $scss_dir . "/_$slug.scss", $scss );
			}
		}

		/**
		 * ACF save routine below.
		 */

		// Make a backup of field group data and remove some args.
		$_field_group = $field_group;
		acf_extract_vars( $_field_group, [ 'ID', 'key', 'title', 'menu_order', 'fields', 'active', '_valid' ] );

		// Create array of data to save.
		$save = [
			'ID'           => $field_group['ID'],
			'post_content' => maybe_serialize( $_field_group ),
		];

		// Unhook wp_targeted_link_rel() filter from WP 5.1 corrupting serialized data.
		remove_filter( 'content_save_pre', 'wp_targeted_link_rel' );

		// Slash data.
		// WP expects all data to be slashed and will unslash it (fixes '\' character issues).
		wp_update_post( wp_slash( $save ) );

		// Flush field group cache.
		acf_flush_field_group_cache( $field_group );

		/**
		 * Fix for JSON save, just resave the file.
		 */
		if ( acf_get_setting( 'json' ) ) {
			acf_write_json_field_group( $field_group );
		}
	}

	/**
	 * Enqueues admin scripts
	 *
	 * @action admin_enqueue_scripts
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function enqueue_scripts() {
		$home_path    = wp_normalize_path( get_home_path() );
		$package_path = wp_normalize_path( dirname( dirname( __FILE__ ) ) );
		$script_url   = site_url( str_replace( $home_path, '', $package_path ) . '/assets/js/acf-block-creator.js' );

		wp_enqueue_script( 'acf-block-creator', $script_url, [ 'jquery' ], '1.0.0', true );
	}

	/**
	 * Enqueues admin scripts
	 *
	 * @since  1.0.0
	 * @param  string $dir Full dir path.
	 * @return bool
	 */
	private function dir_exists( $dir ) {
		$exists = is_dir( $dir );

		if ( ! $exists ) {
			$exists = wp_mkdir_p( $dir );
		}

		return $exists;
	}

	/**
	 * Gets field markup
	 *
	 * @since  1.0.0
	 * @param  array $field Field configuration.
	 * @return string
	 */
	private function get_field_markup( $field ) {
		$markup_file = dirname( __FILE__ ) . '/fields/' . $field['type'] . '.php';

		if ( file_exists( $markup_file ) ) {
			$markup = file_get_contents( $markup_file );
		} else {
			$markup = file_get_contents( dirname( __FILE__ ) . '/fields/default.php' );
		}

		$subfields = [];

		if ( 'repeater' === $field['type'] ) {
			foreach ( $field['sub_fields'] as $sub_field ) {
				$subfields[] = str_replace(
					[
						'get_field(',
						'the_field(',
					],
					[
						'get_sub_field(',
						'the_sub_field(',
					],
					$this->get_field_markup( $sub_field )
				);
			}
		}

		$markup = str_replace(
			[
				'{name}',
				'{subfields}',
			],
			[
				$field['name'],
				implode( "\n", $subfields ),
			],
			$markup
		);

		return $markup;
	}
}
