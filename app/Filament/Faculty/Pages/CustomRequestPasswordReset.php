<?php

declare(strict_types=1);

namespace App\Filament\Faculty\Pages;

use App\Domain\Faculty\Models\Faculty;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Notifications\Auth\ResetPassword as ResetPasswordNotification;
use Filament\Notifications\Notification;

/**
 * @property Form $form
 */
class CustomRequestPasswordReset extends RequestPasswordReset
{

    public function request(): void
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/password-reset/request-password-reset.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/password-reset/request-password-reset.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/password-reset/request-password-reset.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return;
        }

        $data = $this->form->getState();

        $faculty = Faculty::where('email', $data['email'])->first();

        if (!$faculty) {
            Notification::make()
                ->title(__("We cant find your email address."))
                ->danger()
                ->send();

            return;
        }

        $notification = new ResetPasswordNotification("1233x3");
        $notification->url = Filament::getResetPasswordUrl("1233x3", $faculty);

        $faculty->notify($notification);

        Notification::make()
            ->title(__("Reset Password email sent."))
            ->success()
            ->send();

        $this->form->fill();
    }
}
