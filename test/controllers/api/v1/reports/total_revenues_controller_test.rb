require 'test_helper'

class Api::V1::Reports::TotalRevenuesControllerTest < ActionDispatch::IntegrationTest
  setup do
    @api_v1_reports_total_revenue = api_v1_reports_total_revenues(:one)
  end

  test "should get index" do
    get api_v1_reports_total_revenues_url, as: :json
    assert_response :success
  end

  test "should create api_v1_reports_total_revenue" do
    assert_difference('Api::V1::Reports::TotalRevenue.count') do
      post api_v1_reports_total_revenues_url, params: { api_v1_reports_total_revenue: { month_name: @api_v1_reports_total_revenue.month_name, month_revenue: @api_v1_reports_total_revenue.month_revenue } }, as: :json
    end

    assert_response 201
  end

  test "should show api_v1_reports_total_revenue" do
    get api_v1_reports_total_revenue_url(@api_v1_reports_total_revenue), as: :json
    assert_response :success
  end

  test "should update api_v1_reports_total_revenue" do
    patch api_v1_reports_total_revenue_url(@api_v1_reports_total_revenue), params: { api_v1_reports_total_revenue: { month_name: @api_v1_reports_total_revenue.month_name, month_revenue: @api_v1_reports_total_revenue.month_revenue } }, as: :json
    assert_response 200
  end

  test "should destroy api_v1_reports_total_revenue" do
    assert_difference('Api::V1::Reports::TotalRevenue.count', -1) do
      delete api_v1_reports_total_revenue_url(@api_v1_reports_total_revenue), as: :json
    end

    assert_response 204
  end
end
