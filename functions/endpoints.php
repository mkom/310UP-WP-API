<?php
/**
 * Midtrans
 */

require_once dirname(__FILE__) . '/midtrans-php/Midtrans.php';
//$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
//$dotenv->load();

// Set your Merchant Server Key
Midtrans\Config::$serverKey = 'SB-Mid-server-dA_GjJQ5g6VuwuWJQvsmRlAt';
// Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
Midtrans\Config::$isProduction = false;
// Set sanitization on (default)
Midtrans\Config::$isSanitized = true;
// Set 3DS transaction for credit card to true
Midtrans\Config::$is3ds = true;



/**
 * Register custom REST API routes.
 */
add_action(
    'rest_api_init',
    function () {
        register_rest_route( 'cs/v1', 'send_verification/(?P<stringvar>[^/]+)', array(
            'methods'             => 'GET',
            'callback'            => 'user_email',
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( 'cs/v1', 'send_verify', array(
            'methods'             => 'POST',
            'callback'            => 'send_verify',
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( 'cs/v1', 'user_data',array(
            'methods'  => 'GET',
            'callback' => 'user_check',
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'cs/v1', 'user_login',array(
            'methods'  => 'POST',
            'callback' => 'login_user',
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'cs/v1', 'user_update',array(
            'methods'  => 'POST',
            'callback' => 'user_update',
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'cs/v1', 'ads',array(
            'methods'  => 'GET',
            'callback' => 'rest_get_ads',
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'cs/v1', 'news',array(
            'methods'  => 'GET',
            'callback' => 'rest_get_news',
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'cs/v1', 'events',array(
            'methods'  => 'GET',
            'callback' => 'rest_get_events',
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'cs/v1', 'verify_resident',array(
            'methods'  => 'POST',
            'callback' => 'verify_resident',
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'cs/v1', 'all_data',array(
            'methods'  => 'GET',
            'callback' => 'get_all',
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'cs/v1', 'checkout',array(
            'methods'  => 'POST',
            'callback' => 'checkout',
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'cs/v1', 'notification',array(
            'methods'  => 'POST',
            'callback' => 'notification',
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'cs/v1', 'status',array(
            'methods'  => 'GET',
            'callback' => 'status',
            'permission_callback' => '__return_true',
        ));

    }
);



function user_email($data) {

    // Get user by their email address
        $user_id = '';
        $random_password = create_random_code( 6,  [
            'upper_case'        => false,
            'lower_case'        => false,
            'number'            => true,
            'special_character' => false
        ] );
        $user_login  = rawurlencode( $data['stringvar'] );
        $user_email = wp_slash( $data['stringvar']    );
        $user_pass = $random_password;

     if ($data['stringvar']) {
         if ( false == email_exists($data['stringvar'] ) ) {
             $userdata = array('user_login'=>$user_login, 'user_email'=>$user_email, 'user_pass'=>$user_pass);
             $user_idnew = wp_insert_user($userdata);
             update_user_meta( $user_idnew, 'verification_code', $user_pass);

             $user = get_user_by( 'email', $data['stringvar']);
             $user_id = $user->ID;

             global $wpdb;
             $tablename = $wpdb->prefix . "users";

             $wpdb->update( $tablename, array( 'user_login' => $user_email ), array( 'ID' => $user_id ) );
         } else {
             $user = get_user_by( 'email', $data['stringvar']);
             $user_id = $user->ID;

             wp_set_password( $user_pass, $user_id );
             update_user_meta( $user_id, 'verification_code', $user_pass);
         }

         $array_data = array();
         $array_data['email'] =  $data['stringvar'];
         $array_data['vcode'] = get_field('verification_code', 'user_' . $user_id);



         $userdata = get_userdata($user_id);
         $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
         $vcode = get_field('verification_code', 'user_' .$user_id);

         $message = sprintf(__('Username: %s'), $userdata -> user_email)."\r\n";
         $message.= sprintf(__('Verification code: %s'), $vcode)."\r\n";

         wp_mail($userdata ->user_email, sprintf(__('[%s] Your Verification code'), $blogname), $message);


         return rest_ensure_response( [
             'status' => true,
             'message'   => 'success'
         ] );

         return set_status(200);
         return $response;

     } else {
         return rest_ensure_response( [
             'status' => false,
             'message'   => 'error'
         ] );
     }


}

function send_verify($request) {

    // Get user by their email address
    $user_id = '';
    $random_password = create_random_code( 6,  [
        'upper_case'        => false,
        'lower_case'        => false,
        'number'            => true,
        'special_character' => false
    ] );
    $user_login  = $request["email"];
    $user_email = $request["email"];
    $user_pass = $random_password;

    if ( $request["email"]) {
        if ( false == email_exists( $user_email ) ) {
            $userdata = array('user_login'=>$user_login, 'user_email'=>$user_email, 'user_pass'=>$user_pass);
            $user_idnew = wp_insert_user($userdata);
            update_user_meta( $user_idnew, 'verification_code', $user_pass);

            $user = get_user_by( 'email', $user_email);
            $user_id = $user->ID;

            global $wpdb;
            $tablename = $wpdb->prefix . "users";

            $wpdb->update( $tablename, array( 'user_login' => $user_login ), array( 'ID' => $user_id ) );
        } else {
            $user = get_user_by( 'email', $user_email);
            $user_id = $user->ID;

            wp_set_password( $user_pass, $user_id );
            update_user_meta( $user_id, 'verification_code', $user_pass);
        }

        $array_data = array();
        $array_data['email'] =  $user_email;
        $array_data['vcode'] = get_field('verification_code', 'user_' . $user_id);



        $userdata = get_userdata($user_id);
        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        $vcode = get_field('verification_code', 'user_' .$user_id);

        //$message = sprintf(__('Username: %s'), $userdata -> user_email)."\r\n";
        $message = sprintf(__('Verification code: %s'), $vcode)."\r\n";

        wp_mail($userdata ->user_email, sprintf(__('[%s] Your Verification code'), $blogname), $message);


        return rest_ensure_response( [
            'status' => true,
            'message'   => 'success'
        ] );

        return set_status(200);
        //return $response;

    } else {
        return rest_ensure_response( [
            'status' => false,
            'message'   => 'error'
        ] );
    }


}

function user_update($request) {
    $currentuserid_fromjwt = get_current_user_id();

    if ($currentuserid_fromjwt != 0) {
        $user = get_user_by( 'id', $currentuserid_fromjwt);
        $userId = $user->ID;

        $firstName = $request ["first_name"];
        $lastName =  $request["last_name"];
        $phone_number =  $request["phone_number"];
        $user_email = esc_attr($request["user_email"]) ;;
        $address =  $request["address"];

        if ($firstName) {
            $firstName = $firstName ;
        } else {
            $firstName = get_user_meta($userId, 'first_name', true);
        }

        if ($lastName) {
            $lastName = $lastName ;
        } else {
            $lastName = get_user_meta($userId, 'last_name', true);
        }

        if ($user_email) {
            $user_email = $user_email ;
        } else {
            $user_email = $user->data->user_email;
        }

        if ($phone_number) {
            $phone_number = $phone_number;
        } else {
            $phone_number = get_field('phone_number', 'user_' . $userId );
        }

        if ($address) {
            $address = $address;
        } else {
            $address = get_field('address', 'user_' . $userId );
        }

        $file = $request->get_file_params()['profile_image'];
        $fileType = $request->get_file_params()['profile_image']['type'];
        $mimes = array(
            'image/bmp',
            'image/gif',
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/tiff',
            'image/tiff'
        );


        if ($user) {
            $outcome = trim($firstName . " " . $lastName);
            wp_update_user([
                'ID' => $userId, // this is the ID of the user you want to update.
                'first_name' => $firstName,
                'last_name' => $lastName,
                'user_email' => $user_email,
                'display_name' => $outcome,
            ]);

            global $wpdb;
            $tablename = $wpdb->prefix . "users";
            $wpdb->update( $tablename, array( 'user_login' => $user_email ), array( 'ID' => $userId ) );

            update_user_meta( $userId, 'first_name',  $firstName);
            update_user_meta( $userId, 'last_name',  $lastName);
            update_user_meta( $userId, 'phone_number',  $phone_number);
            update_user_meta( $userId, 'address',  $address);
            //update_user_meta( $userId, 'profile_image',  $profile_image);

            $array_data = array();
            $array_data['user_email'] = $user->data->user_email;
            $array_data['first_name'] = get_user_meta($userId, 'first_name', true);
            $array_data['last_name'] = get_user_meta($userId, 'last_name', true);
            $array_data['display_name'] = $user->data->display_name;
            $array_data['address'] = get_field('address', 'user_' . $userId )->post_title;
            $array_data['phone_number'] = get_field('phone_number', 'user_' . $userId );
            $array_data['createdAt'] = $user->data->user_registered;



            if($file['name']) {
                $exists = in_array($fileType, $mimes );
                if ($exists == true) {
                    $upload_dir = wp_upload_dir();
                    $user_id = $user->ID;
                    $timestamp = time();
                    if ( ! empty( $upload_dir['basedir'] ) ) {
                        $user_dirname = $upload_dir['basedir'].'/profile-images';
                        if ( ! file_exists( $user_dirname ) ) {
                            wp_mkdir_p( $user_dirname );
                        }

                        $filename_maker = $user_id.'_'.$timestamp.'_'.$file['name'];
                        $filename = wp_unique_filename( $user_dirname, $filename_maker );
                        // return $filename_maker;
                        $check = move_uploaded_file($file['tmp_name'], $user_dirname .'/'. $filename);
                        // save into database $upload_dir['baseurl'].'/product-images/'.$filename;
                        if($check){
                            $path = $upload_dir['baseurl'].'/profile-images/'.$filename_maker;
                        }
                    }

                    update_user_meta( $userId, 'profile_image',  $path);
                    $array_data['profile_image'] = get_field('profile_image', 'user_' . $userId );

                    return rest_ensure_response( [
                        'message'   => 'User profile updated.',
                        'data' =>  $array_data
                    ] );

                } else {
                    return rest_ensure_response( [
                        'status' => false,
                        'message'   => 'profile image is not a valid image file',
                        //'data' =>  $file
                    ] );
                }
            }

            return rest_ensure_response( [
                'status' => true,
                'message'   => 'User profile updated.',
                'data' =>  $array_data
            ] );
        } else {
            return rest_ensure_response( [
                'status' => false,
                'message'   => 'Error'
            ] );
        }

    } else {
        return rest_ensure_response( [
            'status' => false,
            'message'   => 'Invalid token'
        ] );
    }


}

function user_check($request) {
    $currentuserid_fromjwt = get_current_user_id();

    if ($currentuserid_fromjwt != 0) {
        $user = get_user_by( 'id', $currentuserid_fromjwt);
        $userId = $user->ID;
        $array_data = array();

        if ($user) {
            $array_data['user_email'] = $user->data->user_email;
            $array_data['first_name'] = get_user_meta($userId, 'first_name', true);
            $array_data['last_name'] = get_user_meta($userId, 'last_name', true);
            $array_data['display_name'] =$user->data->display_name;
            $array_data['address'] = get_field('address', 'user_' . $userId )->post_title;
            $array_data['phone_number'] = get_field('phone_number', 'user_' . $userId );
            $array_data['createdAt'] = $user->data->user_registered;
            $array_data['profile_image'] = get_field('profile_image', 'user_' . $userId );
            //$array_data['vcode'] = $user;

            $response = new WP_REST_Response($array_data);
            return rest_ensure_response( [
                'status' => true,
                'data' => $array_data,
                'message'   => 'You have successfully logged in'
            ] );
            return set_status(200);

        } else {
            return rest_ensure_response( [
                'status' => false,
                'message'   => 'Error'
            ] );
        }
    } else {
        return rest_ensure_response( [
            'status' => false,
            'message'   => 'Invalid token'
        ] );
    }

}

function login_user($request)
{
    $creds = [];
    $creds['user_login'] = $request["email"];
    $creds['user_password'] =  $request["verify_code"];
    //$creds['user_email'] =  $request["email"];
    $creds['remember'] = false;

    $user = wp_signon( $creds, false );


    if ( is_wp_error($user) )
        return rest_ensure_response( [
            'status' => false,
            'login' => 0,
            'message'   => 'The verification code or email address is incorrect.'
        ] );

    else {
        wp_clear_auth_cookie();
        wp_set_current_user($user->ID);
       // wp_set_auth_cookie($user->ID,true);
        $nonce = wp_create_nonce('wp_rest');

        if (is_user_logged_in()) {
            $current_user = 'Y';
        } else {
            $current_user = 'N';
        }

        $args = array(
            'body' => array(
                'username' => $request["email"],
                'password' => $request["verify_code"]
            ),
        );

        $request = wp_remote_post( get_rest_url(null,'jwt-auth/v1/token'), $args );

        $response = wp_remote_retrieve_body( $request );
        $response = json_decode($response, true);

        $resident =  get_field('address', 'user_' . $user->ID );

        if ($resident == false) {
            $resident = null;
        } else {
            $resident = get_field('address', 'user_' . $user->ID );
        }

        return rest_ensure_response( [
            'status' => true,
            'login' => 1,
            'id' => $user->ID,
            'nonce' => $nonce,
            'resident' => $resident,
            'token' => $response['token'],
            'is_user_logged_in' => $current_user,
            'message'   => 'You have successfully logged in'
        ] );
        return set_status(200);
    }
}

function rest_get_ads() {
    $args = array(
        'post_type' => 'ads',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    );

    $posts = get_posts($args);

    if ( empty( $posts ) ) {
        return rest_ensure_response( [
            'status' => false,
            'message'   => 'data not found'
        ] );
    }
    $ins_data = array();
    $i = 0;

    foreach ( $posts as $post ) {
        $ins_data[] = array(  // you can ad anything here and as many as you want
            'id' => $posts[$i]->ID,
            'slug' => $posts[$i]->post_name,
            'title' => $posts[$i]->post_title,
            'image' => get_field( 'ads_image', $posts[$i]->ID )['url'],
        );
        $i++;
    }


    // Returned Data
    $response = new WP_REST_Response($ins_data);
    $response->set_status(200);
    return $response;
}

function rest_get_news() {
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    );

    $posts = get_posts($args);

    if ( empty( $posts ) ) {
        return rest_ensure_response( [
            'msg'   => 'data not found'
        ] );
    }
    $ins_data = array();
    $i = 0;

    foreach ( $posts as $post ) {
        $ins_data[] = array(  // you can ad anything here and as many as you want
            'id' => $posts[$i]->ID,
            'slug' => $posts[$i]->post_name,
            'title' => $posts[$i]->post_title,
            'publish_date' => $posts[$i]->post_date,
            'excerpt' => get_the_excerpt($posts[$i]->ID),
            'content' => $posts[$i]->post_content,
            'thumbnail' => get_the_post_thumbnail_url($posts[$i]->ID,'medium'),
            'guid' => $posts[$i]->guid,
            'author' => get_the_author_meta( 'display_name' ,  $posts[$i]->post_author ),
            'author_avatar' => get_avatar( get_the_author_meta( $posts[$i]->post_author ), 32 ),
    );
        $i++;
    }


    // Returned Data
    $response = new WP_REST_Response($ins_data);
    $response->set_status(200);
    return $response;
}

function rest_get_events() {
    $args = array(
        'post_type' => 'event',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    );

    $posts = get_posts($args);

    if ( empty( $posts ) ) {
        return rest_ensure_response( [
            'msg'   => 'data not found'
        ] );
    }
    $ins_data = array();
    $i = 0;

    foreach ( $posts as $post ) {
        $ins_data[] = array(  // you can ad anything here and as many as you want
            'id' => $posts[$i]->ID,
            'slug' => $posts[$i]->post_name,
            'title' => $posts[$i]->post_title,
            'image' => get_field( 'event_image', $posts[$i]->ID )['url'],
            'start_date' => get_field( 'event_date', $posts[$i]->ID )['event_start'],
            'end_date' => get_field( 'event_date', $posts[$i]->ID )['event_end'],
            'location' => get_field( 'event_location', $posts[$i]->ID ),
            'address' => get_field( 'event_address', $posts[$i]->ID ),
            'price' => get_field( 'event_price', $posts[$i]->ID ),
            'description' => get_field( 'event_description', $posts[$i]->ID ),
        );
        $i++;
    }


    // Returned Data
    $response = new WP_REST_Response($ins_data);
    $response->set_status(200);
    return $response;
}

function verify_resident($request) {
    $currentuserid_fromjwt = get_current_user_id();

    if ($currentuserid_fromjwt != 0) {
        $user = get_user_by( 'id', $currentuserid_fromjwt);
        $userId = $user->ID;
        $ins_data = array();

        $norumah = $request ["norumah"];
        $idrumah = $request ["idrumah"];
        $nama = $request ["nama"];
        $phone = $request ["noWa"];

        $args = array(
            'post_type' => 'rumah',
            'posts_per_page' => 1,
            'post_status' => 'publish',
            's' => $norumah,
            'meta_query' => array(
                array(
                    'key' => 'id_rumah',
                    'value' => $idrumah,
                    'compare' => 'LIKE'
                ),
            )
        );

        $posts = get_posts($args);

        if ( empty( $posts ) ) {
            return rest_ensure_response( [
                'status' => false,
                'message'   => 'data not found'
                //'data' => '',
            ] );
        } else {
            $idrm = '';
            foreach ( $posts as $post ) {
                $idrm =  $post->ID;
                $ins_data[] = array(  // you can ad anything here and as many as you want
                    'IDrumah' => $post->ID,
                    //'IDuser' => get_field( 'field_63270532649ec', $post->ID),
                );
            }

            $current_user = get_field( 'field_63270532649ec', $idrm );
            if($current_user) {
                array_push($current_user, $userId);
            } else {
                $current_user = $userId;
            }

            // set rumah
            update_field( 'field_63270532649ec', $current_user, $idrm );

            //update user
            wp_update_user([
                'ID' => $userId, // this is the ID of the user you want to update.
                'display_name' => $nama,
            ]);

            update_user_meta( $userId, 'name_user',  $nama);
            update_user_meta( $userId, 'phone_number',  $phone);
            update_user_meta( $userId, 'address',  $idrm);

            return rest_ensure_response( [
                'status' => true,
                'message'   => 'success',
                'data' => $ins_data,
            ] );
        }

    } else {
        return rest_ensure_response( [
            'status' => false,
            'message'   => 'Invalid token'
        ] );
    }

}

function get_all() {
    $currentuserid_fromjwt = get_current_user_id();

    if ($currentuserid_fromjwt != 0) {
        $user = get_user_by( 'id', $currentuserid_fromjwt);
        $userId = $user->ID;
        $ins_data = array();

        $args = array(
            'post_type' => 'rumah',
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'user',
                    'value' => $userId,
                    'compare' => 'LIKE'
                )
            )
        );

        $posts = get_posts($args);

        if ( empty( $posts ) ) {
            return rest_ensure_response( [
                'status' => false,
                'message'   => 'failed'
                //'data' => '',
            ] );
        } else {
            foreach ( $posts as $post ) {
               // $idrm =  $post->ID;
                $iplbaru = get_field('status_ipl',$post->ID)['ipl_terbaru'];
                $dlast = strtotime($iplbaru);
                $datelast = date('F Y', $dlast);
                setlocale(LC_CTYPE, 'Indonesian');
                setlocale(LC_TIME, 'Indonesian');
                $month_name = strftime('%B %Y', $dlast);
                $ins_data[] = array(  // you can ad anything here and as many as you want
                    'ID_home' => $post->ID,
                    'home' => $post->post_title,
                    'ID_unique' => get_field( 'id_rumah', $post->ID ),
                    'last_ipl' => $iplbaru,
                    'count_ipl' => get_field( 'status_ipl', $post->ID )['jumlah_ipl_belum_bayar'],
                    //'bill_ipl' => get_field( 'status_ipl', $post->ID )['ipl_belum_bayar'],
                    'bill_ipl' => str_replace("\n", ", ",  get_field( 'status_ipl', $post->ID )['ipl_belum_bayar']),
                    'profile' => array(
                        'name' => $user->data->display_name,
                        'email' => $user->data->user_email,
                        'phone' => get_field('phone_number', 'user_' . $userId )
                    )
                );
            }

            return rest_ensure_response( [
                'status' => true,
                'message'   => 'success',
                'data' => $ins_data,
            ] );
        }

    } else {
        return rest_ensure_response( [
            'status' => false,
            'message'   => 'Invalid token'
        ] );
    }

}


function checkout($request) {
    $currentuserid_fromjwt = get_current_user_id();

    if ($currentuserid_fromjwt != 0) {
        $user = get_user_by( 'id', $currentuserid_fromjwt);
        $userId = $user->ID;


        // Required

        //get home
        $args = array(
            'post_type' => 'rumah',
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'user',
                    'value' => $userId,
                    'compare' => 'LIKE'
                )
            )
        );

        $posts = get_posts($args);
        $noRumah = '';
        $no_Rumah = '';
        foreach ( $posts as $post ) {
            $noRumah .= str_replace("-", "",  $post->post_title);
            $no_Rumah .= $post->post_title;

        }

        $orderID = 'INV'.$userId.'IPL70'.$noRumah;
        $transaction_details = array(
            'order_id' => $orderID.time(),
            'gross_amount' => $request->get_params()['price'], // no decimal allowed for creditcard
        );

        // Optional
        $item_details = array(
            array(
                'id' => 'IPL70',
                'price' =>  70000,
                'quantity' =>  $request->get_params()['qty'],
                'name' => 'IPL VC 70.000',
                'brand' => 'Villa Citayam',
                'category'=> 'iuran',
                'merchant_name' => 'Villa Citayam'
            )
        );

        // Optional
        $billing_address = array(
            'first_name'    => $user->data->display_name,
            'last_name'     => "",
            'address'       => $no_Rumah,
            //'city'          => "Sukabumi",
            //'postal_code'   => "143115",
            'phone'         => get_field('phone_number', 'user_' . $userId ),
           // 'country_code'  => 'IDN'
        );

        // Optional
        $customer_details = array(
            'first_name'    => $user->data->display_name,
            //'last_name'     => "Rizky",
            'email'         => $user->data->user_email,
            'phone'         => get_field('phone_number', 'user_' . $userId ),
            'billing_address'  => $billing_address
        );

        $expiry = array(
            'unit' => 'minutes',
            'duration' => 120
        );

        // Optional, remove this to display all available payment methods
        //$enable_payments = array('credit_card','cimb_clicks','mandiri_clickpay','echannel');

        $transaction = array(
            //'enabled_payments' => $enable_payments,
            'transaction_details' => $transaction_details,
            'customer_details' => $customer_details,
            'item_details' => $item_details,
            'custom_field1' => $request->get_params()['desc'],
            'custom_field2' => $request->get_params()['notes'],
            'custom_field3' => $request->get_params()['qty'], // jumlah bulan bayar
            'expiry'        => $expiry,
        );

        $snapToken = Midtrans\Snap::getSnapToken($transaction);
        //echo "snapToken = ".$snapToken;
        return rest_ensure_response( [
            'status' => true,
            'message'   => 'success',
           // 'order_id' => get_field('address', 'user_' . $userId )->post_title,
            'snapToken' => $snapToken,
        ] );

    } else {
        return rest_ensure_response( [
            'status' => false,
            'message'   => 'Invalid token'
        ] );
    }


}

function notification($request) {
    //$currentuserid_fromjwt = get_current_user_id();

    $notif = new Midtrans\Notification();

    $transaction = $notif->transaction_status;
    $type = $notif->payment_type;
    $order_id = $notif->order_id;
    $fraud = $notif->fraud_status;

    //$string = 'INV5IPL70E3101664547996';
    $string = explode("IPL70", $order_id );
    $ID = substr($string[0], 3);
    //echo $string[0];    // will display This is a simple sting

    if ($ID != 0) {
        $user = get_user_by( 'id', $ID);
        $userId = $user->ID;


        // Required

        //get home
        $args = array(
            'post_type' => 'rumah',
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'user',
                    'value' => $userId,
                    'compare' => 'LIKE'
                )
            )
        );

        $posts = get_posts($args);
        $noRumah = '';
        foreach ( $posts as $post ) {
            $idrm =  $post->ID;
            $noRumah .= str_replace("-", "",  $post->post_title);
        }


        $message = 'ok';
        if ($transaction == 'capture') {
            // For credit card transaction, we need to check whether transaction is challenge by FDS or not
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    // TODO set payment status in merchant's database to 'Challenge by FDS'
                    // TODO merchant should decide whether this transaction is authorized or not in MAP
                    $message = "Transaction order_id: " . $order_id ." is challenged by FDS";
                } else {
                    // TODO set payment status in merchant's database to 'Success'
                    $message = "Transaction order_id: " . $order_id ." successfully captured using " . $type;
                }
            }
        } elseif ($transaction == 'settlement') {
            // TODO set payment status in merchant's database to 'Settlement'
            $message = "Transaction order_id: " . $order_id ." successfully transfered using " . $type;
        } elseif ($transaction == 'pending') {
            // TODO set payment status in merchant's database to 'Pending'
            $message = "Waiting customer to finish transaction order_id: " . $order_id . " using " . $type;
        } elseif ($transaction == 'deny') {
            // TODO set payment status in merchant's database to 'Denied'
            $message = "Payment using " . $type . " for transaction order_id: " . $order_id . " is denied.";
        } elseif ($transaction == 'expire') {
            // TODO set payment status in merchant's database to 'expire'
            $message = "Payment using " . $type . " for transaction order_id: " . $order_id . " is expired.";
        } elseif ($transaction == 'cancel') {
            // TODO set payment status in merchant's database to 'Denied'
            $message = "Payment using " . $type . " for transaction order_id: " . $order_id . " is canceled.";
        }



        if ($notif->va_numbers) {
            $bank ='';
            $va_number ='';
            foreach ($notif->va_numbers as $val) {
                $bank .= $val->bank;
                $va_number .= $val->va_number;
            }
        }


        $check_title = get_page_by_title($order_id, 'OBJECT', 'transaksi');

        if (empty($check_title) ) {
            $new_post = array(
                'post_title' => $order_id,
                //'post_content' => $transaction,
                'post_status' => 'publish',
                'post_date' => date('Y-m-d H:i:s'),
                //'post_author' => $user_ID,
                'post_type' => 'transaksi',
                //'post_category' => array(0)
            );

            $post_id = wp_insert_post($new_post);

            update_field('data_transaksi', array('custom_field1'=>$notif->custom_field1), $post_id);
            update_field('data_transaksi', array('custom_field2'=>$notif->custom_field2), $post_id);
            update_field('data_transaksi', array('order_id'=>$notif->order_id), $post_id);
            update_field('data_transaksi', array('transaction_id'=>$notif->transaction_id), $post_id);
            update_field('data_transaksi', array('transaction_time'=>$notif->transaction_time), $post_id);
            update_field('data_transaksi', array('transaction_status'=>$notif->transaction_status), $post_id);
            update_field('data_transaksi', array('payment_type'=>$notif->payment_type), $post_id);
            update_field('data_transaksi', array('gross_amount'=>$notif->gross_amount), $post_id);
            update_field('data_transaksi', array('status_message'=>$notif->status_message), $post_id);
            update_field('data_transaksi', array('payment_status'=>$message), $post_id);

            //set rumah dan user
            update_field( 'rumah_trx', $idrm, $post_id );
            update_field( 'user_trx', $userId, $post_id );
            update_field( 'jumlah_bayar', $notif->custom_field3, $post_id );

            if ($type == 'bank_transfer') {
                update_field('data_transaksi', array('bank'=>$bank), $post_id);
                update_field('data_transaksi', array('va_number'=>$va_number), $post_id);
            }



        } else {
            $new_post = array(
                'ID' =>  $check_title->ID,
                'post_title' => $order_id,
                //'post_content' => $transaction,
                'post_status' => 'publish',
                'post_date' => date('Y-m-d H:i:s'),
                //'post_author' => $user_ID,
                'post_type' => 'transaksi',
                //'post_category' => array(0)
            );

            $post_id = wp_update_post($new_post);


            update_field('data_transaksi', array('custom_field1'=>$notif->custom_field1), $post_id);
            update_field('data_transaksi', array('custom_field2'=>$notif->custom_field2), $post_id);
            update_field('data_transaksi', array('order_id'=>$notif->order_id), $post_id);
            update_field('data_transaksi', array('transaction_id'=>$notif->transaction_id), $post_id);
            update_field('data_transaksi', array('transaction_time'=>$notif->transaction_time), $post_id);
            update_field('data_transaksi', array('transaction_status'=>$notif->transaction_status), $post_id);
            update_field('data_transaksi', array('payment_type'=>$notif->payment_type), $post_id);
            update_field('data_transaksi', array('gross_amount'=>$notif->gross_amount), $post_id);
            update_field('data_transaksi', array('status_message'=>$notif->status_message), $post_id);
            update_field('data_transaksi', array('payment_status'=>$message), $post_id);

            update_field( 'rumah_trx', $idrm, $post_id );
            update_field( 'user_trx', $userId, $post_id );
            update_field( 'jumlah_bayar', $notif->custom_field3, $post_id );

            if ($type == 'bank_transfer') {
                update_field('data_transaksi', array('bank'=>$bank), $post_id);
                update_field('data_transaksi', array('va_number'=>$va_number), $post_id);
            }

        }


        if($transaction == 'settlement') {
            $jumlah = get_field('jumlah_bayar', $check_title->ID);

            $iplTerbaru = get_field('status_ipl',$idrm)['ipl_terbaru'];
            $dlast = strtotime($iplTerbaru);
            $datelast = date('F Y', $dlast);

            $iplTerbaruBayar = date('F Y', strtotime('+'.$jumlah.' months', strtotime($datelast)));
            $dlast2 = strtotime($iplTerbaruBayar);
            $datelast2 = date('F Y', $dlast2);

            if ($iplTerbaruBayar) {


                $thisMouth = date('F Y');

                //list bulan belum bayar
                $start    = new DateTime($datelast);
                $start->modify('first day of next month');
                $end      = new DateTime($datelast2);
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
                update_field( 'bulan_yang_dibayar', $listbln, $post_id );


                /**
                 * set susscess bayar ke rumah
                 */

                $s = $iplTerbaruBayar;
                $d = strtotime($s);
                $date = date('F Y', $d);

                update_field('status_ipl', array('ipl_terbaru'=>$date), $idrm);

                //iplbaru
                $iplbaruarr = explode(", ", $listbln);

                //list ipl
                $ipl = get_field('status_ipl', $idrm)['ipl_terbayar'];
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
                update_field('status_ipl', array('ipl_terbayar'=>$value), $idrm);

                //update fild rumah

                $iplbaru = get_field('status_ipl',$idrm)['ipl_terbaru'];
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
                update_field('status_ipl', array('ipl_belum_bayar'=>$listbln), $idrm);

                //num ipl lum bayar
                update_field('status_ipl', array('jumlah_ipl_belum_bayar'=>$i), $idrm);
            }
        }

        return rest_ensure_response( [
            'status' => true,
            'message'   =>$message,
            // 'order_id' => get_field('address', 'user_' . $userId )->post_title,
            //'snapToken' => $snapToken,
        ] );



    } else {
        return rest_ensure_response( [
            'status' => false,
            'message'   => 'Not found'
        ] );
    }

}

function status($request) {
    $orderID = $request["orderID"];
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.sandbox.midtrans.com/v2/'.$orderID.'/status',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: U0ItTWlkLXNlcnZlci1kQV9HakpRNWc2VnV3dVdKUXZzbVJsQXQ6'
        ),
    ));

    $response = curl_exec($curl);
    $response = json_decode($response);

    curl_close($curl);
    //return $response;

    if ($response) {
        return rest_ensure_response( [
            'status' => true,
            'message'   => 'success',
            'data' => $response,
        ] );
    }

}

