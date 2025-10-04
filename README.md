

# E-Learning Platform

A comprehensive e-learning platform built with Laravel backend and Vue.js frontend, featuring student management, course enrollment, assignments, and real-time notifications.

## 🚀 Features

### Student Features
- **User Authentication**: Registration, login, email verification, password reset
- **Dashboard**: Personal dashboard with enrolled courses, assignments, and statistics
- **Course Management**: Browse, enroll, and access course content
- **Assignment System**: View assignments, submit work, track progress
- **Profile Management**: Update profile information and upload profile pictures
- **Real-time Notifications**: Get notified about new assignments and course updates

### Admin Features
- **Course Management**: Create, edit, and manage courses
- **Assignment Creation**: Create assignments for specific courses
- **User Management**: Manage students and instructors
- **Notification System**: Send notifications to enrolled students

### Technical Features
- **Responsive Design**: Works on desktop, tablet, and mobile devices
- **Dark Mode**: Toggle between light and dark themes
- **Real-time Updates**: Auto-refreshing notifications and data
- **File Upload**: Profile pictures and assignment file uploads
- **Email Notifications**: Automated email notifications for important events

## 🛠️ Technology Stack

### Backend
- **Laravel 12**: PHP framework
- **MySQL**: Database
- **Laravel Sanctum**: API authentication
- **Laravel Notifications**: Email and database notifications
- **Laravel Storage**: File management

### Frontend
- **Vue.js 3**: JavaScript framework
- **Vite**: Build tool
- **Tailwind CSS**: Styling framework
- **Vue Router**: Client-side routing
- **Axios**: HTTP client
- **Heroicons**: Icon library


## 🚀 Installation & Setup

### Backend Setup

1. **Navigate to backend directory**:
   ```bash
   cd backend
   ```

2. **Install dependencies**:
   ```bash
   composer install
   ```

3. **Environment setup**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**:
   ```bash
   php artisan migrate
   php artisan storage:link
   ```

5. **Start server**:
   ```bash
   php artisan serve
   ```

### Frontend Setup

1. **Navigate to frontend directory**:
   ```bash
   cd e-learning/e-learning
   ```

2. **Install dependencies**:
   ```bash
   npm install
   ```

3. **Start development server**:
   ```bash
   npm run dev
   ```

#### Frontend (.env)
```env
VITE_API_URL=http://127.0.0.1:8000/api/v1
```

## 📚 API Endpoints

### Authentication
- `POST /api/v1/student/register` - Student registration
- `POST /api/v1/student/login` - Student login
- `POST /api/v1/forgot-password` - Password reset request
- `POST /api/v1/reset-password` - Password reset

### Student Dashboard
- `GET /api/v1/student/dashboard` - Dashboard statistics
- `GET /api/v1/student/assignments` - Student assignments
- `GET /api/v1/student/notifications` - Student notifications
- `POST /api/v1/student/notifications/{id}/read` - Mark notification as read
- `POST /api/v1/student/notifications/mark-all-read` - Mark all as read

### Profile Management
- `GET /api/v1/student/profile` - Get profile
- `PUT /api/v1/student/profile` - Update profile
- `POST /api/v1/profile/upload-photo` - Upload profile photo

### Courses & Assignments
- `GET /api/v1/courses/public` - Public courses
- `POST /api/v1/assignments` - Create assignment (Admin)
- `GET /api/v1/assignments` - List assignments

## 🎨 UI Components

### Main Components
- **StudentDashboard**: Main student interface
- **TopHeader**: Navigation with notifications
- **Sidebar**: Dashboard navigation
- **ProfileTab**: Profile management
- **CourseCard**: Course display component

### Authentication Components
- **LoginForm**: Student login
- **SignupForm**: Student registration
- **ForgotPasswordForm**: Password reset request
- **ResetPassword**: Password reset form

## 🔔 Notification System

### Features
- **Real-time Notifications**: Auto-refresh every 30 seconds
- **Email Notifications**: Automated email alerts
- **Database Storage**: Persistent notification history
- **Arabic Content**: All notifications in Arabic

### Notification Types
- **New Assignment**: When admin creates assignment
- **Course Updates**: Course-related notifications
- **System Notifications**: Platform updates

## 🎯 Key Features Implemented

### ✅ Completed Features
1. **Student Authentication System**
2. **Profile Management with Photo Upload**
3. **Course Enrollment System**
4. **Assignment Management**
5. **Real-time Notification System**
6. **Responsive Design with Dark Mode**
7. **Arabic Language Support**
8. **Email Notifications**
9. **File Upload System**
10. **Dashboard Statistics**

### 🔧 Technical Implementations
- **Laravel Sanctum Authentication**
- **Vue.js Composition API**
- **Tailwind CSS Styling**
- **Axios HTTP Client**
- **Laravel Notifications**
- **File Storage Management**
- **Database Relationships**
- **API Resource Classes**

## 🚀 Deployment

### Production Setup
1. **Backend**: Deploy Laravel to server with PHP 8.1+
2. **Frontend**: Build Vue.js app with `npm run build`
3. **Database**: Setup MySQL database
4. **Storage**: Configure file storage
5. **Email**: Setup SMTP for notifications

### Build Commands
```bash
# Frontend build
npm run build

# Backend optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📝 Development Notes

### Code Structure
- **Backend**: Follows Laravel conventions
- **Frontend**: Uses Vue.js Composition API
- **Styling**: Tailwind CSS with custom components
- **State Management**: Vue.js reactive system
- **API Communication**: Axios with interceptors

### Best Practices
- **Security**: CSRF protection, input validation
- **Performance**: Database optimization, caching
- **UX**: Loading states, error handling
- **Accessibility**: ARIA labels, keyboard navigation
- **Responsive**: Mobile-first design approach
