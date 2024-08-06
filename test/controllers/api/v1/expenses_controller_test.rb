require 'test_helper'

class Api::V1::ExpensesControllerTest < ActionDispatch::IntegrationTest
  setup do
    @api_v1_expense = api_v1_expenses(:one)
  end

  test "should get index" do
    get api_v1_expenses_url, as: :json
    assert_response :success
  end

  test "should create api_v1_expense" do
    assert_difference('Api::V1::Expense.count') do
      post api_v1_expenses_url, params: { api_v1_expense: { accrual_date: @api_v1_expense.accrual_date, amount: @api_v1_expense.amount, customer_id: @api_v1_expense.customer_id, description: @api_v1_expense.description, transaction_date: @api_v1_expense.transaction_date } }, as: :json
    end

    assert_response 201
  end

  test "should show api_v1_expense" do
    get api_v1_expense_url(@api_v1_expense), as: :json
    assert_response :success
  end

  test "should update api_v1_expense" do
    patch api_v1_expense_url(@api_v1_expense), params: { api_v1_expense: { accrual_date: @api_v1_expense.accrual_date, amount: @api_v1_expense.amount, customer_id: @api_v1_expense.customer_id, description: @api_v1_expense.description, transaction_date: @api_v1_expense.transaction_date } }, as: :json
    assert_response 200
  end

  test "should destroy api_v1_expense" do
    assert_difference('Api::V1::Expense.count', -1) do
      delete api_v1_expense_url(@api_v1_expense), as: :json
    end

    assert_response 204
  end
end
