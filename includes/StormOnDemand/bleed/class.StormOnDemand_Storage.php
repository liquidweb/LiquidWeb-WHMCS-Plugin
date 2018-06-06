<?php

/**********************************************************************
 * Custom developed. (2014-12-18)
 * *
 *
 *  CREATED BY MODULESGARDEN       ->       http://modulesgarden.com
 *  CONTACT                        ->       contact@modulesgarden.com
 *
 *
 * This software is furnished under a license and may be used and copied
 * only  in  accordance  with  the  terms  of such  license and with the
 * inclusion of the above copyright notice.  This software  or any other
 * copies thereof may not be provided or otherwise made available to any
 * other person.  No title to and  ownership of the  software is  hereby
 * transferred.
 *
 *
 **********************************************************************/

/**
 * @author Pawel Kopec <pawelk@modulesgarden.com>
 */

require_once 'class.StormOnDemandConnection.php';
 
class StormOnDemand_Storage extends StormOnDemandConnection{

      public function setDebug($mode){
            $this->debug = (boolean) $mode;
      }
      /**
       * Create  Storage
       * @param string $domain - A string of text, containing no newlines or other control characters.
       * @param int $size A positive integer value (i.e. 1 and up).
       * @param string $attach - A six-character identifier, containing only capital letters and digits.
       * @param boolean $cross_attach -	A boolean value (0 or 1).
       * @param int $region - A positive integer value (i.e. 1 and up).
       * @param int $zone A positive integer value (i.e. 1 and up).
       */
      public function create($domain, $size, $attach=null, $cross_attach=null,  $region=null, $zone=null ){
            $data = array();
            $data['domain'] = $domain;
            $data['size']   =  $size;
            if(isset($attach))
                $data['attach']  = $attach;
            if(isset($cross_attach))
                $data['cross_attach']  = $cross_attach;
            if(isset($region))
                  $data['region'] = $region;
            if(isset($zone))
                  $data['zone'] = $zone;

            return $this->__request('Storage/Block/Volume/create', $data, __METHOD__);

      }
      /**
       * Delete storage
       *
       * @param string $uniq_id
       * @return array
       */
      public function delete($uniq_id){
           $data = array();
           $data['uniq_id'] = $uniq_id;
           return $this->__request('Storage/Block/Volume/delete', $data, __METHOD__);
      }

      /**
       * Resize a volume. Volumes can currently only be resized larger.
       * @param string $uniq_id
       * @param string $new_size
       * @return array
       */
      public function resize ($uniq_id, $new_size){
            $data = array();
            $data['uniq_id']  = $uniq_id;
            $data['new_size'] = $new_size;
            return $this->__request('Storage/Block/Volume/resize', $data, __METHOD__);
      }
      /**
       * Retrieve information about a specific volume.
       *
       * @param string $uniq_id - A six-character identifier, containing only capital letters and digits.
       * @return array
       */
      public function details($uniq_id){
            $data = array();
            $data['uniq_id']  = $uniq_id;
            return $this->__request('Storage/Block/Volume/details', $data, __METHOD__);
      }
      /**
       * Attach a volume to a particular instance.
       *
       * @param string $to
       * @param string $uniq_id
       */
      public function attach($to, $uniq_id){
            $data = array();
            $data['to']  = $to;
            $data['uniq_id']  = $uniq_id;
            return $this->__request('Storage/Block/Volume/attach', $data, __METHOD__);
      }
       /**
       * Detach a volume from an instance. This method is roughly equivalent to unplugging an external drive, and it is important to ensure the volume is unmounted before using this method.
       *
       * @param string $detach_from
       * @param string $uniq_id
       */
      public function detach($detach_from, $uniq_id){
            $data = array();
            $data['detach_from']  = $detach_from;
            $data['uniq_id']  = $uniq_id;
            return $this->__request('Storage/Block/Volume/detach', $data, __METHOD__);
      }
      /**
       * Update an existing volume. Currently, only renaming the volume is supported.
       * More information about the returned data structure can be found in the documentation for storage/block/volume/details/
       * @param boolean $cross_attach
       * @param string $domain
       * @param string $uniq_id
       * @return array
       */
      public function update($cross_attach=null, $domain=null, $uniq_id){
            $data = array();
            if(isset($cross_attach))
                 $data['cross_attach'] = $cross_attach;
            if(isset($domain))
                 $data['domain'] = $domain;
            $data['uniq_id'] = $uniq_id;
            return $this->__request('Storage/Block/Volume/update', $data, __METHOD__);
      }
}