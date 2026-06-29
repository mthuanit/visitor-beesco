<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\LocaleMiddleware::class,
        ]);
        $middleware->alias([
            'api.key' => \App\Http\Middleware\ApiKeyMiddleware::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (Throwable $e) {
            $token = config('services.telegram.bot_token');
            $chatId = config('services.telegram.chat_id');

            if ($token && $chatId) {
                try {
                    $url = request()->fullUrl();
                    $message = "🚨 *LỖI HỆ THỐNG VISITOR-SERVER* 🚨\n\n";
                    $message .= "📍 *URL:* `{$url}`\n";
                    $message .= "⚠️ *Error:* `{$e->getMessage()}`\n";
                    $message .= "📁 *File:* `{$e->getFile()}:{$e->getLine()}`";

                    \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                        'chat_id' => $chatId,
                        'text' => $message,
                        'parse_mode' => 'Markdown',
                    ]);
                } catch (\Throwable $ex) {
                    // Ignore telegram send errors to prevent infinite loops
                }
            }
        });
    })->create();
