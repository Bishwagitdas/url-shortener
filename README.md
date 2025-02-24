# URL Shortener

## Overview
The URL Shortener is a Laravel-based web application that allows users to shorten long URLs and track their usage. It provides a simple UI for URL shortening, redirection, and analytics tracking.

## Features
- Shorten long URLs with a unique short code.
- Track analytics such as total clicks, IP address, and user agent.
- View analytics for each shortened URL.
- Delete URLs when no longer needed.

## Installation & Setup
### Prerequisites
Ensure you have the following installed on your system:
- PHP (>=8.0)
- Composer
- MySQL
- Laravel 10
- Node.js & NPM (for frontend assets)

### Installation Steps
1. **Clone the Repository**
   ```sh
   git clone https://github.com/Bishwagitdas/url-shortener
   cd url-shortener
   ```

2. **Install Dependencies**
   ```sh
   composer install
   npm install && npm run dev
   ```

3. **Configure Environment**
   - Copy `.env.example` to `.env`:
     ```sh
     cp .env.example .env
     ```
   - Update the `.env` file with your database credentials:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=your_database_name
     DB_USERNAME=your_username
     DB_PASSWORD=your_password
     ```

4. **Generate Application Key**
   ```sh
   php artisan key:generate
   ```

5. **Run Migrations**
   ```sh
   php artisan migrate
   ```

6. **Run the Application**
   ```sh
   php artisan serve
   ```
   Your application will now be accessible at `http://127.0.0.1:8000`

## Usage
- Visit the homepage to shorten a URL.
- Copy and share the generated short URL.
- Track analytics by visiting `http://127.0.0.1:8000/url/analytics/{short_code}`.

## Routes
| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/url/shorten` | Shortens a long URL |
| `GET` | `/{code}` | Redirects to the original URL |
| `GET` | `/url/analytics/{code}` | Displays analytics for a short URL |
| `DELETE` | `/url/{id}` | Deletes a shortened URL |

## Future Enhancements
- Implement authentication for URL management.
- Add user accounts to track personal URLs.
- Implement caching for frequently accessed URLs.
- Provide an API for external integrations.

## License
This project is licensed under the MIT License.
