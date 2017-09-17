<?php

namespace Swingtree\Swingbase\Composer;


use Symfony\Component\Filesystem\Filesystem;
use Composer\EventDispatcher\Event;

/**
 * Swingbase Composer Script Handler.
 *
 * Handles installation and updation process over composer
 */
final class SwingbaseScripts {

  
  /**
	* Place files
	**/
  public static function sitesDefaultFiles(Event $event) {

    $fs = new Filesystem();
    $drupal_root = getcwd().'/www';
	$io = $event->getIO();

	/**
	 * @based on varbase and lightning profiles
	 **/
	
    // Prepare the settings file for installation.
    if (!$fs->exists($drupal_root . '/sites/default/settings.php') and $fs->exists($drupal_root . '/profiles/swingbase/src/Assets/settings.php')) {
      $fs->copy($drupal_root . '/profiles/swingbase/src/Assets/settings.php', $drupal_root . '/sites/default/default.settings.php');
      $fs->chmod($drupal_root . '/sites/default/settings.php', 0666);
      $io->write("Default Settings ready");
    }
	
	// Prepare the settings file for installation.
    if (!$fs->exists($drupal_root . '/sites/default/settings.platformsh.php') and $fs->exists($drupal_root . '/profiles/swingbase/src/Assets/settings.platformsh.php')) {
      $fs->copy($drupal_root . '/profiles/swingbase/src/Assets/settings.platformsh.php', $drupal_root . '/sites/default/default.settings.platformsh.php');
      $fs->chmod($drupal_root . '/sites/default/settings.platformsh.php', 0666);
      $io->write("Platform.sh Settings ready");
    }
	
	
    // Prepare the services file for installation.
    if (!$fs->exists($drupal_root . '/sites/default/local.services.yml') and $fs->exists($drupal_root . '/profiles/swingbase/src/Assets/local.services.yml')) {
      $fs->copy($drupal_root . '/profiles/swingbase/src/Assets/local.services.yml', $drupal_root . '/sites/default/local.services.yml');
      $fs->chmod($drupal_root . '/sites/default/local.services.yml', 0666);
      $io->write("Local service ready");
    }
	
	// Prepare drushrc file
	if(!$fs->exists($drupal_root . '/sites/default/drushrc.php') and $fs->exists($drupal_root . '/profiles/swingbase/src/Assets/drushrc.local')){
	  $fs->copy($drupal_root . '/profiles/swingbase/src/Assets/drushrc.php', $drupal_root . '/sites/default/drushrc.php');
      $fs->chmod($drupal_root . '/sites/default/drushrc.php', 0666);
      $io->write("Drushrc ready");
	}
	
	// Place example
	if(!$fs->exists($drupal_root . '/sites/default/example.settings.local.php') and $fs->exists($drupal_root . '/profiles/swingbase/src/Assets/example.settings.local.php')){
	  $fs->copy($drupal_root . '/profiles/swingbase/src/Assets/example.settings.local.php', $drupal_root . '/sites/default/example.settings.local.php');
      $io->write("Local setting example");
	}
	
  }
}