<?php
/**
 * Internal Message Controller
 * Quản lý thông báo nội bộ từ Super Admin đến Admin
 */

require_once 'models/InternalMessage.php';

class InternalMessageController extends BaseController {
    private $internalMessageModel;

    public function __construct() {
        parent::__construct();
        $this->internalMessageModel = new InternalMessage();
    }

    /**
     * Hiển thị trang gửi thông báo (Super Admin)
     */
    public function sendMessage() {
        $this->requireSuperAdmin();

        $recipients = $this->internalMessageModel->getAvailableRecipients();

        $this->loadSuperAdminView('internal_messages/send', [
            'recipients' => $recipients,
            'page_title' => 'Gửi Thông Báo Nội Bộ'
        ]);
    }

    /**
     * Xử lý gửi thông báo
     */
    public function processSendMessage() {
        $this->requireSuperAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/superadmin/internal-messages/send');
        }

        // Validate CSRF token
        if (!$this->validateCSRF()) {
            $this->setFlash('error', 'Token không hợp lệ');
            $this->redirect('/superadmin/internal-messages/send');
        }

        // Validate input
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $messageType = $_POST['message_type'] ?? 'general';
        $priority = $_POST['priority'] ?? 'normal';
        $recipients = $_POST['recipients'] ?? [];
        $isBroadcast = isset($_POST['is_broadcast']) ? 1 : 0;

        if (empty($title) || empty($content)) {
            $this->setFlash('error', 'Tiêu đề và nội dung không được để trống');
            $this->redirect('/superadmin/internal-messages/send');
        }

        if (!$isBroadcast && empty($recipients)) {
            $this->setFlash('error', 'Vui lòng chọn ít nhất một người nhận');
            $this->redirect('/superadmin/internal-messages/send');
        }

