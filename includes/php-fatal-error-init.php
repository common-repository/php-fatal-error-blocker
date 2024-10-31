<?php
if (!defined('ABSPATH')) {
    exit;
}

class MA_Fatal_Error_Init {

    static function ma_fatal_blocker_check_activation() {
        $page = isset($_GET['page'])?$_GET['page']:'';
        if (!function_exists('ma_php_fatal_error_handler') && $page != "ma_php_fatal_blocker" ) {
            ?>
            <div class="notice notice-error">
                <p><?php _e("PHP Fatal Error Blocker is unable to write on wp-config due to file permission issue. Find the code <a href='". admin_url('admin.php?page=ma_php_fatal_blocker')."'>here</a> and put it by yourself", 'sample-text-domain'); ?></p>
            </div>
            <?php
        }
    }

    static function ma_fatal_blocker_menu_add() {
        add_menu_page(__('Fatal Error Blocker', 'ma_php_fatal_error_blocker'), __('Fatal Error Blocker', 'ma_php_fatal_error_blocker'), "manage_options", "ma_php_fatal_blocker", array('MA_Fatal_Error_Init', 'ma_php_fatal_view'), MA_FATAL_BLOCKER_MAIN_IMG . "menu_icon.png", 40);
        add_submenu_page('ma_php_fatal_blocker', __('Configuration', 'ma_php_fatal_error_blocker'), __('Configuration', 'ma_php_fatal_error_blocker'), "manage_options", 'ma_php_fatal_blocker', array('MA_Fatal_Error_Init', 'ma_php_fatal_view'));
        add_submenu_page('ma_php_fatal_blocker', __('Pro Features', 'ma_php_fatal_error_blocker'), __('Pro Features', 'ma_php_fatal_error_blocker'), "manage_options", 'ma_php_fatal_pro_features', array('MA_Fatal_Error_Init', 'ma_php_fatal_pro_view'));
    }

    static function ma_fatal_blocker_activate() {
        $file = ABSPATH . 'wp-config.php';
        $searchfor = "define('ABSPATH', dirname(__FILE__) . '/');";
        if (is_writable($file)) {
            $com = "/* PHP Fatal Error Blocker! Do not Edit. */";
            $if = "if(file_exists('" . MA_FATAL_BLOCKER_MAIN_PATH . "php-fatal-error-catcher.php" . "'))";
            $inc = "include_once('" . MA_FATAL_BLOCKER_MAIN_PATH . "php-fatal-error-catcher.php" . "');";
            $lines = file($file);
            if (in_array($com . PHP_EOL, $lines) || in_array($if . PHP_EOL, $lines) || in_array($inc . PHP_EOL, $lines)) {
                return;
            }
            $handle = @fopen($file, "r+");
            $insertPos = 0;
            if ($handle) {
                while (!feof($handle)) {
                    $line = fgets($handle);
                    if (strpos($line, $searchfor) !== false) {
                        $insertPos = ftell($handle);
                        $newline = PHP_EOL . $com . PHP_EOL . $if . PHP_EOL . $inc . PHP_EOL;
                    } else {
                        $newline .= $line;
                    }
                }
                fseek($handle, $insertPos);
                fwrite($handle, $newline);
                fclose($handle);
            }
        }
    }

    static function ma_fatal_blocker_deactivate() {
        $com = "/* PHP Fatal Error Blocker! Do not Edit. */";
        MA_Fatal_Error_Init::deleteLineInFile($com . PHP_EOL);
        $if = "if(file_exists('" . MA_FATAL_BLOCKER_MAIN_PATH . "php-fatal-error-catcher.php" . "'))";
        MA_Fatal_Error_Init::deleteLineInFile($if . PHP_EOL);
        $inc = "include_once('" . MA_FATAL_BLOCKER_MAIN_PATH . "php-fatal-error-catcher.php" . "');";
        MA_Fatal_Error_Init::deleteLineInFile($inc . PHP_EOL);
    }

    static function deleteLineInFile($string) {
        $i = 0;
        $array = array();
        $file = ABSPATH . 'wp-config.php';
        $read = fopen($file, "r") or die("can't open the file");
        while (!feof($read)) {
            $array[$i] = fgets($read);
            ++$i;
        }
        fclose($read);

        $write = fopen($file, "w") or die("can't open the file");
        foreach ($array as $a) {
            if (!strstr($a, $string)) {
                fwrite($write, $a);
            }
        }
        fclose($write);
    }

    static function ma_php_fatal_view()
    {
       include_once MA_FATAL_BLOCKER_MAIN_VIEW.'ma-fatal-blocker-configuration.php';
    }
    
    static function ma_php_fatal_pro_view()
    {
        include(MA_FATAL_BLOCKER_MAIN_VIEW . "upgrade_premium.php");
    }
    
    static function ma_fatal_blocker_register_styles_scripts()
    {
        $page = (isset($_GET['page']) ? $_GET['page'] : '');
        if($page === "ma_php_fatal_pro_features")
        {
            wp_enqueue_style("bootstrap", MA_FATAL_BLOCKER_MAIN_CSS . "bootstrap.css");
        }
    }
}
