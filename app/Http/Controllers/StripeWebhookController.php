<?php

namespace App\Http\Controllers;

use App\Mail\RideConfirmationMail;
use Carbon\Carbon;
use Hamcrest\Number\OrderingComparison;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                $webhookSecret
            );
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe Webhook Signature Verification Failed: '.$e->getMessage());

            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe Webhook Invalid Payload: '.$e->getMessage());

            return response()->json(['error' => 'Invalid payload'], 400);
        }

        Log::info('Stripe Webhook Event Received: '.$event->type, ['event' => $event->jsonSerialize()]);

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $this->handleCheckoutSessionCompleted($session);
                break;

            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                Log::info('Payment Intent Succeeded: '.$paymentIntent->id);
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                Log::warning('Payment Intent Failed: '.$paymentIntent->id.' - '.$paymentIntent->last_payment_error->message);
                break;

            case 'charge.refunded':
                $charge = $event->data->object;
                Log::info('Charge Refunded: '.$charge->id);
                break;

            default:
                Log::warning('Received unexpected Stripe webhook event type: '.$event->type);
                break;
        }

        return response()->json(['status' => 'success'], 200);
    }

    protected function handleCheckoutSessionCompleted($session)
    {
        Log::info('Checkout Session Completed:', [
            'session_id' => $session->id,
            'customer_email' => $session->customer_details->email ?? 'N/A',
            'amount_total' => $session->amount_total,
            'currency' => $session->currency,
            'client_reference_id' => $session->client_reference_id,
            'metadata' => $session->metadata->toArray() ?? [],
        ]);

        if (isset($session->client_reference_id)) {
            $order = \App\Models\Order::where('id', $session->client_reference_id)->first();

            if ($order) {
                if ($order->status == 'pending') {
                    $order->update(['status' => 'paid']);
                    $user = $order->user;
                    Mail::to($user->email)->queue( new RideConfirmationMail(
                        $user->firstname,
                        $order->scooter->name,
                        $order->duration,
                        $order->booking_date,
                        Carbon::parse($order->start_time)->format('h:i A'),
                        $order->total_price
                    ));
                    $transaction = $order->transaction;
                    Log::info('Order '.$order->id.' status updated to paid.');
                    if ($transaction) {
                        Log::info('Updating existing transaction for order '.$order->id);
                        $transaction->status = 'successful';
                        $transaction->save();
                    } else {
                        Log::warning('No transaction found for order '.$order->id.'. Creating a new transaction.');
                        $order->transaction()->create([
                            'user_id' => $order->user_id,
                            'purpose' => 'Order Payment',
                            'order_id' => $order->id,
                            'transaction_amount' => $order->total_price,
                            'currency' => 'usd',
                            'provider' => 'stripe',
                            'stripe_session_id' => $session->id,
                            'status' => 'successful',
                            'description' => 'Payment for order #'.$order->id,
                        ]);
                    }
                    Log::info('Order '.$order->id.' marked as paid.');
                } else {
                    Log::warning('Checkout session completed for order '.$order->id.', but order was not in pending_payment status. Current status: '.$order->status);
                }
            } else {
                Log::error('No order found with client_reference_id: '.$session->client_reference_id);
            }
        } else {
            Log::error('Checkout session completed, but no client_reference_id found. Session ID: '.$session->id);
        }
    }
}
