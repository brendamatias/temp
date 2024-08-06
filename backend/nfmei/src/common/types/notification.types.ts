export type NotificationChannel = 'SMS' | 'EMAIL';

export interface NotificationPayload {
  percentUsed?: number;
  meiAnnualLimit?: number;
  instructions?: string;
}

export interface ThresholdPayload {
  year: number;
  used: number;
  limit: number;
  remaining: number;
  ratio: number;
  percentUsed: number;
  instructions: string;
}

export type ThresholdResult = {
  hit80: boolean;
  payload: ThresholdPayload;
};
