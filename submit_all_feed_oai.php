<?PHP

	echo file_get_contents("intro_ga.txt");

?>
		</head>
		<?PHP

			echo file_get_contents("post_title.txt");

		?>			
		<div>
		<?PHP

			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_HEADER, 0); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($ch, CURLOPT_URL, $_POST['url']); 
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 50); 
			curl_setopt($ch, CURLOPT_TIMEOUT, 50); 
			curl_setopt($ch, CURLOPT_MAXREDIRS, 10);

			$response = curl_exec($ch);
 
			$data = @simplexml_load_string($response);

			if($data){
 
				if(isset($data->ListRecords)){
 
					$return = new StdClass();

				 	foreach($data->ListRecords as $item) {  

						foreach($item as $record){

							$sxe = new SimpleXMLElement("<record xmlns:dc=\"http://purl.org/dc/elements/1.1/\" xmlns:dct=\"http://purl.org/dc/terms/\"></record>");

							$ns_dc = $record->metadata->children('http://www.openarchives.org/OAI/2.0/oai_dc/'); 

							$metadata = $ns_dc->children('http://purl.org/dc/elements/1.1/');

							$rights = "";
							$subject = "";
							$links = array();
		  
							foreach($metadata as $key => $value){
		  
								@$sxe->addChild("dc:" . $key, "<![CDATA[" . $value . "]]>", "xmlns:dc=\"http://purl.org/dc/elements/1.1/\"");
			
								if($key=="rights"){
			
									$rights = (string)$value[0];
			
								}
			
								if($key=="subject"){
			
									$subject .= $value . ",";
			
								}

								if($key=="identifier"){
			
									array_push($links, $value);
			
								}
		  
							}

							foreach($links as $link){

								$data_object = new StdClass();

								$data_object->title = (string)$metadata->title[0];
								$data_object->rights = $rights;
								$data_object->subject = substr($subject,0,(strlen($subject)-1));
								$data_object->package = $sxe->asXML();

								$return->{$link} = $data_object;

							}

						}
		  
					 }
	 
				 }else{
 
					echo "not a valid XML Feed";
 
				 }
 
			 }else{
 
				echo "not a valid XML Feed";
 
			 }

			$content_info = array();

			$content_info['resource_data_type'] = 'metadata'; 
			$content_info['submitter'] = "ramanathan"; 
			$content_info['curator'] = "ramanathan"; 
			$content_info['active' ] = TRUE; 
			$content_info['payload_schema'] = 'DC 1.1'; 

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

			$resource_data->doc_type = 'resource_data';
			$resource_data->doc_version = '0.23.0';
			$resource_data->resource_data_type = $content_info['resource_data_type'];
			$resource_data->active = $content_info['active'];
			$resource_data->identity = $identity;
			
			$resource_data->payload_placement = 'inline';
			$resource_data->payload_schema = array($content_info['payload_schema']);

			foreach($return as $data => $value){

				echo "<p>URL (" . $data . ") submitted ";				
					
				$content_info['resource_locator'] = $data; 
				$resource_data->resource_locator = $data;

				if($value->rights==""){
					$tos = "None provided";
				}else{
					$tos = $value->rights;
				}

				$content_info['tos'] = $tos;
	
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
				
				$resource_data->TOS = $tos;
				
				$resource_data->resource_data = htmlspecialchars_decode($data_object->package);
	
				if($data_object->subject==""){
					$keys = "None provided";
				}else{
					$keys = $data_object->subject;
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
				curl_setopt($ch, CURLOPT_USERPWD, "fred:flintstone");
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
	
				$doc_data = json_decode($result);

				echo "<a target='_blank' href='http://alpha.mimas.ac.uk/obtain?by_doc_ID=true&request_ID=" . $doc_data->document_results[0]->doc_ID . "'> Document ID : " . $doc_data->document_results[0]->doc_ID . "</a></p>";

			}

		?>
		</div>
	</body>
</html>