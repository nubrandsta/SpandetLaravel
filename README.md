<h1 align="center">Spandet Baru</h1>

<p align="center">A comprehensive data management system for banner and location tracking</p>

## About Spandet Baru

Spandet Baru is a web-based application built with Laravel that provides a robust platform for managing banner data with geographical information. The system allows users to track banners across different locations, manage user accounts and groups, and visualize data on interactive maps.

## Features

- **Interactive Dashboard**
  - Mapbox integration for geographical data visualization
  - Detailed data tables with sorting and filtering capabilities
  - Comprehensive detail panels for each data entry

- **User Management**
  - Create and manage user accounts
  - Reset passwords and modify user information
  - Role-based access control with group assignments

- **Group Management**
  - Create and manage user groups
  - Assign users to specific groups
  - Track data by group

- **Data Management**
  - View, filter, and sort all banner data
  - Export data to CSV and Excel formats
  - Delete data entries when needed
  - Detailed information display including location data

- **Location Tracking**
  - Store and display geographical coordinates
  - Hierarchical location information (thoroughfare, locality, etc.)
  - Interactive map markers with popup information

## Technical Stack

- **Backend**: Laravel PHP Framework
- **Database**: MySQL
- **Frontend**: Bootstrap 5, JavaScript
- **Maps**: Mapbox GL JS
- **Data Export**: CSV and Excel formats

## Getting Started

### Prerequisites

- PHP 8.0 or higher
- Composer
- MySQL database
- Mapbox API key

### Installation

1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure your database settings
4. Add your Mapbox API token to the `.env` file: `MAPBOX_TOKEN=your_token_here`
5. Run `php artisan key:generate`
6. Run `php artisan migrate --seed` to create the database structure and seed initial data
7. Run `php artisan serve` to start the development server

## Environment Configuration

Make sure to set the following in your `.env` file:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spandet_web
DB_USERNAME=root
DB_PASSWORD=

MAPBOX_TOKEN=your_mapbox_token_here
```

## License

This project is proprietary software. All rights reserved.
