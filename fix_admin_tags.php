<?php
$dir = __DIR__ . '/dashboard/admin';
$files = glob($dir . '/*.php');
foreach ($files as $file) {
    if (basename($file) === 'index.php') continue; // already rewrote index
    
    $content = file_get_contents($file);
    
    // Fix broken short echo tags with spaces: "<? =" -> "<?php echo "
    // Also handle possible "<?=" -> "<?php echo "
    $content = preg_replace('/<\?\s*=/', '<?php echo ', $content);
    
    file_put_contents($file, $content);
    echo "Fixed: " . basename($file) . "\n";
}
