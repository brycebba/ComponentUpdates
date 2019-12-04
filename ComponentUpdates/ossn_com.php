<?php

define('__COMPONENT_UPDATES__', ossn_route()->com . 'ComponentUpdates/');

function com_component_updates_init() {
     // //css
     ossn_extend_view('css/ossn.default', 'css/componentupdates');
}

function com_component_updates_GetRemoteComponents() {
     // get the list of components from the component store on OSSN's site
     $curl = curl_init();
     curl_setopt_array($curl, array(
          CURLOPT_URL => "https://www.opensource-socialnetwork.org/api/v1.0/components_store_updates",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_SSL_VERIFYHOST => false,
          CURLOPT_SSL_VERIFYPEER => false,
     ));
     $response_components = curl_exec($curl);
     $err = curl_error($curl);
     curl_close($curl);

     if ($response_components == false || strpos($response_components, 'payload') == 0) {
          $response_components = array();
          $response_components['error'] = $err;
     }
     else {
          $response_components = json_decode($response_components, true);
          $response_components = $response_components['payload'];
     }
     return $response_components;
}

function com_component_updates_FindRemoteComponentWithUpdate($local_component, $remote_components) {
     //error_log("remote_component: " . ossn_dump($remote_component));
     //error_log("local_component: " . ossn_dump($local_component));
     // sample result
     // [0] => Array
     //    (
     //        [com_id] => TextareaSupport
     //        [latest_version] => 2.1
     //        [time_updated] => 1544025492
     //        [ossn_version_prefix] => 5.x
     //        [works_on_latest_version] => yes
     //        [url] => https://www.opensource-socialnetwork.org/component/view/3196/textareasupport
     //    )
     if ($remote_components) {   
          $return = array();
          $site_version = (double)ossn_site_settings('site_version');
          foreach ($remote_components as $remote_component) {
               // if its not a core component as they arent allowed on component store
               if (substr($local_component, 0, 4) != 'Ossn') {
                    // error_log("local_component: " . ossn_dump($local_component));
                    // error_log("response_payload: " . ossn_dump($remote_components));
                    //if the ids are equals
                    if ($local_component->id == $remote_component['com_id']) {
                         $minimum_remote_ossn_version = (double)$remote_component['ossn_version_prefix']; // expect string 5.x for example, double makes it 5
                         // make sure that the versions arent equal and that the update is compatible with the site version
                         if ($local_component->version != $remote_component['version'] && $site_version >= $minimum_remote_ossn_version) {
                              $num_local_version = (double)$local_component->version; // will chop a number like 2.1.4 to 2.1
                              $num_remote_version = (double)$remote_component['latest_version'];
                              $len_local_version = strlen($local_component->version);
                              $len_remote_version = strlen($remote_component['latest_version']);
                              if ($num_remote_version > $num_local_version) {
                                   $return = $remote_component;
                              }
                              elseif ($num_remote_version = $num_local_version && $len_remote_version > $len_local_version) {
                                   $return = $remote_component;
                              }
                         }
                    }
               }    
          }
          return $return;
     }
}
ossn_register_callback('ossn', 'init', 'com_component_updates_init');
