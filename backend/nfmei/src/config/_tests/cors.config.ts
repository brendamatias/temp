import { registerAs } from "@nestjs/config";

export default registerAs('cors', () => {
  const raw = process.env.CORS_ORIGINS ?? '*';
  const origins = raw === '*' ? '*' : raw.split(',').map(_string => _string.trim()).filter(Boolean);
  return { origins };
});