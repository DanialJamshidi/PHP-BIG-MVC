# 🧩 MVC Framework – Custom PHP Core

A full‑stack MVC framework built with PHP 8.0+. It features a custom router, middleware support, database abstraction, view rendering, advanced error handling, email/SMS integration, data export helpers, and security‑focused utilities.

---

## Table of Contents

1. [Features](#features)
2. [Requirements](#requirements)
3. [Installation](#installation)
4. [Environment Configuration](#environment-configuration)
5. [Directory Structure](#directory-structure)
6. [Routing](#routing)
7. [Controllers & Views](#controllers--views)
8. [Middleware](#middleware)
9. [Database](#database)
10. [Models](#models)
11. [Helper Functions](#helper-functions)
12. [Email & SMS](#email--sms)
13. [Error Handling & Debugging](#error-handling--debugging)
14. [Security Features](#security-features)
15. [Running the Application](#running-the-application)

---

## ✨ Features

- 🔀 **Custom Router** – supports `GET` and `POST` with dynamic parameters (`:number`, `:all`)
- 🧩 **Middleware** – easy to add pre‑processing logic
- 🎨 **View Engine** – plain PHP templates with a helper (`require_view`)
- 🗄️ **Database** – PDO with prepared statements (MySQL)
- 📁 **Environment loader** – `.env` support via `PHP_ENV.php`
- 🛡️ **Advanced Error Handler** – beautiful, detailed error pages (with code snippets, request/env info, dark mode) – only in development mode (`WEB=off`)
- 📧 **Email** – PHPMailer with SMTP support
- 📱 **SMS** – Kavenegar & Melipayamak providers
- 📊 **Data export** – Excel (.xls), Word (.doc), CSV, SQL dump helpers
- 🧰 **Global helpers** – `dd()`, `redirect()`, `safeEcho()`, `download()`, CSRF token functions, and more
- 🔒 **Security headers** – X‑Frame‑Options, X‑XSS‑Protection, etc.
- 🧼 **CSRF protection** – built‑in token generation and validation

---

## ⚙️ Requirements

- PHP >= 8.0
- MySQL / MariaDB
- Composer
- Web server (Apache / Nginx) with URL rewriting

---

## 🧰 Installation

```bash
# 1. Clone or copy the project into your web root
cd your-project-folder

# 2. Install dependencies via Composer
composer install

# 3. Create .env file from example
cp .env.example .env

# 4. Edit .env with your database, mail, and SMS credentials

# 5. Point your document root to the `public` folder

🌍 Environment Configuration
Create a .env file in the project root (one level above public).

ini
# Application
WEB=off          # "off" for development (detailed errors), "on" for production (silent errors)
PROJECTNAME=myapp
URL=http://localhost/myapp

# Database
DB_HOST=localhost
DB_NAME=mydb
DB_USER=root
DB_PASS=secret

# Mail (SMTP)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=app-password

# SMS - Kavenegar
SMS_KAVENEGAR_API=your_api_key
SMS_KAVENEGAR_SENDER=1000xxxx

# SMS - Melipayamak
SMS_MELIPAYAMAK_USERNAME=0912xxxxxxx
SMS_MELIPAYAMAK_PASSWORD=your_panel_password
SMS_MELIPAYAMAK_SENDER=5000xxxx
Note:

WEB=off enables the full, stylish error handler.

WEB=on suppresses all display errors (production mode).

PROJECTNAME is used when the app runs in a subfolder (e.g. http://localhost/myapp).

📁 Directory Structure
text
project-root/
├── app/
│   ├── auto/                 # PHP_ENV (loads .env) and PHP_ERROR (error handler)
│   ├── bootstrap/            # bootstrap.php – starts session, headers, loads all
│   ├── configs/              # Config (app settings) and DB (database credentials)
│   ├── controllers/          # Your controllers
│   ├── core/                 # Core.php – router dispatcher
│   ├── database/             # Database connection class
│   ├── errors/               # Simple HTTP error handlers (403, 404, 500)
│   ├── helpers/              # helpers.php – global functions
│   ├── libraries/            # Base Controller class
│   ├── mails/                # PHPMailer wrapper
│   ├── middlewares/          # Middleware classes
│   ├── models/               # Model examples (Model.php, Mvc.php)
│   ├── resources/views/      # View files (plain PHP)
│   ├── routes/               # Route.php (router logic) and Web.php (route definitions)
│   └── sms/                  # SMS providers (Kavenegar, Melipayamak)
├── public/
│   ├── assets/               # CSS, JS, images
│   ├── index.php             # Front controller
│   └── .htaccess (optional)
├── vendor/                   # Composer dependencies
├── .env
├── .env.example
└── composer.json
🚦 Routing
Define all routes in app/routes/Web.php inside the routes() method.

Basic Syntax
php
Route::Get("/", "HomeController", "index", "HomeMiddleware");
Route::Post("/user", "UserController", "create");
Route::Get("/user/:number", "UserController", "show");
Route::Post("/product/:all", "ProductController", "update");
:number → regex ([0-9]+)

:all → regex ([^"<>\\{}|^~$&/]+)

The fourth parameter is an optional middleware name (without namespace).

Example Web.php
php
namespace app\routes;

class Web
{
    public function routes()
    {
        Route::Get("/", "HomeController", "index");
        Route::Get("/users", "UserController", "list");
        Route::Post("/users/create", "UserController", "store", "AuthMiddleware");
        return Route::$routes;
    }
}
🎮 Controllers & Views
Controllers reside in app/controllers/ and extend app\libraries\Controller.

Example Controller
php
namespace app\controllers;
use app\libraries\Controller;
use app\models\User;

class HomeController extends Controller
{
    public function index()
    {
        $data = ['title' => 'Welcome'];
        return Controller::view("home.index", $data);
    }

    public function show($id)
    {
        $user = User::find($id);
        Controller::view("user.profile", ['user' => $user]);
    }

    public function store($postData, $param = null)
    {
        // $postData is the sanitized $_POST array
        // $param is the captured URL parameter (if any)
        // Process and redirect
        redirect("/users");
    }
}
View Rendering
Use Controller::view($viewName, $data) to render a view.
Views are stored in app/resources/views/ and use the .php extension.
Dot notation is converted to directory separators:

php
Controller::view("home.index");    // resources/views/home/index.php
Controller::view("admin.dashboard"); // resources/views/admin/dashboard.php
Inside a view, you can use helper functions like safeEcho(), urlPath(), publicPath(), and require_view() for partials.

Example View (home/index.php)
php
<?php require_view("partials.start"); ?>
<h1><?php safeEcho($title); ?></h1>
<p>Welcome to my MVC framework.</p>
<?php require_view("partials.end"); ?>
require_view() works exactly like Controller::view() but can be used anywhere.

🧩 Middleware
Create a middleware class in app/middlewares/. It must have a handle() method. Return true to continue, false to stop.

php
namespace app\middlewares;

class AuthMiddleware
{
    public function handle()
    {
        if (!isset($_SESSION['user_id'])) {
            redirect("/login");
            return false;
        }
        return true;
    }
}
Attach to a route:
Route::Get("/dashboard", "DashboardController", "index", "AuthMiddleware")

The provided HomeMiddleware calls dd("Middleware") – you can replace it with your logic.

🗄️ Database
The Database class (in app/database/) creates a PDO connection.

php
use app\database\Database;

$db = new Database();
$conn = $db->db; // PDO instance

$stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => 1]);
$user = $stmt->fetch(PDO::FETCH_OBJ);
Credentials are read from .env (DB_HOST, DB_USER, DB_PASS, DB_NAME).
Error mode is set to ERRMODE_EXCEPTION, and default fetch mode is FETCH_OBJ.

📦 Models
A generic Model class is provided as a template. You are encouraged to create your own models for each table.

Example custom model:

php
namespace app\models;
use app\database\Database;

class User
{
    public static function find($id)
    {
        $db = (new Database())->db;
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function all()
    {
        $db = (new Database())->db;
        $stmt = $db->query("SELECT * FROM users");
        return $stmt->fetchAll();
    }
}
The included Mvc model shows a working example for a table named mvc.

🛠️ Helper Functions
All helpers are defined in app/helpers/helpers.php. Key functions:

Function	Description
MakeSecureHash($password)	Argon2id password hash
CheckSecureHashed($hash, $plain)	Verify password
redirect($path)	Redirect to a relative URL (appends URLROOT)
dd($content)	Pretty dump and die with a styled <pre> block
pdf()	Triggers browser print dialog
download($dir, $filename)	Secure file download (checks file is inside PUBLICROOT)
require_view($path)	Include a view file (dot notation)
safeEcho($value)	Print HTML‑escaped value
urlPath($path)	Print full URL based on URLROOT
publicPath($path)	Print relative path starting with ./
getDbConnection()	Returns PDO instance (using Database)
excel($tableName)	Export table as Excel (.xls)
word($tableName)	Export table as Word (.doc)
csv($tableName)	Export table as CSV
tableExport($tableName)	Export table as SQL (CREATE + INSERT)
generateToken()	Generate and store a CSRF token in session
validateToken()	Validate CSRF token from POST (redirects back on failure)
✉️ Email & SMS
Email (PHPMailer)
php
use app\mails\Mail;

Mail::email(
    'sender@example.com',    // from email
    'Sender Name',           // from name
    'receiver@example.com',  // to email
    'Receiver Name',         // to name
    'Subject',               // subject
    'Plain text version',    // alt body
    '<h1>HTML message</h1>'  // HTML body
);
SMTP settings are read from .env (MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD).

SMS – Kavenegar
php
use app\sms\Sms;

$result = Sms::smsKavenegar('09123456789', 'Your code is 1234');
if ($result['success']) {
    echo "Sent, ID: " . $result['message_id'];
} else {
    echo "Error: " . $result['error'];
}
SMS – Melipayamak
php
$result = Sms::smsMelipayamak('09123456789', 'Hello from Melipayamak');
Both return an associative array with success and either message_id or error.

⚠️ Error Handling & Debugging
The framework includes two error handling modes controlled by the WEB environment variable.

Development Mode (WEB=off)
All PHP errors are displayed.

A custom, beautiful error handler takes over:

Shows error type, message, file, line, code snippet, stack trace.

Displays request details (method, URI, POST/GET data, headers).

Shows environment details (PHP version, memory usage, loaded extensions, etc.).

Dark mode UI, collapsible sections, double‑click to copy error details.

Logs errors to error_log.log.

Can send critical errors via email (configurable).

Ajax requests receive a structured JSON error.

Production Mode (WEB=on)
display_errors and log_errors are turned off.

Only HTTP error responses (403, 404, 500) are sent without details.

Manual HTTP Errors
php
use app\errors\Errors;

Errors::_403_(); // Forbidden
Errors::_404_(); // Not Found
Errors::_500_(); // Internal Server Error
These functions send the correct HTTP status code and terminate execution.

🔒 Security Features
.env isolation – sensitive data never exposed in code.

Prepared statements – all database queries use PDO prepared statements to prevent SQL injection.

Output escaping – safeEcho() uses htmlspecialchars().

CSRF protection – generateToken() and validateToken() helpers.

Security headers sent in bootstrap.php:

X-Frame-Options: SAMEORIGIN

X-Content-Type-Options: nosniff

X-XSS-Protection: 1; mode=block

Referrer-Policy: no-referrer-when-downgrade

Path sanitisation – the router strips ../ and normalises slashes.

Download security – download() ensures the requested file is inside PUBLICROOT.

🚀 Running the Application
Set your web server document root to the public folder.

Enable URL rewriting (mod_rewrite for Apache). Example .htaccess for Apache:

apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
Ensure the vendor folder exists (composer install).

Create the .env file with your settings.

Access the app at http://localhost/ (or http://localhost/myapp if PROJECTNAME is set and WEB=on).

The default route (/) will render the home.index view, which displays "Welcome To My Big Mvc".

📄 License
MIT – free for personal and commercial projects.