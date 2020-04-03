<?php

/**
 * Plugin Name: Plugin de comentarios
 * Plugin URI: https://github.com/leifermendez
 * Description: Plugin para agregar comentarios
 * Version: 1.0
 * Author: Leifer Mendez
 * Author URI: https://github.com/leifermendez
 * License: A "Slug" license name e.g. GPL12
 */


require_once(__DIR__ . '/src/HandleData.php');


class PluginsMainClass extends HandleData
{

//    public static $DEBUG_MODE = true;
//    public static $AUTO_GRID = true;
//    public $filters;

    public function __construct()
    {
        global $wpdb;
        if (is_admin()) {
            register_activation_hook(__FILE__, array($this, 'registerActivation'));
//            register_deactivation_hook(__FILE__, array($this, 'dropSettingTable'));
        }

        add_action('wp_enqueue_scripts', array($this, 'assets'));
        add_action('admin_enqueue_scripts', array($this, 'admin_assets'));

        add_action('admin_menu', array($this, 'AdminPage'));

        add_action('wp_ajax_get_source_tag', array($this, 'SearchTag'));
        add_action('wp_ajax_nopriv_get_source_tag', array($this, 'SearchTag'));
        add_action('wp_ajax_other_comment_data', array($this, 'AddComment'));
        add_action('wp_ajax_nopriv_other_comment_data', array($this, 'AddComment'));
        add_action('wp_ajax_get_comments', array($this, 'GetComments'));
        add_action('wp_ajax_nopriv_get_comments', array($this, 'GetComments'));

        // [bartag foo="foo-value"]

        add_shortcode('comments_plugin', array($this, 'ShortCode'));

//        add_action('wp_ajax_nopriv_get_source_tag', array($this, 'SearchTag'));


    }

    private function LoadHtml($src = null)
    {
        try {
            ob_start();
            include(__DIR__ . "/template/$src");
            $html = ob_get_clean();
            echo $html;

        } catch (Exception $e) {
            echo 'NOT_LOAD';
        }
    }

    public function admin_assets()
    {

        $list_js = [
            [
                'name' => 'comments-plugins-comment',
                'url' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js',
                'load' => false
            ],
            [
                'name' => 'comments-plugins-comment',
                'url' => 'https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.7.8/angular.min.js',
                'load' => false
            ],
            [
                'name' => 'comments-plugins-comment',
                'url' => 'https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.7.8/angular-sanitize.min.js',
                'load' => false
            ],
            [
                'name' => 'comments-plugins-comment',
                'url' => 'https://cdnjs.cloudflare.com/ajax/libs/angular-ui-select/0.20.0/select.min.js',
                'load' => false
            ],
            [
                'name' => 'comments-plugins-comment',
                'url' => 'https://rawgithub.com/CodeSeven/toastr/master/toastr.js',
                'load' => false
            ],
            [
                'name' => 'comments-plugins-comment',
                'url' => plugin_dir_url(__FILE__) . 'assets/js/main.js',
                'load' => true
            ],
            [
                'name' => 'comments-plugins-comment',
                'main' => true,
                'url' => plugin_dir_url(__FILE__) . 'assets/js/app.js',
                'load' => true
            ]
        ];

        $list_css = [
            [
                'name' => 'comments-plugins-comment-css',
                'url' => 'http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.css',
            ],
            [
                'name' => 'comments-plugins-comment-css-3',
                'url' => 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css',
            ],
            [
                'name' => 'comments-plugins-comment',
                'url' => 'https://cdnjs.cloudflare.com/ajax/libs/angular-ui-select/0.20.0/select.min.css',
            ],
            [
                'name' => 'comments-plugins-comment-css',
                'url' => plugin_dir_url(__FILE__) . 'assets/css/main.css',
            ]
        ];

        /**
         * Register Styles
         */

        $count_css = 0;
        foreach ($list_css as $css) {
            $count_css++;
            wp_register_style($css['name'] . '_' . $count_css,
                $css['url']);
            wp_enqueue_style($css['name'] . '_' . $count_css);
        }
        /**
         * Register JS
         */
        $count_js = 0;
        foreach ($list_js as $js) {
            $count_js++;
            wp_register_script($js['name'] . '_' . $count_js,
                $js['url'],
                array(), '1.0.0', $js['load']);
            wp_enqueue_script($js['name'] . '_' . $count_js);

            if ($js['main']) {
                $translation_array = array('siteUrl' => get_site_url());
                wp_localize_script($js['name'] . '_' . $count_js,
                    'backend_data', $translation_array);
            }
        }


    }


