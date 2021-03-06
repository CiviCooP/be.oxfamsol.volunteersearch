<?php

class CRM_VolunteerUtil_GetCustomfields_ApiWrapper implements API_Wrapper {

	public function fromApiInput($apiRequest) {
		return $apiRequest;
	}

	public function toApiOutput($apiRequest, $results) {
		// Get custom fields of activities and the status and activity type field.

		$allowedCustomFieldTypes = array('Autocomplete-Select',
			'CheckBox', 'Multi-Select', 'Radio', 'Select', 'Text');

		$optionListIDs = array();
		$customFields = array();

		$activityTypeIdField = civicrm_api3('Activity', 'getfield', array('name' => 'activity_type_id', 'action' => 'get'));
		$optionGroupId = civicrm_api3('OptionGroup', 'getvalue', array('return' => 'id', 'name' => $activityTypeIdField['values']['pseudoconstant']['optionGroupName']));
		$optionListIDs[] = $optionGroupId['result'];
		$customField = array(
			'name' => $activityTypeIdField['values']['name'],
			'column_name' => $activityTypeIdField['values']['name'],
			'html_type' => $activityTypeIdField['values']['html']['type'],
			'label' => ts('Activity type'),
			'id' => $activityTypeIdField['values']['name'],
			'option_group_id' => $optionGroupId,
			'weight' => -999
		);
		$customFields[] = $customField;
		$activityStatusIdField = civicrm_api3('Activity', 'getfield', array('name' => 'status_id', 'action' => 'get'));
		$optionGroupId = civicrm_api3('OptionGroup', 'getvalue', array('return' => 'id', 'name' => $activityStatusIdField['values']['pseudoconstant']['optionGroupName']));
		$optionListIDs[] = $optionGroupId['result'];
		$customField = array(
			'name' => $activityStatusIdField['values']['name'],
			'column_name' => $activityStatusIdField['values']['name'],
			'html_type' => $activityStatusIdField['values']['html']['type'],
			'label' => ts('Activity status'),
			'id' => $activityStatusIdField['values']['name'],
			'option_group_id' => $optionGroupId,
			'weight' => -998,
		);
		$customFields[] = $customField;


		$customGroupAPI = civicrm_api3('CustomGroup', 'get', array(
			'extends' => 'Activity',
			'api.customField.get' => array(
				'html_type' => array('IN' => $allowedCustomFieldTypes),
				'is_active' => 1,
				'is_searchable' => 1
			),
			'sequential' => 1,
			'options' => array('limit' => 0),
		));

		$currentWeight = -997;
		for($i=0; $i<count($customGroupAPI['values']); $i++) {
			for($j=0; $j < count($customGroupAPI['values'][$i]['api.customField.get']['values']); $j++) {
				$customField = $customGroupAPI['values'][$i]['api.customField.get']['values'][$j];
				$customField['weight'] = $currentWeight;
				$customField['id'] = 'activity_'.$customField['id'];
				if (isset($customField['option_group_id'])) {
					$optionListIDs[] = $customField['option_group_id'];
				}
				$customFields[] = $customField;
				$currentWeight ++;
			}
		}

		$optionValueAPI = civicrm_api3('OptionValue', 'get', array(
			'is_active' => 1,
			'opt_group_id' => array('IN' => $optionListIDs),
			'options' => array(
				'limit' => 0,
				'sort' => 'weight',
			)
		));

		$optionData = _volunteer_util_groupBy($optionValueAPI['values'], 'option_group_id');
		foreach($customFields as &$field) {
			$optionGroupId = CRM_Utils_Array::value('option_group_id', $field);
			if ($optionGroupId && $field['html_type'] != 'Multi-Select') {
				$field['options'] = array_merge([
					[
						'value' => '',
						'label' => 'select'
					]
				], $optionData[$optionGroupId]);
			} elseif ($optionGroupId && $field['html_type'] == 'Multi-Select') {
				$field['options'] = $optionData[$optionGroupId];
			} elseif ($field['data_type'] === 'Boolean' && $field['html_type'] === 'Radio') {
				// Boolean fields don't use option groups, so we supply one
				$field['options'] = array(
					array (
						'is_active' => 1,
						'is_default' => 1,
						'label' => ts("Yes", array('domain' => 'org.civicrm.volunteer')),
						'value' => 1,
						'weight' => 1,
					),
					array (
						'is_active' => 1,
						'is_default' => 0,
						'label' => ts("No", array('domain' => 'org.civicrm.volunteer')),
						'value' => 0,
						'weight' => 2,
					),
				);
			}
		}

		$results['values'] = array_merge($results['values'], $customFields);


		return $results;
	}

}
