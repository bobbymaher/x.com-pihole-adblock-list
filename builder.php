<?php



$adblockIP = '0.0.0.0';
$rawData = file_get_contents('raw-data.txt');

function getDomain($url) {
    $parts = explode('.', $url);
    $count = count($parts);
    
    // Handle cases like .co.uk, .com.au
    if ($count > 2 && in_array($parts[$count-2], ['co', 'com', 'org', 'net', 'edu', 'gov'])) {
        return implode('.', array_slice($parts, -3));
    }
    
    // Standard cases
    return implode('.', array_slice($parts, -2));
}

function sortDomains($domains) {
    $grouped = [];
    
    foreach ($domains as $domain) {
        $mainDomain = getDomain($domain);
        $grouped[$mainDomain][] = $domain;
    }
    
    // Sort main domains
    ksort($grouped);
    
    // Sort subdomains within each group
    foreach ($grouped as &$group) {
        sort($group);
    }
    
    $sorted = [];
    foreach ($grouped as $group) {
        $sorted = array_merge($sorted, $group);
    }
    
    return $sorted;
}


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
    if(!in_array($rawLine, $domains)){
        $domains[] = $rawLine;
    }
}

$sortedDomains = sortDomains($domains);
//sortDomains creates duplicates, as it reduces sub sub domains
$sortedDomains = array_unique($sortedDomains);

$writeFileContent = '';
foreach($sortedDomains as $domain){
    $writeFileContent.= $adblockIP . ' ' . $domain . PHP_EOL;
}
echo count($sortedDomains) . ' domains added to list' . PHP_EOL;
file_put_contents('list.txt', $writeFileContent);