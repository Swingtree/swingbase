<?php

assert_options(ASSERT_ACTIVE, TRUE);
\Drupal\Component\Assertion\Handle::register();


$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/local.services.yml';
$config['system.logging']['error_level'] = 'verbose';
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;
$settings['cache']['bins']['render'] = 'cache.backend.null';
# $settings['cache']['bins']['discovery_migration'] = 'cache.backend.memory';
# $settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
$settings['rebuild_access'] = TRUE;
$settings['skip_permissions_hardening'] = TRUE;

$databases['default']['default'] = array(
  'driver' => 'mysql',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'database' => '{@FIX_ME}',
  'username' => '{@FIX_ME}',
  'password' => '{@FIX_ME}',
  'host' => 'localhost',
  'prefix' => '',
);

$settings['trusted_host_patterns'] = array('^{@fix.me\.tld}$');