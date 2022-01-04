<?php
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="adavcefilterdatas.csv"');

$user_CSV[0] = array('slug', 'title', 'brand' ,'canonical_link','season', 'automobile_type' , 'performance_category' , 'year', 'discontinued' , 'almost_discontinued' , 'coming_soon' , 'is_runflat' , 'studded',
'for_nordic_winter','is_oe_model',  'image' ,'manufacturer_page_link','description','tags', 'counters/modes' , 'counters/videos' , 'counters/benchmarks','regions', 'sizing_system','text','load_index', 'dual_load_index',
'speed_index' ,'extra_load','mud_and_snow','rim_protection','tire_width','aspect_ratio','rim_diameter');

if (($open = fopen("chkscsv.csv", "r")) !== FALSE) 
{

  while (($data = fgetcsv($open, 1000, ",")) !== FALSE) 
  {        
    $array[] = $data; 
  }

  fclose($open);
}
$len = sizeof($array);
// echo "<pre>";
// //To display array data
// var_dump($array);
// echo "</pre>"; exit();
for ($i=1; $i <=$len; $i++) { 
    $ch = curl_init();
    $surl = "https://api.wheel-size.com/v2/tires/catalog/".$array[$i][1]."/".$array[$i][0]."/?user_key=6fa6881f2e4cf0213305235aac221d75";
    curl_setopt($ch, CURLOPT_URL,$surl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    $response = curl_exec($ch);
    $result = json_decode($response,true);
    // print_r($result['data']['for_nordic_winter']); exit();
    $ch1 = curl_init();
    $surl1 = "https://api.wheel-size.com/v2/tires/catalog/".$array[$i][1]."/".$array[$i][0]."/modes/?user_key=6fa6881f2e4cf0213305235aac221d75";
    // echo $surl1; exit();
    curl_setopt($ch1, CURLOPT_URL,$surl1);
    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "GET");
    $response1 = curl_exec($ch1);
    $result1 = json_decode($response1,true);
    $lens = sizeof($result1['data']);
    // echo $lens;
    for ($j=0; $j < $lens; $j++) { 
        $lenzs = sizeof($result['data']['regions']);
        $rsd = array();
        // echo $lenzs;
        for ($c=0; $c < $lenzs; $c++) { 
            $rsd[] = $result['data']['regions'][$c]['display'];
        }
        // print_r($rsd); 
        // exit();
        // if($result1['data'][$j]['text'] == $array[$i][2]){
            // $datas = $result1['data'][$j];
            $user_CSV[] = array(
                $result['data']['slug'], $result['data']['display'], $result['data']['brand']['display'] , $result['data']['canonical_link'],
                $result['data']['season']['display'], $result['data']['automobile_type']['display'],$result['data']['performance_category']['display'],
                $result['data']['year'], $result['data']['discontinued'], $result['data']['almost_discontinued'], $result['data']['coming_soon'],
                $result['data']['is_runflat'], $result['data']['studded'], $result['data']['for_nordic_winter'], $result['data']['is_oe_model'],
                $result['data']['image'] , $result['data']['manufacturer_page_link'],
                $result['data']['description'],implode(",",$result['data']['tags']),
                $result['data']['counters']['modes'], $result['data']['counters']['videos'], $result['data']['counters']['benchmarks'],
                implode(",",$rsd),$result1['data'][$j]['sizing_system'],$result1['data'][$j]['text'],$result1['data'][$j]['load_index'],
                $result1['data'][$j]['dual_load_index'],
                $result1['data'][$j]['speed_index'],$result1['data'][$j]['extra_load'],$result1['data'][$j]['mud_and_snow'],
                $result1['data'][$j]['rim_protection'],
                $result1['data'][$j]['tire_width'],$result1['data'][$j]['aspect_ratio'],$result1['data'][$j]['rim_diameter']
            );
        // }
    }
    // echo "<pre>";
    // print_r($datas);
    // exit();
}

$fp = fopen('php://output', 'wb');
foreach ($user_CSV as $line) {
    // though CSV stands for "comma separated value"
    // in many countries (including France) separator is ";"
    fputcsv($fp, $line, ',');
}
fclose($fp);
// echo "<pre>";
// //To display array data
// var_dump($array);
// echo "</pre>";
        // $ch = curl_init();
        // $surl = "https://api.wheel-size.com/v2/tires/catalog/falken/sincera-sn832-ecorun/?user_key=6fa6881f2e4cf0213305235aac221d75";
        // curl_setopt($ch, CURLOPT_URL,$surl);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        // // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        // $response = curl_exec($ch);
        // $result = json_decode($response,true);
        // print_r($result);
        // echo "hello";