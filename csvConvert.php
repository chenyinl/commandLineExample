<?php
/*
 * usage: csvConvert.php [file name] [row limit]
 * A tool to generate CSV from an Exported CSV
 * 2014/7/29
 */

// check if the file exists
if( !is_file( $argv[1] )){
    echo "Faile to open file\n";
    exit();
}

$filename = $argv[1];

// for test only
$maxrow = 10000;
if( isset( $argv[2] )){
    $maxrow = $argv[2];
}

// try to open file to read
try{
    if (($handle = fopen($filename, "r")) === FALSE) {
        echo "Faile to open file\n";
        exit();
    }
} catch(Exception $e){
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

// remove the first lin
$rowData = fgetcsv($handle, 1000, "\t"); //remove first row

// line counting
$count=0;

// open file to write
$output = fopen("mailchimp.".$argv[1], "w");

// write out header
fputcsv( $output, array("email", "first_name", "last_name"));


while (( $tempData = fgetcsv($handle, 1000, "\t")) !== FALSE && 
    $count++ < $maxrow) {
    list(
        $id,  
        $email, 
        $attributes
    ) = $tempData;
    
    // skip row that misses eamil
    if( $email == "NULL") continue;

    // get data from attribute
    $attributes = json_decode($attributes);
    $first_name = $attributes->first_name;
    $last_name = $attributes->last_name;
    echo $email.", ".$first_name.", ".$last_name."\n";
    fputcsv( $output, array($email, $first_name, $last_name));
}
// close output CSV file
fclose($output);

// close source CSV file
fclose($handle);
