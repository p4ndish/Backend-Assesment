# Job Finder API

A comprehensive job search and application platform built with Laravel, featuring user authentication, email verification, and role-based access control.

## Deployed LINKS 
- PostMan: `http://49.13.233.60/docs?api-docs.json`
- Swagger Documentation: `http://49.13.233.60/api/documentation`
## 🚀 Features Implemented

- **User Authentication**: JWT-based authentication with email verification
- **Role-Based Access**: Separate interfaces for applicants and companies
- **Email Verification**: Secure email verification with automatic token renewal
- **API Documentation**: Complete Swagger/OpenAPI documentation
- **User Registration**: Complete registration flow with validation
- **User Login**: JWT token-based authentication
- **Email System**: Professional email templates with Mailtrap integration

## 🚧 Features In Progress

- **Job Management**: Create, view, and manage job listings
- **Application System**: Submit and track job applications
- **File Upload**: Cloudinary integration for resume and profile image storage

## 🛠 Technology Stack

### Backend
- **Laravel 11**: Modern PHP framework with robust features
- **PostgreSQL**: Reliable relational database
- **JWT Authentication**: Secure token-based authentication
- **L5-Swagger**: API documentation generation
- **Mailtrap**: Email testing and delivery

### Frontend Integration
- **RESTful API**: Clean, stateless API design
- **JSON Responses**: Consistent response format
- **CORS Support**: Cross-origin resource sharing enabled

### External Services
- **Mailtrap**: Email delivery and testing (configured)
- **Cloudinary**: Cloud-based file storage (planned)

## 📋 Prerequisites

- PHP 8.2 or higher
- Composer
- PostgreSQL 12 or higher

## 🔧 Installation & Setup

### 1. Clone the Repository
```bash
git clone <repository-url>
cd backend_assessment
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Environment Configuration
Copy the environment file and configure your settings:
```bash
cp .env.example .env
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Configure Database
Update your `.env` file with database credentials:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=assessment_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 6. Run Migrations
```bash
php artisan migrate
```

### 7. Generate JWT Secret
```bash
php artisan jwt:secret
```

### 8. Start the Development Server
```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## 🔐 Environment Variables

### Required Environment Variables

```env
# Application
APP_NAME="Job Finder"
APP_ENV=local
APP_KEY=base64:your_app_key_here
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=assessment_db
DB_USERNAME=your_username
DB_PASSWORD=your_password

# JWT Authentication
JWT_SECRET=your_jwt_secret_here

# Email Configuration (Mailtrap for testing)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@jobfinder.com"
MAIL_FROM_NAME="Job Finder"

# Swagger Documentation
L5_SWAGGER_CONST_HOST=http://localhost:8000
```

### Optional Environment Variables
```env
# Cloudinary (for future file uploads)
CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME
CLOUDINARY_UPLOAD_PRESET=your_upload_preset

# Redis (for caching and queues)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## 📚 API Documentation

### Swagger UI
Access the interactive API documentation at:
```
http://localhost:8000/api/documentation
```

### API Base URL
```
http://localhost:8000/api
```

### Implemented Endpoints

#### Authentication ✅
- `POST /auth/register` - User registration with email verification
- `POST /auth/login` - User login with JWT token
- `POST /auth/verify-email` - Email verification (API call)
- `GET /auth/verify-email` - Email verification (direct link click)
- `POST /auth/resend-verification` - Resend verification email
- `POST /auth/check-verification` - Check email verification status

#### User Management (Planned)
- `GET /auth/getUser` - Get current user (authenticated)
- `POST /auth/update-user` - Update user profile (authenticated)
- `POST /auth/update-password` - Update password (authenticated)

#### Job Management (Planned)
- `GET /jobs` - List all jobs
- `POST /jobs` - Create new job (company only)
- `GET /jobs/{id}` - Get specific job
- `PUT /jobs/{id}` - Update job (company only)
- `DELETE /jobs/{id}` - Delete job (company only)

#### Applications (Planned)
- `POST /applications` - Submit application (applicant only)
- `GET /applications` - List applications (filtered by role)
- `PUT /applications/{id}` - Update application status (company only)

## 🏗 Project Structure

```
backend_assessment/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── AuthController.php ✅
│   │   │   │   ├── RegisterController.php ✅
│   │   │   │   └── EmailVerificationController.php ✅
│   │   │   ├── ApiController.php ✅
│   │   │   └── StorageController.php ✅
│   │   ├── Requests/
│   │   │   └── Auth/
│   │   │       ├── RegisterRequest.php ✅
│   │   │       └── LoginRequest.php ✅
│   │   └── Resources/
│   │       ├── BaseResponse.php ✅
│   │       ├── PaginatedResponse.php ✅
│   │       ├── UserResource.php ✅
│   │       ├── JobListingResource.php ✅
│   │       └── ApplicationResource.php ✅
│   ├── Models/
│   │   ├── User.php ✅
│   │   ├── JobListing.php ✅
│   │   ├── Application.php ✅
│   │   └── EmailVerification.php ✅
│   └── Mail/
│       └── EmailVerificationMail.php ✅
├── database/
│   └── migrations/
│       ├── create_users_table.php ✅
│       ├── create_job_listings_table.php ✅
│       ├── create_applications_table.php ✅
│       └── create_email_verifications_table.php ✅
├── resources/
│   └── views/
│       ├── emails/
│       │   └── verify-email.blade.php ✅
│       └── auth/
│           └── email-verified.blade.php ✅
├── routes/
│   └── api.php ✅
└── storage/
    └── api-docs/
        └── api-docs.json ✅
```