        // Xử lý file đính kèm
        $attachmentPath = null;
        $attachmentName = null;

        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->handleFileUpload($_FILES['attachment']);
            if ($uploadResult['success']) {
                $attachmentPath = $uploadResult['path'];
                $attachmentName = $uploadResult['name'];
            } else {
                $this->setFlash('error', 'Lỗi upload file: ' . $uploadResult['message']);
                $this->redirect('/superadmin/internal-messages/send');
            }
        }

        // Nếu là broadcast, lấy tất cả admin
        if ($isBroadcast) {
            $allRecipients = $this->internalMessageModel->getAvailableRecipients();
            $recipients = array_column($allRecipients, 'id');
        }

        // Tạo thông báo
        $messageData = [
            'sender_id' => $_SESSION['user_id'],
            'title' => $title,
            'content' => $content,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'message_type' => $messageType,
            'priority' => $priority,
            'is_broadcast' => $isBroadcast,
            'recipients' => $recipients
        ];

        $messageId = $this->internalMessageModel->createMessage($messageData);

        if ($messageId) {
            $this->setFlash('success', 'Thông báo đã được gửi thành công!');
            $this->redirect('/superadmin/internal-messages/sent');
        } else {
            $this->setFlash('error', 'Có lỗi xảy ra khi gửi thông báo');
            $this->redirect('/superadmin/internal-messages/send');
        }
    }

    /**
     * Hiển thị danh sách thông báo đã gửi
     */
    public function sentMessages() {
        $this->requireSuperAdmin();

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $messages = $this->internalMessageModel->getSentMessages($_SESSION['user_id'], $limit, $offset);
        $stats = $this->internalMessageModel->getMessageStats($_SESSION['user_id']);

        $this->loadSuperAdminView('internal_messages/sent', [
            'messages' => $messages,
            'stats' => $stats,
            'page' => $page,
            'limit' => $limit,
            'page_title' => 'Thông Báo Đã Gửi'
        ]);
    }

    /**
     * Hiển thị danh sách thông báo đã nhận (Admin)
     */
    public function receivedMessages() {
        $this->requireAdmin();

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $messages = $this->internalMessageModel->getReceivedMessages($_SESSION['user_id'], $limit, $offset);
        $unreadCount = $this->internalMessageModel->getUnreadCount($_SESSION['user_id']);

        $this->loadAdminView('internal_messages/received', [
            'messages' => $messages,
            'unreadCount' => $unreadCount,
            'page' => $page,
            'limit' => $limit,
            'page_title' => 'Thông Báo Nội Bộ'
        ]);
    }

    /**
     * Xem chi tiết thông báo
     */
    public function viewMessage($messageId) {
        $this->requireLogin();

        $message = $this->internalMessageModel->getMessageDetail($messageId, $_SESSION['user_id']);

        if (!$message) {
            $this->setFlash('error', 'Thông báo không tồn tại');
            $this->redirect('/admin/internal-messages');
        }

        // Đánh dấu đã đọc nếu là người nhận
        if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['manager', 'admin'])) {
            $this->internalMessageModel->markAsRead($messageId, $_SESSION['user_id']);
        }

        // Lấy danh sách người nhận nếu là người gửi
        $recipients = null;
        if ($message['sender_id'] == $_SESSION['user_id']) {
            $recipients = $this->internalMessageModel->getMessageRecipients($messageId);
        }

        $viewData = [
            'message' => $message,
            'recipients' => $recipients,
            'page_title' => 'Chi Tiết Thông Báo'
        ];

        if ($_SESSION['user_role'] === 'super_admin') {
            $this->loadSuperAdminView('internal_messages/view', $viewData);
        } else {
            $this->loadAdminView('internal_messages/view', $viewData);
        }
    }

    /**
     * Xóa thông báo
     */
    public function deleteMessage($messageId) {
        $this->requireSuperAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/superadmin/internal-messages/sent');
        }

        if (!$this->validateCSRF()) {
            $this->setFlash('error', 'Token không hợp lệ');
            $this->redirect('/superadmin/internal-messages/sent');
        }

        $success = $this->internalMessageModel->deleteMessage($messageId, $_SESSION['user_id']);

        if ($success) {
            $this->setFlash('success', 'Thông báo đã được xóa thành công');
        } else {
            $this->setFlash('error', 'Không thể xóa thông báo này');
        }

        $this->redirect('/superadmin/internal-messages/sent');
    }

    /**
     * API: Lấy số thông báo chưa đọc
     */
    public function getUnreadCount() {
        if (!isLoggedIn()) {
            $this->jsonResponse(['count' => 0]);
        }

        $count = $this->internalMessageModel->getUnreadCount($_SESSION['user_id']);
        $this->jsonResponse(['count' => $count]);
    }

    /**
     * Xử lý upload file
     */
    private function handleFileUpload($file) {
        $allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        // Kiểm tra kích thước
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'File quá lớn (tối đa 5MB)'];
        }

        // Kiểm tra loại file
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedTypes)) {
            return ['success' => false, 'message' => 'Loại file không được hỗ trợ'];
        }

        // Tạo thư mục upload nếu chưa có
        $uploadDir = 'uploads/internal_messages/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Tạo tên file unique
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'success' => true,
                'path' => $filepath,
                'name' => $file['name']
            ];
        } else {
            return ['success' => false, 'message' => 'Lỗi upload file'];
        }
    }

    /**
     * Download file đính kèm
     */
    public function downloadAttachment($messageId) {
        $this->requireLogin();

        $message = $this->internalMessageModel->getMessageDetail($messageId, $_SESSION['user_id']);

        if (!$message || !$message['attachment_path']) {
            $this->setFlash('error', 'File không tồn tại');
            $this->redirect('/admin/internal-messages');
        }

        $filepath = $message['attachment_path'];
        $filename = $message['attachment_name'];

        if (!file_exists($filepath)) {
            $this->setFlash('error', 'File không tồn tại');
            $this->redirect('/admin/internal-messages');
        }

        // Set headers for download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');

        readfile($filepath);
        exit;
    }

    /**
     * SSE endpoint để gửi thông báo realtime
     */
    public function sse() {
        // Set headers cho SSE
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Cache-Control');

        // Disable output buffering
        if (ob_get_level()) ob_end_clean();

        // Gửi keep-alive mỗi 30 giây
        $lastCheck = time();

        while (true) {
            // Kiểm tra kết nối client
            if (connection_aborted()) {
                break;
            }

            $currentTime = time();

            // Gửi keep-alive mỗi 30 giây
            if ($currentTime - $lastCheck >= 30) {
                echo "data: " . json_encode(['type' => 'ping', 'time' => $currentTime]) . "\n\n";
                $lastCheck = $currentTime;
            }

            // Kiểm tra thông báo mới (mỗi 5 giây)
            if ($currentTime % 5 == 0) {
                $this->checkNewMessages();
            }

            // Flush output
            if (ob_get_level()) ob_flush();
            flush();

            // Sleep 1 giây
            sleep(1);
        }
    }

    /**
     * Kiểm tra và gửi thông báo mới
     */
    private function checkNewMessages() {
        try {
            // Lấy thông báo mới trong 5 giây qua
            $sql = "SELECT im.*, imr.recipient_id, imr.is_read,
                           CONCAT(u.first_name, ' ', u.last_name) as sender_name
                    FROM internal_messages im
                    INNER JOIN internal_message_recipients imr ON im.id = imr.message_id
                    INNER JOIN users u ON im.sender_id = u.id
                    WHERE im.created_at >= DATE_SUB(NOW(), INTERVAL 5 SECOND)
                    ORDER BY im.created_at DESC";

            $stmt = $this->internalMessageModel->getDb()->prepare($sql);
            $stmt->execute();
            $newMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($newMessages)) {
                foreach ($newMessages as $message) {
                    $data = [
                        'type' => 'new_message',
                        'message' => [
                            'id' => $message['id'],
                            'title' => $message['title'],
                            'content' => substr($message['content'], 0, 100) . '...',
                            'sender_name' => $message['sender_name'],
                            'message_type' => $message['message_type'],
                            'priority' => $message['priority'],
                            'created_at' => $message['created_at'],
                            'recipient_id' => $message['recipient_id']
                        ]
                    ];

                    echo "data: " . json_encode($data) . "\n\n";

                    if (ob_get_level()) ob_flush();
                    flush();
                }
            }
        } catch (Exception $e) {
            error_log("SSE Error: " . $e->getMessage());
        }
    }

    /**
     * API endpoint để đánh dấu đã đọc (cho AJAX)
     */
    public function markAsReadAjax() {
        if (!isset($_SESSION['user']['id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $userId = $_SESSION['user']['id'];
        $messageId = $_POST['message_id'] ?? null;

        if (!$messageId) {
            http_response_code(400);
            echo json_encode(['error' => 'Message ID required']);
            return;
        }

        $result = $this->internalMessageModel->markAsRead($messageId, $userId);

        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
    }
}
?>
