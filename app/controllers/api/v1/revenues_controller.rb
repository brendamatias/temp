class Api::V1::RevenuesController < ApplicationController
  before_action :set_api_v1_revenue, only: [:update, :destroy]
  before_action :revenue_params, only: [:create, :update]
  before_action :authorize_request

  # POST /api/v1/revenues
  def create
    @revenue = Revenue.new(revenue_params)

    if @revenue.save
      render json: { revenue_id: @revenue.id }, status: :created
    else
      render json: { error: @revenue.errors }, status: :unprocessable_entity
    end
  end

  # PATCH/PUT /api/v1/revenues/1
  def update
    if @revenue.update(revenue_params)
      render json: { message: 'Updated with success' }
    else
      render json: { error: @revenue.errors }, status: :unprocessable_entity
    end
  end

  # DELETE /api/v1/revenues/1
  def destroy
    if @revenue.destroy
      render json: { message: 'Deleted with success' }
    else
      render json: { error: @revenue.errors }, status: :unprocessable_entity
    end
  end

  private
    # Use callbacks to share common setup or constraints between actions.
    def set_api_v1_revenue
      @revenue = Revenue.find(params[:id])
    end

    # Only allow a trusted parameter "white list" through.
    def revenue_params
      params.require(:revenue).permit(:amount, :invoice_id, :description, :accrual_date, :transaction_date, :customer_id)
    end
end
