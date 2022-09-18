<?php
//set cookie login
add_filter('auth_cookie_expiration', function(){
    return 315360000;
});

function on_jwt_expire_token($exp){
    $days = 3652;
    $exp = time() + (86400 * $days);
    return $exp;
}
add_filter('jwt_auth_expire', 'on_jwt_expire_token',10,1);

add_filter('jwt_auth_token_before_dispatch', function( $data ){
    $data['user_id'] = get_current_user_id();
    return $data;
} ,10,1);

//random password
add_filter( 'random_password', 'my_random_password' );

function my_random_password() {
    $characters ='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $length = 30;
    $password = '';
    for( $i = 0; $i < $length; $i++ ) {
        $password .= substr( $characters , wp_rand( 0, strlen( $characters ) - 1 ), 1 );
    }
    return $password;
}

add_action( 'set_auth_cookie', function ( $cookie ) {
    $cookie_name = is_ssl() ? SECURE_AUTH_COOKIE : AUTH_COOKIE;
    $_COOKIE[ $cookie_name ] = $cookie;
} );

add_action( 'set_logged_in_cookie', function ( $cookie ) {
    $_COOKIE[ LOGGED_IN_COOKIE ] = $cookie;
} );

function create_random_code($length = 8, $in_params = [])
{
    $in_params['upper_case']        = isset($in_params['upper_case']) ? $in_params['upper_case'] : true;
    $in_params['lower_case']        = isset($in_params['lower_case']) ? $in_params['lower_case'] : true;
    $in_params['number']            = isset($in_params['number']) ? $in_params['number'] : true;
    $in_params['special_character'] = isset($in_params['special_character']) ? $in_params['special_character'] : false;

    $chars = '';
    if ($in_params['lower_case']) {
        $chars .= "abcdefghijklmnopqrstuvwxyz";
    }

    if ($in_params['upper_case']) {
        $chars .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    }

    if ($in_params['number']) {
        $chars .= "0123456789";
    }

    if ($in_params['special_character']) {
        $chars .= "!@#$%^&*()_-=+;:,.";
    }

    return substr(str_shuffle($chars), 0, $length);
}

add_action( 'init', 'cp_change_post_object' );
// Change dashboard Posts to News
function cp_change_post_object() {
    $get_post_type = get_post_type_object('post');
    $labels = $get_post_type->labels;
    $labels->name = 'News';
    $labels->singular_name = 'News';
    $labels->add_new = 'Add News';
    $labels->add_new_item = 'Add News';
    $labels->edit_item = 'Edit News';
    $labels->new_item = 'News';
    $labels->view_item = 'View News';
    $labels->search_items = 'Search News';
    $labels->not_found = 'No News found';
    $labels->not_found_in_trash = 'No News found in Trash';
    $labels->all_items = 'All News';
    $labels->menu_name = 'News';
    $labels->name_admin_bar = 'News';
}