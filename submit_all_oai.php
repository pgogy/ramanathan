<?PHP

	echo file_get_contents("intro_ga.txt");

?>
		</head>
		<?PHP

			echo file_get_contents("post_title.txt");

		?>			
		<div class="form_holder">
			<p style="padding-top:10px">Ramanathan is an OAI parser which reads OAI_DC feeds and then sets up submission of items from these feeds into a learning registry node</p>
			<p>This page submits an entire feed OAI_DC OAI Feed (but it doesn't use resumption tokens)</p>
			<form action ="submit_all_feed_oai.php" target="_blank" method ="POST" onsubmit="javascript:getfeed()">
				<label>Enter RSS Feed Url</label><input id="url" name="url" type="text" size="150" />		
				<input type="submit" value="submit" />
			</form>
		</div>
		<div id="links">
		</div>
	</body>
</html>