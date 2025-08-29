# Payment Gateway Setup Guide

This guide explains how to configure the payment gateway integration for your course enrollment system.

## Supported Payment Gateways

The system currently supports the following payment gateways:

1. **ZarinPal** (Primary - Recommended for Iran)
2. **NextPay** (Optional)
3. **Parsijoo** (Optional)

## Environment Configuration

Add the following variables to your `.env` file:

```env
# Payment Gateway Configuration
PAYMENT_DEFAULT=zarinpal
PAYMENT_CURRENCY=Toman
PAYMENT_CURRENCY_MULTIPLIER=10
PAYMENT_TIMEOUT=300
PAYMENT_RETRY_ATTEMPTS=3

# ZarinPal Gateway
ZARINPAL_MERCHANT_ID=your_zarinpal_merchant_id_here
ZARINPAL_CALLBACK_URL=http://your-domain.com/api/payment/callback
ZARINPAL_SANDBOX=true

# NextPay Gateway (Optional)
NEXTPAY_API_KEY=your_nextpay_api_key_here
NEXTPAY_CALLBACK_URL=http://your-domain.com/api/payment/callback
NEXTPAY_SANDBOX=true

# Parsijoo Gateway (Optional)
PARSIJOO_MERCHANT_ID=your_parsijoo_merchant_id_here
PARSIJOO_CALLBACK_URL=http://your-domain.com/api/payment/callback
PARSIJOO_SANDBOX=true

# Payment Settings
PAYMENT_REFUND_HOURS=24
PAYMENT_REFUND_REQUIRE_REASON=true
PAYMENT_REFUND_AUTO_APPROVE_FREE=true

# Payment Notifications
PAYMENT_EMAIL_NOTIFICATIONS=true
PAYMENT_ADMIN_EMAIL=admin@your-domain.com
PAYMENT_SMS_NOTIFICATIONS=false
PAYMENT_SMS_PROVIDER=kavenegar

# Payment Logging
PAYMENT_LOGGING=true
PAYMENT_LOG_LEVEL=info
PAYMENT_LOG_CHANNEL=payment
```

## ZarinPal Setup

### 1. Get Merchant ID
1. Go to [ZarinPal](https://www.zarinpal.com/)
2. Create an account or login
3. Go to "درگاه پرداخت" (Payment Gateway)
4. Copy your Merchant ID

### 2. Configure Callback URL
Set the callback URL to: `http://your-domain.com/api/payment/callback`

### 3. Test Mode
- Set `ZARINPAL_SANDBOX=true` for testing
- Set `ZARINPAL_SANDBOX=false` for production

## NextPay Setup

### 1. Get API Key
1. Go to [NextPay](https://nextpay.ir/)
2. Create an account or login
3. Go to "پنل کاربری" (User Panel)
4. Copy your API Key

### 2. Configure Callback URL
Set the callback URL to: `http://your-domain.com/api/payment/callback`

## Parsijoo Setup

### 1. Get Merchant ID
1. Go to [Parsijoo](https://parsijoo.ir/)
2. Create an account or login
3. Go to "پنل کاربری" (User Panel)
4. Copy your Merchant ID

### 2. Configure Callback URL
Set the callback URL to: `http://your-domain.com/api/payment/callback`

## Testing

### 1. Test Cards (ZarinPal Sandbox)
- **Successful Payment**: Use any valid card number
- **Failed Payment**: Use card number `1234567890123456`

### 2. Test Flow
1. Create a course with price > 0
2. Enroll in the course
3. Select payment method
4. Complete payment flow
5. Check enrollment status

## Production Deployment

### 1. Update Environment Variables
- Set `ZARINPAL_SANDBOX=false`
- Update callback URLs to production domain
- Set proper admin email

### 2. SSL Certificate
Ensure your domain has a valid SSL certificate for secure payments.

### 3. Callback URL
Make sure the callback URL is accessible from the internet.

## Troubleshooting

### Common Issues

1. **Payment Creation Failed**
   - Check merchant ID/API key
   - Verify callback URL is accessible
   - Check payment gateway status

2. **Payment Verification Failed**
   - Check transaction ID
   - Verify payment amount
   - Check payment gateway logs

3. **Callback Not Working**
   - Verify callback URL in gateway settings
   - Check server logs
   - Ensure route is accessible

### Logs
Payment logs are stored in `storage/logs/payment.log` (if configured).

### Support
For payment gateway specific issues, contact the respective gateway's support team.

## Security Considerations

1. **Never commit sensitive data** like merchant IDs to version control
2. **Use environment variables** for all sensitive configuration
3. **Enable HTTPS** in production
4. **Validate payment amounts** server-side
5. **Log all payment activities** for audit purposes
6. **Implement proper error handling** to prevent information leakage

## API Endpoints

### Public Endpoints
- `POST /api/payment/callback` - Payment gateway callback

### Protected Endpoints (Require Authentication)
- `GET /api/payment/methods` - Get available payment methods
- `POST /api/enrollments/{id}/create-payment` - Create payment request
- `GET /api/enrollments/{id}/payment-status` - Check payment status
- `POST /api/enrollments/{id}/refund` - Process refund

## Currency Handling

The system uses Toman as the base currency but converts to Rials for payment gateways:
- 1 Toman = 10 Rials
- Display: Shows amounts in Toman
- Payment: Processes in Rials
- Storage: Stores in Toman

## Refund Policy

- Refunds are allowed within 24 hours of payment
- Free enrollments can be cancelled anytime
- Refund reasons are logged for audit purposes
- Automatic refund processing for failed payments



