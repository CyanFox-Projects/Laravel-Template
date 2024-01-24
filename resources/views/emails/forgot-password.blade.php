{!! Str::markdown(str_replace(
    [
        '{username}',
        '{firstName}',
        '{lastName}',
        '{resetLink}'
    ], [
        $username,
        $firstName,
        $lastName,
        route('auth.forgot-password', [$passwordResetToken])
    ],
     setting('emails_forgot_password_content'))) !!}
