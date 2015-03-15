<?php

spl_autoload_register(function($class_name) {
    $class_file = str_replace('\\', '/', $class_name);
    $file_name = __DIR__.'/../'.$class_file.'.class.php';
    if (file_exists($file_name)) {
        include $file_name;
    }
});

error_reporting(0);

set_error_handler(function($err_type, $err_str, $err_file = null, $err_line = null) {

    echo '<pre>';
    var_dump(array(
        $err_type, $err_str, $err_file, $err_line
    ));
    echo '</pre>';

    exit;
}, E_ALL);

register_shutdown_function(function() {

    $last_error = error_get_last();

    if (is_null($last_error)) {
        return null;
    }

    echo '<pre>';
    var_dump($last_error);
    echo '</pre>';

});

function deb($data) {

    $data = serialize($data);
    $data = base64_encode($data);
    file_put_contents('/home/vagrant/pipe', $data);

}