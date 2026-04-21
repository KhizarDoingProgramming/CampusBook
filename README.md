# 🎓 CampusBook | Social Network for Students

CampusBook is a premium, feature-rich social networking platform designed specifically for university students. It allows students to connect, share updates, organize events, find study partners, and manage campus groups in a modern, responsive environment.

![CampusBook Preview](https://images.unsplash.com/photo-1523240715181-2a049774662c?w=1200&q=80)

## ✨ Key Features

- **📱 Dynamic Feed**: Real-time interaction with posts, including likes, comments, and saved posts.
- **📸 Stories**: 24-hour disappearing stories to share quick updates.
- **🌓 Dark Mode**: Fully integrated dark and light themes with smooth transitions.
- **👥 Group Management**: Create and join departmental or interest-based groups.
- **📅 Event Tracking**: Stay updated with upcoming campus events and hackathons.
- **📚 Study Partners**: Connect with other students for collaborative learning.
- **🛡️ Admin Dashboard**: Robust control panel for managing users and monitoring platform activity.
- **💬 Real-time Chat UI**: Premium messaging interface for quick communication.

## 🛠️ Tech Stack

- **Frontend**: HTML5, Vanilla CSS3 (Custom Design System), JavaScript (ES6+).
- **Backend**: PHP 8.x (Procedural/Functional).
- **Database**: MariaDB / MySQL.
- **Styling**: Google Fonts (Inter & Outfit), FontAwesome 6.4.

## 🚀 Getting Started

### Prerequisites
- PHP 8.0 or higher
- MySQL/MariaDB
- A local server environment (Apache/Nginx or XAMPP/WAMP)

### Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/your-username/CampusBook.git
   cd CampusBook
   ```

2. **Database Setup**:
   - Create a new database named `campusbook`.
   - Import the `database.sql` file provided in the root directory.
   - Configure your connection in `includes/db.php`.

3. **Run the Application**:
   - Move the project to your web root (e.g., `htdocs` or `www`).
   - Access the site via `http://localhost/CampusBook`.

### Default Credentials
- **Admin**: `admin@campusbook.com` / `password`
- **User**: `john@campusbook.com` / `password`

## 📂 Project Structure

```text
├── admin/          # Admin dashboard and management logic
├── api/            # Backend endpoints for async operations
├── assets/         # CSS, JS, and image assets
├── db_data/        # Local database storage (ignored in git)
├── includes/       # Reusable components (Header, Footer, DB config)
├── uploads/        # User-uploaded profile pictures and post images
└── index.php       # Main application entry point
```

## 📄 License
This project is for educational purposes as part of a Web Technologies coursework.

---
*Created with ❤️ for students, by Mustafa.*
