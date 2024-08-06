class Api::V1::ExpensesController < ApplicationController
  before_action :set_api_v1_expense, only: [:show, :update, :destroy]
  before_action :authorize_request

  # GET /api/v1/expenses
  def index
    @expenses = Expense.all

    render json: @expenses
  end

  # GET /api/v1/expenses/1
  def show
    render json: @expense
  end

  # POST /api/v1/expenses
  def create
    @expense = Expense.new(api_v1_expense_params)

    if @expense.save
      render json: { expense_id: @expense.id }, status: :created
      #render json: { amount: @expense.amount, description: @expense.description, accrual_date: @expense.accrual_date, transaction_date: @expense.transaction_date, customer_id: @expense.customer_id }, status: :created
    else
      render json: { error: @expense.errors }, status: :unprocessable_entity
    end
  end

  # PATCH/PUT /api/v1/expenses/1
  def update
    if @expense.update(api_v1_expense_params)
      render json: '', status: :created
    else
      render json: { error: @expense.errors }, status: :unprocessable_entity
    end
  end

  # DELETE /api/v1/expenses/1
  def destroy
    @expense.destroy
  end

  private
    # Use callbacks to share common setup or constraints between actions.
    def set_api_v1_expense
      @expense = Expense.find(params[:id])
    end

    # Only allow a trusted parameter "white list" through.
    def api_v1_expense_params
      params.require(:expense).permit(:amount, :description, :accrual_date, :transaction_date, :customer_id, :category_id)
    end
end
