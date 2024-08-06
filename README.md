# NotificaGeral

**NotificaGeral** is an advanced, scalable, and highly flexible notification management system designed for technology professionals. It provides a unified platform for managing notifications across a variety of channels, including **SMS**, **Email**, **Web Push**, **Desktop**, and **Mobile**. With an open API architecture, **NotificaGeral** allows seamless integration with third-party platforms, empowering businesses to streamline their communication workflows efficiently and securely. This project addresses the need for a robust tool to optimize the delivery of notifications between digital services and end-users.

## Key Features

- **Multi-Channel Notifications**: Effortlessly send notifications via **SMS**, **Email**, **Web Push**, **Desktop**, and **Mobile**, ensuring a comprehensive reach across all devices.
- **Open API Integration**: Easily integrate **NotificaGeral** with any platform using its open API, allowing for customized and automated notifications.
- **Customizable Notification Settings**: Set up notification channels, create custom schedules, and tailor messages to meet specific business needs.
- **Template Management**: Upload and manage email templates in `.html` format, allowing for reusable and professional notification layouts.
- **Real-time Notification Tracking**: Monitor sent notifications in real-time through a detailed dashboard with filtering options.
- **Data Export**: Export notification history to **PDF** or **Excel** for reporting and further analysis.
- **Security Compliance**: Built with robust security measures, including encryption and token-based authentication, ensuring secure data transmission and storage.
- **Cross-Platform Compatibility**: Use **NotificaGeral** on both **Desktop** and **Mobile** platforms for flexible notification management.

## Folder Structure

The following is an overview of the **NotificaGeral** project folder structure:

```plaintext
NotificaGeral/
├── composer.json
├── composer.lock
├── config
│   ├── config.php
│   ├── db.php
│   ├── update_email_config.php
│   ├── update_push_config.php
│   └── update_sms_config.php
├── favicon.ico
├── images
│   ├── avatar.png
│   └── logo.png
├── index.php
├── LICENSE
├── logs
├── package.json
├── public
│   ├── css
│   │   ├── login.css
│   │   └── style.css
│   ├── export.php
│   ├── index.php
│   ├── js
│   │   └── script.js
│   ├── logout.php
│   ├── notification-details.php
│   ├── save-notification.php
│   ├── send-notification.php
│   └── status.php
├── README.md
├── scripts
│   ├── migrate.php
│   └── seed.php
├── src
│   ├── controllers
│   │   ├── NotificationController.php
│   │   └── TemplateController.php
│   ├── models
│   │   ├── Notification.php
│   │   └── Template.php
│   └── views
│       ├── notifications
│       │   └── index.php
│       └── templates
│           ├── create.php
│           └── list.php
├── tests
│   ├── NotificationTest.php
│   └── TemplateTest.php
├── upload
│   └── avatar.png
└── vendor
    ├── autoload.php
    ├── composer

```

### How to use this structure:

- **`config/`**: Contains configuration files for database connection and other general settings.
- **`public/`**: This folder holds web-accessible files like CSS, JS, and the main landing page (`index.php`).
- **`src/`**: Contains the core application logic with controllers, models, and views.
  - **`controllers/`**: Handles requests related to notifications and templates.
  - **`models/`**: Defines the database models for notifications and templates.
  - **`views/`**: Contains the HTML views for managing notifications and templates.
- **`tests/`**: Holds unit and integration tests for the application.
- **`scripts/`**: Contains scripts for database migration and seeding.
- **`.gitlab-ci.yml`**: GitLab CI/CD configuration file for automated testing and deployment.
- **`composer.json`**: PHP dependencies and autoload configuration.
- **`package.json`**: JavaScript dependencies and build tools configuration.
- **`README.md`**: Main documentation file for the project.
- **`LICENSE`**: License information for the project.

## Database Structure

The following tables are used in the **NotificaGeral** database schema:

## Table: `channels`

Stores the configuration details for notification channels such as SMS, Email, and Web Push.

| Field          | Type           | Null | Key  | Default | Extra          |
|----------------|----------------|------|------|---------|----------------|
| `id`           | `int(11)`      | NO   | PRI  | NULL    | auto_increment |
| `user_id`      | `int(11)`      | NO   |      | NULL    | Foreign Key    |
| `channel_type` | `varchar(50)`  | NO   |      | NULL    |                |
| `settings`     | `text`         | NO   |      | NULL    | JSON-encoded   |
| `created_at`   | `timestamp`    | YES  |      | NULL    |                |
| `updated_at`   | `timestamp`    | YES  |      | NULL    |                |