    public function assets()
    {

        $list_js = [
            [
                'name' => 'comments-plugins-comment-front',
                'url' => plugin_dir_url(__FILE__) . 'dependencies/OwlCarousel2-2.3.4/dist/owl.carousel.js',
                'load' => true
            ],
            [
                'name' => 'comments-plugins-comment-front',
                'url' => plugin_dir_url(__FILE__) . 'assets/js/main.js',
                'load' => true
            ]
        ];

        $list_css = [
            [
                'name' => 'comments-plugins-comment-front',
                'url' => plugin_dir_url(__FILE__) . 'dependencies/OwlCarousel2-2.3.4/dist/assets/owl.carousel.css'
            ],
            [
                'name' => 'comments-plugins-comment-front',
                'url' => plugin_dir_url(__FILE__) . 'dependencies/OwlCarousel2-2.3.4/dist/assets/owl.theme.default.css'
            ],
            [
                'name' => 'comments-plugins-comment-front',
                'url' => plugin_dir_url(__FILE__) . 'dependencies/animate/animate.css'
            ],
            [
                'name' => 'comments-plugins-comment-front',
                'url' => plugin_dir_url(__FILE__) . 'assets/css/main.css'
            ],
        ];

        /**
         * Register Styles
         */

        $count_css = 0;
        foreach ($list_css as $css) {
            $count_css++;
            wp_register_style($css['name'] . '_' . $count_css,
                $css['url']);
            wp_enqueue_style($css['name'] . '_' . $count_css);
        }
        /**
         * Register JS
         */
        $count_js = 0;
        foreach ($list_js as $js) {
            $count_js++;
            wp_register_script($js['name'] . '_' . $count_js,
                $js['url'],
                array(), '1.0.0', $js['load']);
            wp_enqueue_script($js['name'] . '_' . $count_js);

        }

    }

    /**
     * Ajustes del menu
     */

    public function AdminPage()
    {
        add_menu_page(
            'Comentarios',
            'Comentarios (1.0)',
            'manage_options',
            'comentarios-plugin',
            function () {
                $this->LoadHtml('admin/main.html'); //template/admin/main.php
            },
            'dashicons-admin-comments'
        );

    }

    /**
     * Esta funcion se ejecutar cuando se entra en la pagina de Plugins en el panel de WP
     */
    public function InitPlugin()
    {
        if (is_admin()) {


        }
    }


    /**
     * Plugin Activacion: Se ejecuta cuando le damos click en activar
     */

    public function registerActivation()
    {
        return parent::FirstRun();
    }

    public function dropSettingTable()
    {
        return parent::DropTableSetting();
    }

    public function InlineNotice($data, $response)
    {
        var_dump($data);
        var_dump($response);
    }

    /**
     * Funciones Generales
     */

    public function SearchTag()
    {
        try {
            $src = $_GET['src'];
            $sources = explode(',', $_GET['sources']);

            if (!isset($_GET['sources'])) {
                throw new Exception('Debes pasar una fuente');
            }

            $data = parent::Search($src, $sources);
            echo json_encode($data);
            wp_die();
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }

    }

// aaa  [comments_plugin mode="products" id="10"]
    public function ShortCode($atts)
    {
        try {
            $mode = $atts['mode'];
            $id = ($atts && $atts['id']) ? $atts['id'] : get_the_ID();
            $data = parent::GetDataShort($mode, $id);
            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function AddComment($comment = '', $source = [])
    {
        try {

            $source = $_POST['select'];
            $data = [
                'user' => $_POST['user_mocked'],
                'comment' => $_POST['comment'],
                'vote' => $_POST['vote'],
            ];
            $data = parent::AddComment($data, $source);
            echo json_encode($data);
            wp_die();

        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function GetComments()
    {
        try {

            $data = parent::GetComments();
            echo json_encode($data);
            wp_die();

        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

}


new PluginsMainClass();
