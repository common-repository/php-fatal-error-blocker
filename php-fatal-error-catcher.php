<?php

if (!defined('ABSPATH')) {
    exit;
}

register_shutdown_function("ma_php_fatal_handler");

function ma_php_fatal_error_handler()
{
    return true;
}

function ma_php_fatal_handler() {
    $errfile = "unknown file";
    $error = error_get_last();
    if ($error !== NULL) {
        if($error['type'] == 2 || $error['type'] == 8)
        {
            return;
        }
        $plug = get_option('active_plugins');
        if(!in_array('php-fatal-error-blocker/php-fatal-error-blocker.php',$plug))
        {
            return;
        }
        if(!function_exists('deactivate_plugins'))
        {
            require_once(ABSPATH . "wp-admin/includes/plugin.php");
        }
        if(!function_exists('current_user_can'))
        {
            require_once(ABSPATH."wp-includes/capabilities.php");
        }
        if(!function_exists('wp_get_current_user'))
        {
            require_once(ABSPATH."wp-includes/pluggable.php");
        }
        $errfile = $error["file"];
        if(strpos($errfile, 'wp-content') !== FALSE && strpos($errfile, 'themes') !== FALSE)
        {
            ma_generate_pro_error_html($error,'theme');
            return;
        }
        if(strpos($errfile, 'wp-content') !== FALSE && strpos($errfile, 'plugins') !== FALSE)
        {
            $pb = plugin_basename($errfile);
            $parsed = explode('/', $pb);
            $main = $parsed[0];
            foreach ($plug as $p) {
                $pp = explode('/', $p);
                if($pp[0] == $main)
                {
                    $main = $p;
                }
            }
            $data = get_plugin_data(WP_PLUGIN_DIR.'/'.$main);
            $deact = false;
            if(current_user_can('activate_plugins'))
            {
                deactivate_plugins(plugin_basename($main));
                $deact = true;
            }
            ma_generate_error_html($data,$error,$pb,$deact);
        }
    }
}

function ma_generate_error_html($data,$error,$pb,$deact) {
    echo '<script>document.body.innerHTML = "";</script>';
    echo "<style>
        body{
            height:100%;
            background-color:#f5f5f5;
            margin:0px;
        }
        .main{
            padding:50px 100px;
        }
        table{
            border: 1px solid black;
            padding:10px;
        }
        </style>";
    echo "
        <div class='main'>
            <center>
                <a href='https://moreaddons.com' target='_blank'><img width='200px' height='50px' src=". plugin_dir_url(__FILE__) ."assets/img/logo.png"."></a>
                <h1>PHP Fatal Error Blocker</h1>
                <p>The Plugin <b>".$data['Title']."</b> by <a href='".$data['AuthorURI']."' target='_blank'><b>".$data['Author']."</b></a> is generating a fatal error.</p>
                <div style='background-color:lightgray;padding:10px;'>
                    <table>
                        <tr>
                            <td>Type</td>
                            <td>:</td>
                            <td>".$error["type"]."</td>
                        </tr>
                        <tr>
                            <td>Line</td>
                            <td>:</td>
                            <td>".$error["line"]."</td>
                        </tr>
                        <tr>
                            <td>File</td>
                            <td>:</td>
                            <td>".$pb."</td>
                        </tr>
                        <tr>
                            <td>Message</td>
                            <td>:</td>
                            <td>".$error["message"]."</td>
                        </tr>
                    </table>
                </div>
                <p>The Plugin is ".(($deact)?"already":"not ")." deactivated. ".(($deact)?"No Worries!":"Contact Administrator!")."</p>
                <a href='".site_url()."' target='_blank'>Back to Site</a><br><br>
                <a href='https://wordpress.org/support/plugin/php-fatal-error-blocker/reviews/#new-post'>Give us feedback</a>
            </center>
        </div>
    ";
}

function ma_generate_pro_error_html($error,$geo) {
    echo '<script>document.body.innerHTML = "";</script>';
    echo "<style>
        body{
            height:100%;
            background-color:#f5f5f5;
            margin:0px;
        }
        .main{
            padding:50px 100px;
        }
        table{
            border: 1px solid black;
            padding:10px;
        }
        </style>";
    echo "
        <div class='main'>
            <center>
                <a href='https://moreaddons.com' target='_blank'><img width='200px' height='50px' src=". plugin_dir_url(__FILE__) ."assets/img/logo.png"."></a>
                <h1>PHP Fatal Error Blocker</h1>
                <p>The Fatal error is may cause from ".$geo." files.</p>
                <div style='background-color:lightgray;padding:10px;'>
                    <table>
                        <tr>
                            <td>Type</td>
                            <td>:</td>
                            <td>".$error["type"]."</td>
                        </tr>
                        <tr>
                            <td>Line</td>
                            <td>:</td>
                            <td>".$error["line"]."</td>
                        </tr>
                        <tr>
                            <td>File</td>
                            <td>:</td>
                            <td>".$error["file"]."</td>
                        </tr>
                        <tr>
                            <td>Message</td>
                            <td>:</td>
                            <td>".$error["message"]."</td>
                        </tr>
                    </table>
                </div>
                <p>The Fatal error from plugin files only can be catched in free version. For all WP Fatal erros please <a href='#' target='_blank'>upgrade to pro</a> to safe your site.</p>
                <a href='".site_url()."' target='_blank'>Back to Site</a><br><br>
                <a href='https://wordpress.org/support/plugin/php-fatal-error-blocker/reviews/#new-post'>Give us feedback</a>
            </center>
        </div>
    ";
}
