class CreateExpenses < ActiveRecord::Migration[5.2]
  def change
    create_table :expenses do |t|
      t.decimal :amount
      t.string :description
      t.date :accrual_date, null: false
      t.date :transaction_date, null: false
      t.references :customer, foreign_key: true
      t.references :category, null: false, foreign_key: true


      t.timestamps
    end
  end
end
