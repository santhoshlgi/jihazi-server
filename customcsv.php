<?php
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="2019_1.csv"');

$user_CSV[0] = array('slug', 'title', 'canonical_link', 'year', 'season', 'automobile_type' , 'image' , 'tire_size' , 'publication_date' , 'regions');
for ($j=1; $j <=6; $j++) { 
    for ($k=1; $k <= 5; $k++) {
        $ch = curl_init();
        $year = 2015 + $i; 
        $url = "https://api.wheel-size.com/v2/tires/tests/?year=".$year."&page=".$k."&per_page=20&user_key=6fa6881f2e4cf0213305235aac221d75";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        $response = curl_exec($ch);
        $result = json_decode($response,true);
        // echo "<pre>";
        // print_r($result['data']); exit();
        $len = sizeof($result['data']);

        for ($i=1; $i <= $len; $i++) { 
            $user_CSV[] = array(
                $result['data'][$i]['slug'], $result['data'][$i]['title'], $result['data'][$i]['canonical_link'] , $result['data'][$i]['year'],
                $result['data'][$i]['season']['display'], $result['data'][$i]['automobile_type']['display'], $result['data'][$i]['image'] , $result['data'][$i]['tire_size'],
                $result['data'][$i]['publication_date'],$result['data'][$i]['regions'][0]['display']
            );
        }
    }
}

// // very simple to increment with i++ if looping through a database result 
// // $user_CSV[1] = array('Quentin', 'Del Viento', 34);
// // $user_CSV[2] = array('Antoine', 'Del Torro', 55);
// // $user_CSV[3] = array('Arthur', 'Vincente', 15);

$fp = fopen('php://output', 'wb');
foreach ($user_CSV as $line) {
    // though CSV stands for "comma separated value"
    // in many countries (including France) separator is ";"
    fputcsv($fp, $line, ',');
}
fclose($fp);

// echo "<pre>";
// print_r($result['data']);

?>