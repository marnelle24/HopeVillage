# Microsoft 365 Email Configuration

Password reset (and other app emails) can be sent via Microsoft 365 / Office 365 SMTP.

## .env settings

In your `.env` file, set:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USERNAME=your-account@yourdomain.com
MAIL_PASSWORD=your-password-or-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-account@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

- **MAIL_USERNAME** / **MAIL_FROM_ADDRESS**: Use the same Microsoft 365 mailbox address.
- **MAIL_PASSWORD**: Use the account password. If the account has multi-factor authentication (MFA), create an **App password** in the [Microsoft 365 security settings](https://account.microsoft.com/security) and use that instead.

## After changing .env

- Restart the PHP process (e.g. `php artisan config:clear` and restart the web server or `php artisan serve` if you use it).
- Test by using the **Forgot Password** flow and choosing **Email** as the reset method.

## Troubleshooting

- **Authentication failed**: Enable SMTP AUTH for the mailbox in the Microsoft 365 admin center (Exchange admin center → Mail flow → Connectors), or use an App password if MFA is on.
- **Connection timeout**: Ensure outbound port 587 (TLS) is allowed from your server to `smtp.office365.com`.
