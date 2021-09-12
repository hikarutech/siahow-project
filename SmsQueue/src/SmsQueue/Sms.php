<?php 

namespace Siahow\SmsQueue;

class Sms
{
    /**
     * create message
     */    
    public function createMessage($message)
    {		
    		$sms_id = $this->generate_sms_id();

    		$date_info = explode("-",$sms_id);
    		
    		$date = $date_info[0];
    		
    		$serial = $date_info[1];

				$vendorDir = dirname(dirname(__FILE__));
				
				$baseDir = dirname($vendorDir);		
				    		
    		$file = $baseDir. "/sms/msg/$sms_id.txt";
    		
				$data = array(
				    'message' => $message,
				);
				
				data_save($file, $data);    		
    		  		
        return $sms_id;
    }   

    /**
     * send message
     */      
    public function sendMessage()
    {		
		    // Initialise for sorting (multisort)
			  $buffer = array(
			  	  array(),
			  	  array(),
			  );    	
        
			  $msg_list = array();
			  {
			  	  $vendorDir = dirname(dirname(__FILE__));
			  	  
			  	  $baseDir = dirname($vendorDir);				
			  	  
			  	  foreach (dir_scan($baseDir. "/sms/msg") as $file) {
			  	      // Skip those not matching "YYYYDDMM-nnnn.txt" format
			  	  	  if(! preg_match('/^\d{8}\-\d+\.txt$/', $file)){
			  	  	  	  continue;
			  	  	  }
			  	  	  
			  	  	  list($date, $serial) = preg_split('/[\-\.]/', $file);
			  	  	  
			  	  	  $buffer[0][] = $date;
			  	  	  $buffer[1][] = $serial;
			  	  }
			  	  
			  	  // Sort by date, follow by serial (FIFO)
			  	  array_multisort(
			  	  	  $buffer[0], SORT_STRING , SORT_ASC,
			  	  	  $buffer[1], SORT_NUMERIC, SORT_ASC
			  	  );
			  	  
			  	  // Get oldest 1 and put into $msg_list
			  	  $i = 0;
			  	  foreach ($buffer[0] as $date) {
			  	  	  $serial = $buffer[1][$i];
			  	  	  
			  	  	  $msg_list[] = "$date-$serial";
			  	  	  $i++;
			  	  	  
			  	  	  // Only get the oldest 1 MO
			  	  	  if ($i >= 1) {
			  	  	  	  break;
			  	  	  }
			  	  }
			  }
			  
			  foreach ($msg_list as $msg_id) {			
			  	  // Process & run
			  	  {		  	  	
			  	  	  // Timing the execution
			  	  	  $t1 = microtime(1);
			  	  	  
			  	  	  $timestr = date("Y-m-d H:i:s");
			  	  	  list($date_now, $time_now) = explode(" ", $timestr);					
			  	  	  
			  	  	  $temp_file = $baseDir . "/sms/msg/$msg_id.txt";
			  	  	  {
			  	  	  	  $lines = file($temp_file);
			  	  	  	  foreach ($lines as $line) {
			  	  	  	  	  $token = preg_split("/[\t]/", $line);
			  	  	  	  	  $message = isset($token[1]) ? $token[1] : '';							
			  	  	  	  }						
			  	  	  	
			  	  	  }
                
			  	  	  $td = round(microtime(1) - $t1, 3);
            
			  	  }
			  	  
			  	  // Delete the msg files
			  	  unlink($temp_file);
			  }			
		    
    	  if (isset($temp_file)) {	  		
        	  return json_encode(array($msg_id => $message));
        } else{
        	return json_encode(array('error' => 'empty queue'));
        }
      
    }  

    /**
     * get total number messages in queue
     */      
    public function totalMessage()
    {		
			  $vendorDir = dirname(dirname(__FILE__));
			  $baseDir = dirname($vendorDir);		
			      		
  		  $directory = $baseDir. "/sms/msg/";    	
			  $filecount = 0;
			  $files = glob($directory . "*");
			  if ($files) {
			      $filecount = count($files);
			  } 		
    	  	  		
        return $filecount;
    }    

    /**
     * get all message in json format
     */    
    public function getAllMessage()
    {		

			  $all_msg_list = array();
			  {
			  	  $vendorDir = dirname(dirname(__FILE__));
			  	  $baseDir = dirname($vendorDir);				
			  	  
			  	  foreach (dir_scan($baseDir. "/sms/msg") as $file) {
			  	  	  $lines = file($baseDir. "/sms/msg/" . $file);
			  	  	  foreach ($lines as $line) {
			  	  	  	  $token = preg_split("/[\t]/", $line);
			  	  	  	  $message = isset($token[1]) ? $token[1] : '';							
			  	  	  	  $all_msg_list[$file] = $message;
			  	  	  }									
			  	  }
			  	
			  }
		    		
        return json_encode($all_msg_list, JSON_PRETTY_PRINT);
    }              
    
    /**
     * generate sms id
     */
		static function generate_sms_id(){
			  // Generating sms_id
			  $vendorDir = dirname(dirname(__FILE__));
			  $baseDir = dirname($vendorDir);			
			  
			  $sms_id = get_date_serial($baseDir. "/sms/msg_id.txt", 8*3600);
			  
			  return $sms_id;
		}     
    
}

