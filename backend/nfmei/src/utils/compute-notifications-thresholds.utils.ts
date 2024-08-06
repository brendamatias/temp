import { ThresholdResult } from "src/common/types/notification.types";

export function computeNotificationThresholds(
  year: number,
  used: number,
  limit: number,
): ThresholdResult {
  const remaining = Math.max(limit - used, 0);
  const ratio = limit > 0 ? used / limit : 0;
  const percentUsed = +(ratio * 100).toFixed(2);
  const hit80 = ratio >= 0.8;

  return {
    hit80,
    payload: {
      year,
      used,
      limit,
      remaining,
      ratio,
      percentUsed,
      instructions: hit80
        ? 'Você está próximo do limite MEI. Evite emitir novas NFs ou avalie enquadramento.'
        : 'Dentro do limite. Monitore antes de emitir novas NFs.',
    },
  };
}