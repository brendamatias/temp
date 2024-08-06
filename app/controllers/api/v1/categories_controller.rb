class Api::V1::CategoriesController < ApplicationController
  before_action :set_api_v1_category, only: [:show, :update, :destroy, :archives]
  before_action :authorize_request

  # GET /api/v1/categories
  def index
    @categories = query_result(params)

    if @categories.present?
      render json: { count: @categories.size, categories: @categories }
    else
      render json: { error: @categories.errors }, status: :unprocessable_entity
    end
  end

  def query_result(params)
    retorno = Category.none
    if params.present?
      result = []
      result << "name ilike '%#{params[:name]}%'" if params[:name].present?
      result << "description ilike '%#{params[:description]}%'" if params[:description].present?
      retorno = Category.where(result.join(" AND "))
    end  
    retorno
  end  

  def archives
    if @category.update(category_params)
      render json: '', status: :created
    else
      render json: @category.errors, status: :unprocessable_entity
    end
  end  

  # GET /api/v1/categories/1
  def show
    if @category.present?
      render json: { category: @category }
    else
      render json: { error: @category.errors }, status: :unprocessable_entity
    end
  end

  # POST /api/v1/categories
  def create
    @category = Category.new(category_params)

    if @category.save
      render json: { category_id: @category.id }, status: :created
    else
      render json: { error: @category.errors }, status: :unprocessable_entity
    end
  end

  # PATCH/PUT /api/v1/categories/1
  def update
    if @category.update(category_params)
      render json: '', status: :created
    else
      render json: @category.errors, status: :unprocessable_entity
    end
  end

  # DELETE /api/v1/categories/1
  def destroy
    @category.destroy
  end

  private
    # Use callbacks to share common setup or constraints between actions.
    def set_api_v1_category
      params[:id] = params[:category_id].present? ? params[:category_id] : params[:id]
      @category = Category.find(params[:id])
    end

    # Only allow a trusted parameter "white list" through.
    def category_params
      params.require(:category).permit(:name, :description)
    end
end
