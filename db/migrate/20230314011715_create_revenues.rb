class CreateRevenues < ActiveRecord::Migration[5.2]
  def change
    create_table :revenues do |t|
      t.decimal :amount
      t.string :invoice_id
      t.string :description
      t.date :accrual_date, null: false
      t.date :transaction_date, null: false
      t.references :customer, null: false, foreign_key: true

      t.timestamps
    end
  end
end