## Table: `email_configuration`

Stores configuration details for email notifications such as SMTP settings.

| Field          | Type           | Null | Key  | Default | Extra          |
|----------------|----------------|------|------|---------|----------------|
| `id`           | `int(11)`      | NO   | PRI  | NULL    | auto_increment |
| `user_id`      | `int(11)`      | NO   |      | NULL    | Foreign Key    |
| `smtp_server`  | `varchar(100)` | NO   |      | NULL    |                |
| `smtp_port`    | `int(11)`      | NO   |      | NULL    |                |
| `email_login`  | `varchar(100)` | NO   |      | NULL    |                |
| `email_password`| `varchar(255)`| NO   |      | NULL    |                |
| `sender_name`  | `varchar(100)` | NO   |      | NULL    |                |
| `sender_email` | `varchar(100)` | NO   |      | NULL    |                |
| `created_at`   | `timestamp`    | YES  |      | NULL    |                |
| `updated_at`   | `timestamp`    | YES  |      | NULL    |                |

## Table: `notifications`

Logs the notifications sent by the system, including the status and channel.

| Field        | Type           | Null | Key  | Default | Extra          |
|--------------|----------------|------|------|---------|----------------|
| `id`         | `int(11)`      | NO   | PRI  | NULL    | auto_increment |
| `user_id`    | `int(11)`      | NO   |      | NULL    | Foreign Key    |
| `channel`    | `varchar(50)`  | NO   |      | NULL    |                |
| `message`    | `text`         | NO   |      | NULL    |                |
| `status`     | `varchar(20)`  | NO   |      | NULL    |                |
| `created_at` | `timestamp`    | YES  |      | NULL    |                |
| `updated_at` | `timestamp`    | YES  |      | NULL    |                |

## Table: `notification_logs`

Records the logs for each notification sent, including timestamps and error messages.

| Field          | Type           | Null | Key  | Default | Extra          |
|----------------|----------------|------|------|---------|----------------|
| `id`           | `int(11)`      | NO   | PRI  | NULL    | auto_increment |
| `notification_id`| `int(11)`    | NO   |      | NULL    | Foreign Key    |
| `log_message`  | `text`         | NO   |      | NULL    |                |
| `created_at`   | `timestamp`    | YES  |      | NULL    |                |

## Table: `push_configurations`

Stores configuration details for web push notifications.

| Field          | Type           | Null | Key  | Default | Extra          |
|----------------|----------------|------|------|---------|----------------|
| `id`           | `int(11)`      | NO   | PRI  | NULL    | auto_increment |
| `user_id`      | `int(11)`      | NO   |      | NULL    | Foreign Key    |
| `site_name`    | `varchar(100)` | NO   |      | NULL    |                |
| `site_url`     | `varchar(255)` | NO   |      | NULL    |                |
| `permission_message`| `text`    | NO   |      | NULL    |                |
| `allow_button_text` | `varchar(50)` | NO |     | NULL    |                |
| `deny_button_text`  | `varchar(50)` | NO |     | NULL    |                |
| `welcome_title` | `varchar(100)`| NO   |      | NULL    |                |
| `welcome_message`| `text`       | NO   |      | NULL    |                |
| `destination_link`| `varchar(255)`| NO |     | NULL    |                |
| `created_at`   | `timestamp`    | YES  |      | NULL    |                |
| `updated_at`   | `timestamp`    | YES  |      | NULL    |                |

## Table: `sms_configuration`

Stores configuration details for SMS notifications.

| Field          | Type           | Null | Key  | Default | Extra          |
|----------------|----------------|------|------|---------|----------------|
| `id`           | `int(11)`      | NO   | PRI  | NULL    | auto_increment |
| `user_id`      | `int(11)`      | NO   |      | NULL    | Foreign Key    |
| `sms_provider` | `varchar(100)` | NO   |      | NULL    |                |
| `sms_login`    | `varchar(100)` | NO   |      | NULL    |                |
| `sms_password` | `varchar(255)` | NO   |      | NULL    |                |
| `created_at`   | `timestamp`    | YES  |      | NULL    |                |
| `updated_at`   | `timestamp`    | YES  |      | NULL    |                |

