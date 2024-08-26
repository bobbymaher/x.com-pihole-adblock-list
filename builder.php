<?php

$adblockIP = '0.0.0.0';

$rawData = file_get_contents('raw-data.txt');
$rawLines = explode("\n", $rawData);

$domains = [];

foreach($rawLines as $rawLine){

    $rawLine = trim($rawLine);
    if(empty($rawLine)){
        continue;
    }

    //some lines can be IP - hostname
    if(str_contains($rawLine, ' - ')){
        $parts = explode(' - ', $rawLine);
        $rawLine = $parts[1];
    }

    $domains[] = $rawLine;
}

$domains = array_unique($domains);
$writeFileContent = '';
foreach($domains as $domain){
    $writeFileContent.= $adblockIP . ' ' . $domain . PHP_EOL;
}
echo count($domains) . ' domains added to list' . PHP_EOL;
file_put_contents('list.txt', $writeFileContent);