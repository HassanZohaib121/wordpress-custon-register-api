<?php
/*
Plugin Name: Custom Registration API
Description: Provides a REST API endpoint for user registration.
Version: 1.0
Author: Hassan Zohaib
*/

// Register User API Endpoint
add_action( 'rest_api_init', function () {
    register_rest_route( 'wp/v2', '/register', array(
        'methods' => 'POST',
        'callback' => 'custom_register_user',
        'permission_callback' => function () {
            return current_user_can( 'edit_posts' );
        }
    ) );
});

// Callback function to register a new user
function custom_register_user( $request ) {
    $parameters = $request->get_params();
    
    $username = sanitize_text_field( $parameters['username'] );
    $email = sanitize_email( $parameters['email'] );
    $password = $parameters['password'];

    // Check if username or email already exists
    if ( username_exists( $username ) || email_exists( $email ) ) {
        return new WP_Error( 'registration_failed', __( 'Username or Email already exists.', 'text-domain' ), array( 'status' => 400 ) );
    }

    // Create user
    $user_id = wp_create_user( $username, $password, $email );

    if ( is_wp_error( $user_id ) ) {
        return new WP_Error( 'registration_failed', __( 'Failed to create user.', 'text-domain' ), array( 'status' => 500 ) );
    }

    // User created successfully
    return array(
        'message' => __( 'User registered successfully.', 'text-domain' ),
        'user_id' => $user_id
    );
}
