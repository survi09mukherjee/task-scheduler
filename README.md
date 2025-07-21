# ⏰ Task Scheduler – PHP Cron-Based Task Manager

![Tech](https://img.shields.io/badge/Tech-PHP%20%7C%20Cron%20%7C%20HTML%2FCSS%2FJS-informational)

> A simple and customizable task scheduling system built with PHP and cron jobs. Automatically execute predefined tasks at regular intervals.

---

## ✨ Features

- 📋 Add, edit, and delete scheduled tasks
- ⏱️ Run tasks at specific intervals using system cron
- 📦 Store task configurations in files or database
- 🔔 Trigger email alerts or custom scripts
- 📊 User-friendly interface with HTML/CSS/JS

---

## 🔧 Tech Stack

- **Frontend**: HTML, CSS, JavaScript  
- **Backend**: PHP  
- **Task Runner**: Linux cron jobs (or Windows Task Scheduler)

---

## 🚀 Getting Started

### 1. Clone the Repository

```bash
git clone https://github.com/survi09mukherjee/task-scheduler.git
cd task-scheduler
```

### 2. Run Locally

You can use PHP’s built-in server or XAMPP:

```bash
php -S localhost:8000
```

Open your browser at:  
[http://localhost:8000](http://localhost:8000)

---

## ⚙️ Setting Up Cron Jobs (Linux/Mac)

1. Open crontab:
   ```bash
   crontab -e
   ```

2. Add a line like this to run every minute:
   ```bash
   * * * * * php /full/path/to/task-scheduler/runner.php
   ```

> Replace `/full/path/to/` with the absolute path of your project directory.

---

## 🖼 Screenshot

>
<img width="724" height="470" alt="1" src="https://github.com/user-attachments/assets/aea93b59-cdad-493d-959b-6021e18d5f9a" />
<img width="726" height="319" alt="2" src="https://github.com/user-attachments/assets/4473aef3-43f6-4c85-b776-5822d7aa3732" />
<img width="912" height="643" alt="3" src="https://github.com/user-attachments/assets/85edf6a0-1e36-442b-b3a9-051889e4b811" />

---

## 📁 Project Structure

```
task-scheduler/
├── index.php         # Main UI for task scheduling
├── runner.php        # Executes tasks based on schedule
├── tasks/            # Directory containing individual task scripts
├── assets/           # UI assets (CSS/JS/images)
└── README.md
```

---

## 🛠 To-Do / Improvements

- Database-based task logs  
- Web UI for cron setup preview  
- Task execution history with timestamps  
- Add authentication and admin panel  
- Mobile responsive UI  

---

## 🙋‍♀️ Author

**Survi Mukherjee**  
🔗 [GitHub Profile](https://github.com/survi09mukherjee)

---

## 🌟 Support

If you like this project, please ⭐ star it and share with others!
