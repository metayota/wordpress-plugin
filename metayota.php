<?php
/**
 * Plugin Name: Metayota
 * Plugin URI: https://www.metayota.com/wordpress
 * Description: Utilize Metayota Editor for custom shortcode development or employ pre-built ones like the form generator. Create forms with no coding experience, or leverage HTML, JavaScript, PHP, and CSS for advanced components. The included database editor component facilitates easy management of custom database tables. 
 * Version: 1.0
 * Author: Metayota
 * Author URI: https://www.metayota.com
 * License: GPL2
 */


include_once( ABSPATH . 'wp-admin/includes/misc.php' );

// Hook for adding admin menus
add_action('admin_menu', 'metayota_plugin_menu');

// Action function for the above hook
function metayota_plugin_menu() {
    global $metayota_plugin_hook_suffix;
    $metayota_plugin_hook_suffix = add_menu_page('Metayota', 'Metayota', 'manage_options', 'metayota', 'metayota_plugin_content');
}

// Function to display the plugin admin page
function metayota_plugin_content() {
    echo '<div id="metayota-container"><iframe id="metayota-iframe" src="/wp-content/plugins/metayota/editor_scripts/editor.index/html.php"></iframe></div>';
}

function metayota_admin_styles() {
    global $metayota_plugin_hook_suffix;
    
    $current_screen = get_current_screen();

    if ($current_screen->id === $metayota_plugin_hook_suffix) {
        echo '<style>
            html, body, #wpwrap, #wpcontent, #wpbody, #wpbody-content, .wrap, #metayota-container {
                height: 100%;
	     /*   margin: 0;*/
		padding: 0;
            }

            #wpbody-content {
                overflow: hidden;
            }

            #metayota-iframe {
                width: 100%;
                height: 100%;
                border: none;
            }
        </style>';
    }
}

add_action('admin_head', 'metayota_admin_styles');


// Function to enqueue the JavaScript file
function metayota_enqueue_script() {
    wp_enqueue_script('metayota-js', home_url() . '/wp-content/plugins/metayota/scripts/framework/framework.javascript', array(), '1.0.0', false);
}

// Hook into the WordPress 'wp_enqueue_scripts' action
add_action('wp_enqueue_scripts', 'metayota_enqueue_script');


function metayota_resource_shortcode($atts,$content,$shortcode_name) {
    $name = $shortcode_name;
    $elID = 'metayota-el-'.rand(0,999999);
    $html = "<div id='$elID'></div>"; // Container for the script to target
    $html.= "<script type=\"text/javascript\">\n";
    $html.= "Resource.wp = true\n";
    $html.= "Tag.registerAndLoad('$name');\n";
    $html.= "Tag.ready('$name').then(result=> {\n";
    $html.= "   let el = document.getElementById('$elID');\n";
    $html.= "   let renderedTag = Tag.render('$name')\n";
    if (!empty($atts)) {
        foreach ($atts as $key => $value) {
            $html .= "   renderedTag.tag.setAttribute('$key', '$value');\n";
        }
    }
    $html.= "   el.appendChild(renderedTag.node);\n";
    $html.= "});";
    $html.= '</script>';

    return $html;
}

global $wpdb;
$resources = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}metayota_resource WHERE type = %s",
        'tag'
    )
);

if(!empty($resources)) {
    foreach($resources as $resource) {
        add_shortcode($resource->name, 'metayota_resource_shortcode');
    }
}


register_activation_hook(__FILE__, 'run_my_sql_file');

function run_my_sql_file() {
    // Get WordPress DB credentials
    $db_name = DB_NAME;
    $db_user = DB_USER;
    $db_password = DB_PASSWORD;
    $db_host = DB_HOST;

    // Create new db connection
    $mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);

    // Check if connection is successful
    if ($mysqli->connect_error) {
        error_log('Connect Error: ' . $mysqli->connect_error);
        return;
    }

    // Path to the SQL file
    $sql_file_path = plugin_dir_path(__FILE__) . 'install.sql';

    // Check if the file exists
    if (file_exists($sql_file_path)) {
        // Read the SQL file
        $sql = file_get_contents($sql_file_path);
	
        // Get WordPress charset and collate
	global $wpdb;
        $charset = $wpdb->charset;
        $collate = $wpdb->collate;

	$sql = str_replace('DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci', "DEFAULT CHARSET=$charset COLLATE=$collate", $sql);
        $sql = str_replace('CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci', "CHARACTER SET $charset COLLATE $collate", $sql);

        // Remove all DROP TABLE statements
        $sql = preg_replace('/^DROP TABLE.*?;/mi', '', $sql);

        // Replace all INSERT INTO with INSERT IGNORE INTO
        $sql = preg_replace('/^INSERT INTO/mi', 'INSERT IGNORE INTO', $sql);
	$sql = preg_replace('/^CREATE TABLE/mi', 'CREATE TABLE IF NOT EXISTS', $sql);


		//$sql = str_replace("DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci", "DEFAULT CHARSET=$charset COLLATE=$collate", $sql);
//$sql = str_replace("CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci", "CHARACTER SET $charset COLLATE $collate", $sql);


        // Execute the SQL commands using multi_query
        if ($mysqli->multi_query($sql)) {
            do {
                // Store result set to free up the next one
                if ($result = $mysqli->store_result()) {
                    $result->free();
                }
            } while ($mysqli->more_results() && $mysqli->next_result());
        } else {
            error_log('Query Error: ' . $mysqli->error);
        }
    } else {
        error_log('SQL file does not exist.');
    }

    // Close the connection
    $mysqli->close();
}



