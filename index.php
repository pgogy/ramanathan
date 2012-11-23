<?PHP

	echo file_get_contents("intro_ga.txt");

?>
		<script type="text/javascript" language="javascript">
			
			var xmlhttp;

			function waiting(){

				document.body.style.cursor = "wait";

			}

			function release(){

				document.body.style.cursor = "default";

			}
			
			function setup_xmlhttp(){
				
				if (window.XMLHttpRequest)
				  {// code for IE7+, Firefox, Chrome, Opera, Safari
				  xmlhttp=new XMLHttpRequest();
				  }
				else
				  {// code for IE6, IE5
				  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				  }
				  
			}
			
			function paradata_submit_url(url,shares){
			
				setup_xmlhttp();
				
				xmlhttp.onreadystatechange=function()
				  {
				  if (xmlhttp.readyState==4 && xmlhttp.status==200)
					{
					
						data = eval('(' + xmlhttp.responseText + ')');
						
						if(data['OK']==true){
						
							for(x in data['document_results']){
							
								document.getElementById(url).childNodes[0].childNodes[7].innerHTML = " Submitted (doc id : <a target='_blank' href='http://alpha.mimas.ac.uk/obtain?by_doc_ID=true&request_ID=" + data['document_results'][0]['doc_ID'] + "'><em>" + data['document_results'][0]['doc_ID'] + "</em></a>)";
							
							}
					
						}else{
						
							alert("Submission failed");
						
						}
						release();
						
					}
				  }
				xmlhttp.open("GET","paradata_submit_url.php?url=" + url + "&shares=" + shares + "&tos=" + document.getElementById(url).getAttribute("rights") + "&keys=" + document.getElementById(url).getAttribute("keywords"),true);
				xmlhttp.send();
				waiting();
			
			}
			
			function paradata_url(url){
			
				setup_xmlhttp();
				
				xmlhttp.onreadystatechange=function()
				  {
				  if (xmlhttp.readyState==4 && xmlhttp.status==200)
					{
						
						release();

						data = eval('(' + xmlhttp.responseText + ')');
						
						shares = data['shares'];
						
						if(data['shares']==undefined){
						
							alert("No Facebook Shares reported");
							shares=0;
						
						}
						
						var share_data = confirm("Do you want to submit this data?");
						
						if(share_data){

							waiting();
						
							paradata_submit_url(url,shares);
						
						}
						
						
					}
				  }
				xmlhttp.open("GET","paradata_url.php?url=" + url,true);
				xmlhttp.send();
				waiting();
			
			}
			
			function submit_url(url){
			
				setup_xmlhttp();
				
				xmlhttp.onreadystatechange=function()
				  {
				  if (xmlhttp.readyState==4 && xmlhttp.status==200)
					{
						
						data = eval('(' + xmlhttp.responseText + ')');
						
						if(data['OK']==true){
						
							for(x in data['document_results']){
							
								document.getElementById(url).childNodes[0].childNodes[5].innerHTML = " Submitted (doc id : <a target='_blank' href='http://alpha.mimas.ac.uk/obtain?by_doc_ID=true&request_ID=" + data['document_results'][0]['doc_ID'] + "'><em>" + data['document_results'][0]['doc_ID'] + "</em></a>) | ";
							
							}
					
						}else{
						
							alert("Submission failed");
						
						}

						release();
						
					}
				  }
				xmlhttp.open("GET","submit_url.php?url=" + url + "&tos=" + document.getElementById(url).getAttribute("rights") + "&keys=" + document.getElementById(url).getAttribute("keywords"),true);
				xmlhttp.send();
				waiting();
			
			}
			
			function check_url(url){
			
				setup_xmlhttp();
				
				xmlhttp.onreadystatechange=function()
				  {
				  if (xmlhttp.readyState==4 && xmlhttp.status==200)
					{

						data = eval('(' + xmlhttp.responseText + ')');

						if(data['error']!=""){
						
							alert("Nothing found for this URL");
						
						}else{
						
							string = "";
						
							for(x in data['getrecord']['record']){
							
								string += "<a target='_blank' href='http://alpha.mimas.ac.uk/obtain?by_doc_ID=true&request_ID=" + data['getrecord']['record'][x]['header']['identifier'] + "'><em>" + data['getrecord']['record'][x]['header']['identifier'] + "</em></a> ";
								
							
							}
							
							document.getElementById(url).childNodes[0].childNodes[3].innerHTML = " document(s) (doc id(s) : " + string + ") | ";
						
						}

						release();
						
					}
				  }
				xmlhttp.open("GET","check_url.php?url=" + url,true);
				xmlhttp.send();
				waiting();
			
			}
			
			function getfeed(){
			
				setup_xmlhttp();
				  
				xmlhttp.onreadystatechange=function()
				  {
				  if (xmlhttp.readyState==4 && xmlhttp.status==200)
					{
					
						data = eval('(' + xmlhttp.responseText + ')');
						
						for(x in data){
						
							document.getElementById("links").innerHTML += "<div rights='" + encodeURIComponent(data[x]['rights']) +"' keywords='" + encodeURIComponent(data[x]['subject']) +"' id='" + encodeURIComponent(x) + "'><p><a href='" + x + "'>" + data[x]['title'] + "</a><span> | </span><a onclick='javascript:check_url(\"" + encodeURIComponent(x) + "\");'>Check for this URL</a><span> | </span><a onclick='javascript:submit_url(\"" + encodeURIComponent(x) + "\");'>Submit this URL</a><span> | </span><a onclick='javascript:paradata_url(\"" + encodeURIComponent(x) + "\");'>Facebook Paradata for this URL</a><span></span></p></div>";
						
						}

						release();
						
					}
				  }
				xmlhttp.open("GET","get_feed.php?url=" + document.getElementById("url").value,true);
				xmlhttp.send();
				waiting();
			
			}
		</script>
		</head>
		<?PHP

			echo file_get_contents("post_title.txt");

		?>			
		<div class="form_holder">
			<p style="padding-top:10px">Ramanathan is an RSS parser which reads RSS feeds and then sets up submission of items from these feeds into a learning registry node</p>
			<p>To submit an entire feed please see the <a href="submit_all.php">submit all page</a> or <a href="submit_all_oai.php">submit all page (OAI:DC)</a></p>
			<p>Enter an RSS Feed you wish to examine - (If the feed contains "dc:rights" or "dc:subject" fields then these will be sent to the registry node)</p>
			<form action = "" method ="POST" onsubmit="javascript:getfeed()">
				<label>Enter RSS Feed Url</label><input id="url" type="text" size="150" />		
				<label class="button" onclick="javascript:getfeed();">Submit</label>
			</form>
		</div>
		<div id="links">
		</div>
	</body>
</html>