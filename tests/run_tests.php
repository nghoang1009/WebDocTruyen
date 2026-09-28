<?php
/**
 * WebDocTruyen - Automated Test Suite
 * Run via CLI: php tests/run_tests.php
 */

require_once __DIR__ . '/../config.php';

// PSR-4 Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) require_once $file;
});

use App\Core\Database;
use App\Core\Auth;
use App\Models\User;
use App\Models\Story;
use App\Models\Chapter;
use App\Models\Category;
use App\Models\Author;
use App\Models\Comment;
use App\Models\Rating;
use App\Models\Interaction;
use App\Models\Stats;
use App\Helpers\Slug;
use App\Helpers\Security;

echo "\n=======================================================\n";
echo "       WEBDOC TRUYEN - AUTOMATED TEST SUITE            \n";
echo "=======================================================\n\n";

$passed = 0;
$failed = 0;

function it(string $description, callable $test) {
    global $passed, $failed;
    try {
        $test();
        echo "  [PASS] {$description}\n";
        $passed++;
    } catch (\Throwable $e) {
        echo "  [FAIL] {$description} -> " . $e->getMessage() . " (" . $e->getFile() . ":" . $e->getLine() . ")\n";
        $failed++;
    }
}

function assertEq($actual, $expected, $message = '') {
    if ($actual !== $expected) {
        throw new \Exception("Assertion failed: expected [" . var_export($expected, true) . "] but got [" . var_export($actual, true) . "] {$message}");
    }
}

function assertTrue($condition, $message = '') {
    if (!$condition) {
        throw new \Exception("Assertion failed: expected true but got false {$message}");
    }
}

// TEST 1: Database Connection & Tables
it('1. Database Connection and Tables Check', function() {
    $db = Database::getInstance();
    $tables = $db->fetchAll("SHOW TABLES");
    assertTrue(count($tables) >= 10, 'Expected at least 10 tables in database');
});

// TEST 2: Vietnamese Slug Generator
it('2. Slug Helper - Vietnamese Diacritics Removal', function() {
    $slug1 = Slug::create('Đấu Phá Thương Khung');
    assertEq($slug1, 'dau-pha-thuong-khung');

    $slug2 = Slug::create('Chương 1: Phế Vật Tiêu Viêm! @#$');
    assertEq($slug2, 'chuong-1-phe-vat-tieu-viem');
});

// TEST 3: Security & XSS Sanitizer
it('3. Security Helper - XSS Filtering', function() {
    $clean = Security::clean('<script>alert("hack")</script><b>Hello</b>');
    assertTrue(!str_contains($clean, '<script>'), 'Script tags must be html encoded');

    $rich = Security::cleanRichText('<p>Paragraph</p><script>evil()</script>');
    assertEq($rich, '<p>Paragraph</p>evil()');
});

// TEST 4: JWT & Password Authentication
it('4. Auth - Password Hashing & JWT Token Generation/Verification', function() {
    $password = 'SecretPassword123!';
    $hash = Auth::hashPassword($password);
    assertTrue(Auth::verifyPassword($password, $hash), 'Password verify failed');
    assertTrue(!Auth::verifyPassword('WrongPass', $hash), 'Wrong password must fail');

    $payload = ['sub' => 999, 'role' => 'admin', 'username' => 'test_user'];
    $token = Auth::generateToken($payload, 3600);
    $decoded = Auth::verifyToken($token);

    assertEq($decoded['sub'], 999);
    assertEq($decoded['role'], 'admin');
});

// TEST 5: Story Listing & Filtering
it('5. Story Model - Listing, Categories & Search', function() {
    $storyModel = new Story();
    $result = $storyModel->listStories([], 1, 10);
    assertTrue($result['total'] >= 10, 'Mock data should have 10+ stories');
    assertTrue(count($result['items']) <= 10);

    // Search
    $searchRes = $storyModel->listStories(['q' => 'Tiêu Viêm'], 1, 10);
    assertTrue($searchRes['total'] >= 1, 'Search query should find Đấu Phá Thương Khung');
});

// TEST 6: Chapter Reader Engine Data
it('6. Chapter Model - Reader Navigation & Content', function() {
    $chapterModel = new Chapter();
    $chapter = $chapterModel->getChapterForReading('dau-pha-thuong-khung', 'chuong-1-phe-vat-tieu-viem');

    assertTrue($chapter !== null, 'Chapter 1 must exist');
    assertEq((int)$chapter['chapter_number'], 1);
    assertTrue($chapter['next'] !== null, 'Chapter 1 must have next chapter');
    assertEq((int)$chapter['next']['chapter_number'], 2);
    assertTrue($chapter['prev'] === null, 'Chapter 1 should not have prev chapter');
});

// TEST 7: 5-Star Rating Calculation
it('7. Rating Model - Average & Count Recalculation', function() {
    $ratingModel = new Rating();
    $storyModel = new Story();

    // User 2 rates Story 3 with 5 stars
    $res = $ratingModel->setRating(2, 3, 5);
    assertTrue($res['rating_avg'] > 0);
    assertTrue($res['rating_count'] >= 1);

    $story = $storyModel->find(3);
    assertEq((float)$story['rating_avg'], (float)$res['rating_avg']);
});

// TEST 8: Comments & Hierarchy
it('8. Comment Model - Comment and Reply', function() {
    $commentModel = new Comment();
    $comments = $commentModel->getByStory(1, 1, 10);
    assertTrue(count($comments['items']) >= 1, 'Story 1 should have mock comments');
});

// TEST 9: Reading History, Follows, Favorites
it('9. Interaction Model - History, Follow & Favorite', function() {
    $interactionModel = new Interaction();

    // History
    $interactionModel->saveHistory(2, 1, 1, 45);
    $history = $interactionModel->getHistory(2, 1, 10);
    assertTrue(count($history['items']) >= 1);

    // Follow Toggle
    $isFollowing = $interactionModel->toggleFollow(2, 5);
    $checkFollow = $interactionModel->isFollowing(2, 5);
    assertEq($isFollowing, $checkFollow);
    // Toggle back
    $interactionModel->toggleFollow(2, 5);
});

// TEST 10: Admin Dashboard Overview & Charts
it('10. Admin Stats - Overview & Daily Metrics', function() {
    $stats = new Stats();
    $overview = $stats->getOverview();
    assertTrue($overview['total_stories'] >= 10);
    assertTrue($overview['total_chapters'] >= 30);
    assertTrue($overview['total_users'] >= 5);

    $dailyViews = $stats->getDailyViews(7);
    assertTrue(is_array($dailyViews));
});

echo "\n-------------------------------------------------------\n";
echo "TEST RESULTS: Passed: {$passed} | Failed: {$failed}\n";
echo "-------------------------------------------------------\n\n";

if ($failed === 0) {
    echo " ALL TESTS PASSED SUCCESSFULLY!\n\n";
    exit(0);
} else {
    echo "[!] SOME TESTS FAILED. PLEASE CHECK LOGS ABOVE.\n\n";
    exit(1);
}
