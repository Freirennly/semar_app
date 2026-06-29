<?php

namespace App\Traits;

trait SafeHookTrait
{
    /**
     * Execute a callback safely without letting exceptions bubble up and interrupt the main flow.
     *
     * @param callable $callback
     * @return void
     */
    protected function safeHook(callable $callback): void
    {
        try {
            $callback();
        } catch (\Throwable $e) {
            // NO INTERRUPTION TO MAIN FLOW
            // Optional: minimal silent log in future phase
        }
    }
}
