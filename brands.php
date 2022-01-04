<?php
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="adavcefilterdata.csv"');

$user_CSV[0] = array('slug', 'title', 'brand' ,'canonical_link','season', 'automobile_type' , 'year',  'image' , 'counters/modes' , 'counters/videos' , 'counters/benchmarks' , 'tire_size','len');

for ($k=1; $k <= 4; $k++) {
    $ch = curl_init();
        $surl = "https://api.wheel-size.com/v2/tires/search/advanced/?t=".$k."&per_page=20&user_key=6fa6881f2e4cf0213305235aac221d75";
        curl_setopt($ch, CURLOPT_URL,$surl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        $response = curl_exec($ch);
        $result = json_decode($response,true);
        $len = $result['meta']['pagination']['total_pages'];
        print_r($len); exit();
    for ($j=1; $j <= $len; $j++) { 
        $ch = curl_init();
        $url = "https://api.wheel-size.com/v2/tires/search/advanced/?t=".$k."&page=".$j."&per_page=20&user_key=6fa6881f2e4cf0213305235aac221d75";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        $response = curl_exec($ch);
        $result = json_decode($response,true);
        // echo "<pre>";
        // print_r($result['data']); die();
        // if(isset($result['data'])){
            $len = sizeof($result['data']);
    
            for ($i=0; $i < $len; $i++) { 
                $user_CSV[] = array(
                    $result['data'][$i]['slug'], $result['data'][$i]['display'], $result['data'][$i]['brand']['display'] , $result['data'][$i]['canonical_link'],
                    $result['data'][$i]['season']['display'], $result['data'][$i]['automobile_type']['display'],$result['data'][$i]['year'], $result['data'][$i]['thumbnail'] ,
                    $result['data'][$i]['counters']['modes'], $result['data'][$i]['counters']['videos'], $result['data'][$i]['counters']['benchmarks'],
                    // $lesd = sizeof($result['data'][$i]['has_modes'][1]);
                    // for ($z=0; $z < $lesd; $z++) { 
                    //    $store_size= 
                    // }
                    implode(",",$result['data'][$i]['has_modes'][$k]),$j
                );
            }
        if ($len == 20) {
                for ($t=21; $t <= $len; $t++) { 
                    $ch = curl_init();
                    $url = "https://api.wheel-size.com/v2/tires/search/advanced/?t=".$k."&page=".$t."&per_page=20&user_key=6fa6881f2e4cf0213305235aac221d75";
                    curl_setopt($ch, CURLOPT_URL,$url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                    // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
                    $response = curl_exec($ch);
                    $result = json_decode($response,true);
                    // echo "<pre>";
                    // print_r($result['data']); die();
                    // if(isset($result['data'])){
                        $len = sizeof($result['data']);
                
                        for ($i=0; $i < $len; $i++) { 
                            $user_CSV[] = array(
                                $result['data'][$i]['slug'], $result['data'][$i]['display'], $result['data'][$i]['brand']['display'] , $result['data'][$i]['canonical_link'],
                                $result['data'][$i]['season']['display'], $result['data'][$i]['automobile_type']['display'],$result['data'][$i]['year'], $result['data'][$i]['thumbnail'] ,
                                $result['data'][$i]['counters']['modes'], $result['data'][$i]['counters']['videos'], $result['data'][$i]['counters']['benchmarks'],
                                // $lesd = sizeof($result['data'][$i]['has_modes'][1]);
                                // for ($z=0; $z < $lesd; $z++) { 
                                //    $store_size= 
                                // }
                                implode(",",$result['data'][$i]['has_modes'][$k]),$t
                            );
                        }  
                }
        }
        // } 
    }    
}
$fp = fopen('php://output', 'wb');
foreach ($user_CSV as $line) {
    // though CSV stands for "comma separated value"
    // in many countries (including France) separator is ";"
    fputcsv($fp, $line, ',');
}
fclose($fp);
// // very simple to increment with i++ if looping through a database result 
// // $user_CSV[1] = array('Quentin', 'Del Viento', 34);
// // $user_CSV[2] = array('Antoine', 'Del Torro', 55);
// // $user_CSV[3] = array('Arthur', 'Vincente', 15);



// echo "<pre>";
// print_r($result['data']);

?>