## 🔒 Security Features Implemented

### Authentication & Authorization ✅
- **JWT Tokens**: Secure, stateless authentication
- **Role-Based Access**: Separate permissions for applicants and companies
- **Email Verification**: Required before account activation
- **Password Hashing**: Bcrypt encryption for passwords
- **Strong Password Validation**: Uppercase, lowercase, number, special character

### Data Protection ✅
- **Input Validation**: Comprehensive request validation
- **SQL Injection Prevention**: Eloquent ORM with parameter binding
- **XSS Protection**: Output escaping and sanitization
- **CSRF Protection**: Built-in Laravel CSRF protection

### Email Security ✅
- **Time-Limited Tokens**: 1-hour expiration for verification tokens
- **Token Regeneration**: Automatic new token generation for expired tokens
- **Secure Links**: HTTPS verification links
- **Token Cleanup**: Expired tokens are automatically deleted

## 📧 Email System ✅

### Mailtrap Integration
The project uses Mailtrap for email testing and development:
- All emails are captured in Mailtrap dashboard
- No real emails sent during development
- Professional email templates
- Automatic token renewal for expired verifications

### Email Templates ✅
- **Verification Email**: Professional HTML template with verification link
- **Success Page**: Beautiful HTML page for successful verification
- **Responsive Design**: Works on all devices

### Email Verification Flow ✅
1. User registers with valid data
2. System creates user account (unverified)
3. System generates secure token and sends verification email
4. User clicks verification link or uses token
5. System verifies token and marks email as verified
6. User can now log in to the system

## 🗄 Database Design ✅

### Core Tables Implemented
- **users**: User accounts with role-based access
- **job_listings**: Job postings with status management
- **applications**: Job applications with status tracking
- **email_verifications**: Email verification token management

### Relationships Implemented
- Users can create multiple jobs (companies)
- Users can submit multiple applications (applicants)
- Jobs can have multiple applications
- Email verifications linked to users

## 🚀 Non-Functional Requirements

### Performance ✅
- **Database Indexing**: Optimized queries with proper indexing
- **Eager Loading**: Prevents N+1 query problems
- **Database Transactions**: ACID compliance for data integrity

### Scalability ✅
- **Stateless API**: JWT-based authentication for horizontal scaling
- **Database Optimization**: Efficient queries and relationships
- **Clean Architecture**: Separation of concerns

### Reliability ✅
- **Database Transactions**: ACID compliance for data integrity
- **Error Handling**: Comprehensive exception handling
- **Validation**: Input validation at multiple layers
- **Logging**: Detailed logging for debugging and monitoring

### Maintainability ✅
- **Clean Architecture**: Separation of concerns
- **API Resources**: Consistent response formatting
- **Documentation**: Complete Swagger/OpenAPI documentation
- **Code Standards**: PSR-12 coding standards

### Usability ✅
- **Consistent API**: Standardized response format
- **Clear Error Messages**: User-friendly error responses
- **Comprehensive Documentation**: Interactive API documentation
- **Email Verification**: User-friendly verification process

## 🧪 Testing

### Manual Testing ✅
1. **Registration Flow**: Test user registration and email verification
2. **Login Flow**: Test authentication with JWT tokens
3. **Email Verification**: Test verification link clicks and token validation

### API Testing ✅
Use the Swagger UI at `http://localhost:8000/api/documentation` to test all endpoints interactively.

### Testing Steps ✅
1. **Register a User**:
   ```bash
   POST http://localhost:8000/api/auth/register
   {
     "name": "John Doe",
     "email": "john@example.com",
     "password": "Password123!",
     "password_confirmation": "Password123!",
     "role": "applicant"
   }
   ```

2. **Check Mailtrap**: View verification email in Mailtrap dashboard

3. **Verify Email**: Click verification link or use API endpoint

4. **Test Login**:
   ```bash
   POST http://localhost:8000/api/auth/login
   {
     "email": "john@example.com",
     "password": "Password123!"
   }
   ```

## 🔧 Development Commands

```bash
# Start development server
php artisan serve

# Run migrations
php artisan migrate

# Generate API documentation
php artisan l5-swagger:generate

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Generate JWT secret
php artisan jwt:secret

# Create new migration
php artisan make:migration create_table_name

# Create new controller
php artisan make:controller ControllerName

# Create new model
php artisan make:model ModelName
```

## 📋 User Stories Implemented

### ✅ User Story 1: Signup
- Complete user registration with validation
- Email verification system
- Role-based user creation (applicant/company)
- Professional email templates

### ✅ User Story 2: Email Verification
- Secure verification links
- Time-limited tokens (1 hour)
- Automatic token renewal
- User-friendly verification process

### ✅ User Story 3: Login
- JWT-based authentication
- Email verification requirement
- Secure password validation
- Token-based session management

## 🚧 Next Steps

### Planned Features
- **Job Management**: CRUD operations for job listings
- **Application System**: Job application submission and tracking
- **File Upload**: Resume and profile image uploads
- **User Profile Management**: Profile updates and password changes
- **Advanced Search**: Job search and filtering capabilities

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License.

## 📞 Support

For support and questions:
- Email: support@jobfinder.com
- API Documentation: http://localhost:8000/api/documentation
- Issues: Please use the GitHub issues page

---

**Job Finder API** - Building the future of job search and recruitment.
