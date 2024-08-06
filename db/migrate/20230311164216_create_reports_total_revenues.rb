class CreateReportsTotalRevenues < ActiveRecord::Migration[5.2]
  def change
    create_table :reports_total_revenues do |t|
      t.string :month_name
      t.decimal :month_revenue

      t.timestamps
    end
  end
end
