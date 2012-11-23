<?PHP

	echo file_get_contents("intro_ga.txt");

?>
		</head>
		<?PHP

			echo file_get_contents("post_title.txt");

		?>			
		<div class="form_holder">
			<p style="padding-top:10px">Ramanathan is an RSS parser which reads RSS feeds and then sets up submission of items from these feeds into a learning registry node</p>
			<p>This page submits an entire feed - for inidividual items please see <a href="index.php">individual rss item submission</a></p>
			<p>Enter an RSS Feed you wish to submit (If the feed contains "dc:rights" or "dc:subject" fields then these will be sent to the registry node)</p>
			<form action ="submit_all_feed.php" target="_blank" method ="POST" onsubmit="javascript:getfeed()">
				<label>Enter RSS Feed Url</label><input id="url" name="url" type="text" size="150" />		
				<input type="submit" value="submit" />
			</form>
		</div>
		<div id="links">
		</div>
	</body>
</html>