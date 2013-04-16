<?php
// Build script for the L10nTag plugin

ini_set('display_errors', 1); // Just in case

/* Setup paths */
$componentCore = dirname(dirname(__FIlE__)).'/core/components/l10ntag/';
$sources = array(
	'componentCore' => $componentCore,
	'build' => dirname(__FILE__).'/',
	'resolvers' => dirname(__FILE__).'resolvers/',
	'data' => dirname(__FILE__).'data/');

/* Initialize MODx main object */
require_once dirname(__FILE__) . '/build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx = new modX();
$modx->initialize('mgr');

/* Setup logging */
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

/* Load utility classes */
$modx->loadClass('transport.modPackageBuilder', '', false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage('l10nTag', '1.0.1', 'pl');
$builder->registerNamespace('l10nTag', false, true, '{core_path}components/l10nTag/');

/* Objects */
$plugin = $modx->newObject('modPlugin');
$plugin->set('id', 1);
$plugin->set('name', 'l10nTag');
$plugin->set('description', 'Adds support for multilingual tags in any field and templates');
$plugin->set('plugincode', file_get_contents($sources['componentCore'].'/elements/plugin.l10ntag.php'));
$modx->log(modX::LOG_LEVEL_INFO,'Plugin added successfully!');

$pluginEvent = $modx->newObject('modPluginEvent');
$pluginEvent->set('event', 'OnWebPagePrerender');
$modx->log(modX::LOG_LEVEL_INFO,'Adding onWebPagePrerender event...');

$resultAddEvents = $plugin->addMany($pluginEvent);
if(!$resultAddEvents){
	$modx->log(modX::LOG_LEVEL_INFO,'Events registered successfully');
} else {
	$modx->log(modX::LOG_LEVEL_INFO,'Error adding events to the extra!');
}
/* Go on wheels! */
$vehicle = $builder->createVehicle($plugin, array(
   xPDOTransport::UNIQUE_KEY => 'name',
   xPDOTransport::UPDATE_OBJECT => true,
   xPDOTransport::PRESERVE_KEYS => false,
   // The following 6 lines took from
   // https://github.com/splittingred/Articles/blob/develop/_build/build.transport.php
   xPDOTransport::RELATED_OBJECTS => true,
   xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array ( 
	  'PluginEvents' => array(
		  xPDOTransport::PRESERVE_KEYS => true,
		  xPDOTransport::UPDATE_OBJECT => false,
		  xPDOTransport::UNIQUE_KEY => array('pluginid','event'),
							  ))));

$vehicle->resolve('file', array(
	'source' => $sources['componentCore'],
	'target' => 'return MODX_CORE_PATH . "components/";'));

$builder->putVehicle($vehicle);

/* Package metadata etc */
$builder->setPackageAttributes(array(
	'license' 	=> file_get_contents(dirname(dirname(__FILE__)) . '/LICENSE'),
	'changelog'	=> file_get_contents(dirname(dirname(__FILE__)) . '/CHANGELOG'),
	'readme' 	=> file_get_contents(dirname(dirname(__FILE__)) . '/README')));

$builder->pack();
exit();
$modx->log(modX::LOG_LEVEL_INFO,'Build successfull!');
// Done!
?>