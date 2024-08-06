class Api::V1::SettingsController < ApplicationController
  before_action :authorize_request
  
  def update
    @settings = true
    if @settings.present?
      render json: { max_revenue_amount: 0.0, sms_notification: true, email_notification: true }, status: :created
    else
      render json: { error: @settings.errors }, status: :unprocessable_entity
    end
  end
  
  def show
    @settings = true
    if @settings.present?
      render json: { settings: @settings }, status: :created
    else
      render json: { error: @settings.errors }, status: :unprocessable_entity
    end
  end  
end
