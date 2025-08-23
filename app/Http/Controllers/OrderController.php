<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\TransactionResource;
use App\Models\Order;
use App\Models\Scooter;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $orders = Order::query();
            if (!auth()->user()->isAdmin()) {
                $orders = Order::where('user_id', auth()->id())->with(['user', 'scooter']);
            }

            if ($request->has('status')) {
                $orders = $orders->where('status', $request->input('status'));
            }

            if ($request->has('scooter_id')) {
                $orders = $orders->where('scooter_id', $request->input('scooter_id'));
            }

            return response()->json([
                'message' => 'Orders retrieved successfully.',
                'data' => OrderResource::collection($orders->get()),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve orders.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                // Check if the user is an admin
                if (auth()->user()->isAdmin()) {
                    return response()->json([
                        'message' => 'Unauthorized to create orders as an admin.',
                    ], 403);
                }

                // Check if the scooter is available
                $scooter = Scooter::findOrFail($request->scooter_id);
                if (!$scooter->isAvailable()) {
                    return response()->json([
                        'message' => 'Scooter is not available for booking currently.',
                    ], 400);
                }
                $user = auth()->user();
                $validated = $request->validated();
                $validated['booking_date'] = Carbon::parse($validated['start_time'])->toDateString();

                $validated['end_time'] = Carbon::create($validated['start_time'])->addHours($validated['duration'])->format('Y-m-d H:i:s');
                $validated['total_price'] = Scooter::findOrFail($validated['scooter_id'])->pricing[$validated['duration']];
               
                $order = $user->orders()->create($validated);
                $paymentDetails = $this->paymentService->createOrderCheckoutSession($order);
                $order->update(['stripe_session_id' => $paymentDetails['sessionId']]);

                $transaction = $user->transactions()->create([
                    'purpose' => 'order_payment',
                    'order_id' => $order->id,
                    'transaction_amount' => $order->total_price,
                    'stripe_session_id' => $paymentDetails['sessionId'],
                    'status' => 'pending',
                    'description' => "Payment for order #{$order->id}",
                ]);

                // Optionally, you can send a notification to the user about the order creation
                return response()->json([
                    'message' => 'Order created successfully.',
                    'data' => [
                        'order' => new OrderResource($order),
                        'transaction' => new TransactionResource($transaction),
                    ],
                    'payment_url' => $paymentDetails['url'],
                ]);
            });

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        try {
            if (auth()->user()->isAdmin() || auth()->user()->id === $order->user_id) {
                return response()->json([
                    'message' => 'Order retrieved successfully.',
                    'data' => new OrderResource($order),
                ]);
            } else {
                return response()->json([
                    'message' => 'Unauthorized to view this order.',
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        try {
            if (auth()->user()->isAdmin()) {
                $order->delete();

                return response()->json([
                    'message' => 'Order deleted successfully.',
                ]);
            } else {
                return response()->json([
                    'message' => 'Unauthorized to delete this order.',
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
