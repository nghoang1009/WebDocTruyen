<?php
/**
 * WebDocTruyen - Main Application Entry Point
 */

require_once __DIR__ . '/../config.php';

// PSR-4 Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Start PHP Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\StoryController;
use App\Controllers\ChapterController;
use App\Controllers\CategoryController;
use App\Controllers\AuthorController;
use App\Controllers\InteractionController;
use App\Controllers\AdminController;

$router = new Router();

// ==========================================
// 1. REST API ROUTES
// ==========================================

// Auth APIs
$router->post('/api/auth/register', [AuthController::class, 'register']);
$router->post('/api/auth/login', [AuthController::class, 'login']);
$router->post('/api/auth/logout', [AuthController::class, 'logout']);
$router->get('/api/auth/me', [AuthController::class, 'me']);
$router->post('/api/auth/profile', [AuthController::class, 'updateProfile']);
$router->post('/api/auth/change-password', [AuthController::class, 'changePassword']);

// Story APIs
$router->get('/api/stories', [StoryController::class, 'index']);
$router->get('/api/stories/:slug', [StoryController::class, 'show']);
$router->post('/api/stories', [StoryController::class, 'store']);
$router->post('/api/stories/:id', [StoryController::class, 'update']);
$router->delete('/api/stories/:id', [StoryController::class, 'destroy']);
$router->post('/api/stories/:id/view', [StoryController::class, 'view']);

// Chapter APIs
$router->get('/api/stories/:storyId/chapters', [ChapterController::class, 'getByStory']);
$router->get('/api/read/:storySlug/:chapterSlug', [ChapterController::class, 'read']);
$router->post('/api/chapters', [ChapterController::class, 'store']);
$router->post('/api/chapters/:id', [ChapterController::class, 'update']);
$router->delete('/api/chapters/:id', [ChapterController::class, 'destroy']);

// Category & Author APIs
$router->get('/api/categories', [CategoryController::class, 'index']);
$router->get('/api/categories/:slug', [CategoryController::class, 'show']);
$router->post('/api/categories', [CategoryController::class, 'store']);
$router->post('/api/categories/:id', [CategoryController::class, 'update']);
$router->delete('/api/categories/:id', [CategoryController::class, 'destroy']);

$router->get('/api/authors', [AuthorController::class, 'index']);
$router->get('/api/authors/:slug', [AuthorController::class, 'show']);
$router->post('/api/authors', [AuthorController::class, 'store']);
$router->post('/api/authors/:id', [AuthorController::class, 'update']);
$router->delete('/api/authors/:id', [AuthorController::class, 'destroy']);

// Comment APIs
$router->get('/api/stories/:storyId/comments', [InteractionController::class, 'getComments']);
$router->post('/api/stories/:storyId/comments', [InteractionController::class, 'addComment']);
$router->post('/api/comments/:id', [InteractionController::class, 'updateComment']);
$router->delete('/api/comments/:id', [InteractionController::class, 'deleteComment']);

// Rating APIs
$router->get('/api/stories/:storyId/rating', [InteractionController::class, 'getUserRating']);
$router->post('/api/stories/:storyId/rating', [InteractionController::class, 'rate']);

// Follow & Favorite APIs
$router->post('/api/stories/:storyId/follow', [InteractionController::class, 'toggleFollow']);
$router->get('/api/user/following', [InteractionController::class, 'getFollowing']);
$router->post('/api/stories/:storyId/favorite', [InteractionController::class, 'toggleFavorite']);

// History APIs
$router->get('/api/user/history', [InteractionController::class, 'getHistory']);
$router->post('/api/user/history', [InteractionController::class, 'saveHistory']);
$router->delete('/api/user/history/:storyId', [InteractionController::class, 'deleteHistoryItem']);
$router->delete('/api/user/history', [InteractionController::class, 'clearHistory']);

// Admin APIs
$router->get('/api/admin/dashboard', [AdminController::class, 'dashboard']);
$router->get('/api/admin/users', [AdminController::class, 'users']);
$router->post('/api/admin/users/:id', [AdminController::class, 'updateUser']);
$router->delete('/api/admin/users/:id', [AdminController::class, 'deleteUser']);
$router->get('/api/admin/comments', [AdminController::class, 'comments']);

// ==========================================
// 2. FRONTEND WEB VIEWS
// ==========================================

$router->get('/', 'home');
$router->get('/stories', 'stories');
$router->get('/search', 'search');
$router->get('/history', 'history');
$router->get('/following', 'following');
$router->get('/profile', 'profile');
$router->get('/login', 'auth/login');
$router->get('/register', 'auth/register');

// Reader route
$router->get('/stories/:storySlug/chapter/:chapterSlug', 'reader');

// Story detail route
$router->get('/stories/:slug', 'story-detail');

// Admin Web routes
$router->get('/admin', 'admin/dashboard');
$router->get('/admin/stories', 'admin/stories');
$router->get('/admin/chapters', 'admin/chapters');
$router->get('/admin/categories', 'admin/categories');
$router->get('/admin/authors', 'admin/authors');
$router->get('/admin/users', 'admin/users');
$router->get('/admin/comments', 'admin/comments');

// Database Init Routes
$router->any('/database/init.php', function() {
    require_once __DIR__ . '/../database/init.php';
});
$router->any('/init.php', function() {
    require_once __DIR__ . '/../database/init.php';
});
$router->any('/init', function() {
    require_once __DIR__ . '/../database/init.php';
});
$router->any('/install', function() {
    require_once __DIR__ . '/../database/init.php';
});
$router->any('/install.php', function() {
    require_once __DIR__ . '/../database/init.php';
});

// Execute routing
$router->dispatch();
