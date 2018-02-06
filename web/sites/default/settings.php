<?php

/**
 * Load services definition file.
 */
$settings['container_yamls'][] = __DIR__ . '/services.yml';

/**
 * Include the Pantheon-specific settings file.
 *
 * n.b. The settings.pantheon.php file makes some changes
 *      that affect all envrionments that this site
 *      exists in.  Always include this file, even in
 *      a local development environment, to ensure that
 *      the site settings remain consistent.
 */
include __DIR__ . "/settings.pantheon.php";

/**
 * Place the config directory outside of the Drupal root.
 */
$config_directories = array(
  CONFIG_SYNC_DIRECTORY => dirname(DRUPAL_ROOT) . '/config',
);

/**
 * If there is a local settings file, then include it
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}

/**
 * Always install the 'standard' profile to stop the installer from
 * modifying settings.php.
 *
 * See: tests/installer-features/installer.feature
 */
$settings['install_profile'] = 'standard';

// DO NOT redirect to https on local lando website
if ($_SERVER['HTTP_HOST'] !== 'causy.lndo.site') {

  // code from - https://pantheon.io/docs/domains/#redirects
  if (isset($_SERVER['PANTHEON_ENVIRONMENT']) && php_sapi_name() != 'cli') {

    // Redirect to https://$primary_domain in the Live environment
    if ($_ENV['PANTHEON_ENVIRONMENT'] === 'live') {
      // Replace www.example.com with your registered domain name
      $primary_domain = 'www.causy.org';
    }
    else {
      // Redirect to HTTPS on every Pantheon environment.
      $primary_domain = $_SERVER['HTTP_HOST'];
    }

    if ($_SERVER['HTTP_HOST'] != $primary_domain
        || !isset($_SERVER['HTTP_X_SSL'])
        || $_SERVER['HTTP_X_SSL'] != 'ON' ) {

      # Name transaction "redirect" in New Relic for improved reporting (optional)
      if (extension_loaded('newrelic')) {
        newrelic_name_transaction("redirect");
      }

      header('HTTP/1.0 301 Moved Permanently');
      header('Location: https://'. $primary_domain . $_SERVER['REQUEST_URI']);
      exit();
    }
    // Drupal 8 Trusted Host Settings
    if (is_array($settings)) {
      $settings['trusted_host_patterns'] = array('^'. preg_quote($primary_domain) .'$');
    }
  }
}

// emails will be sent through sendgrid only on live server 
if ($_ENV['PANTHEON_ENVIRONMENT'] === 'live') {
    /*
    $config['key.key.sendgrid']['key_provider_settings'] = [
        'key_value' => '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'
        ];
    */
    $config['key.config_override.sendgrid_override'] = ['key_id' => 'sendgrid'];
}

/*
if ($_ENV['PANTHEON_ENVIRONMENT'] === 'live') {
    $config['commerce_payment.commerce_payment_gateway.paypal_express_checkout']['configuration'] = [
        'api_username' => 'david-facilitator_api1.3paces.com',
        'api_password' => '56HX7E237HY5QZM9',
        'signature' => 'AFcWxV21C7fd0v3bYYYRCpSSRl31ArxK-Ecqwjw2RNMACft2fvTh8LS2',
    ];
}
*/
    /*
     *
     * api_username: david-facilitator_api1.3paces.com
  api_password: 56HX7E237HY5QZM9
  shipping_prompt: shipping_skip
  signature: AFcWxV21C7fd0v3bYYYRCpSSRl31ArxK-Ecqwjw2RNMACft2fvTh8LS2

app_secret: sq0csp-VQgEphNJFVxfoEtJ1M_2KysrdfzP2_ugNWnlMPwZaZk
sandbox_app_id: sandbox-sq0idp-wWACO1oVx0PhRbXkdUUg9Q
sandbox_access_token: sandbox-sq0atb-KVmmWPEp3znJkFsvje76sQ
production_app_id: sq0idp-wWACO1oVx0PhRbXkdUUg9Q
    */
