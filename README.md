# 🏢 Hostel Management System

![License](https://img.shields.io/badge/License-MIT-blue.svg)  
> A simple and responsive web-based Hostel Management System built using PHP, HTML, CSS, JavaScript, and MySQL. This system helps hostel administrators manage student registrations, room availability, and complaints efficiently.

---

## 🌐 Live Demo

🔗 [https://hostelmanagement.free.nf/](https://hostelmanagement.free.nf/)

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
| Hosting     | Infinityfree(Frontend & Backend) |

---

## 🚀 Getting Started

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
- Import the file `hostel_db.sql` from the repo

3. **Configure Database Connection**

Edit `db_connect.php` with your database credentials:

```php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "hostel_db";
```

4. **Run Locally**

- Start Apache and MySQL from XAMPP
- Navigate to `http://localhost/hostelmanagement/` in your browser

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

## 📝 License

This project is licensed under the **MIT License**. See the [LICENSE](LICENSE) file for details.

---

## 💬 Feedback

Feel free to open an issue or submit a pull request for suggestions or improvements!

If you like this project, please ⭐ the repo and share it with others.
