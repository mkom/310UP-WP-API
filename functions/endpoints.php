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

date_default_timezone_set('Asia/Jakarta');


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

        register_rest_route( 'cs/v1', 'iuran_code',array(
            'methods'  => 'POST',
            'callback' => 'iuran_code',
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'cs/v1', 'join_iuran',array(
            'methods'  => 'POST',
            'callback' => 'join_iuran',
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'cs/v1', 'iuran',array(
            'methods'  => 'GET',
            'callback' => 'iuran',
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'cs/v1', 'user_iuran',array(
            'methods'  => 'GET',
            'callback' => 'user_iuran',
            'permission_callback' => '__return_true',
        ));


        register_rest_route( 'cs/v1', 'verify_resident',array(
            'methods'  => 'POST',
            'callback' => 'verify_resident',
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'cs/v1', 'set_profile',array(
            'methods'  => 'POST',
            'callback' => 'set_profile',
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'cs/v1', 'user_profile',array(
            'methods'  => 'GET',
            'callback' => 'user_profile',
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

        register_rest_route( 'cs/v1', 'transaction',array(
            'methods'  => 'GET',
            'callback' => 'get_transaction',
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'cs/v1/moota/', 'checkout',array(
            'methods'  => 'POST',
            'callback' => 'checkout_moota',
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'cs/v1/moota/', 'transaction/(?P<stringvar>[^/]+)',array(
            'methods'  => 'GET',
            'callback' => 'transaction_moota',
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'cs/v1/moota/', 'transaction',array(
            'methods'  => 'GET',
            'callback' => 'transaction_moota',
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'cs/v1', 'tester',array(
            'methods'  => 'GET',
            'callback' => 'tester',
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'cs/v1/moota/', 'callback',array(
            'methods'  => 'POST',
            'callback' => 'moota_callback',
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'cs/v1/', 'report/(?P<id>[^/]+)',array(
            'methods'  => 'GET',
            'callback' => 'report',
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'cs/v1/', 'report/user_iuran/(?P<id>[^/]+)',array(
            'methods'  => 'GET',
            'callback' => 'report_iuran_iuran',
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

//        $array_data = array();
//        $array_data['email'] =  $user_email;
//        $array_data['vcode'] = get_field('verification_code', 'user_' . $user_id);



        $userdata = get_userdata($user_id);
        //$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        $blogname = 'VCPAY';
        //$vcode = get_field('verification_code', 'user_' .$user_id);
        $vcode = get_user_meta($user_id, 'verification_code', true);

        //$message = sprintf(__('Username: %s'), $userdata -> user_email)."\r\n";
        $message = sprintf(__('Verification code: %s'), $vcode)."\r\n";

        wp_mail($userdata ->user_email, sprintf(__('[%s] Your Verification code'), $blogname), $message);


        return rest_ensure_response( [
            'status' => true,
            'message'   => $blogname
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

function login_user($request) {
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

        //$resident =  get_field('address', 'user_' . $user->ID );
        $resident = get_user_meta($user->ID, 'address', true);

        if ($resident == false) {
            $resident = null;
        } else {
            $resident = get_user_meta($user->ID, 'address', true);
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

function set_profile($request) {
    $currentuserid_fromjwt = get_current_user_id();

    if ($currentuserid_fromjwt != 0) {
        $user = get_user_by( 'id', $currentuserid_fromjwt);
        $userId = $user->ID;
        $ins_data = array();

        $nama = $request ["nama"];
        $phone = $request ["noWa"];
        $norumah = $request ["norumah"];
        $status = $request ["status"];
        $bulan = $request ["bulan"];
        $tahun = $request ["tahun"];
        $blok = $request ["blok"];


        //update user
        wp_update_user([
            'ID' => $userId, // this is the ID of the user you want to update.
            'display_name' => $nama,
        ]);

        update_user_meta( $userId, 'name_user',  $nama);
        update_user_meta( $userId, 'phone_number',  $phone);
        update_user_meta( $userId, 'no_rumah',  $norumah);
        update_user_meta( $userId, 'phone_number',  $phone);
        update_user_meta( $userId, 'status_kepemilikan',  $status);
        update_user_meta( $userId, 'bulan',  $bulan);
        update_user_meta( $userId, 'tahun',  $tahun);


        //create rumah
        $check_title = get_page_by_title($norumah, 'OBJECT', 'rumah');

        $new_post = array(
            'post_title' => $norumah,
            'post_status' => 'publish',
            'post_date' => date('Y-m-d H:i:s'),
            //'post_author' => $userId,
            'post_type' => 'rumah',
        );

        if(empty($check_title)) {
            $post_id = wp_insert_post($new_post);

        } else {
            $args = array(
                'post_type' => 'rumah',
                'posts_per_page' => 1,
                'post_status' => 'publish',
                's' => $norumah,
            );
            $posts = get_posts($args);

            foreach ( $posts as $post ) {
                $post_id = $post->ID;

            }
        }
        //update user address
        update_user_meta( $userId, 'address',  $post_id);
        $current_user = get_field( 'field_63270532649ec', $post_id );
        if($current_user) {
            array_push($current_user, $userId);
        } else {
            $current_user = $userId;
        }

        // set rumah
        update_field( 'field_63270532649ec', $current_user, $post_id );

        $taxonomy = 'taxblok';
        $termObj  = get_term_by( 'name', $blok, $taxonomy);
        $term_id = $termObj->term_id;
        wp_set_object_terms($post_id, intval( $term_id ), $taxonomy);

        //create user profile
        $userProfileTitle = 'user'.$userId.'-'.$nama;
        $userProfile= get_page_by_title($userProfileTitle, 'OBJECT', 'user-profile');

        $new_post_profile = array(
            'post_title' => $userProfileTitle,
            'post_status' => 'publish',
            'post_date' => date('Y-m-d H:i:s'),
            //'post_author' => $userId,
            'post_type' => 'user-profile',
        );


        if(empty($userProfile)) {
            $profile_id = wp_insert_post($new_post_profile);
        } else {
            $args = array(
                'post_type' => 'user-profile',
                'posts_per_page' => 1,
                'post_status' => 'publish',
                's' => $userProfileTitle,
            );
            $posts = get_posts($args);

            foreach ( $posts as $post ) {
                $profile_id = $post->ID;

            }
        }

        update_field( 'field_633c73ae0f44f', $userId, $profile_id ); // link user


        return rest_ensure_response( [
            'status' => true,
            'message'   => 'success',
            //'data' => $check_title,
        ] );

    } else {
        return rest_ensure_response( [
            'status' => false,
            'message'   => 'Invalid token'
        ] );
    }

}

function user_profile() {
    $currentuserid_fromjwt = get_current_user_id();

    if ($currentuserid_fromjwt != 0) {
        $user = get_user_by( 'id', $currentuserid_fromjwt);
        $userId = $user->ID;
        $ins_data = array();

        $args = array(
            'post_type' => 'user-profile',
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'user_full',
                    'value' => $userId,
                    'compare' => 'LIKE'
                )
            )
        );

        $posts = get_posts($args);

        $rmid = '';

        $argsrm = array(
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

        $postrm = get_posts($argsrm);

        foreach ( $postrm as $rm ) {
            $rmid = $rm->ID;
        }

        $iuranUser = array();
        $argsiu = array(
            'post_type' => 'user-iuran',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'creator',
                    'value' => $userId,
                    'compare' => 'LIKE'
                )
            )
        );

        $postsiu = get_posts($argsiu);

        foreach ( $postsiu as $iu ) {
            $lastIPLB = get_field('status_iuran',$iu->ID)['bulan'];
            $lastIPLT = get_field('status_iuran',$iu->ID)['tahun'];

            $lastIPL = $lastIPLB.' '.$lastIPLT;
            $getIuran = get_field('iuran',$iu->ID);
            $nominal = '';

            $argsmiu = array(
                'post_type' => 'iuran',
                'posts_per_page' => 1,
                'post_status' => 'publish',
                'post__in' => [ $getIuran->ID ]
            );

            $postmiu = get_posts($argsmiu);
            $iuranCode = '';
            foreach ( $postmiu as $miu ) {
                $nominal = get_field( 'nominal', $miu->ID );
                $iuranCode = get_field( 'kode_iuran', $miu->ID );
            }


            $iuranUser[] = array(  // you can ad anything here and as many as you want
                'iuran_link_ID' => $iu->ID,
                'iuranID' => $getIuran->ID,
                'iuran_name' => $getIuran->post_title,
                'iuran_slug' => $getIuran->post_name,
                'iuran_code' => $iuranCode,
                'nominal'   =>$nominal,
                'last_ipl' => $lastIPL,
                'count_ipl' => get_field( 'status_iuran', $iu->ID )['jumlah_ipl_belum_bayar'],
                'bill_ipl' => str_replace("\n", ", ",  get_field( 'status_ipl', $iu->ID )['ipl_belum_bayar']),
                'description' => get_field( 'description', $miu->ID ),

            );
        }


        $taxonomy = 'taxblok';
        $terms = get_the_terms( $rmid ,$taxonomy );
        if ( !empty( $terms ) ){
            // get the first term
            $term = array_shift( $terms );
            $blok =  $term->name;
        }

        if ( empty( $posts ) ) {
            return rest_ensure_response( [
                'status' => false,
                'message'   => 'failed'
                //'data' => '',
            ] );
        } else {
            foreach ( $posts as $post ) {
                // $idrm =  $post->ID;
                // $iplbaru = get_field('status_ipl',$post->ID)['ipl_terbaru'];
                // $dlast = strtotime($iplbaru);
                // $datelast = date('F Y', $dlast);
                // setlocale(LC_CTYPE, 'Indonesian');
                // setlocale(LC_TIME, 'Indonesian');
                // $month_name = strftime('%B %Y', $dlast);


                $ins_data[] = array(  // you can ad anything here and as many as you want
                    'name' => $user->data->display_name,
                    'email' => $user->data->user_email,
                    'rumah' => get_field('no_rumah', 'user_' . $userId ),
                    'blok' => $blok,
                    'status_rumah' => get_field('status_kepemilikan', 'user_' . $userId ),
                    'menetap' => get_field('bulan', 'user_' . $userId ).' '.get_field('tahun', 'user_' . $userId ),
                    'link_to_profile' => $post->ID,
                    'iuran_yang_diikuti' => $iuranUser,
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

function get_transaction() {

    $currentuserid_fromjwt = get_current_user_id();

    if ($currentuserid_fromjwt != 0) {
        $user = get_user_by( 'id', $currentuserid_fromjwt);
        $userId = $user->ID;
        $ins_data = array();

        $args = array(
            'post_type' => 'transaksi',
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
                $ins_data[] = array(  // you can ad anything here and as many as you want
                    'id' => $post->ID,
                    'title' => $post->post_title,
                    'user' =>  get_field( 'data_transaksi', $post->ID )['user_trx'],
                    'home' => get_field( 'data_transaksi', $post->ID )['rumah_trx'],
                    'count' =>  get_field( 'data_transaksi', $post->ID )['rumah_trx'],
                    'description' =>  get_field( 'data_transaksi', $post->ID )['custom_field1'],
                    'notes' =>  get_field( 'data_transaksi', $post->ID )['custom_field2'],
                    'transaction_id' =>  get_field( 'data_transaksi', $post->ID )['transaction_id'],
                    'transaction_time' =>  get_field( 'data_transaksi', $post->ID )['transaction_time'],
                    'transaction_status' =>  get_field( 'data_transaksi', $post->ID )['transaction_status'],
                    'payment_type' =>  get_field( 'data_transaksi', $post->ID )['payment_type'],
                    'bank' =>  get_field( 'data_transaksi', $post->ID )['bank'],
                    'va_number' =>  get_field( 'data_transaksi', $post->ID )['va_number'],
                    'gross_amount' =>  get_field( 'data_transaksi', $post->ID )['gross_amount'],
                    'settlement_time' =>  get_field( 'data_transaksi', $post->ID )['settlement_time'],
                    'status_message' =>  get_field( 'data_transaksi', $post->ID )['status_message'],
                    'payment_status' => get_field( 'data_transaksi', $post->ID )['payment_status'],

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

function iuran_code($request) {
    $codeiu = $request ["codeiu"];

    $args = array(
        'post_type' => 'iuran',
        'posts_per_page' => 1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'kode_iuran',
                'value' => $codeiu,
                //'compare' => 'LIKE'
            ),
        )
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
            'ID' => $post->ID,
            'iuran' => $post->post_title,
            'code_iuran' =>  get_field( 'kode_iuran', $post->ID),
            'nominal' => get_field( 'nominal', $post->ID),
            'status' => get_field( 'status_iuran', $post->ID),
            'description' => get_field( 'description', $post->ID),
        );
        $i++;
    }


    // Returned Data
    return rest_ensure_response( [
        'status' => true,
        'message'   => 'success',
        'data' => $ins_data,
    ] );
}

function join_iuran($request) {
    $currentuserid_fromjwt = get_current_user_id();

    if ($currentuserid_fromjwt != 0) {
        $user = get_user_by( 'id', $currentuserid_fromjwt);
        $userId = $user->ID;
        $ins_data = array();


        $code = $request ["code"];
        $bulan = $request ["bulan"];
        $tahun = $request ["tahun"];

        //get berlaku iuran
        $args = array(
            'post_type' => 'iuran',
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'kode_iuran',
                    'value' => $code,
                )
            )
        );

        $posts = get_posts($args);
        $idberlakuiuran = '';
        foreach ( $posts as $post ) {
            $idberlakuiuran =  $post->ID;
            $iuranName =  $post->post_title;
        }

        //awal berlaku iran

        $awalberlakubulan = get_field('berlaku',$idberlakuiuran)['bulan'];
        $awalberlakutahun = get_field('berlaku',$idberlakuiuran)['tahun'];

        if($request ["bulan"]) {
            $bulan = $request ["bulan"];
        } else {
            $bulan = $awalberlakubulan;
        }

        if($request ["tahun"]) {
            $tahun = $request ["tahun"];
        } else {
            $tahun = $awalberlakutahun;
        }

        $iuran = $request ["iuranID"];
        $title = 'IuranuserID'.$userId.'/'.$iuranName;

        //create iuran user
        $check_title = get_page_by_title($title, 'OBJECT', 'user-iuran');

        $new_post = array(
            'post_title' => $title,
            'post_status' => 'publish',
            'post_date' => date('Y-m-d H:i:s'),
            'post_type' => 'user-iuran',
        );

        if(empty($check_title)) {
            $post_id = wp_insert_post($new_post);

        } else {
            //$post_id = wp_update_post($new_post);
        }

        update_field( 'field_633c98ca24b58', $iuran, $post_id );// link user
        update_field( 'creator', $userId, $post_id );// link user

        //status iuran
        update_field('field_633c97cad3c42', array('field_633c97cad7585'=>$bulan), $post_id);
        update_field('field_633c97cad3c42', array('field_633c97cad7942'=>$tahun), $post_id);

        //awal iuran
        update_field('awal_iuran', array('bulan'=>$bulan), $post_id);
        update_field('awal_iuran', array('tahun'=>$tahun), $post_id);



        //update status iuran
        // fungtion update ipl
        $lastIPLB = get_field('status_iuran',$post_id)['bulan'];
        $lastIPLT = get_field('status_iuran',$post_id)['tahun'];

        if ($lastIPLB) {
            $lastIPLB = get_field('status_iuran',$post_id)['bulan'];
        } else {
            $lastIPLB = $awalberlakubulan;
        }

        if ($lastIPLB) {
            $lastIPLT = get_field('status_iuran',$post_id)['tahun'];
        } else {
            $lastIPLB = $awalberlakutahun;
        }


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
            array_push($per, $dt->format("F Y"));

        }

        $listbln = implode("\n",$per);

        update_field('status_ipl', array('ipl_belum_bayar'=>$listbln), $post_id);

        //num ipl lum bayar
        update_field('status_iuran', array('jumlah_ipl_belum_bayar'=>$i), $post_id);


        return rest_ensure_response( [
            'status' => true,
            'message'   => 'success',
            //'data' => $bulan,
        ] );

    } else {
        return rest_ensure_response( [
            'status' => false,
            'message'   => 'Invalid token'
        ] );
    }
}

function user_iuran($request) {
    $currentuserid_fromjwt = get_current_user_id();

    if ($currentuserid_fromjwt != 0) {
        $user = get_user_by( 'id', $currentuserid_fromjwt);
        $userId = $user->ID;
        $ins_data = array();


        $code = $request ["code"];

        $argsiu = array(
            'post_type' => 'user-iuran',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'creator',
                    'value' => $userId,
                    'compare' => 'LIKE'
                )
            )
        );

        $postsiu = get_posts($argsiu);

        foreach ( $postsiu as $iu ) {
            $lastIPLB = get_field('status_iuran', $iu->ID)['bulan'];
            $lastIPLT = get_field('status_iuran', $iu->ID)['tahun'];

            $lastIPL = $lastIPLB . ' ' . $lastIPLT;
            $getIuran = get_field('iuran', $iu->ID);
            $nominal = '';

            $argsmiu = array(
                'post_type' => 'iuran',
                'posts_per_page' => 1,
                'post_status' => 'publish',
                'post__in' => [$getIuran->ID]
            );

            $postmiu = get_posts($argsmiu);
            $iuranCode = '';
            foreach ($postmiu as $miu) {
                $nominal = get_field('nominal', $miu->ID);
                $iuranCode = get_field('kode_iuran', $miu->ID);
            }


            $iuranUser[] = array(  // you can ad anything here and as many as you want
                'iuran_link_ID' => $iu->ID,
                'iuranID' => $getIuran->ID,
                'iuran_name' => $getIuran->post_title,
                'iuran_code' => $iuranCode,
                'nominal' => $nominal,
                'last_ipl' => $lastIPL,
                'count_ipl' => get_field('status_iuran', $iu->ID)['jumlah_ipl_belum_bayar'],
                'bill_ipl' => str_replace("\n", ", ", get_field('status_ipl', $iu->ID)['ipl_belum_bayar']),

            );

        };

        return rest_ensure_response( [
            'status' => true,
            'message'   => 'success',
            'data' => $iuranUser,
        ] );

    } else {
        return rest_ensure_response( [
            'status' => false,
            'message'   => 'Invalid token'
        ] );
    }
}

function checkout_moota($request) {
    $currentuserid_fromjwt = get_current_user_id();

    if ($currentuserid_fromjwt != 0) {
        $user = get_user_by( 'id', $currentuserid_fromjwt);
        $userId = $user->ID;

        $code = $request ["codeiu"];

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
            $rumahID = $post->ID;

        }


        //get iuran
        $argsiu = array(
            'post_type' => 'iuran',
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'kode_iuran',
                    'value' => $code,
                )
            )
        );

        $postsiu = get_posts($argsiu);
        foreach ( $postsiu as $postiu ) {
            $iuranID =  $postiu->ID;
            $iuransku =  str_replace(" ", "",  $postiu->post_title);
            $iuranName =  $postiu->post_title;
        }

        $invoice = 'INV'.$iuransku.''.$userId.''.time();

        $amount = intval($request["price"]);
        //$payment_method_id ='DZ4jAJYOWAo';
        //$type ='bca';
        $qty = intval( $request ["qty"]);
        $callback_url ='https://komtest.dev.s360.is/wp-json/cs/v1/moota/callback';
        //$callback_url ='https://webhook.site/df5a57fb-ae9a-4f80-9b9c-7d2683c27bbd';
        $description = $request ["desc"];
        $notes = $request ["notes"];
        $expired_date = date("Y-m-d H:i:s", strtotime("now +1 hour"));


        //transaction
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://app.moota.co/api/v2/contract',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "invoice_number" : "'.$invoice.'",
                "amount" : '.$amount.',
                "payment_method_id" : "bpPkBddxjB2",
                "type" : "jenius",
                "callback_url" : "'.$callback_url.'",
                "expired_date" : "'.$expired_date.'",
                "description" : "'.$description.'",
                "increase_total_from_unique_code" : 1,
                "customer": {
                    "name": "'.$user->data->display_name.'",
                    "email": "'.$user->data->user_email.'",
                    "phone": "'.get_field('phone_number', 'user_' . $userId ).'"
                },
                "items":[
                    {
                        "name":"'.$iuranName.'",
                        "qty":'.$qty.',
                        "price":'.$amount.',
                        "sku":"SKU-'.$iuranID.'",
                        "image_url":"https://via.placeholder.com/150"
                    }
                ],
                "with_unique_code" : 0,
                "start_unique_code" : 10,
                "end_unique_code" :20,
                "unique_code" : 0
            }',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiJucWllNHN3OGxsdyIsImp0aSI6Ijc1OTRiMmRkMzBhNzhjZjE5ODVkYzdiOGY1YzAzNWU0Yzc0YmE0MzYyNDViY2I5OWQxOGE5ZTAyYzgwMGE2OTBjYmFiYjBlMWE4OTljNTcyIiwiaWF0IjoxNjY1NDU4MTQ5Ljg1MTEyOCwibmJmIjoxNjY1NDU4MTQ5Ljg1MTEzMSwiZXhwIjoxNjk2OTk0MTQ5Ljg0ODg3NCwic3ViIjoiMjU1ODAiLCJzY29wZXMiOlsiYXBpIl19.ZGw67Bn23w-vDxhkv90Nr1dhTx2NLma9eV5b5BWbclgrq-zt7NgrhwMJeueejQhnr5ZByy6I8EQpTR-C-_k3yyCthruD1IUxD0F_geVcXYTx-CNJBtEKu_X2ztdi1lEzSkK4bQHWMTYj1j2p8dSIW3_IsRFpJaSUTQe_m6iGHb3c2m7SmyERgNJjsHfuURPIP-ooxDVCMvCzMMxdDJx0e1dDXW4vE1MdSvC21YEZBBL24YtgVzMqjlhXFvfM7Krer4t5mpAzD-odDfPkKUAoxAepfx048y2vCF_bgukXuDS8Rwk2zxFfGoV2-ra0KPR7MS-AVaUx3DJFAjV4ZD_wUKI3ql-79cs6bHAilW7GuEaiBxSwoBpOQtMcMOEoD5gK32rd6XaUCsJ98mHhAncnXhHKmCH_C-fxVXmfOyLyhiJoN5iwIduSbNC-Lfd6QZ3DtDkgLSd-tlylfNgCJVQjYZ9mSM2sTHQII-z00U0VCJM7thfp1RbVPgqSISY4d5Y9LkG_J3OYkgLkxNl9UOFsDIkKlHZC8STtlOC4LkutBhOuVbvciiRH2rKOFQEfkoD72Lhs0kh0eA6dorDCECI2QHmLpmX7ISnUbZRTbX6ActeycCU3otCfNMn-rtfylxBcph5mItDbuBnrXc7aOiDvdNjU7nxvGe_qGQWDAuu2SkY',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);

        if ($response->success == true) {
            $new_post = array(
                'post_title' => $invoice,
                'post_status' => 'publish',
                'post_date' => date('Y-m-d H:i:s'),
                //'post_author' => $user_ID,
                'post_type' => 'transaksiv2',
                //'post_category' => array(0)
            );

            $post_id = wp_insert_post($new_post);

            update_field( 'trx_id', $response->data->trx_id, $post_id );//trxid
            update_field( 'link-pembayaran', $response->data->payment_link, $post_id );
            update_field( 'status', $response->data->status, $post_id );
            update_field( 'total', $response->data->total, $post_id );
            update_field( 'jumlah_bulan', $response->data->items[0]->qty, $post_id );
            update_field( 'tanggal_transaksi', date("Y-m-d H:i:s", time()), $post_id );
            update_field( 'tanggal_kadaluarsa', $expired_date, $post_id );
            update_field( 'rumah', $rumahID, $post_id );
            update_field( 'iuran', $iuranID, $post_id );
            update_field( 'user', $userId, $post_id );
            update_field( 'keterangan', $description, $post_id );
            update_field( 'catatan', $notes, $post_id );


            //set bulan bayar
            $startMouth = $request["startM"];
            $dlast = strtotime($startMouth);
            $datelast = date('F Y', $dlast);

            $endMouth = $request["endM"];;
            $dEnd = strtotime($endMouth);
            $dateEnd = date('F Y', $dEnd);

            //list bulan belum bayar
            $start    = new DateTime($datelast);
            $start->modify('first day of this month');
            $end      = new DateTime($dateEnd);
            $end->modify('first day of next month');
            $interval = new DateInterval('P1M');
            $period   = new DatePeriod($start, $interval, $end);

            $per = [];
            $terms_bln = [];
            $terms_thn = [];
            $i= 0;
            $valueid = [];

            foreach ($period as $dt) {
                $i++;
               
                array_push($per, $dt->format("F | Y"));

                // new post bulan iuran
                $check_title = get_page_by_title($dt->format("F Y"), 'OBJECT', 'bulan-iuran');
            
                $new_bln_iu = array(
                    'post_title' => $dt->format("F Y"),
                    'post_status' => 'publish',
                    'post_date' => date('Y-m-d H:i:s'),
                    //'post_author' => $user_ID,
                    'post_type' => 'bulan-iuran',
                    //'post_category' => array(0)
                );

                if(empty($check_title)) {
                    $bln_iu_id = wp_insert_post($new_bln_iu);
                    update_field( 'bulan_iu', $dt->format("F"), $bln_iu_id );
                    update_field( 'tahun_iu', $dt->format("Y"), $bln_iu_id );

                    array_push($valueid, $bln_iu_id);
                    
                } else {

                    $bln_args = array(
                        'post_type' => 'bulan-iuran',
                        'post_status' => 'publish',
                        's' => $dt->format("F Y"),
                    );
            
                    $bln_posts = get_posts($bln_args);

                    foreach ( $bln_posts as $post ) {
                        $bln_id =  $post->ID;
                        array_push($valueid, $bln_id);
                    }

                }
                 

            }
            update_field('bulan_bayar', $valueid, $post_id);

            $listbln = implode("\n",$per);
            update_field( 'bulan_tahun', $listbln, $post_id );
        }

        return rest_ensure_response( [
            'status' =>  $response->success,
            'message'   => 'success',
            'data' => $response->data,
        ] );

    } else {
        return rest_ensure_response( [
            'status' => false,
            'message'   => 'Invalid token'
        ] );
    }
}

function moota_callback ($request) {
    $body = $request->get_params();
    $invoice = $body['invoice_number'];
    $status = $body['status'];
    $tanggal_bayar = $body['payment_at'];

    if ($invoice) {
        $args = array(
            'post_type' => 'transaksiv2',
            'posts_per_page' => 1,
            'post_status' => 'publish',
            's' => $invoice,

        );

        $posts = get_posts($args);
        foreach ( $posts as $post ) {
            $post_id =  $post->ID;
            $userID = get_field( 'user', $post->ID );
        }

        update_field( 'status',$status, $post_id );
        update_field( 'tanggal_bayar',$tanggal_bayar, $post_id );

        if ($status == 'success') {

            $argsiu = array(
                'post_type' => 'user-iuran',
                'posts_per_page' => 1,
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => 'creator',
                        'value' => $userID,
                       'compare' => 'LIKE'
                    )
                )

            );

            $postsiu = get_posts($argsiu);
            $post_iuID = '';
            foreach ( $postsiu as $post ) {
                $post_iuID =  $post->ID;
            }

            $bulan_bayar = get_field( 'bulan_tahun', $post_id );

            $listbln = explode("\n",$bulan_bayar);

            $blnarray = [];
            $thnarray = [];
            //$i= 0;
            foreach ($listbln as $dt) {
                //$i++;
                //array_push($per, $dt->format("F | Y"));
                //
                $blnI = substr($dt, 0, strpos($dt, " | "));;
                $thnI = explode(' | ', $dt)[1];
                array_push($blnarray, $blnI);
                array_push($thnarray, $thnI);
            }

            $setbln =  end($blnarray);
            $setthn =  end($thnarray);

            update_field('status_ipl', array('ipl_terbayar'=>$bulan_bayar), $post_iuID);
            update_field('status_iuran', array('bulan'=>$setbln), $post_iuID);
            update_field('status_iuran', array('tahun'=>$setthn), $post_iuID);

            ///
            $lastIPL = $setbln.' '.$setthn;
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
                array_push($per, $dt->format("F Y"));

            }

            $listbln = implode("\n",$per);

            update_field('status_ipl', array('ipl_belum_bayar'=>$listbln), $post_iuID);
            //num ipl lum bayar
            update_field('status_iuran', array('jumlah_ipl_belum_bayar'=>$i), $post_iuID);

            ///
            ///
            //list ipl
            $ipl = get_field('status_ipl', $post_iuID)['ipl_terbayar'];

            $value = nl2br ($ipl." \n ".$bulan_bayar);
            update_field('status_ipl', array('ipl_terbayar'=>$value), $post_iuID);
        }

    }


    return rest_ensure_response( [
        'status' =>  true,
        'message'   => 'success',
        'data' =>   $body,
    ] );


}

function transaction_moota($req) {
    $currentuserid_fromjwt = get_current_user_id();

    if ($currentuserid_fromjwt != 0) {
        $user = get_user_by('id', $currentuserid_fromjwt);
        $userId = $user->ID;

        $invoice = $req['stringvar'];
        $limit = $req['limit'];

        if($invoice) {
            $args = array(
                'post_type' => 'transaksiv2',
                'posts_per_page' => 1,
                'post_status' => 'publish',
                's' => $invoice,
                'meta_query' => array(
                    array(
                        'key' => 'user',
                        'value' => $userId,
                       // 'compare' => 'LIKE'
                    )
                )
            );
        } else if($limit) {
            $args = array(
                'post_type' => 'transaksiv2',
                'posts_per_page' => $limit,
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => 'user',
                        'value' => $userId,
                        //'compare' => 'LIKE'
                    )
                )
            );
        } else {
            $args = array(
                'post_type' => 'transaksiv2',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => 'user',
                        'value' => $userId,
                       // 'compare' => 'LIKE'
                    )
                )
            );
        }


        $posts = get_posts($args);

        if ( empty( $posts ) ) {
            return rest_ensure_response( [
                'status' => false,
                'message'   => $invoice
            ] );
        }
        $ins_data = array();
        $i = 0;

        foreach ( $posts as $post ) {
            $bulan_bayar_arr = [];
            $bulan_bayar = get_field('bulan_bayar', $post->ID);
            
            foreach ($bulan_bayar as $val) {
                array_push($bulan_bayar_arr, get_the_title( $val->ID) );
            }
            $ins_data[] = array(  // you can ad anything here and as many as you want
                'ID' => $post->ID,
                'invoice' => $post->post_title,
                'iuran_name' => get_field( 'iuran', $post->ID)->post_title,
                'status' =>  get_field( 'status', $post->ID),
                'jumlah_bulan' => get_field('jumlah_bulan',$post->ID),
                'bulan_bayar' => $bulan_bayar_arr,
                'tgl_buat' => get_field( 'tanggal_transaksi', $post->ID),
                'tgl_kadaluarsa' => get_field( 'tanggal_kadaluarsa', $post->ID),
                'tanggal_bayar' => get_field( 'tanggal_bayar', $post->ID),
                'nominal' => get_field( 'total', $post->ID),
                'description' => get_field( 'keterangan', $post->ID),
                'notes' => get_field( 'catatan', $post->ID),

            );
            $i++;
        }


        // Returned Data
        return rest_ensure_response( [
            'status' => true,
            'message'   => 'success',
            'data' => $ins_data,
        ] );

    } else {
        return rest_ensure_response( [
            'status' => false,
            'message'   => 'Invalid token'
        ] );
    }



}

function report($request) {
    //$currentuserid_fromjwt = get_current_user_id();
    $ID = $request['id'];

    $args = array(
        'post_type' => 'transaksiv2',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'iuran',
                'value' => $ID,
                //'compare' => 'LIKE'
            )
        ),
        'meta_query' => array(
            array(
                'key' => 'status',
                'value' => 'success',
                'compare' => 'LIKE'
            )
        )
        
    );

    $posts = get_posts($args);

    if ( empty( $posts ) ) {
        return rest_ensure_response( [
            'status' => false,
            'message'   => 'failed'
        ] );
    } else {
        $sum = 0;
        $name_iuran = '';
        foreach ( $posts as $post ) {
            $bulanarray =   explode("\n",  get_field( 'bulan_tahun', $post->ID));
            $ipltemp = [];
            foreach ($bulanarray as $val) {
                array_push($ipltemp, $val);
            }
            $trx[] = array(  // you can ad anything here and as many as you want
                'ID' => $post->ID,
                'invoice' => $post->post_title,
                //'iuran_name' => get_field( 'iuran', $post->ID)->post_title,
                'status' =>  get_field( 'status', $post->ID),
                'nominal' => intval(get_field( 'total', $post->ID)),
                'tgl_trx' => get_field( 'tanggal_transaksi', $post->ID),
                'bulan' => $ipltemp,
            );
            $sum+= get_field( 'total', $post->ID);
            $name_iuran =get_field( 'iuran', $post->ID)->post_title;

        }


        $ins_data [] = array(
            'name' => $name_iuran,
            'total' => $sum,
            'transaction' => $trx,

        );

        return rest_ensure_response( [
            'status' => true,
            'message'   => 'success',
            'data' => $ins_data,
        ] );
    }
}

function report_iuran_iuran($request) {
    $currentuserid_fromjwt = get_current_user_id();
    $ID = $request['id'];
    
    if ($currentuserid_fromjwt != 0) {
        $user = get_user_by('id', $currentuserid_fromjwt);
        $userId = $user->ID;

        $args = array(
            'post_type' => 'transaksiv2',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby'   => array(
                'date' =>'ASC',
                /*Other params*/
            ),
            'meta_query' => array(
                'relation'      => 'AND',
                array(
                    'key' => 'iuran',
                    'value' => $ID,
                    //'compare' => 'LIKE'
                ),
                array(
                    'key' => 'status',
                    'value' => 'success',
                    'compare' => '='
                ),
                array(
                    'key' => 'user',
                    'value' => $userID,
                    'compare' => 'LIKE'
                )
            )
        );
    
        $posts = get_posts($args);
    
        if ( empty( $posts ) ) {
            return rest_ensure_response( [
                'status' => false,
                'message'   => 'failed'
            ] );
        } else {
            $name_iuran = '';
            foreach ( $posts as $post ) {
                $bulanarray =   explode("\n",  get_field( 'bulan_tahun', $post->ID));
                $ipltemp = [];
    
                foreach ($bulanarray as $val) {
                    $bulan = explode("|",  $val);
                    array_push($ipltemp, $val);
                }

                $iuranBayar = [];
                $bulan_bayar = get_field('bulan_bayar', $post->ID);

                foreach( $bulan_bayar as $post_bulan) {
                    //array_push($iuranBayar, 'Bulan');
                    $iuranBayar[] = array('bulan' => get_field( 'bulan_iu', $post_bulan->ID), 'tahun' => get_field( 'tahun_iu', $post_bulan->ID), 'id_trx' => $post->post_title,  'tgl_trx' => get_field( 'tanggal_transaksi', $post->ID));
                }
    
                $trx[] = array(  // you can ad anything here and as many as you want
                    'ID' => $post->ID,
                    'invoice' => $post->post_title,
                    'iuran_name' => get_field( 'iuran', $post->ID)->post_title,
                    'status' =>  get_field( 'status', $post->ID),
                    'nominal' => intval(get_field( 'total', $post->ID)),
                    'tgl_trx' => get_field( 'tanggal_transaksi', $post->ID),
                    'iuran' => $iuranBayar,
                );
    
                $bln[] = $iuranBayar;
                
                $name_iuran =get_field( 'iuran', $post->ID)->post_title;
    
            }
    
           
            $blnMerged = array_merge([], ...$bln);
    
            $ins_data [] = array(
                'user' => $userId,
                'name' => $name_iuran,
                'iuran' =>$iuranBayar,
               // 'transaction' => $trx,
    
            );
    
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