<?php

function _civicrm_api3_contact_volunteersearch_spec() {
	return array(
		'need_id' => array(
			'required' => true,
		)
	);
}

/**
 * Returns available volunteers for CiviVolunteer assignment
 * - extra needed param: need_id
 *
 * @param $params
 * @return array
 */
function civicrm_api3_contact_volunteersearch($params) {
	$contact_params = $params;

	// First lookup the contacts who are not available because they are already booked
	// in civivolunteer. We will exclude those contacts.
	// We first lookup the start and end date and time of the current shift
	// then we will get all contact ids which are already booked at the same time.
	$need_id = $params['need_id'];
	unset($contact_params['need_id']);
	$need = civicrm_api3('VolunteerNeed', 'getsingle', array('id' => $need_id));
	$needStartDate = new DateTime($need['start_time']);
	if (!empty($need['end_time'])) {
		$needEndDate = new DateTime($need['end_time']);
	} else {
		$needEndDate = new DateTime($need['start_time']);
		$needEndDate->modify('+ '.$need['duration'].' minutes');
	}

	$volunteerNeedCustomGroup = civicrm_api3('CustomGroup', 'getsingle', array('name' => 'CiviVolunteer'));
	$volunteerNeedCustomField = civicrm_api3('CustomField', 'getsingle', array('custom_group_id' => 'CiviVolunteer', 'name' => 'Volunteer_Need_Id'));

	$sqlParams[1] = array($needStartDate->format('Y-m-d H:i:s'), 'String');
	$sqlParams[2] = array($needEndDate->format('Y-m-d H:i:s'), 'String');
	$sql = "SELECT DISTINCT civicrm_activity_contact.contact_id as contact_id
FROM civicrm_volunteer_need
INNER JOIN civicrm_volunteer_project ON civicrm_volunteer_project.id = civicrm_volunteer_need.project_id
INNER JOIN {$volunteerNeedCustomGroup['table_name']} ON {$volunteerNeedCustomGroup['table_name']}.{$volunteerNeedCustomField['column_name']} = civicrm_volunteer_need.id
INNER JOIN civicrm_activity_contact ON civicrm_activity_contact.activity_id = {$volunteerNeedCustomGroup['table_name']}.entity_id
WHERE civicrm_volunteer_need.is_active = 1 and civicrm_volunteer_project.is_active = 1
AND civicrm_volunteer_need.start_time IS NOT NULL
AND
(
	(civicrm_volunteer_need.end_time IS NOT NULL AND
		(
			%1 BETWEEN civicrm_volunteer_need.start_time AND civicrm_volunteer_need.end_time
			OR
			%2 BETWEEN civicrm_volunteer_need.start_time AND civicrm_volunteer_need.end_time
		)
	)
	OR
	(civicrm_volunteer_need.duration IS NOT NULL AND
		(
			%1 BETWEEN civicrm_volunteer_need.start_time AND DATE_ADD(civicrm_volunteer_need.start_time, INTERVAL civicrm_volunteer_need.duration MINUTE)
			OR
			%2 BETWEEN civicrm_volunteer_need.start_time AND DATE_ADD(civicrm_volunteer_need.start_time, INTERVAL civicrm_volunteer_need.duration MINUTE)
		)
	)
);";

	$nonAvailableContactIds = array();
	$contactDao = CRM_Core_DAO::executeQuery($sql, $sqlParams);
	while($contactDao->fetch()) {
		$nonAvailableContactIds[] = $contactDao->contact_id;
	}
	if (count($nonAvailableContactIds)) {
		$contact_params['id'] = ['NOT IN' => $nonAvailableContactIds];
	}

	// check whether we need to search for activities if so fill the activity params
	$activity_params = array();
	foreach($contact_params as $field => $value) {
		if (stripos($field, 'custom_activity_') === 0) {
			$activity_params['custom_'.substr($field, 16)] = $value;
			unset($contact_params[$field]);
		} elseif ($field == 'custom_activity_type_id') {
			$activity_params['activity_type_id'] = $value;
			unset($contact_params[$field]);
		} elseif ($field == 'custom_status_id') {
			$activity_params['status_id'] = $value;
			unset($contact_params[$field]);
		}
	}
	$noContacts = false;
	if (count($activity_params)) {
		$activity_params['options']['limit'] = 0;
		$activity_params['return'] = 'id';
		$activity_params['sequential'] = 0;
		$activities = civicrm_api3('Activity', 'get', $activity_params);
		$activityIds = array_keys($activities['values']);
		$notAvailableContacts = '';
		if (count($nonAvailableContactIds)) {
			$notAvailableContactsSql = ' AND contact_id NOT IN ('.implode(",", $nonAvailableContactIds).')';
		}
		if (count($activityIds) > 0) {
			$activityContactSql = "SELECT DISTINCT contact_id FROM civicrm_activity_contact WHERE activity_id IN (".implode(",", $activityIds).") $notAvailableContactsSql";
			$activityContacts = CRM_Core_DAO::executeQuery($activityContactSql);
			$activityContactIds = array();
			while ($activityContacts->fetch()) {
				$activityContactIds[] = $activityContacts->contact_id;
			}
			$contact_params['id'] = array('IN' => $activityContactIds);
		} else {
			$noContacts = true;
		}
	}

	if ($noContacts) {
		$contacts['values'] = array();
		$contactsFound = 0;
	} else {
		$contacts = civicrm_api3('Contact', 'get', $contact_params);
		$contactsFound = civicrm_api3('Contact', 'getcount', $contact_params);
	}
	$result = civicrm_api3_create_success($contacts['values'], "VolunteerUtil", "loadbackbone", $params);
	$result['metadata']['total_found'] = $contactsFound;
	return $result;
}
