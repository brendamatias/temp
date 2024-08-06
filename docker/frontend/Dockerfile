FROM node:18-alpine


WORKDIR /var/www/html

COPY . /var/www/html

RUN npm install

EXPOSE 5173

CMD ["npm", "run", "dev"] 