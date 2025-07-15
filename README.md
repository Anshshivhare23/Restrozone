# Restrozone - Restaurant Management System

[![Live Demo](https://img.shields.io/badge/Live%20Demo-Visit%20Site-blue)](https://restrozone.free.nf/)

## 🍽️ About Restrozone

Restrozone is a comprehensive restaurant management system that allows users to browse restaurants, order food online, and book tables. The platform provides separate dashboards for customers, administrators, and restaurant owners.

**Live Website**: [https://restrozone.free.nf/](https://restrozone.free.nf/)

## ✨ Features

### Customer Features
- 🔐 **User Registration & Login** - Secure authentication system
- 🏪 **Restaurant Browsing** - View available restaurants by category
- 🍜 **Menu Browsing** - Browse dishes with images and descriptions
- 🛒 **Online Ordering** - Add items to cart and place orders
- 📅 **Table Booking** - Reserve tables at restaurants
- 👤 **Profile Management** - Update personal information
- 📦 **Order History** - Track current and past orders
- 💬 **Feedback System** - Leave reviews and ratings

### Admin Features
- 📊 **Dashboard** - Complete overview of system statistics
- 🏪 **Restaurant Management** - Add, edit, and remove restaurants
- 🍕 **Menu Management** - Manage dishes and categories
- 👥 **User Management** - View and manage customer accounts
- 📋 **Order Management** - Process and track orders
- 🔧 **System Settings** - Configure platform settings

### Restaurant Owner Features
- 🏪 **Restaurant Dashboard** - Manage restaurant-specific data
- 📊 **Order Tracking** - Monitor incoming orders
- 🍽️ **Menu Updates** - Add and modify dishes
- 📈 **Sales Analytics** - View performance metrics

## 🛠️ Tech Stack

- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap
- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Server**: Apache (XAMPP)
- **Styling**: Font Awesome, Animate.css

## 🏗️ Project Structure

```
Restrozone/
├── admin/              # Admin panel files
│   ├── dashboard.php
│   ├── add_restaurant.php
│   ├── all_orders.php
│   ├── all_users.php
│   └── ...
├── connection/         # Database connection
│   └── connect.php
├── css/               # Stylesheets
│   ├── bootstrap.min.css
│   ├── style.css
│   └── ...
├── DATABASE FILE/     # Database schema
│   ├── restrozone.sql
│   └── table_management.sql
├── images/            # Image assets
├── js/                # JavaScript files
├── restaurant/        # Restaurant owner panel
├── index.php          # Homepage
├── login.php          # User login
├── registration.php   # User registration
├── restaurants.php    # Restaurant listing
├── dishes.php         # Menu items
├── table_book.php     # Table booking
├── your_orders.php    # Order history
└── feedback.php       # Feedback system
```

## 🚀 Installation & Setup

### Prerequisites
- XAMPP or WAMP server
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Steps to Run Locally

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/restrozone.git
   cd restrozone
   ```

2. **Start XAMPP**
   - Start Apache and MySQL services

3. **Database Setup**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `restrozone`
   - Import the SQL file: `DATABASE FILE/restrozone.sql`

4. **Configure Database Connection**
   - Edit `connection/connect.php` with your database credentials:
   ```php
   $host = "localhost";
   $username = "root";
   $password = "";
   $dbname = "restrozone";
   ```

5. **Access the Application**
   - Open browser and navigate to `http://localhost/restrozone`

## 📱 User Guide

### For Customers
1. **Register/Login** - Create an account or login with existing credentials
2. **Browse Restaurants** - View available restaurants by category
3. **Order Food** - Select dishes and add to cart
4. **Book Tables** - Reserve tables for dining
5. **Track Orders** - Monitor order status in real-time

### For Administrators
1. **Login** - Access admin panel at `/admin/`
2. **Default Credentials**:
   - Username: `restrozone`
   - Password: `restrozone123`
3. **Manage System** - Handle restaurants, orders, and users

## 🍜 Available Restaurants

The platform features four main restaurant categories:

1. **Chinese** - Noodles, Manchurian, Dumplings
2. **South Indian** - Dosa, Idli, Biryani, Medu Vada
3. **North Indian** - Butter Chicken, Fish Curry, Aloo Paratha
4. **Maharashtrian** - Misal Pav, Vada Pav, Batata Vada, Poha

## 📊 Database Schema

### Key Tables
- `admin` - Administrator accounts
- `users` - Customer accounts
- `restaurant` - Restaurant information
- `dishes` - Menu items
- `users_orders` - Order records
- `table_bookings` - Table reservations
- `res_category` - Restaurant categories

## 🔒 Security Features

- Password hashing using MD5
- Session management for user authentication
- SQL injection prevention
- Input validation and sanitization
- Role-based access control

## 🎨 UI/UX Features

- **Responsive Design** - Works on desktop and mobile
- **Modern Interface** - Clean and intuitive design
- **Animation Effects** - Smooth transitions and hover effects
- **User-Friendly Navigation** - Easy-to-use menu system
- **Visual Appeal** - High-quality food images

## 📈 Performance Optimizations

- Optimized database queries
- Image compression for faster loading
- Minified CSS and JavaScript files
- Efficient session handling
- Proper indexing in database

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📞 Support

For support and queries:
- Email: anshshivhare9@gmail.com
- Live Demo: [https://restrozone.free.nf/](https://restrozone.free.nf/)

## 🚀 Future Enhancements

- [ ] Payment Gateway Integration
- [ ] Mobile App Development
- [ ] Real-time Order Tracking
- [ ] Advanced Analytics Dashboard
- [ ] Multi-language Support
- [ ] Email Notifications
- [ ] Social Media Integration
- [ ] Customer Reviews & Ratings

## 📷 Screenshots
1. User Dashboard
<img width="1916" height="930" alt="image" src="https://github.com/user-attachments/assets/9060ae26-6b6f-41bc-a5b2-ac554d212279" />
<img width="1900" height="897" alt="image" src="https://github.com/user-attachments/assets/8af36ad1-a121-4897-a2eb-7a1dd68bfad5" />
<img width="1919" height="872" alt="image" src="https://github.com/user-attachments/assets/41b8dd6d-f98a-4d92-9aa2-471bd7235571" />

2. Restaurant Dashboard
<img width="1902" height="868" alt="image" src="https://github.com/user-attachments/assets/8baf6d90-312a-4885-b4fd-71565a259ac2" />

3. Admin Dashboard
<img width="1899" height="867" alt="image" src="https://github.com/user-attachments/assets/0056ee23-0044-4714-9218-196a13fe92a6" />


---

⭐ **Star this repository if you find it helpful!**

**Live Demo**: [https://restrozone.free.nf/](https://restrozone.free.nf/)
