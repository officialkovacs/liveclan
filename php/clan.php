<?php
    $json = [];
    $data = ['request'=>'server_time', 'user_id'=>'69'];
    $ch = curl_init('http://app.ninjasaga.com/fb_oauth_2.0/ajax.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $json['server_time'] = strtoupper(curl_exec($ch));
    $json['ranking'] = array();
    curl_close($ch);
    $html = file_get_contents('http://ninjasaga.com/game-info/all_clan.php');
    preg_match_all('/<tr.*?>(.*?)<\/tr>/si', $html, $matches);
    $matches[0] = preg_replace('/<tr.*?>/',  '<tr>', $matches[0]);
    $matches[0] = preg_replace('/<td.*?>/',  '<td>', $matches[0]);
    $matches[0] = preg_replace('/<a.*?>/',  '<a>', $matches[0]);
    preg_match_all('/<td.*?>(.*?)<\/td>/si', $matches[0][5], $champion);
    $champion = filter_var($champion[0][4], FILTER_SANITIZE_NUMBER_INT);
    for($i=5; $i<30; $i++){
        $clan = [];
        preg_match_all('/<td.*?>(.*?)<\/td>/si', $matches[0][$i], $cur_rank);
        $reputation = filter_var($cur_rank[0][4], FILTER_SANITIZE_NUMBER_INT);
        $clan['rank'] = str_replace(['<tr>','<td>','<a>','</tr>','</td>','</a>'], '', $cur_rank[0][0]);
        $clan['clan'] = str_replace(['<tr>','<td>','<a>','</tr>','</td>','</a>'], '', $cur_rank[0][1]);
        $clan['master'] = str_replace(['<tr>','<td>','<a>','</tr>','</td>','</a>'], '', $cur_rank[0][2]);
        $clan['member'] = str_replace(['<tr>','<td>','<a>','</tr>','</td>','</a>'], '', $cur_rank[0][3]);
        $clan['reputation'] = str_replace(['<tr>','<td>','<a>','</tr>','</td>','</a>'], '', $cur_rank[0][4]);
        if($champion > $reputation){
            $gap = $champion - $reputation;
        }
        else{
            $gap = 'Champion';
        }
        $clan['gap'] = $gap;
        array_push($json['ranking'], $clan);
    }
    $json = json_encode($json);
    $file = fopen('../json/clan.json', 'w+');
    fwrite($file, $json);
    fclose($file);
?>