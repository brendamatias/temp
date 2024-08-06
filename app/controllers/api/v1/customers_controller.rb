class Api::V1::CustomersController < ApplicationController
  before_action :authorize_request
  before_action :set_api_v1_customer, only: [:update, :destroy, :show]
  before_action :set_params, only: [:index]

  # GET /api/v1/customers
  def index
    @customers = params[:customer].present? ? Customer.where(query_params(params)) : Customer.all

    if @customers.present?
      render json: {count: @customers.size, customers: @customers }
    else
      render json: {error: @customers.errors}, status: :unprocessable_entity
    end
  end

  def query_params(params)
    if params.present?
      result = []
      result << "commercial_name = '#{params[:customer][:commercial_name]}'" if params[:customer][:commercial_name].present?
      result << "cnpj = '#{params[:customer][:cnpj]}'" if params[:customer][:cnpj].present?
      result.join(" OR ")
    end  
  end  

  # GET /api/v1/customers/1
  def show
    render json: { customer: @customer }, status: :created
  end

  # POST /api/v1/customers
  def create
    @customer = Customer.new(customer_params)

    if @customer.save
      render json: { customer_id: @customer.id }, status: :created
    else
      render json: @customer.errors, status: :unprocessable_entity
    end
  end

  # PATCH/PUT /api/v1/customers/1
  def update
    if @customer.update(customer_params)
      render json: { customer: @customer }, status: 200
    else
      render json: @customer.errors, status: :unprocessable_entity
    end
  end

  # DELETE /api/v1/customers/1
  def destroy
    @customer.destroy
  end

  private
    def set_params
      #raise params.inspect
      params[:customer][:commercial_name] = params[:customer][:name] if params[:customer].present?
      params[:customer].delete :name
      params.require(:customer).permit(:cnpj, :commercial_name)
    end  
    # Use callbacks to share common setup or constraints between actions.
    def set_api_v1_customer
      @customer = Customer.find(params[:id])
    end

    # Only allow a trusted parameter "white list" through.
    def customer_params
      params.require(:customer).permit(:cnpj, :commercial_name, :legal_name)
    end
end
