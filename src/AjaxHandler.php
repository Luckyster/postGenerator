<?php
namespace PostGenerator;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class AjaxHandler {
    public static function generatePost() {
        check_ajax_referer('post_generator_nonce', 'nonce');

        $user_prompt = isset($_POST['prompt']) ? sanitize_text_field($_POST['prompt']) : '';
        if (empty($user_prompt)) {
            wp_send_json_error(['message' => 'Prompt cannot be empty.']);
        }

        $api_key = get_option('post_generator_api_key');
        if (empty($api_key)) {
            wp_send_json_error(['message' => 'API key is not configured.']);
        }

        $overall_prompt = "Generate a blog post. Use the following topic to create a concise post. Return only a title and content. Topic: " . $user_prompt;

        $client = new Client();
        $log_message = date("Y-m-d H:i:s") . " | Request prompt: " . $overall_prompt . "\n";
        self::logToFile($log_message);

        try {
            $response = $client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer ' . $api_key,
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'user', 'content' => $overall_prompt],
                    ],
                    'temperature' => 0.7
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            self::logToFile($body);
            $generated_text = isset($body['choices'][0]['message']['content'])
                ? trim($body['choices'][0]['message']['content'])
                : '';

            if (empty($generated_text)) {
                wp_send_json_error(['message' => 'Failed to generate text.']);
            }

            $parts = explode("\n", $generated_text, 2);
            $post_title = isset($parts[0]) ? trim($parts[0]) : 'Untitled';
            $post_title = preg_replace('/^\*\*Title:\s*|\*\*$/i', '', $post_title);
            $post_content = isset($parts[1]) ? trim($parts[1]) : $generated_text;
            $post_content = MarkdownConverter::toHtml($post_content);
            $post_id = wp_insert_post([
                'post_title'   => $post_title,
                'post_content' => $post_content,
                'post_status'  => 'publish'
            ]);

            if (is_wp_error($post_id)) {
                wp_send_json_error(['message' => 'Error creating post.']);
            }

            wp_send_json_success(['message' => 'Post created successfully!', 'post_id' => $post_id]);
        } catch (ClientException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 401) {
                Logger::logToFile("Unauthorized: Incorrect API key provided. Error: " . $e);
                wp_send_json_error(['message' => 'Unauthorized: Incorrect API key provided. Please check your API key in the Settings tab.']);
            }
            Logger::logToFile("Client error generating post: " . $e);
            wp_send_json_error(['message' => 'Client error: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            Logger::logToFile("Error generating post: " . $e);
            wp_send_json_error(['message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

}

