<?php
echo "<h1>🎉 Website Access Test</h1>";
echo "<p><strong>Current Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Server:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Current File:</strong> " . __FILE__ . "</p>";

echo "<hr>";
echo "<h2>📁 File Structure Test</h2>";

$files_to_check = [
    'index.php',
    'admindsh.php',
    'admin/category.php',
    'actions/add_category_action.php',
    'classes/category_class.php',
    'controllers/category_controller.php',
    'js/category.js'
];

echo "<ul>";
foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "<li>✅ <strong>$file</strong> - EXISTS</li>";
    } else {
        echo "<li>❌ <strong>$file</strong> - MISSING</li>";
    }
}
echo "</ul>";

echo "<hr>";
echo "<h2>🔗 Quick Navigation Links</h2>";
echo "<ul>";
echo "<li><a href='index.php'>🏠 Main Website (index.php)</a></li>";
echo "<li><a href='admindsh.php'>📊 Admin Dashboard</a></li>";
echo "<li><a href='admin/category.php'>🏷️ Category Management</a></li>";
echo "<li><a href='setup_database.php'>🗄️ Setup Database</a></li>";
echo "<li><a href='setup_admin.php'>👤 Setup Admin User</a></li>";
echo "<li><a href='setup_categories.php'>📂 Setup Categories Table</a></li>";
echo "<li><a href='test_categories.php'>🧪 Test Categories System</a></li>";
echo "</ul>";

echo "<hr>";
echo "<h2>🔧 Troubleshooting Info</h2>";
echo "<p><strong>Your website URL should be:</strong></p>";
echo "<code>http://localhost/Ecommerce/LABBTWOO/register_sample/register_sample/</code>";
echo "<br><br>";
echo "<p><strong>If you can see this page, your web server is working correctly!</strong></p>";

if (isset($_SESSION)) {
    echo "<p><strong>Session Status:</strong> Active</p>";
} else {
    echo "<p><strong>Session Status:</strong> Not started</p>";
}
?>
