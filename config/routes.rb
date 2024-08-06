Rails.application.routes.draw do
  resources :users, param: :_username
  post '/auth/login', to: 'authentication#login'
  #get '/*a', to: 'application#not_found'

  namespace :api do
    namespace :v1 do
      resources :categories, only: [:create, :update, :show, :index] do
        put '/archives/' => 'categories#archives' 
      end  
      resources :expenses, only: [:create, :update, :destroy] 
      resources :customers, only: [:create, :update, :destroy, :show, :index]
      resources :revenues, only: [:create, :update, :destroy]
      post '/reports/total-revenue/' => 'reports/reports#total_revenue'
      post '/reports/revenue-by-month/' => 'reports/reports#revenue_by_month'
      post '/reports/revenue-by-customer/' => 'reports/reports#revenue_by_customer'
      resources :settings, only: [:update, :show]
    end
  end
end
