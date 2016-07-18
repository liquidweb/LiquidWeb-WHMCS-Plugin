<?php

/**
 * Simple class to writing logs into file
 */

class FileLogger
{
    //Default settings
    private $settings = array
    (
        'error_file'    =>  'error_file.log',
        'info_file'     =>  'info_file.log',
    );
    
    public function __construct($settings = null)
    {
        //load settings if user specified some options
        if($settings)
        {
            foreach($settings as $key => $s)
            {
                if(isset($this->settings[$key]))
                {
                    $this->settings[$key] = $s;
                }
            }
        }
    }
    
    /**
     * Add line to info file
     * @param type $line 
     */
    public function addInfo($line)
    {
        if(!file_exists(($filename = $this->settings['info_file'])))
        {
                fopen($filename, 'w');
        }
        
        if(file_exists($filename))
        {
            file_put_contents($this->settings['info_file'], date("Y-m-d G:i:s").': '.$line."\n", FILE_APPEND);
        }
    }
    
    /**
     * Add line to error file
     * @param type $line 
     */
    public function addError($line)
    {
        if(!file_exists(($filename = $this->settings['error_file'])))
        {
                fopen($filename, 'w');
        }
        
        if(file_exists($filename))
        {
            file_put_contents($this->settings['error_file'], date("Y-m-d G:i:s").': '.$line."\n", FILE_APPEND);
        }
    }
}
?>
