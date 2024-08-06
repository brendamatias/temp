# CONTROL_NF

# Instalação ASDFRAI:
 - **Obs1:** caso não possua já instalado o asdf, realize a instalação abaixo, executando no terminal:
  - git clone https://github.com/asdf-vm/asdf.git ~/.asdf --branch v0.11.1
  - asdf info
  - asdf plugin-add ruby https://github.com/asdf-vm/asdf-ruby.git
 - **Obs2:** Instale os pacotes abaixo para o ruby:
  - sudo apt install build-essential
  - sudo apt install libssl-dev libffi-dev libncurses5-dev zlib1g zlib1g-dev libreadline-dev libbz2-dev libsqlite3-dev make gcc
  - sudo apt-get install -y libpq-dev
  - sudo apt-get install postgresql postgresql-contrib

 - **Obs3:** Instale os pacotes abaixo para o ruby:
  - ASDF_RUBY_BUILD_VERSION=master asdf install ruby 2.7.0

- **Obs2:** escreve via echo para o bashrc conforme abaixo:
  - echo -e '\n. $HOME/.asdf/asdf.sh' >> ~/.bashrc
  - echo -e '\n. $HOME/.asdf/completions/asdf.bash' >> ~/.bashrc
  - source ~/.bashrc

- **Obs3:** caso possua instalado o zshrc,escreve via echo para o zshrc conforme abaixo:
  - echo -e '\n. $HOME/.asdf/asdf.sh' >> zshrc
  - echo -e '\n. $HOME/.asdf/completions/asdf.bash' >> zshrc
  - source ~/.zshrc
  
- **Obs4:** após concluída a instalação do asdf, instale a versão abaixo:
  - asdf plugin-add ruby https://github.com/asdf-vm/asdf-ruby.git
  - asdf plugin-add nodejs https://github.com/asdf-vm/asdf-nodejs.git

 # Clonar projeto:
 - git clone https://git.vibbra.com.br/sancho/control_nf.git
 - cd control_nf
 
 - **Obs1:** execute as instalações das Gems e Yarn:
 - asdf install nodejs 16.10.0
 - asdf install ruby 2.7.0
 - asdf global ruby 2.7.0
 - bundle install 
 - yarn install
# Started project
 - rails db:create 
 # Obs: Ao criar o banco acesse via pgadmin, acesse o banco control control_nf_development, e crie um schema chamado control_nf
 - rails db:migrate
 - rails s


# Vídeo pacotes, sistema operacional, instalações e infra-estrutura via Readme:
https://www.loom.com/share/d24e9d12f696447e8ac3ffc5d61a8f0c

# Criando banco/schema/tabelas do zero no postgres:
# Criando usuário/autenticando/gerando jwt/ utilizando no header jwt nos endpoints:
https://www.loom.com/share/12bcfdbb67394535b57c037af89e3c27

# Utilizando os serviços:
Observação: os serviços registrados pelo postman foram exportados e salvos dentro do
projeto com o doc, este é o caminho dentro do projeto: doc/postman/Vibbra.postman_collection.json

https://www.loom.com/share/83b8568358124bb99f713d25d7d8a4cf