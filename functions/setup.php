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

        $s = 'January 2022';
        $d = strtotime($s);
        //$date = date('F Y', $d);
        $date = '';


        if ($date) {
            //update ipl terbaru
            update_field('field_63237a73847e9', array('field_6324294c3758b'=>$date), $post_id);
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
            update_field('field_63237a73847e9', array('field_6324117f847ea'=>$value), $post_id);
        }

        $iplbaru = get_field('status_ipl',$post_id)['ipl_terbaru'];
        $dlast = strtotime($iplbaru);
        $datelast = date('F Y', $dlast);


//        //ipl belum bayar
//        $s = 'December 2022';
//        $d = strtotime($s);
//        $date = date('F Y', $d);

        $thisMouth = date('F Y');

//        $date1=date_create($datelast);
//        $date2=date_create($thisMouth);
//        $diff =date_diff($date1,$date2);

        //update_field('field_63237a73847e9', array('field_63243d2624597'=>$diff->format("%m")), $post_id);

//
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
        update_field('field_63237a73847e9', array('field_63243d3a24598'=>$listbln), $post_id);

        //num ipl lum bayar
        update_field('field_63237a73847e9', array('field_63243d2624597'=>$i), $post_id);




       // $valu2 = implode("\n",$ipltemp);
       // update_field('field_63237a73847e9', array('field_632438ee92493'=>$valu2 ), $post_id);


//        $ipl2 = explode('<br />', $ipl);
//        //$ipl2 = explode('\n', $ipl2);
//        $ipllam = '';
//
//        //update_field('field_63237a73847e9', array('field_6324294c3758b'=>$ipl2), $post_id);
//        $string2= 'Januari 2022, February 2022';
//        $my_array1 = explode(", ", $string2);
//        $skillset= array(
//            'JavaScript',
//            'Python',
//            'February 2022',
//        );
//
//        foreach ($my_array1 as $val) {
//            $ipllam .=$val;
//            array_push($skillset, $val);
//        }
//
//        //array_push($skillset, $ipllam);
//        $value = implode("\n",$skillset);
//        $valu2 = implode(", ",$skillset);
//        update_field('field_63237a73847e9', array('field_6324117f847ea'=>$value), $post_id);
//        update_field('field_63237a73847e9', array('field_6324294c3758b'=>$valu2), $post_id);
    }

}

//function my_acf_load_field( $field ) {
//    //$field['required'] = true;
//
//    $field['choices'] = array(
//        'custom_4'  => 'My Custom Choice 4',
//        'custom_3'  => 'My Custom Choice 3',
//        'custom_2'  => 'My Custom Choice 2',
//        'custom'    => 'My Custom Choice',
//    );
//    //array_push( $field['choices'], 'My Custom Choice 5','My Custom Choice 6');
//    return $field;
//}
//
//add_filter('acf/load_field/key=field_6324117f847ea', 'my_acf_load_field');
