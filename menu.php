<html>
<pre>

<?

include("config.php");

function read_dir($path){
	$handle=opendir($path);
	while ($file = readdir($handle)) {           
		if ($file != "." && $file != ".." && is_dir($path.$file)) {
			$sub_dir = $path . $file . "/" ;
			echo '<a href="index.php?ddir=' . $sub_dir . '">' . $sub_dir . '</a><br>';
			read_dir($sub_dir);
			}
		}
	return $dir_arr ;
}

echo ('Root dir: <a href="index.php?ddir=' . $root_dir . '">' . $root_dir . '</a><br><br>');
read_dir("$root_dir");

?>

</pre>
</html>
