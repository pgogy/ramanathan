<?PHP

 $ch = curl_init(); 
 curl_setopt($ch, CURLOPT_HEADER, 0); 
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
 curl_setopt($ch, CURLOPT_URL, "http://graph.facebook.com/" . $_GET['url']);
 curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 50); 
 curl_setopt($ch, CURLOPT_TIMEOUT, 50); 
 curl_setopt($ch, CURLOPT_MAXREDIRS, 10); 
 $data = curl_exec($ch);
 
 echo $data;
 
 curl_close($ch); 
 
?>