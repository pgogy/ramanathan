<?PHP

 $ch = curl_init(); 
 curl_setopt($ch, CURLOPT_HEADER, 0); 
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
 curl_setopt($ch, CURLOPT_URL, $_GET['url']); 
 curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 50); 
 curl_setopt($ch, CURLOPT_TIMEOUT, 50); 
 curl_setopt($ch, CURLOPT_MAXREDIRS, 10); 
 $data = @simplexml_load_string(curl_exec($ch));
 
 if($data){
 
    if(isset($data->channel->item)){
 
		session_start();
		
		$return = new StdClass();
	 
		 foreach ($data->channel->item as $item) {  
		  
		  $sxe = new SimpleXMLElement("<item xmlns:dc=\"http://purl.org/dc/elements/1.1/\" xmlns:dct=\"http://purl.org/dc/terms/\"></item>");
		 
		  $ns_dc = $item->children('http://purl.org/dc/elements/1.1/'); 

		  $rights = "";
		  $subject = "";
		  
		  foreach($ns_dc as $key => $value){
		  
			@$sxe->addChild("dc:" . $key, "<![CDATA[" . $value . "]]>", "xmlns:dc=\"http://purl.org/dc/elements/1.1/\"");
			
			if($key=="rights"){
			
				$rights = (string)$value[0];
			
			}
			
			if($key=="subject"){
			
				$subject .= $value . ",";
			
			}
		  
		  }
		  
		  foreach($item as $key => $value){
		  
			@$sxe->addChild($key, "<![CDATA[" . $value . "]]>");
		  
		  }
		  
		  $_SESSION[urlencode($item->link)] = $sxe->asXML();

          $data_object = new StdClass();

		  $data_object->title = (string)$item->title[0];
		  $data_object->rights = $rights;
		  $data_object->subject = substr($subject,0,(strlen($subject)-1));
	
		  $return->{$item->link} = $data_object;
		  
		 }
	 
	 }else{
 
		echo "not a valid XML Feed";
 
	}
 
 }else{
 
	echo "not a valid XML Feed";
 
 }
 
 print_r(json_encode($return));
 
 curl_close($ch); 
 
?>