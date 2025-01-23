<?php
/**
 * Plugin Name: Student Onboarding Plugin
 * Description: A WordPress plugin to onboard students via a secure REST API.
 * Version: 1.0.0
 * Text Domain: options-plugin
 * Author: Freeman Odedipe
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

//Checking if class already exists
if(!class_exists('Student_Onboarding_Plugin')){

    // Load plugin functionality
    class Student_Onboarding_Plugin {

        public function __construct() {
            add_action('rest_api_init', [$this, 'register_api_endpoints']);
        }

        /**
         * Register REST API Endpoint
         */
        public function register_api_endpoints() {
            register_rest_route('Student-Onboarding-Plugin/v1', '/students', [
                'methods'  => 'POST',
                'callback' => [$this, 'handle_student_onboarding'],
                'permission_callback' => [$this, 'authenticate_request'],
            ]);
        }

        /**
         * Handle the student onboarding process
         */
        public function handle_student_onboarding($request) {
            $parameters = $request->get_json_params();

            // Fire pre-validation hook
            do_action('Student_Onboarding_Plugin_pre_validate_student', $parameters);

            // Validate input
            if (!$this->validate_student_data($parameters)) {
                return new WP_Error('invalid_data', 'Invalid student data', ['status' => 400]);
            }

            // Fire pre-create user hook
            do_action('Student_Onboarding_Plugin_pre_create_student', $parameters);

            // Check if user exists
            if (email_exists($parameters['email'])) {
                return new WP_Error('user_exists', 'User already exists', ['status' => 400]);
            }

            // Create new user
            $user_id = wp_create_user($parameters['email'], wp_generate_password(), $parameters['email']);
            wp_update_user(['ID' => $user_id, 'display_name' => $parameters['student_name']]);
            wp_set_current_user($user_id);

            // Fire post-create user hook
            do_action('Student_Onboarding_Plugin_post_create_student', $user_id, $parameters);

            // Send welcome email
            $this->send_welcome_email($user_id, $parameters);

            return new WP_REST_Response([
                'success' => true,
                'user_id' => $user_id,
                'message' => 'Student successfully registered',
                'email_status' => 'sent'
            ], 201);
        }

        /**
         * Validate student data
         */
        private function validate_student_data($data) {
            if (empty($data['student_name']) || empty($data['email']) || empty($data['course'])) {
                return false;
            }
            if (!is_email($data['email'])) {
                return false;
            }
            return true;
        }

        /**
         * Send welcome email
         */
        private function send_welcome_email($user_id, $data) {
            $email_content = apply_filters('Student_Onboarding_Plugin_welcome_email_content', 'Welcome ' . $data['student_name'] . ', you are enrolled in ' . $data['course'], $user_id);
            wp_mail($data['email'], 'Welcome to the Course', $email_content);

            do_action('Student_Onboarding_Plugin_post_send_welcome_email', $user_id, true);
        }

        /**
         * Authenticate API request
         */
        public function authenticate_request($request) {
            $headers = apache_request_headers();
            $auth = isset($headers['Authorization']) ? $headers['Authorization'] : '';

            list($username, $password) = explode(':', base64_decode(str_replace('Basic ', '', $auth)));
            
            if ($username === getenv('API_USERNAME') && $password === getenv('API_PASSWORD')) {
                return true;
            }

            return new WP_Error('unauthorized', 'Invalid authentication credentials', ['status' => 401]);
        }
    }

    // Initialize the plugin
    new Student_Onboarding_Plugin();
}
