<?php

if (!defined('ABSPATH')) {
    exit;
}

?>
<div class="wrap postbox" style="padding: 10px">
    <h1 style="padding: 0px;">PHP Fatal Error Blocker - <?php echo ((function_exists('ma_php_fatal_error_handler'))?"<span style='color:green;'>Configured</span>":"<span style='color:red;'>Needs Configuration<span>"); ?></h1>
    <hr>
    <?php
        if(function_exists('ma_php_fatal_error_handler'))
        {
            echo "<p>The following code we added in your wp-config.php in order to catch the fatal errors.</p>";
        }
        else
        {
            echo "<p>The following code you need to add it in your wp-config.php in order to catch the fatal errors</p><p>Add those lines after this line <b>define('ABSPATH', dirname(__FILE__) . '/')</b>. Nearly you need to add it in 88th line of your wp-config.php.</p>";
        }
    ?>
    <textarea rows="3" readonly style="width:100%;padding: 15px;"><?php
echo "/* PHP Fatal Error Blocker! Do not Edit. */
if(file_exists('" . MA_FATAL_BLOCKER_MAIN_PATH . "php-fatal-error-catcher.php" . "'))
include_once('" . MA_FATAL_BLOCKER_MAIN_PATH . "php-fatal-error-catcher.php" . "');";?></textarea>
    <?php
        if(function_exists('ma_php_fatal_error_handler'))
        {
            echo "<p>Once the plugin is deactivated, we will remove the added code automatically.</p>";
        }
        else
        {
            echo "<p>Once the plugin is deactivated, please remove the code that you added manually.</p>";
        }
    ?>
</div>