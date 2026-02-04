<?php

namespace App\Controllers;

use App\Libraries\Polar;

/**
 * Polar.sh Webhook Controller
 *
 * Handles incoming webhook events from Polar.sh for subscription management.
 * This is a public endpoint - no authentication required.
 * Security is handled via webhook signature verification.
 */
class Polar_webhook extends App_Controller {

    private $polar;

    function __construct() {
        parent::__construct();
        $this->polar = new Polar();
    }

    /**
     * Main webhook endpoint
     * URL: /polar_webhook or /polar_webhook/index
     */
    function index() {
        // Only accept POST requests
        if ($this->request->getMethod() !== 'post') {
            return $this->response->setStatusCode(405)->setBody('Method not allowed');
        }

        // Get raw payload
        $payload = file_get_contents('php://input');

        if (empty($payload)) {
            log_message('error', 'Polar webhook: Empty payload');
            return $this->response->setStatusCode(400)->setBody('Empty payload');
        }

        // Get signature header
        $signature = $this->request->getHeaderLine('Webhook-Signature');

        if (empty($signature)) {
            // Try alternative header names
            $signature = $this->request->getHeaderLine('X-Webhook-Signature');
        }

        // Verify signature
        if (!$this->polar->verify_webhook_signature($payload, $signature)) {
            log_message('error', 'Polar webhook: Invalid signature');
            return $this->response->setStatusCode(401)->setBody('Invalid signature');
        }

        // Parse the event
        $event = json_decode($payload, true);

        if (!$event) {
            log_message('error', 'Polar webhook: Invalid JSON');
            return $this->response->setStatusCode(400)->setBody('Invalid JSON');
        }

        // Log the event type for debugging
        $event_type = $event['type'] ?? 'unknown';
        log_message('info', "Polar webhook received: {$event_type}");

        // Process the event
        $result = $this->polar->process_webhook_event($event);

        if ($result) {
            return $this->response->setStatusCode(200)->setBody('OK');
        } else {
            // Return 200 anyway to prevent Polar from retrying
            // The error is already logged
            return $this->response->setStatusCode(200)->setBody('Processed with errors');
        }
    }

    /**
     * Health check endpoint
     * URL: /polar_webhook/health
     */
    function health() {
        return $this->response
            ->setStatusCode(200)
            ->setJSON(array(
                'status' => 'ok',
                'service' => 'polar_webhook',
                'configured' => $this->polar->is_configured()
            ));
    }

    /**
     * Test endpoint for development
     * URL: /polar_webhook/test
     *
     * Only works in development environment
     */
    function test() {
        if (ENVIRONMENT !== 'development') {
            return $this->response->setStatusCode(404);
        }

        // Simulate a subscription.active event
        $test_event = array(
            'type' => 'subscription.active',
            'data' => array(
                'id' => 'test_sub_' . time(),
                'customer_id' => 'test_cust_' . time(),
                'status' => 'active',
                'current_period_start' => date('c'),
                'current_period_end' => date('c', strtotime('+1 month')),
                'metadata' => array(
                    'user_id' => $this->request->getGet('user_id') ?: '1'
                ),
                'product_name' => 'AI Assistant Monthly'
            )
        );

        $result = $this->polar->process_webhook_event($test_event);

        return $this->response->setJSON(array(
            'success' => $result,
            'event' => $test_event
        ));
    }
}
