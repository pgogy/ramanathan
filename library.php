<?PHP

	session_start();
	
	print_r($SESSION[$_GET['url']);
	
	die();

	$content_info = array();
	$content_info['resource_locator'] = "http://politicsinspires.org"; // <---- PUT URL HERE
	$content_info['resource_data_type'] = 'metadata'; // <---- LEAVE INTACT
	$content_info['submitter'] = "Pat Lockley"; // <---- CHANGE IF NOT YOU
	$content_info['tos'] = "For use by Pat"; // <---- CHANGE PERHAPS TO CC LICENCE?

	// Currently set curator and submitter the same, once we get a better sense
	// of what each field means, this will change.
	$content_info['curator'] = "Pat Lockley"; // <---- CHANGE IF NOT YOU
	$content_info['active' ] = TRUE; // <---- LEAVE INTACT
	$content_info['payload_schema'] = 'DC 1.1'; // <---- NO IDEA :)

	//
	// OK So we need to make some XML for the metadata
	// This block of code is basically the binding of our XML book
	// You don't need to change this
	// Unless you want to
	//
	
	$source = '<?xml version="1.0"?>
    <!DOCTYPE rdf:RDF PUBLIC "-//DUBLIN CORE//DCMES DTD 2002/07/31//EN"
      "http://dublincore.org/documents/2002/07/31/dcmes-xml/dcmes-xml-dtd.dtd">
    <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
      xmlns:dc ="http://purl.org/dc/elements/1.1/">
      <rdf:Description rdf:about="http://dublincore.org/">
      </rdf:Description>
    </rdf:RDF>';
	
	// Behind the scences XML Stuff

	$xml_document = new DOMDocument();
	$xml_document->loadXML($source);
	
	// We need to add more data to the XML book we've created, so lets open the book and start writing
	$node_list = $xml_document->getElementsByTagNameNS('http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'Description');
	$dc_node = $node_list->item(0);

	// Set the TITLE in DC terms for this element
	$element = $xml_document->createElementNS('http://purl.org/dc/elements/1.1/', 'title', "World of Pat" /* <--- CHANGE ME */);
	
	// Write this to the xml
	$dc_node->appendChild($element);
	
	// Set the DESCRIPTION in DC terms for this element
	$element = $xml_document->createElementNS('http://purl.org/dc/elements/1.1/', 'description', "Pat's world. It is interesting" /* <--- CHANGE ME */);
	
	// Write this to the xml
	$dc_node->appendChild($element);

	// Set the DC identifier to the URL of the resource
	$element = $xml_document->createElementNS('http://purl.org/dc/elements/1.1/', 'identifier', "http://politicsinspires.org" /* <----- CHANGE ME */);
	
	// Write this to the xml
	$dc_node->appendChild($element);

	// Pretty format the XML before sending it to the LR.
	// Must be reloaded, won't work if just set on $xmlDoc.
	$return_xml = new DOMDocument();
	$return_xml->preserveWhiteSpace = FALSE;
	$return_xml->formatOutput = TRUE;
	$return_xml->loadXML($xml_document->saveXML());	
	
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
	
	$identity->submitter_type = 'agent';
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
	$resource_data->resource_data = $return_xml->saveXML();
	
	echo "<p>Your data structure looks like this - the XML < and > won't display as the browser thinks they are HTML</p>";
	
	echo "<pre>";

	print_r($resource_data);
	
	echo "</pre>";
	
	// Complete the LR PHP data structure
	
	$submission = new StdClass;
	
	$submission->documents[] = $resource_data;
	
	$data_to_send = json_encode($submission);
	
	// Curl is some PHP stuff to send data across the interwebs
	
	$ch = curl_init();
	$curl_options = array(
		CURLOPT_URL => 'http://81.187.87.93/publish',
		CURLOPT_POST => TRUE,
		CURLOPT_POSTFIELDS => $data_to_send,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_HTTPHEADER => array('Content-type: application/json'),
	);
	curl_setopt_array($ch, $curl_options);
	
	//CURL exec sends the data
	
	$result = curl_exec($ch);
	
	//Traps the error if something bad happens
	
	$error = curl_error($ch);
	curl_close($ch);
	
	// Convert the data from LR json back into PHP so we can check it
	
	$result_data = json_decode($result);
		
	if($result_data->OK==1){
	
		echo "<p>Submission success - Please see</p>";
	
		echo "<a href='http://81.187.87.93/harvest/getrecord?by_doc_ID=TRUE&request_ID=" . $result_data->document_results[0]->doc_ID . "'>http://81.187.87.93/harvest/getrecord?by_doc_ID=TRUE&request_ID=" . $result_data->document_results[0]->doc_ID . "</a>";
	
	}else{
	
		echo "<p>Failed</p>";
				
		echo "<pre>";
		
		print_r($result_data);
		
		print_r($error);
		
	}

?>