<?php

class CRM_VolunteerUtil_Loadbackbone_ApiWrapper implements API_Wrapper {

  public function fromApiInput($apiRequest) {
    return $apiRequest;
  }

  public function toApiOutput($apiRequest, $result) {
    $ccr = CRM_Core_Resources::singleton();
    $results['scripts'][] = $ccr->getUrl('be.oxfamsol.volunteersearch', 'js/volunteersearch.js');
    return $result;
  }

}