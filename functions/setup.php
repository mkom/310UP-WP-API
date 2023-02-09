<?php

//generate IDrumah
add_action('acf/save_post', 'update_homeID');
function update_homeID($post_id) {

    //set kode rumah
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

    //set kode iuran

    $iuranID = get_field('field_6340883b5484c', $post_id);
    if (get_post_type($post_id) =='iuran') {
        if (empty($iuranID)) {
            update_field('field_6340883b5484c',$key_start.'IU'.$key_end , $post_id);
        }
    }

    ////////////////////

    $s = 'December 2022';
    $d = strtotime($s);
   // $date = date('F Y', $d);
    $date = '';

    // function untuk ipl terbayar
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

    // fungtion update ipl
    $lastIPLB = get_field('status_iuran',$post_id)['bulan'];
    $lastIPLT = get_field('status_iuran',$post_id)['tahun'];
    $lastIPL = $lastIPLB.' '.$lastIPLT;
    $dlast = strtotime($lastIPL);
    $datelast = date('F Y', $dlast);

    //update ipl terbaru
    //update_field('status_ipl', array('ipl_terbaru'=>$datelast), $post_id);

    $iplbaru = $lastIPL;
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
        array_push($per, $dt->format("F | Y"));

    }

    $listbln = implode("\n",$per);

    update_field('status_ipl', array('ipl_belum_bayar'=>$listbln), $post_id);

    //num ipl lum bayar
    update_field('status_iuran', array('jumlah_ipl_belum_bayar'=>$i), $post_id);

}

//autoupdate data IPL

//function run_update_ipl() {
//
//
//    $args = array(
//    'post_type' => 'rumah',
//    'posts_per_page' => -1,
//    'post_status' => 'publish',
//
//    );
//
//    $posts = get_posts($args);
//
//    //now check meta and update taxonomy for every post
//    foreach ( $posts as $post ) {
//        $post_id = $post->ID;
//        $iplbaru = get_field('status_ipl',$post_id)['ipl_terbaru'];
//        $dlast = strtotime($iplbaru);
//        $datelast = date('F Y', $dlast);
//
//        $thisMouth = date('F Y');
//
//        //list bulan belum bayar
//        $start    = new DateTime($datelast);
//        $start->modify('first day of next month');
//        $end      = new DateTime($thisMouth);
//        $end->modify('first day of next month');
//        $interval = new DateInterval('P1M');
//        $period   = new DatePeriod($start, $interval, $end);
//
//        $thisMouth = date('F Y');
//
//        //list bulan belum bayar
//        $start    = new DateTime($datelast);
//        $start->modify('first day of next month');
//        $end      = new DateTime($thisMouth);
//        $end->modify('first day of next month');
//        $interval = new DateInterval('P1M');
//        $period   = new DatePeriod($start, $interval, $end);
//
//        $per = [];
//        $i= 0;
//        foreach ($period as $dt) {
//            $i++;
//            //echo $dt->format("Y-m") . "<br>\n";
//            array_push($per, $dt->format("F Y"));
//        }
//
//        $listbln = implode("\n",$per);
//        update_field('status_ipl', array('ipl_belum_bayar'=>$listbln), $post_id);
//
//        //num ipl lum bayar
//        update_field('status_ipl', array('jumlah_ipl_belum_bayar'=>$i), $post_id);
//    }
//}

function run_update_ipl() {
    $args = array(
    'post_type' => 'user-iuran',
    'posts_per_page' => -1,
    'post_status' => 'publish',

    );

    $posts = get_posts($args);

    //now check meta and update taxonomy for every post
    foreach ( $posts as $post ) {
        $post_id = $post->ID;

        $lastIPLB = get_field('status_iuran',$post_id)['bulan'];
        $lastIPLT = get_field('status_iuran',$post_id)['tahun'];
        $lastIPL = $lastIPLB.' '.$lastIPLT;

        $dlast = strtotime($lastIPL);
        $datelast = date('F Y', $dlast);

        $thisMouth = date('F Y');

        //list bulan belum bayar
        $start    = new DateTime($datelast);
        $start->modify('first day of next month');
        $end      = new DateTime($thisMouth);
        $end->modify('first day of next month');
        $interval = new DateInterval('P1M');
        $period   = new DatePeriod($start, $interval, $end);

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
            array_push($per, $dt->format("F | Y"));
        }

        $listbln = implode("\n",$per);
        update_field('status_ipl', array('ipl_belum_bayar'=>$listbln), $post_id);

        //num ipl lum bayar
        update_field('status_iuran', array('jumlah_ipl_belum_bayar'=>$i), $post_id);
    }
}

add_action ('cronjobIPL', 'run_update_ipl');

