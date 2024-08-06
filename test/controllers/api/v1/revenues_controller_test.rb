require 'test_helper'

class Api::V1::RevenuesControllerTest < ActionDispatch::IntegrationTest
  setup do
    @api_v1_revenue = api_v1_revenues(:one)
  end

  test "should get index" do
    get api_v1_revenues_url, as: :json
    assert_response :success
  end

  test "should create api_v1_revenue" do
    assert_difference('Api::V1::Revenue.count') do
      post api_v1_revenues_url, params: { api_v1_revenue: { accrual_date: @api_v1_revenue.accrual_date, amount: @api_v1_revenue.amount, description: @api_v1_revenue.description, invoice_id: @api_v1_revenue.invoice_id, transaction_date: @api_v1_revenue.transaction_date } }, as: :json
    end

    assert_response 201
  end

  test "should show api_v1_revenue" do
    get api_v1_revenue_url(@api_v1_revenue), as: :json
    assert_response :success
  end

  test "should update api_v1_revenue" do
    patch api_v1_revenue_url(@api_v1_revenue), params: { api_v1_revenue: { accrual_date: @api_v1_revenue.accrual_date, amount: @api_v1_revenue.amount, description: @api_v1_revenue.description, invoice_id: @api_v1_revenue.invoice_id, transaction_date: @api_v1_revenue.transaction_date } }, as: :json
    assert_response 200
  end

  test "should destroy api_v1_revenue" do
    assert_difference('Api::V1::Revenue.count', -1) do
      delete api_v1_revenue_url(@api_v1_revenue), as: :json
    end

    assert_response 204
  end
end
