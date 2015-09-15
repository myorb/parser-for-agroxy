<?php


$file = 'items.txt';
$items = array();
$handle = fopen($file, "r");
if ($handle) {
    $i = 1;
    while (($i<3) && (($line = fgets($handle)) !== false)) {
        $items[] = $line;

        // process the line read.
        $i++;
    }

    fclose($handle);
} else {
    // error opening the file.
} 
// var_dump($items);
foreach ($items as $key => $value) {
	$item = json_decode($value);
	// var_dump(json_decode($value));
var_dump($item->title);
	# code...
}
// var_dump(json_decode($items));


?>