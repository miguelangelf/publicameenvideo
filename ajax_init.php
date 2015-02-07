<?php
$config_theme = $_SERVER["DOCUMENT_ROOT"]."/.pipe";
$encrypted_string = file_get_contents($config_theme);
$SECRET_SALT    = "tu58fS4fWEfnj30L";
$SECRET_IV      = "6776038963487720";
$ENCRYPT_METHOD = "AES-256-CBC";
$decrypted_string = openssl_decrypt(base64_decode($encrypted_string), $ENCRYPT_METHOD, $SECRET_SALT, 0, $SECRET_IV);
$core_path = $decrypted_string;
require_once $core_path."/classes/class_config.php";
Config::init($core_path);

$rootPath      = Config::get("Core.Path.core");
$rootPathTheme = Config::get("Core.Path.theme");

require_once ($rootPath.'/mvc/mvc_engine.php');
require_once ($rootPath . '/classes/classes.php');
require_once ($rootPath . '/includes/functions.php');

req($rootPathTheme . 'includes/functions.php');
req($rootPath . '/classes/models/models.php');
req($rootPath . '/classes/dao/dao.php');
//req($rootPath . '/e/eavEngine.php');

if (!isset($user)) {
    $user = new User();
}

if (SESSION::exists('uid')) {
    $user = UserDAO::getUser(SESSION::get('uid'));
} else {
    $user->set_id("0");
    $user->set_roles(array("2"));
}
if (!Utils::checkLanguage($user->get_language())) { // if the user don`t have a valid language selected
    $user->set_language(Utils::getBrowserLanguage());
    if (!Utils::checkLanguage($user->get_language())) {
        $user->set_language(Config::get("Theme.Web.default_language"));
    }
}
require_once $rootPath.'/modules/moduleEngine.php'; 
?>