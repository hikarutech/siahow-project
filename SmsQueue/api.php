<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/functions.php';

use Siahow\SmsQueue\Sms;

if(isset($_POST['c'])){

	$message = new Sms();
	
	if($_POST['c'] == 'createMessage'){
		$msg_id = $message->createMessage($_POST['message']);
		echo $msg_id;
	}
	elseif($_POST['c'] == 'sendMessage'){
		$result = $message->sendMessage();
		echo $result;		
	}
	elseif($_POST['c'] == 'totalMessage'){
		$result = $message->totalMessage();
		echo $result;				
	}
	elseif($_POST['c'] == 'getAllMessage'){
		$result = $message->getAllMessage();
		echo $result;			
	}	
	else{
		
	}
}
else{
	echo "Error";
}
