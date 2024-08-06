class Api::V1::Reports::ReportsController < ApplicationController

  def total_revenue
    @revenue = Revenue.where(id: params[:fiscal_year])
    if @revenue.present?
      render json: { total_revenue: @revenue.count, max_revenue_amount: 0.0 }
    else
      render json: { error: @revenue.errors }, status: :unprocessable_entity
    end
  end
  
  def revenue_by_month
    @revenues = Revenue.where(id: params[:fiscal_year])
    if @revenues.present?
      render json: { revenue: [month_name: '', month_revenue: 0.0], max_revenue_amount: 0.0 }
    else
      render json: { error: @revenues.errors }, status: :unprocessable_entity
    end
  end  

  def revenue_by_customer
    @revenues = Revenue.where(id: params[:fiscal_year])
    if @revenues.present?
      render json: { revenue: [customer_name: '', revenue: 0.0], max_revenue_amount: 0.0 }
    else
      render json: { error: @revenues.errors }, status: :unprocessable_entity
    end
  end  

end  