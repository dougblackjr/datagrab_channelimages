<?php

$basedir = 'files';
// Set grid folder and filename ending
$gridFolder = $basedir.'/'.'_grid';
$gridEnding = '__grid';

// Set hero folder and filename ending
$heroFolder = $basedir.'/'.'_hero';
$heroEnding = '__hero';

// Set thumbs folder and filename ending
$thumbFolder = $basedir.'/'.'_thumbs';
$thumbEnding = '';

// Set starting and ending numbers for fold
$startno = 359;
$endno = 785;

// For each numbered folder
for ($i = $startno; $i <= $endno; $i++) {
	// Open numbered folder
	if ($handle = opendir($basedir.'/'.$i)) {
    	while (false !== ($entry = readdir($handle))) {
        	if ($entry != "." && $entry != "..") {
            	// Find file name
            	// echo $entry;
				// Check in grid folder
				if(is_file($gridFolder.'/'.$entry)) {
					// Parse file name
					$path_parts = pathinfo($entry);
					$grid_file_name = $path_parts['filename'].$gridEnding.'.'.$path_parts['extension'];
					// Move file to numbered folder and rename
					$move_file = file_get_contents($gridFolder.'/'.$entry);
					file_put_contents($basedir.'/'.$i.'/'.$grid_file_name, $move_file);
				}
				// Check in hero folder
				if(is_file($heroFolder.'/'.$entry)) {
					// Parse file name
					$path_parts = pathinfo($entry);
					$hero_file_name = $path_parts['filename'].$heroEnding.'.'.$path_parts['extension'];
					// Move file to numbered folder and rename
					$move_file = file_get_contents($heroFolder.'/'.$entry);
					file_put_contents($basedir.'/'.$i.'/'.$hero_file_name, $move_file);
				}
				// Check in thumbs folder
				// Parse file name
				// Move file to numbered folder and rename
        	}
    	}
    	closedir($handle);
	}
}

// Echo done
echo 'done';