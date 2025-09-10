# 🏢 Hostel Management System

> A simple and responsive web-based Hostel Management System built using PHP, HTML, CSS, JavaScript, and MySQL. This system helps hostel administrators manage student registrations, room availability, and complaints efficiently.

---

## ✨ Features

- 📝 **Student Registration**
- 🛏️ **Room Availability Display**
- 📋 **View All Registered Students**
- 🗣️ **Complaint Submission Form**
- 🔐 **Student Login System**
- 🎨 **Dark Glassmorphism UI Theme**
- 📱 **Fully Responsive Design**

---

## 🧰 Tech Stack

| Layer       | Technology              |
|-------------|--------------------------|
| Frontend    | HTML, CSS, JavaScript    |
| Backend     | PHP                      |
| Database    | MySQL                    |
| Hosting     | InfinityFree / Localhost |

---

## 🚀 Getting Started (Localhost)

### 🔧 Requirements

- XAMPP / WAMP / LAMP or any local server
- MySQL Database
- Web Browser

### 🛠️ Setup Instructions

1. **Clone the Repository**

```bash
git clone https://github.com/thelokesh369/hostelmanagement.git
cd hostelmanagement
```

2. **Import the Database**

- Open `phpMyAdmin`
- Create a database named `hostel_db`
- Import `hostel_db.sql` from the repo

3. **Configure Database Connection**

Edit `db_connect.php`:

```php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "hostel_db";
```

4. **Run Locally**

- Start Apache and MySQL from XAMPP
- Go to `http://localhost/hostelmanagement/`

---

## ☁️ Hosting on InfinityFree

You can deploy this project for free on [InfinityFree](https://infinityfree.net):

### 🌐 Deployment Steps

1. **Sign Up at** [infinityfree.net](https://infinityfree.net)  
2. **Upload Your Files**
   - Use File Manager or FTP to upload all files (except `.sql`) to the `htdocs/` folder
3. **Create MySQL Database**
   - In the Control Panel → MySQL Databases
   - Copy the:
     - DB Name
     - Username
     - Password
     - Hostname (e.g., `sql301.epizy.com`)
4. **Import `hostel_db.sql`**
   - Access phpMyAdmin
   - Import `hostel_db.sql` into your new DB
5. **Update `db_connect.php`**

```php
$host = "your-database-host";     // e.g., sql301.epizy.com
$user = "your-database-username";
$password = "your-db-password";
$dbname = "your-database-name";
```

6. ✅ Visit your URL: `https://your-subdomain.free.nf`

---

## 📁 Project Structure

```
hostelmanagement/
│
├── css/
│   └── style.css
├── js/
│   └── main.js
├── db_connect.php
├── register_student.php
├── view_students.php
├── login.php
├── index.html
├── hostel_db.sql
└── README.md
```

---

## 👤 Author

  
📧 Email: [lokesharumugam1826@gmail.com](mailto:lokesharumugam1826@gmail.com)

---


## 💬 Feedback

Feel free to open issues or submit pull requests for ideas, bugs, or improvements.  
If you like this project, give it a ⭐ and share it!
