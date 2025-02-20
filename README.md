#Informative Chatbot using Laravel 11
# 🤖 **Informative Chatbot using Laravel 11**

An intelligent, dynamic chatbot system built with Laravel 11, blade ,React, PHP 8.2, and MySQL. This project provides fast, accurate, and real-time responses to user queries, making it perfect for websites, customer support platforms, and it is made for good working  portals.

---

## 🚀 **Key Features**

✅ Dynamic chatbot responses based on user queries  
✅ Fuzzy matching for accurate answers  
✅ Caching & indexing for faster replies  
✅ Real-time message updates using WebSockets  
✅ Background jobs for heavy tasks  
✅ Rate limiting & logging for security  
✅ Elegant, user-friendly React frontend  

---

## ⚙️ **Tech Stack**

- **Backend:** Laravel 11, PHP 8.2  
- **Frontend:** blade  
- **Database:** MySQL  
- **Environment:** XAMPP, Composer, Node.js  

---

## 📦 **Installation & Setup**

### 1. **Clone the Repository**

```bash
git clone https://github.com/akshaysharmaksg/Informative-Chatbot-using-Laravel-11-.git
cd Informative-Chatbot-using-Laravel-11-

# Install backend dependencies
composer install

# Install frontend dependencies
npm install
cp .env.example .env
php artisan key:generate
DB_DATABASE=chatbot
DB_USERNAME=root
DB_PASSWORD=
php artisan migrate --seed
# Start the Laravel backend
php artisan serve

# Start the React frontend
npm run dev