// add custom interval
function cron_add_minute( $schedules ) {
    // Adds once every minute to the existing schedules.
    $schedules['everyminute'] = array(
        'interval' => 60,
        'display' => __( 'Once Every Minute' )
    );
    return $schedules;
}
add_filter( 'cron_schedules', 'cron_add_minute' );

// create a scheduled event (if it does not exist already)
function cronstarter_activation() {
    if( !wp_next_scheduled( 'cronjobIPL' ) ) {
        wp_schedule_event( time(), 'everyminute', 'cronjobIPL' );
    }
}
// and make sure it's called whenever WordPress loads
add_action('init', 'cronstarter_activation');

add_filter( 'manage_transaksiv2_posts_columns', 'set_custom_edit_transaksiv2_columns' );
add_action( 'manage_transaksiv2_posts_custom_column' , 'custom_transaksiv2_column', 10, 2 );

//function set_custom_edit_transaksiv2_columns( $cols ) {
//    $cols = array(
//        'title'      => 'Title',
//        'iuran' => 'Iuran',
//        'total' => 'Total',
//        'status' => 'Status',
//        'bulan' => 'Bulan',
//        'tahun' => 'Tahun',
//        'date'       => 'Release Date'
//    );
//    return $cols;
//}

function set_custom_edit_transaksiv2_columns($columns) {
    //unset( $columns['title'] );
    $columns['iuran'] = __( 'Iuran', 'your_text_domain' );
    $columns['status'] = __( 'Status', 'your_text_domain' );
    $columns['total'] = __( 'Total', 'your_text_domain' );
    //$columns['date'] = __( 'Release', 'your_text_domain' );

    return $columns;
}

function custom_transaksiv2_column( $column, $post_id ) {
    switch ( $column ) {

        case 'status' :
            $status = get_field('status', $post_id);
            if( $status ) {
                echo $status;
            }
            else
                _e('-' );
            break;

        case 'total' :
            $total = get_field('total', $post_id);
            if( $total ) {
                echo $total;
            }
            else
                _e('-' );
            break;

        case 'iuran' :
            $iuran = get_field('iuran', $post_id)->post_title;
            if( $iuran ) {
                echo $iuran;
            }
            else
                _e('-' );
            break;

        // case 'bulan' :
        //     $taxonomy = 'tax_bulan';
        //     $post_type = get_post_type($post_id);
        //     $terms = get_the_terms($post_id, $taxonomy);
        //     if (!empty($terms) ) {
        //         foreach ( $terms as $term )
        //             $post_terms[] ="<a href='edit.php?post_type={$post_type}&{$taxonomy}={$term->slug}'> " .esc_html(sanitize_term_field('name', $term->name, $term->term_id, $taxonomy, 'edit')) . "</a>";
        //         echo join('', $post_terms );
        //     }
        //     else
        //         _e('-' );
        //     break;

        // case 'tahun' :
        //     $taxonomy = 'tax_tahun';
        //     $post_type = get_post_type($post_id);
        //     $terms = get_the_terms($post_id, $taxonomy);
        //     if (!empty($terms) ) {
        //         foreach ( $terms as $term )
        //             $post_terms[] ="<a href='edit.php?post_type={$post_type}&{$taxonomy}={$term->slug}'> " .esc_html(sanitize_term_field('name', $term->name, $term->term_id, $taxonomy, 'edit')) . "</a>";
        //         echo join('', $post_terms );
        //     }
        //     else
        //         _e('-' );
        //     break;
    }
}

add_filter( 'manage_bulan-iuran_posts_columns', 'set_custom_edit_bulan_iuran_columns' );
add_action( 'manage_bulan-iuran_posts_custom_column' , 'custom_bulan_iuran_column', 10, 2 );

function set_custom_edit_bulan_iuran_columns($columns) {
    //unset( $columns['title'] );
    $columns['bulan'] = __( 'Bulan', 'your_text_domain' );
    $columns['tahun'] = __( 'Tahun', 'your_text_domain' );

    return $columns;
}

function custom_bulan_iuran_column( $column, $post_id ) {
    switch ( $column ) {

        case 'bulan' :
            $status = get_field('bulan_iu', $post_id);
            if( $status ) {
                echo $status;
            }
            else
                _e('-' );
            break;

        case 'tahun' :
            $total = get_field('tahun_iu', $post_id);
            if( $total ) {
                echo $total;
            }
            else
                _e('-' );
            break;
    }
}

// add_action('future_to_publish', 'set_status_publish'); 
// function set_status_publish( $post ) { 
//     if ( $post && $post->post_type =="user-profile"){
//        $post->post_status="online"; // change the post_status
//        wp_update_post( $post );
//     }    
// } 