register_activation_hook(__FILE__, 'create_and_populate_usergroup_table');

function create_and_populate_usergroup_table() {
    global $wpdb;

    // Create table
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'metayota_usergroup';

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        title text NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Populate table with existing roles
    $roles = wp_roles(); // This function retrieves all existing roles

    // Adding a check to avoid duplicate insertion
    $existing_roles = $wpdb->get_col("SELECT name FROM $table_name");

    if(!in_array('visitor', $existing_roles)) {
        $wpdb->insert($table_name,['name'=>'visitor','title'=>'Visitor'],['%s','%s']);
    }

    foreach ($roles->role_names as $role => $title) {
        if(!in_array($role, $existing_roles)) {
            $wpdb->insert(
                $table_name,
                array(
                    'name' => $role,
                    'title' => $title
                ),
                array(
                    '%s',
                    '%s'
                )
            );
        }
    }
}


add_action('admin_menu', 'metayota_add_dynamic_admin_menus');

function metayota_add_dynamic_admin_menus() {
    global $wpdb;
    // Tabelle für die Sprachen und Menü-Items
    $tableName = $wpdb->prefix . 'metayota_admin_menu';
    $languageTableName = $wpdb->prefix . 'metayota_language';
    $translationTableName = $wpdb->prefix . 'metayota_translation';

    // Ermitteln der aktuellen Sprache
    $locale = get_locale();
    $current_language = substr($locale, 0, 2);

    // Überprüfen, ob die Sprache unterstützt wird
    $supported_languages = $wpdb->get_col("SELECT language_key FROM {$languageTableName}");
    if (!in_array($current_language, $supported_languages)) {
        $current_language = 'en'; // Standard auf Englisch setzen, wenn nicht unterstützt
    }

    $menu_items = $wpdb->get_results("SELECT * FROM {$tableName}");

    foreach ($menu_items as $item) {
        // JSON aus der Datenbank decodieren
        $resource_with_parameter = json_decode($item->resource_with_parameter, true);
        $options = urlencode(json_encode($resource_with_parameter['options']));
        $tag = $resource_with_parameter['resourcetype'];

        // Menütitel setzen und übersetzen, falls erforderlich
        $menu_title = $item->title;
        $translated_title = $wpdb->get_var($wpdb->prepare("SELECT translation FROM {$translationTableName} WHERE language = %s AND translation_key = %s", $current_language, $menu_title));
        if ($translated_title) {
            $menu_title = $translated_title;
        }

        // Menüslug generieren
        $menu_slug = 'metayota-menu-' . sanitize_title($menu_title);

        // Menü hinzufügen
        add_menu_page(
            $menu_title, // Seite Titel
            $menu_title, // Menü Titel
            'manage_options', // Capability
            $menu_slug, // Menü Slug
              function() use ($tag, $options, $current_language) { // Anonyme Funktion für den Menüinhalt
        // Direktes Einbinden des JavaScript Frameworks
        echo '<script type="text/javascript" src="/wp-content/plugins/metayota/scripts/framework/framework.javascript"></script>'."\n";
        echo '<script>'."\n";
        echo "  Resource.wp = true;\n";
        echo "  Resource.scriptsPath = '/wp-content/plugins/metayota/scripts/';\n";
        // Fügen Sie den Sprachparameter zu allen Anfragen hinzu
        echo "  Resource.appendToAllRequests({'language':'$current_language'});\n";
        echo '</script>'."\n";

        // Dekodieren der $options, um sie als JavaScript-Objekt zu verwenden
        $decodedOptions = json_decode(urldecode($options), true);
        $jsonOptions = json_encode($decodedOptions);

        $name = $tag; // Dies sollte der Wert von `$tag` sein
        $elID = 'metayota-el-'.rand(0,999999);
        $html = "<div class=\"wrap\"><div id='$elID'></div></div>"; // Container für das Ziel-Skript
        $html .= "<script type=\"text/javascript\">\n";
        $html .= "Resource.wp = true;\n";
        $html .= "Resource.scriptsPath = '/wp-content/plugins/metayota/scripts/';\n";
        $html .= "Tag.registerAndLoad('$name', $jsonOptions);\n";
        $html .= "Tag.ready('$name').then(result => {\n";
        $html .= "   let el = document.getElementById('$elID');\n";
        $html .= "   let renderedTag = Tag.render('$name', $jsonOptions);\n";
        $html .= "   el.appendChild(renderedTag.node);\n";
        $html .= "});\n";
        $html .= "</script>\n";

        echo $html;
    }
     /*       function() use ($tag, $options, $current_language) { // Anonyme Funktion für den Menüinhalt
                $iframeSrc = "/wp-content/plugins/metayota/editor_scripts/index/index.php?tag=$tag&params=$options&language=$current_language";
                echo "<iframe src=\"$iframeSrc\" width=\"100%\" height=\"500px\"></iframe>";
     }*/
        );
    }
}


?>
