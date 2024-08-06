# MEI Organizer

A comprehensive system for managing MEI (Individual Microentrepreneur) businesses, helping entrepreneurs organize their invoices, expenses, and financial records.

## Features

- **User Management**
  - User registration and authentication
  - Profile management with email and phone updates
  - Password change functionality

- **Invoice Management**
  - Create and manage invoices
  - Track invoice status
  - View invoice history
  - Export invoice reports

- **Partner Companies**
  - Register and manage partner companies
  - Track company information
  - Associate companies with invoices

- **Expense Management**
  - Categorize expenses
  - Track expense details
  - Monitor spending limits
  - Generate expense reports

- **Preferences**
  - Configure notification settings
  - Set up email and SMS alerts
  - Define spending limits
  - Customize system behavior

## Technical Stack

### Backend
- PHP 8.2
- Laravel Framework
- MySQL 8.0
- RESTful API Architecture

### Frontend
- Vue.js 3
- Vite
- Tailwind CSS
- Axios for API communication

### Development Tools
- Docker & Docker Compose
- Git for version control
- Composer for PHP dependencies
- NPM for JavaScript dependencies

## System Requirements

- Docker
- Docker Compose
- Git
- Node.js 18+ (for local development)
- PHP 8.2+ (for local development)
- Composer (for local development)

## Installation

1. Clone the repository:
```bash
git clone [repository-url]
cd mei-organizer
```

2. Copy the environment file:
```bash
cp .env.example .env
```

3. Configure your environment variables in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=mei_organizer
DB_USERNAME=your_username
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email
MAIL_FROM_NAME="${APP_NAME}"

TWILIO_SID=your_twilio_sid
TWILIO_AUTH_TOKEN=your_twilio_token
TWILIO_FROM=your_twilio_phone
```

4. Start the Docker containers:
```bash
docker-compose up -d
```

5. Install dependencies and set up the application:
```bash
# Install PHP dependencies
docker-compose exec backend composer install

# Install Node.js dependencies
docker-compose exec frontend npm install

# Generate application key
docker-compose exec backend php artisan key:generate

# Run database migrations
docker-compose exec backend php artisan migrate

# Seed the database with initial data
docker-compose exec backend php artisan db:seed
```

6. Access the application:
- Frontend: http://localhost:5173
- Backend API: http://localhost:80/api

## Development

### Running Tests
```bash
docker-compose exec backend php artisan test
```

### Code Style
The project follows PSR-12 coding standards. To check your code:
```bash
docker-compose exec backend composer check-style
```

### Database Migrations
To create a new migration:
```bash
docker-compose exec backend php artisan make:migration migration_name
```

### API Documentation
The API documentation is available at `/api/documentation` when running the application.

## Docker Services

### Backend Service
- PHP 8.2 with FPM
- Laravel application
- Composer for dependency management
- Port: 9000 (internal)

### Frontend Service
- Node.js 18
- Vue.js application
- Vite development server
- Port: 5173

### Nginx Service
- Web server
- Reverse proxy
- Port: 80

### MySQL Service
- Database server
- Port: 3306
- Persistent volume for data storage

## Security

- All API endpoints are protected with Laravel Sanctum
- Passwords are hashed using bcrypt
- CSRF protection enabled
- Input validation on all endpoints
- Rate limiting on authentication endpoints

## Deployment

1. Build the Docker images:
```bash
docker-compose build
```

2. Push the images to your registry:
```bash
docker-compose push
```

3. On the production server:
```bash
docker-compose -f docker-compose.prod.yml up -d
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

