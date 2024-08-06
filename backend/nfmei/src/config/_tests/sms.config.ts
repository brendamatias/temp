import { registerAs } from "@nestjs/config";

export default registerAs('sms', () => ({
  provider:   process.env.SMS_PROVIDER ?? 'none',
  from:       process.env.SMS_FROM,
  accountSid: process.env.SMS_ACCOUNT_SID,
  authToken:  process.env.SMS_AUTH_TOKEN,
}));