<?php

//generate IDrumah
add_action('acf/save_post', 'update_homeID');
function update_homeID($post_id) {
    $characters ='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $length = 3;
    $key_rumah = '';
    for( $i = 0; $i < $length; $i++ ) {
        $key_start .= substr( $characters , wp_rand( 0, strlen( $characters ) - 1 ), 1 );
        $key_end .= substr( $characters , wp_rand( 0, strlen( $characters ) - 1 ), 1 );
    }
    $title = str_replace("-", "", get_the_title($post_id));

    $homeID = get_field('field_63222fff6bb1d', $post_id);

    if (get_post_type($post_id) =='rumah') {
        if (empty($homeID)) {
            update_field('field_63222fff6bb1d',$key_start.$title.$key_end , $post_id);
        }
    }

    ////////////////////

    $s = 'December 2022';
    $d = strtotime($s);
   // $date = date('F Y', $d);
    $date = '';


    if ($date) {
        //update ipl terbaru
        update_field('status_ipl', array('ipl_terbaru'=>$date), $post_id);

        //iplbaru
        $iplbaru = get_field('status_ipl',$post_id)['ipl_terbaru'];
        $iplbaruarr = explode(", ", $iplbaru);

        //list ipl
        $ipl = get_field('status_ipl', $post_id)['ipl_terbayar'];
        $iplarr = explode("<br />",$ipl);

        $ipltemp = [];
        foreach ($iplarr as $val) {
            array_push($ipltemp, $val);
        }

        $iplupdate = [];
        foreach ($iplbaruarr as $val) {
            array_push($iplupdate, $val);
            array_push($ipltemp, $val);
        }

        //masukan ipl baru ke list ipl
        foreach ($iplarr as $val) {
            array_push($iplupdate, $val);
        }

        $value = implode("\n",$ipltemp);
        update_field('status_ipl', array('ipl_terbayar'=>$value), $post_id);
    }

    $iplbaru = get_field('status_ipl',$post_id)['ipl_terbaru'];
    $dlast = strtotime($iplbaru);
    $datelast = date('F Y', $dlast);


    $thisMouth = date('F Y');

    //list bulan belum bayar
    $start    = new DateTime($datelast);
    $start->modify('first day of next month');
    $end      = new DateTime($thisMouth);
    $end->modify('first day of next month');
    $interval = new DateInterval('P1M');
    $period   = new DatePeriod($start, $interval, $end);

    $per = [];
    $i= 0;
    foreach ($period as $dt) {
        $i++;
        //echo $dt->format("Y-m") . "<br>\n";
        array_push($per, $dt->format("F Y"));
    }

    $listbln = implode("\n",$per);
    update_field('status_ipl', array('ipl_belum_bayar'=>$listbln), $post_id);

    //num ipl lum bayar
    update_field('status_ipl', array('jumlah_ipl_belum_bayar'=>$i), $post_id);

}
