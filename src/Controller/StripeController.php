<?php
declare(strict_types=1);

namespace App\Controller;

use Exception;
use JornaticCore\Service\StripeService;

/**
 * Controlador Stripe
 *
 * Gestión de datos en tiempo real desde Stripe API
 */
class StripeController extends AppController
{
    /**
     * Servicio de Stripe
     *
     * @var \JornaticCore\Service\StripeService
     */
    protected StripeService $stripeService;

    /**
     * Función de inicialización
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        // Inicializar servicio de Stripe
        $this->stripeService = new StripeService();

        // Cargar componente de logging
        $this->loadComponent('Logging');

        // Skip authorization para todas las acciones (por ahora)
        $this->Authorization->skipAuthorization();
    }

    /**
     * Función para obtener detalles de suscripción desde Stripe
     *
     * @param string $subscriptionId ID de la suscripción en Stripe
     * @return \Cake\Http\Response
     */
    public function subscriptionDetails(string $subscriptionId)
    {
        $this->request->allowMethod(['get']);

        try {
            // Verificar que Stripe esté configurado
            if (!$this->stripeService->isConfigured()) {
                throw new Exception('Stripe no está configurado correctamente');
            }

            // Obtener datos de la suscripción desde Stripe
            $stripeSubscription = $this->stripeService->getSubscription($subscriptionId);

            // Obtener datos del customer desde Stripe
            $stripeCustomer = null;
            if ($stripeSubscription->customer) {
                $stripeCustomer = $this->stripeService->getCustomer($stripeSubscription->customer);
            }

            // Obtener facturas recientes usando el cliente de Stripe
            $stripeClient = $this->stripeService->getStripeClient();
            $recentInvoices = $stripeClient->invoices->all([
                'subscription' => $subscriptionId,
                'limit' => 5,
                'expand' => ['data.payment_intent'],
            ]);

            // Obtener método de pago por defecto
            $defaultPaymentMethod = null;
            if ($stripeCustomer && $stripeCustomer->invoice_settings->default_payment_method) {
                $defaultPaymentMethod = $stripeClient->paymentMethods->retrieve(
                    $stripeCustomer->invoice_settings->default_payment_method,
                );
            }

            // Preparar datos de respuesta
            $responseData = [
                'success' => true,
                'subscription' => [
                    'id' => $stripeSubscription->id,
                    'status' => $stripeSubscription->status,
                    'current_period_start' => $stripeSubscription->current_period_start,
                    'current_period_end' => $stripeSubscription->current_period_end,
                    'created' => $stripeSubscription->created,
                    'trial_end' => $stripeSubscription->trial_end,
                    'cancel_at_period_end' => $stripeSubscription->cancel_at_period_end,
                    'canceled_at' => $stripeSubscription->canceled_at,
                ],
                'customer' => $stripeCustomer ? [
                    'id' => $stripeCustomer->id,
                    'email' => $stripeCustomer->email,
                    'name' => $stripeCustomer->name,
                    'currency' => $stripeCustomer->currency,
                    'balance' => $stripeCustomer->balance,
                ] : null,
                'recent_invoices' => [],
                'payment_method' => $defaultPaymentMethod ? [
                    'type' => $defaultPaymentMethod->type,
                    'card' => $defaultPaymentMethod->card ?? null,
                ] : null,
            ];

            // Procesar facturas recientes
            foreach ($recentInvoices->data as $invoice) {
                $responseData['recent_invoices'][] = [
                    'id' => $invoice->id,
                    'amount_paid' => $invoice->amount_paid,
                    'amount_due' => $invoice->amount_due,
                    'currency' => $invoice->currency,
                    'status' => $invoice->status,
                    'created' => $invoice->created,
                    'period_start' => $invoice->period_start,
                    'period_end' => $invoice->period_end,
                    'hosted_invoice_url' => $invoice->hosted_invoice_url,
                    'invoice_pdf' => $invoice->invoice_pdf,
                ];
            }

            // Registrar acceso exitoso
            $this->Logging->logView('stripe_subscription', null);

            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode($responseData, JSON_PRETTY_PRINT));
        } catch (Exception $e) {
            // Registrar error
            $this->Logging->logAction('STRIPE_ERROR', false, 'subscription', null, [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
            ]);

            // Respuesta de error
            $errorData = [
                'success' => false,
                'error' => $e->getMessage(),
                'subscription_id' => $subscriptionId,
            ];

            return $this->response
                ->withType('application/json')
                ->withStatus(500)
                ->withStringBody(json_encode($errorData));
        }
    }
}