## Table: `templates`

Stores the email templates uploaded for notification purposes.

| Field        | Type           | Null | Key  | Default | Extra          |
|--------------|----------------|------|------|---------|----------------|
| `id`         | `int(11)`      | NO   | PRI  | NULL    | auto_increment |
| `name`       | `varchar(100)` | NO   |      | NULL    |                |
| `file_path`  | `varchar(255)` | NO   |      | NULL    |                |
| `created_at` | `timestamp`    | YES  |      | NULL    |                |
| `updated_at` | `timestamp`    | YES  |      | NULL    |                |

## Table: `users`

This table stores user information, including login credentials.

| Field        | Type           | Null | Key  | Default | Extra          |
|--------------|----------------|------|------|---------|----------------|
| `id`         | `int(11)`      | NO   | PRI  | NULL    | auto_increment |
| `name`       | `varchar(100)` | NO   |      | NULL    |                |
| `email`      | `varchar(100)` | NO   | UNI  | NULL    |                |
| `password`   | `varchar(255)` | NO   |      | NULL    |                |
| `created_at` | `timestamp`    | YES  |      | NULL    |                |
| `updated_at` | `timestamp`    | YES  |      | NULL    |                |


## API Endpoints

**NotificaGeral** provides a set of RESTful API endpoints to manage notifications and integrations:

- `POST /api/notifications/send`: Send a notification to the specified channel.
- `GET /api/notifications/history`: Retrieve the history of notifications sent, with filtering options.
- `POST /api/channels/configure`: Configure notification channels (SMS, Email, Web Push).
- `GET /api/templates`: List all available email templates.
- `POST /api/templates/upload`: Upload a new email template to be used in notifications.

API documentation can be accessed from within the platform for detailed instructions and usage examples.

## Security

- **Encryption**: Sensitive data such as passwords, API keys, and user credentials are encrypted at rest and in transit.
- **Token-Based Authentication**: API access is secured using tokens to ensure only authorized users can interact with the system.
- **Audit Logging**: All critical actions, including notifications and API access, are logged for auditing and security purposes.

## Getting Started

### Prerequisites

To get started with **NotificaGeral**, you will need:

- **PHP** 7.4+ installed
- **MySQL/MariaDB** for database management
- **Apache/Nginx** as a web server
- **Composer** for dependency management
- **Git** for version control

### Installation

1. Clone the repository:

   git clone https://git.vibbra.com.br/bruno-1640871322/notificageral.git
   
   
2. Install PHP dependencies using Composer:

   composer install
   
3. Copy the .env.example file to create your .env file:

   cp .env.example .env
   
4. Configure the .env file with your database credentials and other necessary environment variables.

5. Run database migrations to set up the necessary tables:

    php artisan migrate
    
6. Serve the application locally to start development: 

    php artisan serve
    
The application will be available at http://localhost:8000.

### Usage

1. **User Authentication**: Once installed, users can register, log in, and start configuring their notification preferences.

2. **API Access**: Use the provided API documentation to integrate **NotificaGeral** with your platform.

3. **Notification Configuration**: From the user interface, configure notification channels (Email, SMS, Web Push) and set custom schedules, templates, and recipients.

4. **Real-Time Monitoring**: Use the dashboard to monitor notification history in real-time, apply filters, and export data.

## API Endpoints

**NotificaGeral** offers a variety of RESTful API endpoints for managing notifications and integrations:

- `POST /api/notifications/send`: Sends a notification through the configured channel.
- `GET /api/notifications/history`: Retrieves the notification history with filtering options.
- `POST /api/channels/configure`: Configures notification channels (SMS, Email, Web Push).
- `GET /api/templates`: Lists available email templates.
- `POST /api/templates/upload`: Uploads new email templates for use in notifications.

## Security

- **Encryption**: Sensitive data such as passwords, API keys, and user credentials are encrypted at rest and in transit.
- **Token-Based Authentication**: API access is secured using tokens to ensure only authorized users can interact with the system.
- **Audit Logging**: All critical actions, including notification sending and API access, are logged for auditing and security purposes.
   


   
   
   

   


