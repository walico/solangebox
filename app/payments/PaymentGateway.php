<?php

declare(strict_types=1);

interface PaymentGateway
{
    public function createCheckout(array $order): string;

    public function handleReturn(): array;
}

function payment_gateway(): PaymentGateway
{
    $provider = setting('payment_mode', config('payment.provider', 'sandbox'));
    return match ($provider) {
        default => new SandboxGateway(),
    };
}
