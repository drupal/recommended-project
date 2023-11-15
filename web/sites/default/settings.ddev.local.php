<?php

/**
 * SMTP configuration.
 */
$config['smtp.settings']['smtp_host'] = 'localhost';
$config['smtp.settings']['smtp_port'] = 1025;
$config['smtp.settings']['smtp_from'] = 'no-reply@example.local';
$config['smtp.settings']['smtp_username'] = 'no-reply@example.local';
$config['smtp.settings']['smtp_password'] = '';
$config['smtp.settings']['smtp_protocol'] = 'standard';
$config['smtp.settings']['smtp_autotls'] = 'false';
$config['system.site']['mail'] = 'no-reply@example.local';

/**
 * Use Math captcha on local.
 */
$config['captcha.settings']['default_challenge'] = 'captcha/Math';

/**
 * Environment indicator.
 */
$config['environment_indicator.indicator']['bg_color'] = '#4caf50';
$config['environment_indicator.indicator']['fg_color'] = '#000000';
$config['environment_indicator.indicator']['name'] = 'Development';
$config['environment_indicator.switcher.development']['status'] = FALSE;

/**
 * Stage file proxy.
 */
$config['stage_file_proxy.settings']['origin'] = 'https://www.example.org';
$config['stage_file_proxy.settings']['use_imagecache_root'] = FALSE;

/**
 * Advanced CSS/JS Aggregation.
 */
$config['advagg.settings']['enabled'] = FALSE;

/**
 * SOLR server configuration.
 */
$config['search_api.server.solr']['backend_config']['connector_config']['host'] = 'solr';
$config['search_api.server.solr']['backend_config']['connector_config']['core'] = 'dev';
$config['search_api.server.solr']['backend_config']['connector_config']['solr_install_dir'] = '/opt/solr';

/**
 * ClamAV virus scanner configuration.
 */
$config['clamav.settings']['scan_mode'] = 0;
$config['clamav.settings']['mode_daemon_tcpip']['hostname'] = 'clamav';
$config['clamav.settings']['mode_daemon_tcpip']['port'] = 3310;

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
$settings['rebuild_access'] = FALSE;

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

/**
 * Specifies the frontend domain for the application.
 *
 * This setting is used to dynamically generate URLs for the "Go to Frontend"
 * button in the Drupal admin toolbar. The button redirects to this domain,
 * and the domain is also used as a base URL to construct specific paths for
 * meeting and meeting_section content types.
 */
$settings['frontend_domain'] = "https://new.ozonactionmeetings.org";

/**
 * The upstream OAuth library checks the private key's file permissions by
 * default. On local instances we can skip these checks.
 */
$settings['simple_oauth.key_permissions_check'] = FALSE;