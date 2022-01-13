<?php
$btoken = "Authorization: Bearer x";

$searches = array(
    'nft',
    '.nft',
    'btc',
    '.btc',
    'eth',
    '.eth',
    'crypto'
);
$words = array(
    ' nft',
    ' NFT',
    'NFT ',
    'nft ',
    '.eth',
    '.ETH',
    'crpyto'
);

// --------

$foundct = 0;
$notct = 0;
$lista = "";
$cURL = curl_init();
curl_setopt($cURL, CURLOPT_HTTPGET, true);

curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Accept: application/json',
    $btoken
));

curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($cURL, CURLOPT_RETURNTRANSFER, TRUE);
global $pagenumber;
$pagenumber = 1;
foreach ($searches as $search) {
    echo $search;
    echo "\n";
    do {
        $query = "https://api.twitter.com/1.1/users/search.json?count=20&q=" . $search . "&page=" . $pagenumber;
        echo "page " . $pagenumber;
        echo "\n";

        curl_setopt($cURL, CURLOPT_URL, $query);

        $resp = curl_exec($cURL);

        $resp = json_decode($resp, TRUE);
        foreach ($resp as $user) {
            $found = 0;
            foreach ($words as $word) {
                if (strpos($user['name'], $word)) {
                    $found = 1;
                } else if (strpos($user['screen_name'], $word)) {
                    $found = 1;
                }
            }
            if ($found = 1) {
                echo "found screen name: " . $user['screen_name'];
                $lista .= $user['screen_name'] . "\n";
                echo "\n";
                $foundct++;
            } else {
                echo "ignoring user: " . $user['screen_name'];
                echo "\n";
                $notct++;
            }
        }

        //sleep(1);

        file_put_contents('list.txt', $lista, FILE_APPEND);

        $pagenumber++;
    } while ($pagenumber <= 51);
    $pagenumber = 1;
}

echo "Found " . $foundct;
echo "\n";
echo "Not found " . $notct;
echo "\n";

curl_close($cURL);

