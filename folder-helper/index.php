<?php

/*

Generate file management commands to sort images on local filesystem:
- create folder for train/tram/bus with name and relevant locations
- then move images into those folders
Based on caption data entered against the images

*/

error_reporting(E_ALL);
ini_set('display_errors', '1');
DEFINE ('WEBPATH', "");
define('OFFSET_PATH', 3);
require_once(dirname(dirname(dirname(__FILE__))) . '/zp-core/admin-globals.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/zp-core/template-functions.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/themes/wongm/functions-link-definitions.php');
admin_securityChecks(ALBUM_RIGHTS, currentRelativeURL());
echo '</head>';
?>
<body>
	<?php printLogoAndLinks(); ?>
	<div id="main">
		<?php printTabs(); ?>
		<div id="content">
		<h1>Organise files</h1>
		
		<form action="" method="GET">
			<label for="date-select">Date:</label>
			<input type="date" id="date-select" name="date" required>
			<button type="submit">Search</button>
		</form>
<?php

printAdminHeader('overview', gettext('Organise files'));

if (isset($_GET['date']))
{
	$date = $_GET['date'];
	if (DateTime::createFromFormat('Y-m-d', $date) !== false) {
	  runSearch($date);
	}
}

function runSearch($date)
{
	global $_zp_db;
	global $popularImageText;
	
// Read words from file (one per line)
	$locationNames = file('Locations.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$streetNames = file('Streets.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$outputCommands = [];

	foreach(array('trains', 'buses', 'trams') as $photoType)
	{
		$foldername = "";
		
		$imageTitlesForDayQuery = "SELECT i.title, i.filename
			FROM " . $_zp_db->prefix('images') . " i 
			INNER JOIN " . $_zp_db->prefix('albums') . " a ON i.albumid = a.id
			WHERE (" .  $popularImageText[$photoType]['where'] . ")
			AND (i.date >= '" . $date . "' AND i.date <= '" . $date . "' + INTERVAL 1 DAY)";
		
		$imageTitlesForDayResults = $_zp_db->queryFullArray($imageTitlesForDayQuery);
		$imageTitles = array_column($imageTitlesForDayResults, 'title');
		$imageFilenames = array_column($imageTitlesForDayResults, 'filename');
		
		$matchingLocations = $matchingStreets = [];
		
		if ($photoType != 'trains')
		{
			foreach ($streetNames as $word) {
				$word = trim($word);
				if ($word === '') continue;

				foreach ($imageTitles as $title) {
					if (stripos($title, $word . " Street") !== false || stripos($title, $word . " and ") !== false) {
						if (stripos($title, $word . " Street crossover") === false 
							&& stripos($title, "and " . $word) === false) {
							$matchingStreets[] = $word;
						}
					}
				}
			}
		}

		foreach ($locationNames as $word) {
			$word = trim($word);
			if ($word === '') continue;

			foreach ($imageTitles as $title) {
				$pattern = '/\b' . preg_quote($word, '/') . '\b/ui';
				
				if (preg_match($pattern, $title)) {
					if (stripos($title, $word . " line") === false 
						&& stripos($title, $word . " road") === false 
						&& stripos($title, $word . " service") === false 
						&& stripos($title, $word . " tracks") === false  
						&& stripos($title, $word . " and") === false 
						&& stripos($title, $word . " loop") === false 
						&& stripos($title, $word . " train") === false 
						&& stripos($title, $word . " river") === false 
						&& stripos($title, $word . " bus service") === false 
						&& stripos($title, $word . " highway") === false 
						&& stripos($title, $word . " street") === false 
						&& stripos($title, $word . "-") === false 
						&& stripos($title, $word . " Transit") === false 
						&& stripos($title, $word . " Bus Lines") === false 
						&& stripos($title, $word . " rail replacement") === false 
						&& stripos($title, $word . " creek") === false 
						&& stripos($title, $word . " Crescent") === false 
						&& stripos($title, $word . " parade") === false 
						&& stripos($title, $word . " east") === false 
						&& stripos($title, $word . " racecourse") === false 
						&& stripos($title, $word . " glue works") === false 
						&& stripos($title, $word . " bus") === false 
						&& stripos($title, $word . " bound") === false 
						&& stripos($title, $word . " junction") === false 
						&& stripos($title, $word . " route") === false 
						&& stripos($title, $word . " grammar") === false 
						&& stripos($title, $word . " or") === false 
						&& stripos($title, $word . " freight") === false 
						&& stripos($title, $word . " railway") === false 
						&& stripos($title, $word . " air museum") === false 
						&& stripos($title, $word . " builders") === false 
						&& stripos($title, $word . " multiple") === false 
						&& stripos($title, $word . ",") === false 
						&& stripos($title, "poster") === false 
						&& stripos($title, "advertisement") === false 
						&& stripos($title, "down " . $word) === false
						&& stripos($title, "south " . $word) === false
						&& stripos($title, "north " . $word) === false
						&& stripos($title, "middle " . $word) === false
						&& stripos($title, "of " . $word) === false
						&& stripos($title, "or " . $word) === false
						&& stripos($title, "east " . $word) === false
						&& stripos($title, "west " . $word) === false
						&& stripos($title, "from Alstom " . $word) === false
						&& stripos($title, "up " . $word) === false
						&& stripos($title, "for " . $word) === false
						&& stripos($title, " to " . $word) === false
						&& stripos($title, "from " . $word) === false
						&& stripos($title, "and " . $word) === false
						&& stripos($title, "brighton " . $word) === false
						&& stripos($title, "st kilda " . $word) === false
						&& stripos($title, "upper " . $word) === false
						&& stripos($title, "towards " . $word) === false
						&& stripos($title, "steamrail " . $word) === false
						&& stripos($title, "ssr " . $word) === false
						&& stripos($title, "bound for " . $word) === false) {
						
						// skip some locations for trains
						if ($photoType == 'trains' 
							&& ($word == "West Melbourne" 
							|| $word == "Melbourne Airport" 
							|| $word == "Sunshine North" 
							|| $word == "Altona Loop" 
							)) {
						// skip some locations for trains
						} else if ($photoType == 'buses' 
							&& ($word == "North Brighton" 
							|| $word == "Middle Brighton" 
							)) {
						// skip some locations for trams
						} else if ($photoType == 'trams' 
							&& ($word == "Town Hall" 
							)) {
						} else {
							$matchingLocations[] = $word;
						}
						
						
						//echo stripos($title, $word . " service") . (stripos($title, $word . " service") === false) . $word . " $title<BR>";
					}
				}
			}
		}

		// Remove duplicates and reindex
		$matchingLocations = array_values(array_unique($matchingLocations));
		$matchingStreets = array_values(array_unique($matchingStreets));
		
		if (sizeof($matchingLocations) > 0 || sizeof($matchingStreets) > 0)
		{
			if (sizeof($matchingStreets) > 0)
			{
				$streetString = str_replace("La Trobe", "Latrobe", join(" + ", $matchingStreets) . " Street");
				$matchingLocations[] = $streetString;
			}
			
			$foldername = join(" + ", $matchingLocations);
			$folderDate =  str_replace("-", "_", $date);
			$directory = "$folderDate $foldername $photoType";
			$sourceDirectory = "D:\\Photos\\Camera\\$folderDate";
			
			echo "<h2>$directory</h2>";
			echo "<p>" . join("<br>", $imageTitles) . "</p>";
			
			$outputCommands[] = "mkdir \"$sourceDirectory\\$directory\"\n";
			foreach ($imageFilenames as $imageFilename)
			{
				$outputCommands[] = "move \"$sourceDirectory\\$imageFilename\" \"$sourceDirectory\\$directory\"\n";
			}
			echo "</textarea><hr>";
		}
	}
	
	echo "<textarea style=\"width: 1000px; height: 500px;\" onclick=\"this.focus();this.select();\">";
	echo join("", $outputCommands);
	echo "</textarea><hr>";
}
?>
		</div><!-- content -->
	</div><!-- main -->
	<?php printAdminFooter(); ?>
</body>
</html>