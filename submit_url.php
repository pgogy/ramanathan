<?PHP

	session_start();
	
	$xml = $_SESSION[urlencode($_GET['url'])];

	$content_info = array();
	$content_info['resource_locator'] = $_GET['url']; 
	$content_info['resource_data_type'] = 'metadata'; 
	$content_info['submitter'] = "ramanathan"; 
	if($_GET['tos']==""){
		$tos = "None provided";
	}else{
		$tos = $_GET['tos'];
	}
	$content_info['tos'] = $tos;
	$content_info['curator'] = "ramanathan"; 
	$content_info['active' ] = TRUE; 
	$content_info['payload_schema'] = 'DC 1.1'; 
	
	//
	// This below helps to format your PHP data so it matches the JSON required by the LR
	// Basically you need to create a data structure using the
	// variables set above and the XML created. So it might help to think of this as a black box
	// Fire and forget
	//
	
	$opt_id_fields = array(
		'curator',
		'owner',
		'signer',
	);

	$opt_res_fields = array(
		'submitter_timestamp',
		'submitter_TTL',
		'keys',
		'resource_TTL',
		'payload_schema_locator',
		'payload_schema_format',
	);

	$opt_sig_fields = array(
		'signature',
		'key_server',
		'key_locations',
		'key_owner',
		'signing_method',
	);

	$opt_tos_fields = array(
		'tos_submission_attribution',
	);
	
	// Make some parts of the PHP data structure
	
	$identity = new StdClass;
	$resource_data = new StdClass;
	
	$identity->submitter_type = 'ramanathan';
	$identity->submitter = $content_info['submitter'];
	
	$tos = new StdClass;

	$tos->submission_TOS = $content_info['tos'];

	// Optional identity values.
	foreach ($opt_id_fields as $field) {
		if (array_key_exists($field, $content_info)) {
			$identity->$field = $content_info[$field];
		}
	}

	// Optional resource_data values.
	foreach ($opt_res_fields as $field) {
		if (array_key_exists($field, $content_info)) {
			$resource_data->$field = $content_info[$field];
		}
	}

	// Optional TOS values.
	foreach ($opt_tos_fields as $field) {
		if (array_key_exists($field, $content_info)) {
			$tos->$field = $content_info[$field];
		}
	}
	
	// Now the data structure is sort of finished, so add in some extra bits

	$resource_data->doc_type = 'resource_data';
	$resource_data->doc_version = '0.23.0';
	$resource_data->resource_data_type = $content_info['resource_data_type'];
	$resource_data->active = $content_info['active'];
	$resource_data->identity = $identity;
	$resource_data->TOS = $tos;

	$resource_data->resource_locator = $content_info['resource_locator'];
	$resource_data->payload_placement = 'inline';
	$resource_data->payload_schema = array($content_info['payload_schema']);
	$resource_data->resource_data = htmlspecialchars_decode($xml);
	
	if($_GET['keys']==""){
		$keys = "None provided";
	}else{
		$keys = $_GET['keys'];
	}
	
	$resource_data->keys = explode(",",$keys);
	
	$submission = new StdClass;
	
	$submission->documents[] = $resource_data;
	
	$data_to_send = json_encode($submission);
	
	// Curl is some PHP stuff to send data across the interwebs
	
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_URL, "http://alpha.mimas.ac.uk/publish");
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 50); 
	curl_setopt($ch, CURLOPT_USERPWD, "");
	curl_setopt($ch, CURLOPT_TIMEOUT, 50); 
	curl_setopt($ch, CURLOPT_MAXREDIRS, 10); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json')); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_to_send);
	
	//CURL exec sends the data
	
	$result = curl_exec($ch);
	
	//Traps the error if something bad happens
	
	$error = curl_error($ch);
	curl_close($ch);
	
	// Convert the data from LR json back into PHP so we can check it
	
	print_r($result);

?>