<?php

class FS_Plugin {

    protected $ssp_admin;
    protected $ssp_settings;

    protected $ssp_unset_settings = [
        'general' => [
            'use_post_types',
            'include_in_main_query',
            'player_locations',
            'player_content_location',
            'player_content_visibility',
            'player_meta_data_enabled',
            'player_style',
            'player_background_skin_colour',
            'player_wave_form_colour',
            'player_wave_form_progress_colour',
        ],
        'security',
        'castos-hosting',
        'extensions', // @todo why is this not working?
    ];

    protected $post_type = 'fs_sermon';

    public function __construct() {
        global $ssp_admin, $ssp_settings;

        $this->ssp_admin = $ssp_admin;
        $this->ssp_settings = $ssp_settings;

        add_action( 'init', [ $this, 'register_sermons' ] );
        add_action( 'init', [ $this, 'register_taxonomies' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_assets' ] );
        add_action( 'admin_menu', [ $this, 'add_settings_page' ] );

        remove_action( 'admin_footer_text', [ $this->ssp_admin, 'admin_footer_text' ], 1 );
        remove_action( 'init', [ $this->ssp_admin, 'register_post_type' ], 1 );
        remove_action( 'admin_menu', [ $this->ssp_settings, 'add_menu_item' ] );

        add_filter( 'ssp_settings_fields', [ $this, 'unset_ssp_settings' ] );
        add_filter( 'ssp_podcast_post_types', [ $this, 'podcast_post_type' ] );
    }

    public function register_sermons() {
        if ( apply_filters( 'fs_enable_sermons', true ) ) {
            register_post_type( $this->post_type, apply_filters( 'fs_sermon_args', [
                'labels' => [
                    'name' => 'Sermons',
                    'singular_name' => 'Sermon',
                    'add_new' => 'Add New',
                    'add_new_item' => 'Add New Sermon',
                    'edit_item' => 'Edit Sermon',
                    'new_item' => 'New Sermon',
                    'view_item' => 'View Sermon',
                    'view_items' => 'View Sermons',
                    'search_items' => 'Search Sermons',
                    'not_found' => 'No sermons found.',
                    'not_found_in_trash' => 'No sermons found in trash.',
                    'all_items' => 'All Sermons',
                ],
                'public' => true,
                'menu_icon' => 'dashicons-controls-play',
                'supports' => [ 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions' ],
                'has_archive' => true,
                'rewrite' => [ 'slug' => 'sermons', 'with_front' => true ],
                'show_in_rest' => true,
            ] ) );
        }
    }

    public function register_taxonomies() {
        if ( apply_filters( 'fs_enable_series', true ) ) {
            $this->register_taxonomy( 'Series', 'Series' );
        }

        if ( apply_filters( 'fs_enable_speakers', true ) ) {
            $this->register_taxonomy( 'Speakers', 'Speaker' );
        }

        if ( apply_filters( 'fs_enable_books', true ) ) {
            $this->register_taxonomy( 'Books', 'Book' );
        }

        if ( apply_filters( 'fs_enable_topics', true ) ) {
            $this->register_taxonomy( 'Topics', 'Topic' );
        }
    }

    public function admin_assets() {
        wp_enqueue_style( 'fs-admin', FS_URL . '/assets/css/admin.css' );
    }

    public function add_settings_page() {
        add_submenu_page(
            'edit.php?post_type=' . $this->post_type,
            'Settings',
            'Settings',
            'manage_podcast',
            'podcast_settings',
            [ $this->ssp_settings, 'settings_page' ]
        );
    }

    public function podcast_post_type() {
        return [ $this->post_type ];
    }

    public function unset_ssp_settings( $settings ) {
        foreach ( $settings as $key => $tab ) {
            foreach ( $tab['fields'] as $i => $field ) {
                if ( in_array( $key, $this->ssp_unset_settings ) ) {
                    unset( $settings[ $key ] );
                } elseif ( isset( $this->ssp_unset_settings[ $key ] ) && in_array( $field['id'], $this->ssp_unset_settings[ $key ] ) ) {
                    unset( $settings[ $key ]['fields'][ $i ] );
                }
            }
        }

        return $settings;
    }

    protected function register_taxonomy( $name, $singular_name ) {
        $name_key = sanitize_key( $name );
        $name_lower = strtolower( $name );
        $singular_name_key = sanitize_key( $singular_name );
        $singular_name_lower = strtolower( $singular_name );

        register_taxonomy( 'fs_' . $singular_name_key, $this->post_type, apply_filters( "fs_{$singular_name_key}_args", [
            'labels' => [
                'name' => $name,
                'singular_name' => $singular_name,
                'all_items' => "All {$name}",
                'edit_item' => "Edit {$singular_name}",
                'view_item' => "View {$singular_name}",
                'update_item' => "Update {$singular_name}",
                'add_new_item' => "Add New {$singular_name}",
                'new_item_name' => "New {$singular_name} Name",
                'search_items' => "Search {$name}",
                'popular_items' => "Popular {$name}",
                'separate_items_with_commas' => "Separate {$name_lower} with commas",
                'add_or_remove_items' => "Add or remove {$name_lower}",
                'choose_from_most_used' => "Choose from the most used {$name_lower}",
                'not_found' => "No {$name_lower} found.",
            ],
            'show_in_rest' => true,
            'show_admin_column' => true,
            'rewrite' => [ 'slug' => 'sermon-' . $name_key, 'with_front' => false ],
        ] ) );
    }

}

$GLOBALS['fixel-sermons']['plugin'] = new FS_Plugin();
