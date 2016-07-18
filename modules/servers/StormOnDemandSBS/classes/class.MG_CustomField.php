<?php
/**********************************************************************
 * Product developed. (2014-12-18)
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
class MG_CustomField {

      public $id;
      public $type;
      public $relid;
      public $fieldname;
      public $fieldtype;
      public $description;
      public $fieldoptions;
      public $regexpr;
      public $adminonly;
      public $required;
      public $showorder;
      public $showinvoice;
      public $sortorder;
      
      const _table = 'tblcustomfields';
      const typeProduct = 'product';
      const typeClient = 'client';
      const fieldText = 'text';
      public $childrenId;
      
      private $value;
      private $fieldExist;

      public function __construct($id=null, $fieldname=null, $type=null,$partentId=0, $childrenId=null) {
            $this->id = $id; 
            if($fieldname && $type=='client' && $childrenId){
                  $fieldId = self::getFieldId($fieldname, $type, 0);
                  $this->id = $fieldId;
                  $this->fieldExist  = (boolean)$fieldId;
            } else if($fieldname && $type=='product' && $partentId){
                  $fieldId = self::getFieldId($fieldname, $type, $partentId);
                  if(empty( $fieldId))
                      throw new Exception ("Custom Field \"{$fieldname}\" not found") ;
                  $this->id = $fieldId;
            }
            $this->childrenId = $childrenId;
            $this->relid = $partentId;

            if(!empty($params)){
                  foreach($params as $k => $v)
                        $this->$k = $v;
            }else if($this->id)
                  $this->load();
      }
      public function setValue($value){
            $row = mysql_get_row("select * from `tblcustomfieldsvalues` WHERE `fieldid`=? AND `relid`=? ", array($this->id, $this->childrenId));
            if ($row ) {
                  return mysql_safequery("UPDATE `tblcustomfieldsvalues` SET `value`=? WHERE `fieldid`=? AND `relid`=? LIMIT 1", array($value, $this->id, $this->childrenId));
            } else {
                  return mysql_safequery("INSERT INTO `tblcustomfieldsvalues` (`fieldid`, `relid`, `value`) VALUES (?,?,?) ", array($this->id, $this->childrenId, $value));
            }
      }
      public function getValue(){
            $rows =  mysql_get_row("SELECT * FROM `tblcustomfieldsvalues` where fieldid =? and relid=?",array($this->id, $this->childrenId));
            $this->value = $rows['value'];
            return $this->value;
      }
      /**
       * Get Fields names
       * @param string $type
       * @return \MG_CustomField
       */
      public static function getAll($type){
          $results = array();
          $rows = mysql_get_array('SELECT * FROM '.self::_table.' WHERE  `type`=? ORDER BY `fieldname` ASC', array($type));
          foreach($rows as $row){
                $results[] = new MG_CustomField($row['id']);
          }
          return $results;
      }
      private function load(){
          $rows = mysql_get_row('SELECT * FROM '.self::_table.' WHERE  `id`=?', array($this->id));
          foreach($rows as $k =>$v){
                $this->$k = $v;
          }
      }
      public static function getFieldId( $fieldname, $type, $relid){
            if (strpos($fieldname, '|') !== false){
                  $ex = explode("|",$fieldname);
                  $fieldname = current($ex)."|%";
            }else
                   $fieldname .="%";
            
            $row = mysql_get_row('SELECT id FROM '.self::_table.' WHERE `type` = ? AND relid = ? AND `fieldname` LIKE ? ', array($type, (int)$relid, $fieldname ));   
            return $row['id'];
      }
      
      public static function isField( $fieldname, $type, $relid){
            if (strpos($fieldname, '|') !== false){
                  $ex = explode("|",$fieldname);
                  $fieldname = current($ex)."|%";
            }else
                  $fieldname .="|%";
            
            $q = mysql_safequery('SELECT id FROM '.self::_table.' WHERE `type` = ? AND relid = ? AND `fieldname` LIKE ? ', array($type, (int)$relid, $fieldname ));   
            return (bool)mysql_num_rows($q);
      }
      
      public static function create($type='client',$relid=0,$fieldname,$fieldtype='text',$description="",$fieldoptions="",$regexpr="",$adminonly="",$required="",$showorder="",$showinvoice="",$sortorder=0) {

            $result = mysql_safequery('INSERT INTO tblcustomfields(`type`,`relid`,`fieldname`,`fieldtype`,`description`,fieldoptions,regexpr,adminonly,required,showorder,showinvoice,sortorder)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array($type,$relid,$fieldname,$fieldtype,$description,$fieldoptions,$regexpr,$adminonly,$required,$showorder,$showinvoice,$sortorder));
            if($result)
                  return mysql_insert_id ();
            else{
                  throw New Exception("MySQL: ".mysql_error());
            }
      }
}