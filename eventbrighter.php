<?php

require_once 'eventbrighter.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function eventbrighter_civicrm_config(&$config) {
  _eventbrighter_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function eventbrighter_civicrm_xmlMenu(&$files) {
  _eventbrighter_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function eventbrighter_civicrm_install() {
  return _eventbrighter_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function eventbrighter_civicrm_uninstall() {
  return _eventbrighter_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function eventbrighter_civicrm_enable() {
  return _eventbrighter_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function eventbrighter_civicrm_disable() {
  return _eventbrighter_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function eventbrighter_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _eventbrighter_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function eventbrighter_civicrm_managed(&$entities) {
  return _eventbrighter_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function eventbrighter_civicrm_caseTypes(&$caseTypes) {
  _eventbrighter_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function eventbrighter_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _eventbrighter_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

function eventbrighter_civicrm_tokens(&$tokens) {
  $params = array('version' => 3, 'sequential' => 1, 'is_template' => 0, 'is_online_registration' => 1, 'is_active' => 1, 'end_date' > date('Y-m-d'));
  $result = civicrm_api('Event', 'get', $params);
  $tokens['eventbrighter'] = array();
  $my_tokens = array('maplink' => 'Map Link', 'registrationurl' => 'Registration Url', 'infourl' => 'Event Information URL', 'description' => 'Description','summary' => 'Summary','title' => 'Title','dates' => 'Dates', 'registration' => 'Registration Button', 'location' => 'Location');
  foreach($result['values'] as $event) {
    $token_title = $event['title'].' ('.substr($event['start_date'],0,10).')';
    foreach($my_tokens as $key => $label) {
      $token = 'eventbrighter.'.$key.'_'.$event['id'];
      // $token = $key.'_'.$event['id'];
      $tokens['eventbrighter'][$token] = $token_title.' '.$label;
    }
  }
}

function eventbrighter_civicrm_tokenValues(&$values, &$contactIDs, $job = null, $tokens = array(), $context = null) {
  if (!empty($tokens['eventbrighter'])){
    $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
    $host = $protocol.$_SERVER['SERVER_NAME'];
    $event_tokens = array();
    foreach(array_keys($tokens['eventbrighter']) as $token) {
      list($key,$event_id) = explode('_',$token,2);
      if (empty($event_tokens[$event_id])) {
        $event_tokens[$event_id] = array();
      }
      $event_tokens[$event_id][] = $key;
    }
    foreach($event_tokens as $event_id => $my_tokens) {
      $params = array('id' => $event_id);
      $event = civicrm_api3('Event', 'getsingle', $params);
      $register_url = CRM_Utils_System::url('civicrm/event/register',
        'reset=1&id='.$event['id'], TRUE);
      $info_url = CRM_Utils_System::url('civicrm/event/info',
        'reset=1&id='.$event['id'], TRUE);

      foreach($my_tokens as $key) {
        $token_key = 'eventbrighter.'.$key.'_'.$event_id;
        switch($key) {
          case 'location': // calculate a nice address html
            $lparams = array('version' => 3, 'sequential' => 1, 'id' => $event['loc_block_id'], 'return' => 'address');
            $address  = civicrm_api('LocBlock','getvalue',$lparams);
            // $lparams = array('version' => 3, 'sequential' => 1, 'id' => $event['loc_block_id'], 'return' => 'address_id');
            // $address_id  = civicrm_api('LocBlock','getsingle',$lparams);
            // $aparams = array('version' => 3, 'sequential' => 1, 'id' => $address_id['address_id']);
            // $address  = civicrm_api('Address','getsingle',$aparams);
            $html = '';
            foreach(array('name','street_address','city') as $lkey) {
              if (!empty($address[$lkey])) {
                $html .= '<br />'.$address[$lkey];
              }
            }
            foreach($contactIDs as $cid) {
              $values[$cid][$token_key] = $html;
            }
            break;
          case 'maplink': // calculate a nice address html
            if (!empty($address['geo_code_1'])) {
              $html = '<div><a href="https://maps.google.ca/?q='.$address['geo_code_1'].','.$address['geo_code_2'].'">View Map</a></div>';
              foreach($contactIDs as $cid) {
                $values[$cid][$token_key] = $html;
              }
            }
            break;
          case 'dates': // 
            $html = CRM_Utils_Date::customFormat($event['event_start_date']). (empty($event['event_end_date']) ? '' : ' to '.CRM_Utils_Date::customFormat($event['event_end_date']));
            foreach($contactIDs as $cid) {
              $values[$cid][$token_key] = $html;
            }
            break;
          case 'registration': //  registration button
            foreach($contactIDs as $cid) {
              $checksum = CRM_Contact_BAO_Contact_Utils::generateChecksum($cid);
              $values[$cid][$token_key] = '<a href="'.$register_url.'&cs='.$checksum.'&cid='.$cid.'">Register Now</a>';
            }
            break;
          case 'registrationurl': //  registration url
            foreach($contactIDs as $cid) {
              $checksum = CRM_Contact_BAO_Contact_Utils::generateChecksum($cid);
              $values[$cid][$token_key] = $register_url.'&cs='.$checksum.'&cid='.$cid;
            }
            break;
          case 'infourl': //  registration url
            foreach($contactIDs as $cid) {
              $values[$cid][$token_key] = $info_url;
            }
            break;
          default:
            if (isset($event[$key])) {
              foreach($contactIDs as $cid) {
                $values[$cid][$token_key] = $event[$key];
              }
            }
            break;
        }
      }
    }
  }
}
