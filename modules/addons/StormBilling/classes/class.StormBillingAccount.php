<?php

/**********************************************************************
 *  StormBilling Trunk (2014-01-17)
 * *
 *
 *  CREATED BY MODULESGARDEN       ->        http://modulesgarden.com
 *  CONTACT                        ->       contact@modulesgarden.com
 *
 *
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
 * @author Mariusz Miodowski <mariusz@modulesgarden.com>
 */



if(!class_exists('StormBillingAccount'))
{
    class StormBillingAccount
    {  
        private $amountPrecision    =   2;
        
        private $usagePrecision     =   3;
        
        private $accountId    =   null;

        public function __construct($accountId) 
        {
            $this->accountId  =   $accountId;
        }

        public function getSummary($productId, $startDate = null, $endDate = null)
        { 
            //Load Product Class
            require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'class.SBProduct.php';
            $p = new SBProduct($productId);
            $type       =   $p->getServerType();
            $resources  =   $p->getResources();
            $settings   =   $p->getSettings();
            
            $sql_filters = '';
            $params = array
            (
                'hosting_id'    =>  $this->accountId,
                'product_id'    =>  $productId,
            );
            
            //Detect start and end range
            if(!$startDate || !$endDate)
            {
                $start                  =   mysql_get_row("SELECT date FROM StormBilling_".$type."_prices WHERE hosting_id = ? AND product_id = ?  ORDER BY `date` ASC LIMIT 1 ", $params);
                $startDate              =   $start['date'];
                unset($start);
                
                $end                    =   mysql_get_row("SELECT date FROM StormBilling_".$type."_prices WHERE hosting_id = ? AND product_id = ?  ORDER BY `date` DESC LIMIT 1 ", $params);
                $endDate                =   $end['date'];
                unset($end);
            }
            
            //Set dates for filters
            $params['start_date']   =   $startDate;
            $params['end_date']     =   $endDate;
            
            //Prepare filters
            $sql_filters .= ' AND DATE(`date`) BETWEEN DATE(?) AND DATE(?)';
 
            //Counte difference between dates (in hours)
            $timeDiff = (strtotime($endDate) - strtotime($startDate)) / 3600;
            
            //Count records in database and calculate average amount of records in one hour
            $q = mysql_get_row("SELECT COUNT(record_id) as `count` FROM StormBilling_".$type."_prices WHERE hosting_id = ? AND product_id = ? ".$sql_filters, $params);
            $averageRecordsPerHour  =   $q['count'] / $timeDiff;
            
            //Get Relids!
            $rel_ids = mysql_get_array("SELECT DISTINCT rel_id FROM StormBilling_".$type."_prices WHERE hosting_id = ? AND product_id = ? ".$sql_filters, $params);

            //Get Summary Price
            $sum_price_sql = '';
            foreach($resources as $record_type => $records_values)
            {
                $sum_price_sql.= ' SUM(`'.$record_type.'`) as `'.$record_type.'`, ';
            }
            unset($record_type, $records_values);
            $sum_price_sql = trim($sum_price_sql, ', ');

            foreach($rel_ids as $rel_id)
            {
                $params['rel_id'] =     $rel_id['rel_id'];
                $summary_prices[$rel_id['rel_id']] = mysql_get_row("SELECT ".$sum_price_sql." FROM StormBilling_".$type."_prices WHERE  hosting_id = ? AND product_id = ?  ".$sql_filters." AND rel_id = ? ", $params);
            }
            unset($rel_id);
            
            //Get summary usage
            $sum_usage_sql = '';
            foreach($resources as $record_type => $records_values)
            {
                if(isset($records_values['DisplayType']))
                {
                    switch($records_values['DisplayType'])
                    {
                        case StormBillingResource::DISPLAY_AVERAGE:
                            $sum_usage_sql .= ' AVG(`'.$record_type.'`) as `'.$record_type.'`, ';
                            break;
                        
                        case StormBillingResource::DISPLAY_SUMMARY:
                            $sum_usage_sql .= ' SUM(`'.$record_type.'`) as `'.$record_type.'`, ';
                            break;
                        
                        case StormBillingResource::DISPLAY_HOURLY_SUM:
                            $sum_usage_sql .= ' SUM(`'.$record_type.'`)  / '.$averageRecordsPerHour.' as `'.$record_type.'`, ';
                            break;
                    }
                } 
                else
                {
                    switch($records_values['type'])
                    {
                        case 'summary':
                            $sum_usage_sql .= ' SUM(`'.$record_type.'`) as `'.$record_type.'`, ';
                            break;

                        case 'average':
                            $sum_usage_sql .= ' AVG(`'.$record_type.'`) as `'.$record_type.'`, ';
                            break;
                    }
                }
            }

            $sum_usage_sql = trim($sum_usage_sql, ', ');
            foreach($rel_ids as $rel_id)
            {
                $params['rel_id'] =     $rel_id['rel_id'];
                $summary_usage[$rel_id['rel_id']] = mysql_get_row("SELECT ".$sum_usage_sql." FROM StormBilling_".$type."_records WHERE hosting_id = ? AND product_id = ? ".$sql_filters." AND rel_id = ? ", $params);
            }

            $sum_prices     =   $summary_prices;
            $sum_usage      =   $summary_usage;

            $summary_prices =   array();
            $summary_usage  =   array();
            $parts          =   array();

            foreach($sum_prices as $key => &$records)
            {
                foreach($records as $record_key => $record_val)
                {
                    if(!isset($summary_prices[$record_key]['total']))
                    {
                        $summary_prices[$record_key]['total'] = 0;
                    }
                    $summary_prices[$record_key]['total']           +=  doubleval($record_val);
                    
                    if(!isset($parts[$record_key]['parts'][$key]['total']))
                    {
                        $parts[$record_key]['parts'][$key]['total'] =   0;
                    }
                    $parts[$record_key]['parts'][$key]['total']     =   doubleval($record_val);
                }
            }

            foreach($sum_usage as $key => &$records)
            {
                foreach($records as $record_key => $record_val)
                {
                    if(!isset($summary_usage[$record_key]['usage']))
                    {
                        $summary_usage[$record_key]['usage'] = 0;
                    }
                    $summary_usage[$record_key]['usage']        +=  doubleval($record_val);
                    
                    if(!isset($parts[$record_key]['parts'][$key]['usage']))
                    {
                        $parts[$record_key]['parts'][$key]['usage'] =   0;
                    }
                    $parts[$record_key]['parts'][$key]['usage'] =   doubleval($record_val);
                }
            }
            
            foreach($resources as $res_key => $res_val)
            {
                //total price
                $summarized[$res_key]['total']       =   !empty($summary_prices[$res_key]['total']) ? $summary_prices[$res_key]['total'] : 0;

                //summarized usage
                $summarized[$res_key]['usage']       =   !empty($summary_usage[$res_key]['usage']) ? $summary_usage[$res_key]['usage'] : 0;
                
                //get parts
                $summarized[$res_key]['parts']       =   !empty($parts[$res_key]['parts']) && is_array($parts[$res_key]['parts']) ? $parts[$res_key]['parts'] : array();
            }

            
            //Add FriendlyName
            foreach($summarized as $res_key => &$res_val)
            {
                $res_val['name']            =   $resources[$res_key]['FriendlyName'];
                if(isset($resources[$res_key]['ExtendedPricing']))
                {
                    $res_val['pricing'] = $resources[$res_key]['ExtendedPricing'];
                }
                else
                {
                    $res_val['price']   = $resources[$res_key]['price'];
                }
            }

            //Remove records parts with only one part with relid 0
            foreach($summarized as $res_key => &$res_val)
            {
                if(count($res_val['parts']) == 1 && isset($res_val['parts'][0]))
                {
                    unset($summarized[$res_key]['parts']);
                }
            }

            //Get relid names
            $relids = array();
            foreach($summarized as $res_key => &$res_val)
            {
                if(!empty($res_val['parts']))
                {
                    foreach($res_val['parts'] as $relid => &$vals)
                    {
                        if(!isset($relid[$relid]))
                        {
                            $relids[$relid] =   $p->module()->getRelIDName($relid);
                        }

                        $vals['name']    =   $p->module()->getRelIDName($relids[$relid]);
                    }
                }
            }
            
            //Change Units
            foreach($summarized as $res_key => &$res_val)
            {
                if(!empty($res_val['parts']))
                {
   
                    foreach($res_val['parts'] as $res_key_pert => &$res_val_part)
                    {
                        if(isset($settings['resource_settings'][$res_key]['AvailableUnits']) && array_key_exists($settings['resource_settings'][$res_key]['Unit'],$settings['resource_settings'][$res_key]['AvailableUnits']))
                        {
                            $unit                       =   $settings['resource_settings'][$res_key]['unit'];
                            $res_val_part['unit']       =   $unit;
                            $res_val_part['usage']      *=  $settings['resource_settings'][$res_key]['AvailableUnits'][$unit];
                        }
                        else 
                        {
                            $res_val_part['unit']       = $settings['resource_settings'][$res_key]['Unit'];
                        }
                    }
                }
                
                if(isset($settings['resource_settings'][$res_key]['AvailableUnits']) && array_key_exists($settings['resource_settings'][$res_key]['Unit'],$settings['resource_settings'][$res_key]['AvailableUnits']))
                {
                    $unit               =   $settings['resource_settings'][$res_key]['unit'];
                    $res_val['unit']    =   $unit;
                    $res_val['usage']   *=  $settings['resource_settings'][$res_key]['AvailableUnits'][$unit];
                }
                else 
                {
                    $res_val['unit'] = $settings['resource_settings'][$res_key]['Unit'];
                }
                
                //$res_val['default_unit']    =   $settings['resource_settings'][$res_key]['Unit'];
            }
            
            return $summarized;
        }
        
        public function getSummaryLines($productId, $startDate = null, $endDate = null)
        {
            //get summary usage
            $summary        =   $this->getSummary($productId, $startDate, $endDate);
            
            //Load Product Class
            require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'class.SBProduct.php';
            $p = new SBProduct($productId);
            
            //Get Resources
            $resources  =   $p->getResources();           
            
            //should we display multi resources on invouice?
            $showMultiResourcesOnInvoice    =   $p->module()->showMulitResourcesOnInvoice();
            
            
            $lines = array();
            
            if($showMultiResourcesOnInvoice)
            {
                foreach($summary as $recordKey  =>  $record)
                {
                    foreach($record['parts'] as $partKey    =>  $part)
                    {
                        if(!in_array($resources[$recordKey]['type'], array('average', 'summary')))
                        {
                            continue;
                        }

                        $line = array
                        (
                            'amount'    =>  round($part['total'], $this->amountPrecision),
                            'usage'     =>  round($part['usage'], $this->usagePrecision),
                            'partName'  =>  $part['name'],
                            'unit'      =>  $record['unit'],
                            'price'     =>  $record['price'],
                            'name'      =>  MG_Language::translate($record['name'])
                        );
                        
                        //Invoice description
                        if(!empty($resources[$recordKey]['InvoiceDescription']))
                        {
                            $line['invoiceDescription']    =   str_replace(array('{name}', '{price}', '{unit}', '{usage}', '{amount}', '{partName}'), array($line['name'], $line['price'], $line['unit'], number_format($line['usage'], $this->usagePrecision),  number_format($line['amount'], $this->amountPrecision), $line['partName']), $resources[$recordKey]['InvoiceDescription']);
                        }
                        else
                        {
                            $line['invoiceDescription']   =   $line['name'].' - '.$line['partName'].' - '.$line['usage'].$line['unit'];
                        }

                        //Admin area description
                        if(!empty($resources[$recordKey]['AdminAreaDescription']))
                        {
                            $line['dminAreaDescription']    =   str_replace(array('{name}', '{price}', '{unit}', '{usage}', '{amount}', '{partName}'), array($line['name'], $line['price'], $line['unit'], number_format($line['usage'], $this->usagePrecision),  number_format($line['amount'], $this->amountPrecision), $line['partName']), $resources[$recordKey]['AdminAreaDescription']);
                        }
                        else
                        {
                            $line['adminAreaDescription']   =   $line['name'].' - '.$line['partName'].' - '.$line['usage'].$line['unit'];
                        }

                        //Client area description
                        if(!empty($resources[$recordKey]['ClientAreaDescription']))
                        {
                            $line['clientAreaDescription']    =   str_replace(array('{name}', '{price}', '{unit}', '{usage}', '{amount}', '{partName}'), array($line['name'], $line['price'], $line['unit'], number_format($line['usage'], $this->usagePrecision),  number_format($line['amount'], $this->amountPrecision), $line['partName']), $resources[$recordKey]['ClientAreaDescription']);
                        }
                        else
                        {
                            $line['clientAreaDescription']   =   $line['name'].' - '.$line['partName'].' - '.$line['usage'].$line['unit'];
                        }

                        $lines[] = $line;
                    }
                }
            }
            else
            {
                foreach($summary as $recordKey  =>  $record)
                { 
                    if(!in_array($resources[$recordKey]['type'], array('average', 'summary')))
                    {
                        continue;
                    }
                    
                    $line = array
                    (
                        'amount'    =>  round($record['total'], $this->amountPrecision),
                        'usage'     =>  round($record['usage'], $this->usagePrecision),
                        'unit'      =>  $record['unit'],
                        'price'     =>  $record['price'],
                        'name'      =>  MG_Language::translate($record['name'])
                    );
                    
                    //Invoice description
                    if(!empty($resources[$recordKey]['InvoiceDescription']))
                    {
                        $line['invoiceDescription']    =   str_replace(array('{name}', '{price}', '{unit}', '{usage}', '{amount}'), array($line['name'], $line['price'], $line['unit'], number_format($line['usage'], $this->usagePrecision),  number_format($line['amount'], $this->amountPrecision),), $resources[$recordKey]['InvoiceDescription']);
                    }
                    else
                    {
                        $line['invoiceDescription']   =   $line['name'].' '.$line['usage'].$line['unit'];
                    }
                    
                    //Admin area description
                    if(!empty($resources[$recordKey]['AdminAreaDescription']))
                    {
                        $line['dminAreaDescription']    =   str_replace(array('{name}', '{price}', '{unit}', '{usage}', '{amount}'), array($line['name'], $line['price'], $line['unit'], number_format($line['usage'], $this->usagePrecision),  number_format($line['amount'], $this->amountPrecision),), $resources[$recordKey]['AdminAreaDescription']);
                    }
                    else
                    {
                        $line['adminAreaDescription']   =   $line['name'].' '.$line['usage'].$line['unit'];
                    }
                    
                    //Client area description
                    if(!empty($resources[$recordKey]['ClientAreaDescription']))
                    {
                        $line['clientAreaDescription']    =   str_replace(array('{name}', '{price}', '{unit}', '{usage}', '{amount}'), array($line['name'], $line['price'], $line['unit'], number_format($line['usage'], $this->usagePrecision),  number_format($line['amount'], $this->amountPrecision),), $resources[$recordKey]['ClientAreaDescription']);
                    }
                    else
                    {
                        $line['clientAreaDescription']   =   $line['name'].' '.$line['usage'].$line['unit'];
                    }
                    
                    $lines[] = $line;
                }
            }
            
            //Count total amount
            $amount = 0;
            foreach($lines as $line)
            {
                $amount += $line['amount'];
            }
           
            //Output array
            $out = array
            (
                'amount'    =>  round($amount, $this->amountPrecision),
                'lines'     =>  $lines,
                'startDate' =>  $startDate,
                'endDate'   =>  $endDate
            );
            
            return $out;
        }
    }
}