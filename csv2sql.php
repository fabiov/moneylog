#!/usr/bin/php
<?php

$options = getopt('i:');
$inputFile  = $options['i'];

if (($handle = fopen($inputFile, "r")) !== FALSE) {
    while (($row = fgetcsv($handle, null, ',')) !== FALSE) {

        $amount = (float) str_replace(',', '.', $row[4]);

        if ($amount) {
            list($d, $m, $y) = explode('/', $row[0]);
            $description = str_replace("'", '', $row[1] . ', ' . $row[2] . ', ' . $row[3]);
            echo "INSERT INTO Movement (`accountId`, `date`, `amount`, `description`) VALUES (76, '$y-$m-$d', $amount, '$description');\n";
        }
    }
    fclose($handle);
}