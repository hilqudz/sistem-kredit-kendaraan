# Vehicle Credit Application System

A web-based vehicle credit application and approval system built with Laravel and MySQL, configured for Railway deployment.

## 🚀 Key Features

- **Credit Application System**: Interface for sales to submit customer credit applications
- **Approval Dashboard**: Interface for marketing supervisors to approve or reject applications
- **ID Card Upload**: Upload and store ID card images in Base64 format
- **Strict Validation**: Validation for ID numbers, vehicle prices, and customer data
- **Responsive Design**: Responsive interface using Tailwind CSS
- **Security Headers**: Implementation of security headers for application security
- **Railway Ready**: Complete configuration for Railway deployment

## 📋 Main Features

### Application Form (Sales)
- Input customer data (name, ID number, vehicle type, price)
- Upload ID card photo with validation
- Unique ID number validation (no duplicates allowed)
- Vehicle price validation ($10,000 - $2 billion)

### Approval Dashboard (Marketing Supervisor)
- View all applications with status
- Filter by status (Submitted, Approved, Rejected)
- Search by name or ID number
- Approve/reject applications with notes
- View ID card images in modal
- Statistics dashboard

## 🛠️ Tech Stack

- **Backend**: PHP 8.2, Laravel 11
- **Database**: MySQL
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
- **Deployment**: Railway with Docker
- **Storage**: Base64 image storage (Railway compatible)

## 📦 Installation

### Local Development

1. **Clone repository**
```bash
git clone <repository-url>
cd credit-system
```

2. **Install dependencies**
```bash
composer install
```

3. **Setup environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database** 
Update `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=credit_system
DB_USERNAME=root
DB_PASSWORD=
```

5. **Run migrations**
```bash
php artisan migrate
```

6. **Start development server**
```bash
php artisan serve
```

Visit http://localhost:8000

### Railway Deployment

1. **Prepare Railway Project**
   - Create new project on Railway
   - Add MySQL database service
   - Connect GitHub repository

2. **Configure Environment Variables**
   - Set the following variables in Railway dashboard:
   ```
   APP_NAME="Credit System"
   APP_ENV=production
   APP_KEY=(generated automatically)
   APP_DEBUG=false
   APP_URL=https://your-app.railway.app
   
   DB_CONNECTION=mysql
   DB_HOST=(from Railway MySQL service)
   DB_PORT=3306
   DB_DATABASE=(from Railway MySQL service)
   DB_USERNAME=(from Railway MySQL service)
   DB_PASSWORD=(from Railway MySQL service)
   ```

3. **Deploy**
   - Push code to GitHub
   - Railway will automatically build and deploy using Dockerfile

## 📁 Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── CreditController.php
│   │   ├── Middleware/
│   │   │   └── SecurityHeaders.php
│   │   └── Requests/
│   │       └── CreditApplicationRequest.php
│   └── Models/
│       └── CreditApplication.php
├── database/
│   └── migrations/
│       └── 2024_01_01_000001_create_credit_applications_table.php
├── resources/
│   └── views/
│       └── dashboard.blade.php
├── routes/
│   └── web.php
├── docker/
│   └── apache.conf
├── Dockerfile
├── railway-start.sh
└── README.md
```

## 🔒 Security Features

- **CSRF Protection**: CSRF tokens for all forms
- **Input Validation**: Strict validation for all inputs
- **Security Headers**: X-Frame-Options, CSP, XSS Protection
- **File Upload Security**: File type and size validation
- **Database Security**: Prepared statements, input sanitization

## 📊 Database Schema

### Table: credit_applications

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT(20) | Primary key |
| customer_name | VARCHAR(255) | Customer name |
| nik | VARCHAR(16) | Customer ID number (unique) |
| vehicle_type | VARCHAR(100) | Vehicle type |
| vehicle_price | DECIMAL(15,2) | Vehicle price |
| ktp_image_base64 | TEXT | ID card image in Base64 |
| status | VARCHAR(50) | Application status |
| notes | TEXT | Approval/rejection notes |
| approved_at | TIMESTAMP | Approval time |
| approved_by | VARCHAR(255) | Approver name |
| created_at | TIMESTAMP | Application time |
| updated_at | TIMESTAMP | Last update time |

## 🎯 API Endpoints

| Method | URL | Description |
|--------|-----|-------------|
| GET | `/` | Main dashboard |
| POST | `/submit` | Submit credit application |
| POST | `/approve/{id}` | Approve application |
| POST | `/reject/{id}` | Reject application |
| GET | `/health` | Health check endpoint |

## 📱 Screenshots

### Application Form
- Clean interface for customer data input
- ID card upload with drag & drop
- Real-time validation

### Approval Dashboard
- Table with pagination
- Filter and search functionality
- Modal for viewing ID cards
- Statistics cards

## 🚨 Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Ensure MySQL service is running
   - Check database credentials
   - Verify network connectivity

2. **File Upload Error**
   - Check file size (max 2MB)
   - Verify image format (JPG/PNG)
   - Ensure proper permissions

3. **Railway Deployment Issues**
   - Check environment variables
   - Verify Dockerfile syntax
   - Monitor build logs

## 📞 Support

For questions or issues:
1. Check this documentation
2. Review error logs
3. Contact development team

## 📄 License

MIT License

---

**Built with ❤️ for easy vehicle credit applications**