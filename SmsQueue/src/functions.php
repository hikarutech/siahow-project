<?php

/**
 * Save data from text file with the first field as key
 *
 * @param string $filename
 * @param array $data
 * @return
 */
function data_save($filename, &$data)
{   	
	  $fh = fopen($filename, 'wb') or die("Error: Fail to open '$filename'");
    flock($fh, LOCK_EX)          or die('Error: Fail to lock file');

	  // Saving data
	  foreach ($data as $key => $value) {
	      fwrite($fh, "$key\t".$value)
	  		or die('Error: Fail to write content');
	  }
	  fflush($fh)                  or die('Error: Fail to flush file');
    
	  flock($fh, LOCK_UN)          or die('Error: Fail to unlock file');
	  fclose($fh);
}

/**
 * Generate and return a unique id for MO in YYYYMMDD-nnnn format
 *
 */
function get_date_serial($ref_file, $const_gmt_offset)
{	
    $fp = fopen($ref_file, "r+");

	  if (flock($fp, LOCK_EX)) {
	      // Read curr_num from the file	  			
	  	  $curr_num = '';
	  	  $curr_num = fread($fp, filesize($ref_file));
	  	  $data = explode("-",trim($curr_num));
	  	  $date_file = $data[0];
	  	  $num_file = $data[1];
	  	  $new_num = $num_file + 1;
	  	  
	  	  // get current date with YYYYMMDD format
	  	  $time = time();		
	  	  $gmt_offset = date('Z');			
	  	  $parts = getdate($time-$gmt_offset+$const_gmt_offset);
	  	  
	  	  $date_part = join('', array(
	  	      sprintf('%04d', $parts['year']),
	  	  	  sprintf('%02d', $parts['mon']),
	  	  	  sprintf('%02d', $parts['mday']),
	  	  ));
	  	
	  	  // compare current date format with file date format
	  	  if ($date_part > $date_file) {			
	  	      $new_data = $date_part."-1";			
	  	  } elseif ($date_part == $date_file){			
	  	  	  $new_data = $date_part."-".$new_num;			
	  	  } else {			
	  	  	  $new_data = $date_part."-1";			
	  	  }
	  	  
	  	  ftruncate($fp, 0);
	  	  fwrite($fp, $new_data);
	  	  fflush($fp) or die('Error: Fail to flush file');
	  	  flock($fp, LOCK_UN);	  			
	  } else {
	  	//echo "Couldn't get the lock!\n";
	  }

	  fclose($fp);
		
	  return $new_data;
}

/**
 * Scan the directory files
 *
 */
function dir_scan($main_dir)
{
    $result = array();
	
    $dir = scandir($main_dir);
	
    foreach ($dir as $file) {
        if ($file === '.' || $file === '..') {
            continue; 
		    }
		
		    $result[] = $file;        
    } 

    return $result;
} 

?>