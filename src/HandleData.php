<?php

/**
 * Class HandleData
 * Esta clase se encargara de manekar todo los querys con la BD
 */
class HandleData
{
    static $DB_COMMENTS = 'comments_plugin';
    static $DB_COMMENTS_RELATIONS = 'comments_plugin_relations';
    static $DB_POSTS = 'posts';
    static $DB_DOCTORS = 'vithas_doctors';
    static $DB_EXTRA = '';

    protected function FirstRun()
    {
        try {
            global $wpdb;
            $table = $wpdb->prefix . self::$DB_COMMENTS; // wp_comments_plugin
            $table_relations = $wpdb->prefix . self::$DB_COMMENTS_RELATIONS; // wp_comments_plugin
            $sql = "CREATE TABLE $table (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `users_id` int(11) DEFAULT NULL,
              `content` text DEFAULT NULL,
              `vote` int(11) NOT NULL DEFAULT 0,
              `tags` text DEFAULT NULL,
              `extra` text DEFAULT NULL,
              PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ";

            $wpdb->query($sql);

            $sql = "CREATE TABLE $table_relations (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `comment_id` int(11) DEFAULT NULL,
              `source` text DEFAULT NULL,
              `extra` text DEFAULT NULL,
              PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ";

            $wpdb->query($sql);

            return true;

        } catch (Exception $e) {
            return $e->getMessage();
        }

    }

    protected function AddComment($data = '', $source = [])
    {
        try {
            global $wpdb;
            $table = $wpdb->prefix . self::$DB_COMMENTS; // wp_comments_plugin
            $data = array(
                'content' => $data['comment'],
                'vote' => $data['vote'],
                'extra' => json_encode($data['user']),
            );

            $wpdb->insert($table, $data);
            $id_comment = $wpdb->insert_id;

            foreach ($source as $src) {
                $table = $wpdb->prefix . self::$DB_COMMENTS_RELATIONS; // wp_comments_plugin
                $data = array(
                    'comment_id' => $id_comment,
                    'extra' => $src['id'],
                    'source' => $src['source']
                );

                $wpdb->insert($table, $data);
            }

            return array('id' => $id_comment);

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    protected function GetDataShort($source = null, $id = null)
    {
        try {
            global $wpdb;
            $table_comments_relations = $wpdb->prefix . self::$DB_COMMENTS_RELATIONS;
            $table_comments = $wpdb->prefix . self::$DB_COMMENTS; // wp_comments_plugin

            $data = $wpdb->get_results("SELECT c.*,r.source FROM $table_comments_relations as r 
                        INNER JOIN $table_comments as c ON c.id = r.comment_id WHERE
                         r.extra = $id AND r.source = '" . $source . "'");


            if (!empty($data)) {
                ob_start();
                include(__DIR__ . "/../template/front/comments.php");
                $html = ob_get_clean();
                echo $html;
            } else {
                return null;
            }


        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    protected function Search($src = '', $source = [])
    {
        try {
            global $wpdb;

            $table = '';
            $tmp = [];
            $result = [];
            $result_parse = [];
            $table_products = $wpdb->prefix . self::$DB_POSTS; // wp_comments_plugin
            $table_doctors = $wpdb->prefix . self::$DB_DOCTORS; // wp_comments_plugin
            foreach ($source as $item) {
                if ($item === 'products') {
                    $table = $table_products;
                    $tmp = $wpdb->get_results("SELECT * FROM $table WHERE post_title LIKE '%$src%'");
                }
                if ($item === 'doctors') {
                    $table = $table_doctors;
                    $tmp = $wpdb->get_results("SELECT *,  ID as `doctors` FROM $table
                                WHERE `name` LIKE '%$src%' OR `surname1` LIKE '%$src%'
                                    OR `surname2` LIKE '%$src%'");
                }

                $result = array_merge($result, $tmp);
            }

            foreach ($result as $res) {


//                {
//                    "value": 1 , "text": "Amsterdam"   , "continent": "Europe"    }

                if ((gettype($res) === 'object') && ($res->doctors)) {
                    $result_parse[] = array(
                        'id' => $res->id,
                        'name' => $res->name . ' ' . $res->surname1 . ' ' . $res->surname2,
                        'source' => 'doctors'
                    );
                } else {
                    $result_parse[] = array(
                        'id' => $res->ID,
                        'name' => $res->post_title,
                        'source' => 'products'
                    );
                }


            }
            return $result_parse;

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function GetComments()
    {
        try {
            global $wpdb;
            $table = $wpdb->prefix . self::$DB_COMMENTS; // wp_comments_plugin
            $table_join = $wpdb->prefix . self::$DB_COMMENTS_RELATIONS; // wp_comments_plugin
            $data = $wpdb->get_results(
                "SELECT i.*, (SELECT GROUP_CONCAT(`source`) FROM $table_join f WHERE f.comment_id = i.id) AS tags FROM $table as i"
            );
            $data = (array)$data;
            foreach ($data as $key => $datum) {
                $data[$key] = (array)$datum;
                if ($data[$key]['extra']) {
                    $parse_extra = json_decode($data[$key]['extra'], 1);
                    $data[$key]['extra'] = $parse_extra;
                }
                if ($data[$key]['tags']) {
                    $data[$key]['tags'] = explode(',', $data[$key]['tags']);
                }
            }

            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

}




//SELECT r.*, c.vote, c.content, c.extra FROM `wp_comments_plugin` as c INNER JOIN wp_comments_plugin_relations as r ON r.comment_id = c.id
//$wpdb = parent::$wpdb;
//$table = $wpdb->prefix . parent::$DB_TAXONOMIES_WC;
//$table_term = $wpdb->prefix . parent::$DB_TERM;
//$table_term_tax = $wpdb->prefix . parent::$DB_TAXONOMIES_WP;
//$data = $wpdb->get_results("SELECT * FROM $table ORDER BY attribute_id ASC");
//
//$data = (array)$data;
//
//$html = array();
//
//foreach ($data as $datum) {
//
//    $tax = "pa_" . $datum->attribute_name;
//
//    $sql_inside = "
//                    SELECT wp_terms.name as value,
//                    wp_terms.term_id,
//                    wp_term_tax.taxonomy
//                     FROM $table_term_tax as wp_term_tax
//                    INNER JOIN $table_term as wp_terms on
//                    wp_term_tax.term_id = wp_terms.term_id
//                    WHERE wp_term_tax.taxonomy = '" . $tax . "'
//                ";
//
//    $data_term = $wpdb->get_results($sql_inside);
//    $data_term = (array)$data_term;
//
//    $html[] = [
//        'tax' => $tax,
//        'attribute_label' => $datum->attribute_label,
//        'attribute_name' => $datum->attribute_name,
//        'attribute_child' => $data_term
//    ];
//
//}
