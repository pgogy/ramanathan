<?PHP

 $ch = curl_init(); 
 curl_setopt($ch, CURLOPT_HEADER, 0); 
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
 curl_setopt($ch, CURLOPT_URL, "http://alpha.mimas.ac.uk/harvest/getrecord?by_resource_id=TRUE&request_id=" . urlencode($_GET['url']));
 curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 50); 
 curl_setopt($ch, CURLOPT_TIMEOUT, 50); 
 curl_setopt($ch, CURLOPT_MAXREDIRS, 10); 
 $data = curl_exec($ch);
 
 echo $data;
 
 curl_close($ch); 
 
?>