<?php



require './Db/conn.php';

date_default_timezone_set('Asia/Colombo'); // Sri Lanka timezone
$currentDate = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Required fields
    $name      = $_POST['name'] ?? '';
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $company   = trim($_POST['company'] ?? '');
   
    $createdBy = trim($_POST['createdBy'] ?? '');
    $flyers    = json_decode($_POST['flyers'] ?? '[]', true); // Convert JSON string to array
    $n         = 0;

    // Validate required fields
    if (empty($name) || empty($email) || empty($phone) || empty($flyers)){
        echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        exit;
    }

    // Count valid flyers (check if PDF exists)
    $flyerDir = __DIR__ . '/Flyer/';
    foreach ($flyers as $flyer) {
        if (file_exists($flyerDir . basename($flyer) . '.pdf')) $n++;
    }

    // 1️⃣ Save customer record to database first
    try {
        $sql = "INSERT INTO `customers` 
            (`customerName`, `customerEmail`, `customerPhone`, `Company`, `TotalDownloads`, `CreateBy`, `createdDate`) 
            VALUES (:customerName, :customerEmail, :customerPhone, :Company, :TotalDownloads, :CreateBy, :createdDate)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':customerName'    => $name,
            ':customerEmail'   => $email,
            ':customerPhone'   => $phone,
            ':Company'         => $company,
            ':TotalDownloads'  => $n,
            ':CreateBy'        => $createdBy,
            ':createdDate'     => $currentDate
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        exit;
    }

    // 2️⃣ Prepare email
    $subject = "Your Requested Flyers from ExpoFlyer Delivery";
    $message = "Hello $name,\n\nThank you for visiting our booth!\nPlease find the requested flyers attached below.\n\n";
    if (!empty($company))  $message .= "Company: $company\n";
    if (!empty($interest)) $message .= "Interest: $interest\n";
    $message .= "\nBest Regards,\nExpoFlyer Team";

    $boundary = md5(time());
    $headers  = "From: ExpoFlyer <no-reply@expoflyer.com>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

    $body  = "--{$boundary}\r\n";
    $body .= "Content-Type: text/plain; charset=\"utf-8\"\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $body .= $message . "\r\n\r\n";

    // Attach flyers
    foreach ($flyers as $flyer) {
        $filename = basename($flyer) . '.pdf';
        $filepath = $flyerDir . $filename;

        if (file_exists($filepath)) {
            $fileContent = chunk_split(base64_encode(file_get_contents($filepath)));
            $body .= "--{$boundary}\r\n";
            $body .= "Content-Type: application/pdf; name=\"{$filename}\"\r\n";
            $body .= "Content-Disposition: attachment; filename=\"{$filename}\"\r\n";
            $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $body .= $fileContent . "\r\n\r\n";
        }
    }

    $body .= "--{$boundary}--";

    // 3️⃣ Send email
    if (mail($email, $subject, $body, $headers)) {
        echo json_encode(['success' => true, 'message' => 'Record saved and email sent successfully.']);
    } else {
        echo json_encode(['success' => true, 'message' => 'Record saved, but email sending failed.']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
