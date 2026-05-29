# LvlUp Project Setup Guide for Collaborators

Welcome to the **LvlUp** codebase! If you have cloned or pulled the repository and need to set up your local development environment, follow this guide to configure and launch the application.

---

## 🛠️ Step 1: Environment Configuration

1. **Copy the Environment File:**
   ```bash
   cp .env.example .env
   ```

2. **Configure Database Connection:**
   Open the `.env` file and set up your local database details. For example:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=lvlup
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

3. **Check Cache/Session/Queue Settings:**
   *Note: In previous versions, the system was configured to use Valkey/Redis. To avoid needing a running Redis server locally, we have configured the drivers to use `database` or `sync` as fallback. Ensure your `.env` contains the following:*
   ```env
   CACHE_STORE=database
   SESSION_DRIVER=database
   QUEUE_CONNECTION=sync
   ```

4. **Third-Party Services (Optional):**
   - **Cloudinary (for thumbnails/certificates uploads):**
     Ensure you configure `CLOUDINARY_URL` if you want dynamic uploads to work locally.
     ```env
     CLOUDINARY_URL=cloudinary://api_key:api_secret@cloud_name
     ```
   - **NVIDIA AI (for Resume AI Pipeline):**
     Ensure you configure `NVIDIA_API_KEY` if you want to test the resume generation pipeline.
     ```env
     NVIDIA_API_KEY=your_key_here
     ```

---

## 📦 Step 2: Install Dependencies

1. **Install PHP Packages (Composer):**
   ```bash
   composer install
   ```

2. **Install JavaScript/CSS Packages (NPM):**
   ```bash
   npm install
   ```

3. **Generate App Key:**
   ```bash
   php artisan key:generate
   ```

---

## 🗄️ Step 3: Database & Seeding

1. **Run Migrations & Seeders:**
   This command will compile your schemas, set up the `users`, `projects`, `skills`, `skill_nodes`, and `badges` tables, and load the skill tree data:
   ```bash
   php artisan migrate:fresh --seed
   ```

2. **Default Test Account Created:**
   - **Email:** `test@lvlup.dev`
   - **Password:** `password`
   - **Pre-populated Stats:** Level 5, 10 Skill Points, 500 Primogems, and 3-day active streak.

---

## 🚀 Step 4: Run the Application

1. **Compile Assets (Vite):**
   - For active development (hot-reload):
     ```bash
     npm run dev
     ```
   - For a production-ready build:
     ```bash
     npm run build
     ```

2. **Start the Laravel Dev Server:**
   ```bash
   php artisan serve
   ```
   *Your local site will be running at `http://127.0.0.1:8000`.*

---

## 🧪 Step 5: Verify Setup (Run Tests)

To ensure everything is working correctly on your machine, run the test suite:
```bash
php artisan test
```

Enjoy coding on LvlUp! 🎮
