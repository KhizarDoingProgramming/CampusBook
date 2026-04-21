CREATE DATABASE IF NOT EXISTS campusbook;
USE campusbook;

-- Drop existing tables to recreate with new structure and data
DROP TABLE IF EXISTS group_members;
DROP TABLE IF EXISTS groups;
DROP TABLE IF EXISTS saved_posts;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS likes;
DROP TABLE IF EXISTS stories;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS study_requests;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    department VARCHAR(100),
    profile_pic VARCHAR(255) DEFAULT NULL,
    bio TEXT,
    is_online BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255),
    category ENUM('General', 'Study Help', 'Events') DEFAULT 'General',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE stories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    duration INT DEFAULT 5, -- seconds to display
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    UNIQUE KEY unique_like (user_id, post_id)
);

CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    event_date DATE NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE study_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE saved_posts (
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, post_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

CREATE TABLE groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    cover_image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE group_members (
    group_id INT NOT NULL,
    user_id INT NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (group_id, user_id),
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ==========================================
-- MASSIVE SEED DATA FOR DEMO PURPOSES
-- ==========================================

-- Password is 'password' for all users
INSERT INTO users (name, email, password, department, profile_pic) VALUES 
('Admin User', 'admin@campusbook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Computer Science', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&h=150&fit=crop'),
('John Doe', 'john@campusbook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Engineering', 'https://images.unsplash.com/photo-1599566150163-29194dcaad36?w=150&h=150&fit=crop'),
('Emma Wilson', 'emma@campusbook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Arts', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150&h=150&fit=crop'),
('Michael Brown', 'michael@campusbook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Business', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop'),
('Sarah Davis', 'sarah@campusbook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Biology', 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop'),
('James Miller', 'james@campusbook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Physics', 'https://images.unsplash.com/photo-1527980965255-d3b416303d12?w=150&h=150&fit=crop'),
('Olivia Taylor', 'olivia@campusbook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Architecture', 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150&h=150&fit=crop'),
('David Anderson', 'david@campusbook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Law', 'https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?w=150&h=150&fit=crop');

-- Insert 15+ Posts (Using Unsplash absolute URLs so images load natively)
INSERT INTO posts (user_id, content, image, category, created_at) VALUES 
(2, 'Just finished my final project for Data Structures! So relieved.', NULL, 'General', DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(3, 'The sunset from the library is incredible today! Who else is studying late?', 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=800&q=80', 'General', DATE_SUB(NOW(), INTERVAL 3 HOUR)),
(4, 'Does anyone have the syllabus for Econ 101? I seem to have lost my copy and the midterms are approaching fast.', NULL, 'Study Help', DATE_SUB(NOW(), INTERVAL 5 HOUR)),
(5, 'Our biology lab results came back and we absolutely crushed it! Thanks to my awesome lab partners.', 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=800&q=80', 'General', DATE_SUB(NOW(), INTERVAL 12 HOUR)),
(6, 'Reminder: The Physics club is hosting a stargazing event this Friday night at the observatory. Everyone is welcome!', NULL, 'Events', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(7, 'Spending the afternoon working on my new architectural models. Architecture students, how are we holding up?', 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800&q=80', 'General', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(8, 'Can anyone explain the concept of strict liability in Tort law? I am struggling with the upcoming assignment.', NULL, 'Study Help', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 'Welcome everyone to the new CampusBook! We hope this platform helps you connect with your peers better. Let us know if you find any bugs!', 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&q=80', 'General', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(3, 'Art exhibition opening next week at the student center! Check out the amazing work by the senior class.', 'https://images.unsplash.com/photo-1460661419201-fd4cecdf8a8b?w=800&q=80', 'Events', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(2, 'Hackathon preparations are in full swing! Coding all night.', 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=800&q=80', 'General', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(4, 'Anyone up for a coffee run? I am falling asleep in the study hall.', NULL, 'General', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(5, 'Study group for Bio 202 is meeting at 5 PM in room 304. Bring your textbook!', NULL, 'Study Help', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(7, 'Just submitted my final design! Time to finally get some sleep.', 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=800&q=80', 'General', DATE_SUB(NOW(), INTERVAL 6 DAY)),
(8, 'Mock trial was intense today but we won! Great job team.', NULL, 'General', DATE_SUB(NOW(), INTERVAL 7 DAY));

-- Insert Comments
INSERT INTO comments (user_id, post_id, comment, created_at) VALUES 
(3, 1, 'Congrats John! You deserve a break.', DATE_SUB(NOW(), INTERVAL 50 MINUTE)),
(4, 1, 'Let''s celebrate tonight!', DATE_SUB(NOW(), INTERVAL 45 MINUTE)),
(1, 3, 'I can send you the PDF, check your messages.', DATE_SUB(NOW(), INTERVAL 4 HOUR)),
(5, 4, 'We make a great team!', DATE_SUB(NOW(), INTERVAL 11 HOUR)),
(2, 5, 'I will be there! Sounds awesome.', DATE_SUB(NOW(), INTERVAL 23 HOUR));

-- Insert Likes
INSERT INTO likes (user_id, post_id) VALUES 
(1,1), (3,1), (4,1), (5,1),
(2,2), (4,2), (6,2), (8,2), (1,2),
(1,3), (5,3),
(2,4), (3,4), (7,4),
(1,5), (2,5), (3,5), (4,5);

-- Insert Stories
INSERT INTO stories (user_id, image_url, created_at) VALUES 
(3, 'https://images.unsplash.com/photo-1513258496099-48166d2847ea?w=600&h=1000&fit=crop', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(4, 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=600&h=1000&fit=crop', DATE_SUB(NOW(), INTERVAL 5 HOUR)),
(7, 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=600&h=1000&fit=crop', DATE_SUB(NOW(), INTERVAL 8 HOUR)),
(2, 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=600&h=1000&fit=crop', DATE_SUB(NOW(), INTERVAL 12 HOUR));

-- Insert Events
INSERT INTO events (title, event_date, description) VALUES 
('Spring Hackathon 2026', '2026-05-15', 'Join us for a 48-hour coding marathon! Food and drinks provided. Win awesome prizes.'),
('Career Fair', '2026-04-25', 'Meet top employers and find your next internship. Over 50 companies attending.'),
('Open Mic Night', '2026-04-20', 'Come show off your talent at the student union! Music, poetry, comedy, everything is welcome.');

-- Insert Groups
INSERT INTO groups (name, description, cover_image) VALUES 
('Computer Science Society', 'For all CS majors to discuss code, projects, and career advice.', 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=800&q=80'),
('Photography Club', 'Share your best shots and organize weekend photo walks.', 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&q=80'),
('Law Student Association', 'Study groups and debate practice for law students.', 'https://images.unsplash.com/photo-1589829085413-56de8ae18c73?w=800&q=80'),
('Campus Bookworms', 'Monthly book discussion group. All are welcome.', 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=800&q=80');
