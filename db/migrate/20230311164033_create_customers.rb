class CreateCustomers < ActiveRecord::Migration[5.2]
  def change
    create_table :customers do |t|
      t.string :cnpj
      t.string :commercial_name
      t.string :legal_name

      t.timestamps
    end
  end
end
