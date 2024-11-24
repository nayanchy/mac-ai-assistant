<?php

/**
 * Plugin Name: Mac AI Assistant
 * Description: A WordPress plugin to integrate Gemini AI.
 * Version: 1.0
 * Author: Nayan Chowdhury
 * Author URI: https://nayanchowdhury.com
 */

// Enqueue plugin styles
function mac_ai_enqueue_styles()
{
    wp_enqueue_style(
        'mac-ai-assistant-style',
        plugin_dir_url(__FILE__) . 'assets/css/style.css',
        [],
        '1.0'
    );
}
add_action('wp_enqueue_scripts', 'mac_ai_enqueue_styles');

// Enqueue scripts
function mac_ai_enqueue_scripts()
{
    wp_enqueue_script(
        'mac-ai-script',
        plugin_dir_url(__FILE__) . 'assets/js/script.js',
        ['jquery'],
        '1.0',
        true
    );

    wp_localize_script('mac-ai-script', 'mac_ai_data', [
        'questions' => json_encode(mac_ai_get_questions()),
        'ajax_url' => admin_url('admin-ajax.php'),
        'api_key' => 'AIzaSyBV5vjOb2RBBY2aKRx70LvYdnqCc1ec-Mw',
    ]);
}
add_action('wp_enqueue_scripts', 'mac_ai_enqueue_scripts');


// Register shortcode to display the form
function mac_ai_render_form()
{
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/form.php';
    return ob_get_clean();
}
add_shortcode('mac_ai_form', 'mac_ai_render_form');

// Define the questions
function mac_ai_get_questions()
{
    return [
        'character_name' => 'Character Name',
        'age' => 'Age',
        'sexual_preferences' => [
            'label' => 'Sexual Preferences',
            'options' => [
                'Heterosexual',
                'Gay/Lesbian',
                'Bisexual',
                'Pansexual',
                'Asexual',
                'Other',
            ],
        ],
        'relationship_status' => 'Relationship Status',
        'gender' => [
            'label' => 'Gender',
            'options' => ['Male', 'Female', 'Other'],
        ],
        'race_species' => 'Race/Species',
        'birthplace' => 'Birthplace',
        'current_residence' => 'Current Residence',
        'occupation' => 'Occupation',
        'education' => 'Level of Education',
        'social_class' => 'Social Class',
        'moral_beliefs' => 'Moral/Ethical Beliefs',
        'religious_beliefs' => 'Religious Beliefs',
    ];
}

// Ajax for API response
function mac_ai_generate_answer()
{
    $question = sanitize_text_field($_POST['question']);
    $api_key = sanitize_text_field($_POST['api_key']);

    // Gemini API Endpoint
    $api_url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=$api_key";

    // Request payload, formatting based on the cURL provided
    $request_payload = json_encode([
        'contents' => [
            [
                'parts' => [
                    ['text' => $question] // The question will be sent as the text part
                ]
            ]
        ]
    ]);

    // Initialize cURL
    $curl = curl_init($api_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
    ]);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $request_payload);

    // Execute the cURL request
    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    // Handle the API response
    if ($http_code === 200 && $response) {
        $data = json_decode($response, true);

        // Check if the response has the generated content
        if (isset($data['contents'][0]['parts'][0]['text'])) {
            $generated_answer = $data['contents'][0]['parts'][0]['text'];
            wp_send_json_success(['response' => $generated_answer]);
        } else {
            wp_send_json_error(['error' => 'Invalid response format from API.']);
        }
    } else {
        wp_send_json_error(['error' => 'Failed to fetch AI response.']);
    }
}
add_action('wp_ajax_mac_ai_generate_answer', 'mac_ai_generate_answer');
add_action('wp_ajax_nopriv_mac_ai_generate_answer', 'mac_ai_generate_answer');
