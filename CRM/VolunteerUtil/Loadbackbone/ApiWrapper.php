<?php

class CRM_VolunteerUtil_Loadbackbone_ApiWrapper implements API_Wrapper {

  public function fromApiInput($apiRequest) {
    return $apiRequest;
  }

  public function toApiOutput($apiRequest, $results) {
    $ccr = CRM_Core_Resources::singleton();
    $results['values']['scripts'][] = $ccr->getUrl('be.oxfamsol.volunteersearch', 'js/volunteersearch.js');
    return $results;
  }

}