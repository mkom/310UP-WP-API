<?php
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
            $array_data['address'] = get_field('address', 'user_' . $userId );
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
            $array_data['address'] = get_field('address', 'user_' . $userId );
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

        return rest_ensure_response( [
            'status' => true,
            'login' => 1,
            'id' => $user->ID,
            'nonce' => $nonce,
            'resident' => get_field('address', 'user_' . $user->ID ),
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
                        'name' =>$user->data->display_name,
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