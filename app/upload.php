<?php
$host = '10.0.11.10';
$db = 'storage';
$user = 'donnie';
$pass = 'G0d0fg@ming1995';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $filename = basename($file['name']);
        $filesize = $file['size'];
        $filetype = mime_content_type($file['tmp_name']);
        $filedata = file_get_contents($file['tmp_name']);

        $stmt = $pdo->prepare("INSERT INTO uploads (filename, filetype, filesize, filedata) VALUES (?, ?, ?, ?)");
        $stmt->execute([$filename, $filetype, $filesize, $filedata]);

        echo "<p style='color:green;'>File uploaded successfully!</p>";
    } else {
        echo "<p style='color:red;'>Upload error: " . $file['error'] . "</p>";
    }
}

// Handle file download
if (isset($_GET['download'])) {
    $id = intval($_GET['download']);
    $stmt = $pdo->prepare("SELECT filename, filetype, filedata FROM uploads WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetch();

    if ($file) {
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $file['filetype']);
        header('Content-Disposition: attachment; filename="' . $file['filename'] . '"');
        header('Content-Length: ' . strlen($file['filedata']));
        echo $file['filedata'];
        exit;
    } else {
        echo "<p style='color:red;'>File not found.</p>";
    }
}

// Handle file deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM uploads WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: upload.php");
    exit;
}

// Show list of uploaded files
$stmt = $pdo->query("SELECT id, filename, filetype, filesize, uploaded_at FROM uploads ORDER BY uploaded_at DESC");
$files = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>File Upload with Database</title>
    <style>
        body { font-family: Arial; margin: 2em; }
        table { border-collapse: collapse; width: 100%; margin-top: 2em; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        a.button { padding: 4px 8px; background: #007BFF; color: white; text-decoration: none; border-radius: 4px; }
        a.delete { background: #dc3545; }
    </style>
</head>
<body>
    <h1>Upload a File</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="file" required />
        <button type="submit">Upload</button>
    </form>

    <h2>Uploaded Files</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Filename</th>
            <th>Type</th>
            <th>Size</th>
            <th>Uploaded</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($files as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['filename']) ?></td>
            <td><?= htmlspecialchars($row['filetype']) ?></td>
            <td><?= number_format($row['filesize']) ?> bytes</td>
            <td><?= htmlspecialchars($row['uploaded_at']) ?></td>
            <td>
                <a class="button" href="?download=<?= $row['id'] ?>">Download</a>
                <a class="button delete" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
