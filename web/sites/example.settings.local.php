<?php

// phpcs:ignoreFile

/**
 * @file
 * Local development override configuration feature.
 *
 * To activate this feature, copy and rename it such that its path plus
 * filename is 'sites/default/settings.local.php'. Then, go to the bottom of
 * 'sites/default/settings.php' and uncomment the commented lines that mention
 * 'settings.local.php'.
 *
 * If you are using a site name in the path, such as 'sites/example.com', copy
 * this file to 'sites/example.com/settings.local.php', and uncomment the lines
 * at the bottom of 'sites/example.com/settings.php'.
 */

/**
 * Assertions.
 *
 * The Drupal project primarily uses runtime assertions to enforce the
 * expectations of the API by failing when incorrect calls are made by code
 * under development.
 *
 * @see http://php.net/assert
 * @see https://www.drupal.org/node/2492225
 *
 * It is strongly recommended that you set zend.assertions=1 in the PHP.ini file
 * (It cannot be changed from .htaccess or runtime) on development machines and
 * to 0 or -1 in production.
 *
 * @see https://wiki.php.net/rfc/expectations
 */
assert_options(ASSERT_ACTIVE, TRUE);
assert_options(ASSERT_EXCEPTION, TRUE);

/**
 * Enable local development services.
 */
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';

/**
 * Show all error messages, with backtrace information.
 *
 * In case the error level could not be fetched from the database, as for
 * example the database connection failed, we rely only on this value.
 */
$config['system.logging']['error_level'] = 'verbose';

/**
 * Disable CSS and JS aggregation.
 */
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;

/**
 * Disable the render cache.
 *
 * Note: you should test with the render cache enabled, to ensure the correct
 * cacheability metadata is present. However, in the early stages of
 * development, you may want to disable it.
 *
 * This setting disables the render cache by using the Null cache back-end
 * defined by the development.services.yml file above.
 *
 * Only use this setting once the site has been installed.
 */
$settings['cache']['bins']['render'] = 'cache.backend.null';

/**
 * Disable caching for migrations.
 *
 * Uncomment the code below to only store migrations in memory and not in the
 * database. This makes it easier to develop custom migrations.
 */
$settings['cache']['bins']['discovery_migration'] = 'cache.backend.memory';

/**
 * Disable Internal Page Cache.
 *
 * Note: you should test with Internal Page Cache enabled, to ensure the correct
 * cacheability metadata is present. However, in the early stages of
 * development, you may want to disable it.
 *
 * This setting disables the page cache by using the Null cache back-end
 * defined by the development.services.yml file above.
 *
 * Only use this setting once the site has been installed.
 */
$settings['cache']['bins']['page'] = 'cache.backend.null';

/**
 * Disable Dynamic Page Cache.
 *
 * Note: you should test with Dynamic Page Cache enabled, to ensure the correct
 * cacheability metadata is present (and hence the expected behavior). However,
 * in the early stages of development, you may want to disable it.
 */
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';

/**
 * Allow test modules and themes to be installed.
 *
 * Drupal ignores test modules and themes by default for performance reasons.
 * During development it can be useful to install test extensions for debugging
 * purposes.
 */
# $settings['extension_discovery_scan_tests'] = TRUE;

/**
 * Enable access to rebuild.php.
 *
 * This setting can be enabled to allow Drupal's php and database cached
 * storage to be cleared via the rebuild.php page. Access to this page can also
 * be gained by generating a query string from rebuild_token_calculator.sh and
 * using these parameters in a request to rebuild.php.
 */
$settings['rebuild_access'] = TRUE;

/**
 * Skip file system permissions hardening.
 *
 * The system module will periodically check the permissions of your site's
 * site directory to ensure that it is not writable by the website user. For
 * sites that are managed with a version control system, this can cause problems
 * when files in that directory such as settings.php are updated, because the
 * user pulling in the changes won't have permissions to modify files in the
 * directory.
 */
$settings['skip_permissions_hardening'] = TRUE;

#
# Project specific settings
#

$settings['file_private_path'] = realpath($app_root . '/../private');

# Project trusted host pattern
$settings['trusted_host_patterns'] = [
  '^example\.localhost',
];

/** Customize to your local configuration */
$databases['default']['default'] = array (
  'database' => 'drupal',
  'username' => 'root',
  'password' => 'root',
  'prefix' => '',
  'host' => 'localhost',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
  'init_commands' => ['isolation_level' => "SET SESSION tx_isolation='READ-COMMITTED'"],
);

# SMTP configuration
$config['smtp.settings']['smtp_host'] = 'localhost';
$config['smtp.settings']['smtp_port'] = 25;
$config['smtp.settings']['smtp_protocol'] = 'standard';
$config['smtp.settings']['smtp_from'] = 'no-reply@example.com';
$config['smtp.settings']['smtp_username'] = 'no-reply@example.com';
$config['smtp.settings']['smtp_password'] = '';

$config['system.site']['mail'] = 'no-reply@example.com';

# Recaptcha settings.
$config['recaptcha.settings']['site_key'] = '';
$config['recaptcha.settings']['secret_key'] = '';

// Enable 2FA login on prod.
$config['tfa.settings']['enabled'] = TRUE;
// Generate with `dd if=/dev/urandom bs=32 count=1 | base64 -i`.
$config['key.key.encryption_key']['key_provider_settings']['key_value'] = 'ZGlBTZCuMC65j3QVeq/CenbHjOaaFGT7nKsvkmW4Cw4=';
$config['key.key.encryption_key']['key_provider_settings']['base64_encoded'] = TRUE;

// Every cache table will have a maximum of 5000 rows.
$settings['database_cache_max_rows']['default'] = 5000;
// Override the cache_dynamic_page_cache table max rows setting, if it needs to be higher.
// $settings['database_cache_max_rows']['bins']['dynamic_page_cache'] = 100000;

// If ClamAV is not available, allow files to be uploaded without being scanned.
$config['clamav.settings']['outage_action'] = 1;
