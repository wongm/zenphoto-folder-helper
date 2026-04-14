<?php

/*

Output list of directory names and title all albums currently on gallery

*/

define('OFFSET_PATH', 3);
require_once(dirname(dirname(dirname(__FILE__))) . '/zp-core/admin-globals.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/zp-core/template-functions.php');
admin_securityChecks(ALBUM_RIGHTS, currentRelativeURL());
echo '</head>';
?>
<body>
	<?php printLogoAndLinks(); ?>
	<div id="main">
		<?php printTabs(); ?>
		<div id="content">
		<h1>All album info</h1>
<?php

$albumQuery = "SELECT folder, title as albumtitle
	FROM " . $_zp_db->prefix('albums') . "
	WHERE folder NOT LIKE '%.alb'";
	
$albums = array_values($_zp_db->queryFullArray($albumQuery));

printAdminHeader('overview', gettext('All album info'));

echo "<textarea style=\"width: 800px; height: 400px;\" onclick=\"this.focus();this.select();\">";
foreach ($albums as $key => $value)
{
	$title = $value['folder'] . ", \"" . get_language_string($value['albumtitle']) . "\"";
	echo $title . "\n";
}
echo "</textarea>";
?>
		</div><!-- content -->
	</div><!-- main -->
	<?php printAdminFooter(); ?>
</body>
</html>