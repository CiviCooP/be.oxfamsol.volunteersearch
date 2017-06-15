<?php

require_once 'volunteersearch.civix.php';

function volunteersearch_civicrm_apiWrappers(&$wrappers, $apiRequest) {
  if (strtolower($apiRequest['entity']) == 'volunteerutil' && strtolower($apiRequest['action']) == 'loadbackbone') {
  	// Add our javascript to manipulate the search for volunteers
    $wrappers[] = new CRM_VolunteerUtil_Loadbackbone_ApiWrapper();
  }
	if (strtolower($apiRequest['entity']) == 'volunteerutil' && strtolower($apiRequest['action']) == 'getcustomfields') {
		// Get custom fields of activities and the status and activity type field.
		$wrappers[] = new CRM_VolunteerUtil_GetCustomfields_ApiWrapper();
	}
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function volunteersearch_civicrm_config(&$config) {
  _volunteersearch_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function volunteersearch_civicrm_xmlMenu(&$files) {
  _volunteersearch_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function volunteersearch_civicrm_install() {
  _volunteersearch_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function volunteersearch_civicrm_postInstall() {
  _volunteersearch_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function volunteersearch_civicrm_uninstall() {
  _volunteersearch_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function volunteersearch_civicrm_enable() {
  _volunteersearch_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function volunteersearch_civicrm_disable() {
  _volunteersearch_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function volunteersearch_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _volunteersearch_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function volunteersearch_civicrm_managed(&$entities) {
  _volunteersearch_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function volunteersearch_civicrm_caseTypes(&$caseTypes) {
  _volunteersearch_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function volunteersearch_civicrm_angularModules(&$angularModules) {
  _volunteersearch_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function volunteersearch_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _volunteersearch_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function volunteersearch_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function volunteersearch_civicrm_navigationMenu(&$menu) {
  _volunteersearch_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'be.oxfamsol.volunteersearch')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _volunteersearch_civix_navigationMenu($menu);
} // */
