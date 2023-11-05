<?php

declare(strict_types=1);

namespace App\Filament\Faculty\Pages;

use App\Domain\Faculty\Models\Faculty;
use Filament\Pages\Auth\PasswordReset\ResetPassword;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\PasswordResetResponse;
use Filament\Notifications\Notification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;

/**
 * @property Form $form
 */
class CustomResetPassword extends ResetPassword
{
    public function resetPassword(): ?PasswordResetResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/password-reset/reset-password.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/password-reset/reset-password.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/password-reset/reset-password.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();

        $data['email'] = $this->email;
        $data['token'] = $this->token;

        $faculty = Faculty::where("email", $data['email'])->first();

        $faculty->forceFill([
            'password' => Hash::make($data['password']),
        ])->save();

        event(new PasswordReset($faculty));

        Notification::make()
            ->title(__("Your Password has been reset."))
            ->success()
            ->send();

        return app(PasswordResetResponse::class);
    }
}
