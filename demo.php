<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sms Queue Exercise</title>
    <style>
        div {padding: 3px;}
        div span {width: 120px; display: inline-block;}
    </style>
</head>
<body>

    <?php
 	      $url = 'http://localhost/SmsQueue/api.php';     
 	      
	      function curl_post($url, $params)
	      {       
	      	  $ch = curl_init($url);
	      	  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	      	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	      	  curl_setopt($ch, CURLOPT_POST, 1);
	      	  curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	      	  
	      	  $response = curl_exec($ch);
	      	  
	      	  curl_close($ch);
	      	  
	      	  return $response;
	      }		 
    ?>

    2) HTTP API to insert an SMS Message in the queue
    <form method="post" action="demo.php">
        <input type="hidden" name="insert_form" id="insert_form" />
        <div class="lblName">
            <span>SMS Messsage : </span> 
            <input type="text" name="message" maxlength="160" placeholder="This is my message">
            <input type="submit" value="Insert">
        </div>   
    </form>
    <?php
    
        if (isset($_POST['insert_form'])) {
            $params = array(
        			  'c' => 'createMessage',
        			  'message' => $_POST['message'],
        		);									
        		$response = curl_post($url, $params);
        		print "<font color='green'>Message ID Inserted:</font> " . $response;
        }
    
    ?>
    <br/><br/>


    3) HTTP API to consume an SMS Message from the queue and returns it in JSON format
    <form method="post" action="demo.php">
    		<input type="hidden" name="send_form" id="send_form" />
        <div class="lblName">
        	  <span>Send SMS: </span>
            <input type="submit" value="Click to Send">
        </div>   
    </form>
    <?php
    
    if (isset($_POST['send_form'])) {
    		$params = array(
    			  'c' => 'sendMessage',
    		);									
    		$response = curl_post($url, $params);
    		print "<font color='green'>Message Sent:</font> " . $response;
    }
    
    ?>
    <br/><br/>

    4) HTTP API to get the total number of messages in the queue
    <div class="lblName">  
    <?php
    
    		$params = array(
    			  'c' 			=> 'totalMessage',
    		);									
    		$response = curl_post($url, $params);
    		print "<font color='green'>Total Messages:</font> " . $response;
    
    ?>
    </div>
    <br/><br/>


    5) HTTP API to get all SMS messages in the queue in JSON format
    <div class="lblName">  
    <?php
    
    		$params = array(
    			  'c' 			=> 'getAllMessage',
    		);									
    		$response = curl_post($url, $params);
    		print "<font color='green'>All Message:</font> <pre>" . $response . "</pre>";
    
    ?>
    </div>
    <br/><br/>

</body>
</html>
