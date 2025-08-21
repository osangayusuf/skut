<?php

namespace App\Services;

use App\Models\Order;
use Stripe\StripeClient;

class PaymentService
{
    protected StripeClient $stripeClient;

    protected string $currency = 'CAD'; // Canadian Dollar

    public function __construct()
    {
        $this->stripeClient = new StripeClient(config('services.stripe.secret'));
    }

    public function createOrderCheckoutSession(Order $orderDetails): array
    {
        try {
            $scooter = $orderDetails->scooter;
            $session = $this->stripeClient->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => $this->currency,
                            'product_data' => [
                                'name' => $scooter->name,
                            ],
                            'unit_amount' => $orderDetails['total_price'] * 100, // Convert to cents
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'metadata' => [
                    'order_id' => $orderDetails->id,
                    'scooter_id' => $scooter->id,
                ],
                'customer_email' => $orderDetails->user->email,
                'client_reference_id' => $orderDetails->id,
                'success_url' => config('frontend.order.success'),
                'cancel_url' => config('frontend.order.cancel'),
            ]);

            return [
                'sessionId' => $session->id,
                'url' => $session->url,
            ];
        } catch (\Exception $e) {
            throw new \Exception('Failed to create checkout session: '.$e->getMessage());
        }
    }
}
