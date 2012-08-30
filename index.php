<html>
<head>
<META HTTP-EQUIV="refresh" CONTENT="3000; url=">
</head>
<body>
<?
/**
	Kismet Log Files Parser

README:
The script will read all csv files in directory, parse them and 
make HTML output. This piece of software IS VERY bugyy so use it just
for your own needs. 

If you find any bug please contact me by mail (maco_at_maco.sk) and I will
try to debug the code ASAP. If you think of any other feature which is not already implemented, 
drop me some lines and the same as above - I'll try to add the feature ASAP.

watch http://www.maco.sk/ for updates

INSTALLATION:
- unpack
- point $dir var to the .csv Kismet log file
- directory HAS TO BE READABLE AND EXECUTABLE for http server +
	the same for the files - it is recomended to copy log files to 'data' directory
	under kismet2html directory
- done

- you can rewrite $dir variable in URL by settinf $ddir variable such as:

http://www.maco.sk/kismet2html/index.php?ddir=/var/log/kismet/

**/

//the only variable to edit ($dir) WITH leading slash
$dir = "../data/";


//don't edit below
//=============================================================
if (isset($ddir)) {
	$dir = $ddir;
};

$ext = "csv";
 
$handle = opendir("$dir");
while (false !== ($file = readdir($handle)))
if ($file != "." && $file != ".." && eregi("\.". $ext ."$", $file)) {

	$lines = file($dir.$file);
	foreach ($lines as $line_num => $line) {
		$ap_info = explode(";",$line);
		
		$net_type = $ap_info[1];
		$essid = $ap_info[2];
                $bssid = $ap_info[3];
		$chan = $ap_info[5];
		$wep = $ap_info[7];
		$last_seen = $ap_info[20];

		if ($chan == '0') {
			$chan = 'unknown';
		};
	
		$wk_dir = htmlentities($dir);
		$file = htmlentities($file);
		$essid = htmlentities($essid);
		$bssid = htmlentities($bssid);
		$net_type = htmlentities($net_type);
		$wep = htmlentities($wep);


		if (($bssid != '') and ($bssid != 'BSSID') and (!strstr($essid,'DTTP'))) {
			//keep this first for correst sorting	
			$sta[$essid][essid] = $essid;

			$sta[$essid][dir] = $wk_dir;
			$sta[$essid][file] = $file;
			$sta[$essid][net_type] = $net_type;
			$sta[$essid][wep] = $wep;
			
			$sta[$essid][last_seen] = $last_seen;

			if (!strstr($sta[$essid][chan], $chan)) {
				$sta[$essid][chan] = "$chan, " . $sta[$essid][chan];
			};

			if ($chan == '14') {
				if (!strstr($haluzak, $bssid)) {
					$haluzak = "$bssid, " . $haluzak;
				};
			};

			if (!strstr($sta[$essid][bssid], $bssid)) {
				$sta[$essid][bssid] = "$bssid<br>" . $sta[$essid][bssid];
			};
		};
	};
		 
};


$all = count($sta);
asort($sta);
reset($sta);
//print_r($sta);

$font = '<font face="verdana" size="1">';
$date = date("Y-M-d");
echo '<h3><font face="verdana">Kismet logs</font></h3>';
echo "$font 	Generated on $date with kismet2html PHP script 
			<a href=\"http://www.maco.sk\">http://www.maco.sk</a> by Marcel Hecko<br>
		Directory with log files: $dir<br>
		Total unique SSIDs: $all<br>
		<br>";
echo '	<table bgcolor="white" border="0" cellspacing="1" cellpadding="0">
	' . 	"<tr>
			<td bgcolor=red>$font #</td>
			<td bgcolor=red>$font BSSID(mac)</td>
			<td bgcolor=red>$font ESSID</td>
			<td bgcolor=red>$font channels</td>
			<td bgcolor=red>$font file/last seen/directory</td>
			<td bgcolor=red>$font net type</td>
			<td bgcolor=red>$font wep?</td>
		</tr>";
$num = 1;
foreach ($sta as $w) {
	$chans = $chans . $w[chan];
	if ($w[wep] == 'No') { 
		$wep = 'bgcolor="lightgreen"';
	} else {
		$wep = 'bgcolor="orange"';
	};
	echo "<tr>
		  <td $wep>$font $num</td>
		  <td $wep>$font $w[bssid]</td>
		  <td $wep>$font $w[essid]</td>
		  <td $wep>$font $w[chan]</td>
		  <td $wep>$font $w[file]<br>$w[last_seen]<br>$w[dir]</td>
		  <td $wep>$font $w[net_type]</td>
		  <td $wep>$font Wep: $w[wep]</td>
	     </tr>";
	$num++;
};
echo "</table>$font";

$chan = explode(", ", $chans);
$chan = array_count_values($chan);
ksort($chan);
reset($chan);
//print_r($chan);
$chan_val = -2;
echo "<br><br>Haluzaci (BSSIDs of stations on the 14th channel): $haluzak";
echo "<br>Channel usage: (chan/num) (conuting per SSID, not BSSID!!)<br>";
foreach ($chan as $ch) {
	$chan_val++;
	if ($chan_val >= "1") { 
		echo("<table><tr><td bgcolor=\"black\" width=\"" . $ch*3 . "\">
			<font size=\"2\" color=\"#ffffff\">$chan_val/$ch</font></td></tr></table>");
	};
};
?>
</body>
</html>
