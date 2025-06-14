<?php

/**
 * Booking Controller
 */

require_once 'BaseController.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/mail_helper.php';
require_once __DIR__ . '/../helpers/pdf_helper.php';

class BookingController extends BaseController
{
    private $bookingModel;
    private $userModel;

    public function __construct()
    {
        $this->bookingModel = new Booking();
        $this->userModel = new User();
    }

    public function index()
    {
        $data = [
            'title' => 'Đặt Bàn - ' . SITE_NAME
        ];

        $this->loadView('customer/booking/index', $data);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleBookingSubmission();
        } else {
            $this->index();
        }
    }
    public function checkAvailability()
    {
        error_log("BookingController - checkAvailability called");
        error_log("BookingController - POST data: " . json_encode($_POST));

        // Lấy dữ liệu từ POST
        $date = $_POST['booking_date'] ?? '';
        $time = $_POST['booking_time'] ?? '';
        $partySize = $_POST['party_size'] ?? '';

        error_log("BookingController - Extracted: date=$date, time=$time, partySize=$partySize");

        // Validate dữ liệu
        if (!$date || !$time || !$partySize) {
            $response = [
                'available' => false,
                'message' => 'Vui lòng chọn đầy đủ ngày, giờ và số lượng khách.'
            ];
            error_log("BookingController - Validation failed: " . json_encode($response));
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        // Gọi model để kiểm tra bàn trống
        try {
            $result = $this->bookingModel->checkAvailability($date, $time, $partySize);
            error_log("BookingController - Result from model: " . json_encode($result));
        } catch (Exception $e) {
            error_log("BookingController - Exception: " . $e->getMessage());
            $result = [
                'available' => false,
                'message' => 'Có lỗi xảy ra khi kiểm tra bàn trống.'
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    public function myBookings()
    {
        $this->requireLogin();

        $userId = $_SESSION['user_id'];

        // Phân trang
        $perPage = 10;
        $currentPage = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
        $totalBookings = $this->bookingModel->countUserBookings($userId);
        $totalPages = ceil($totalBookings / $perPage);
        $offset = ($currentPage - 1) * $perPage;

        $bookings = $this->bookingModel->getUserBookings($userId, $perPage, $offset);

        foreach ($bookings as &$booking) {
            if (!isset($booking['booking_date']) && isset($booking['reservation_time'])) {
                $booking['booking_date'] = date('Y-m-d', strtotime($booking['reservation_time']));
            }
            if (!isset($booking['booking_time']) && isset($booking['reservation_time'])) {
                $booking['booking_time'] = date('H:i', strtotime($booking['reservation_time']));
            }
            if (!isset($booking['guest_count']) && isset($booking['number_of_guests'])) {
                $booking['guest_count'] = $booking['number_of_guests'];
            }
        }

        $data = [
            'title' => 'Lịch Sử Đặt Bàn - ' . SITE_NAME,
            'bookings' => $bookings,
            'total_pages' => (int)$totalPages,
            'current_page' => (int)$currentPage,
            'filter_params' => '',
        ];

        $this->loadView('customer/booking/my_bookings', $data);
    }

    public function detail()
    {
        $this->requireLogin();

        $bookingId = intval($_GET['id'] ?? 0);

        if ($bookingId <= 0) {
            redirect('/index.php?page=booking&action=myBookings');
        }

        $userId = $_SESSION['user_id'];
        $booking = $this->bookingModel->getBookingDetails($bookingId, $userId ?? null);
        $booking = $this->bookingModel->transformBookingForView($booking);

        // Check if booking belongs to current user (unless admin)
        if (!$booking || ($booking['user_id'] != $_SESSION['user_id'] && !isAdmin() && !isManager())) {
            redirect('/index.php?page=booking&action=myBookings');
        }

        $data = [
            'title' => 'Chi Tiết Đặt Bàn - ' . SITE_NAME,
            'booking' => $booking
        ];

        $this->loadView('customer/booking/detail', $data);
    }

    public function cancel()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $bookingId = $data['booking_id'] ?? null;
            $userId = $_SESSION['user_id'] ?? null;

            if ($bookingId && $userId) {
                $result = $this->bookingModel->cancelBooking($bookingId, $userId);
                if ($result) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Cancel failed (maybe wrong user or booking not found)']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Missing booking_id or user_id']);
            }
            exit;
        }
        header('Location: index.php?page=booking');
        exit;
    }

    private function handleBookingSubmission()
    {
        $this->validateCSRF();

        // Get form data
        $customerName = sanitizeInput($_POST['customer_name'] ?? '');
        $customerEmail = sanitizeInput($_POST['customer_email'] ?? '');
        $customerPhone = sanitizeInput($_POST['customer_phone'] ?? '');
        $bookingDate = $_POST['booking_date'] ?? '';
        $bookingTime = $_POST['booking_time'] ?? '';
        $partySize = intval($_POST['party_size'] ?? 0);
        $specialRequests = sanitizeInput($_POST['special_requests'] ?? '');

        // Validation
        $errors = [];

        if (empty($customerName)) {
            $errors[] = 'Vui lòng nhập họ tên';
        }

        if (empty($customerEmail) || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }

        if (empty($customerPhone)) {
            $errors[] = 'Vui lòng nhập số điện thoại';
        }

        if (empty($bookingDate)) {
            $errors[] = 'Vui lòng chọn ngày đặt bàn';
        }

        if (empty($bookingTime)) {
            $errors[] = 'Vui lòng chọn giờ đặt bàn';
        }

        if ($partySize <= 0 || $partySize > 20) {
            $errors[] = 'Số lượng khách không hợp lệ (1-20 người)';
        }

        // Validate date and time
        $bookingDateTime = $bookingDate . ' ' . $bookingTime;
        if (strtotime($bookingDateTime) < time()) {
            $errors[] = 'Không thể đặt bàn cho thời gian trong quá khứ';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $_SESSION['form_data'] = $_POST;
            $this->index();
            return;
        }

        // Check availability again
        $availability = $this->bookingModel->checkAvailability($bookingDate, $bookingTime, $partySize);

        if (!$availability['available']) {
            $_SESSION['error'] = $availability['message'];
            $_SESSION['form_data'] = $_POST;
            $this->index();
            return;
        }

        // Create booking data
        $bookingData = [
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'customer_phone' => $customerPhone,
            'booking_datetime' => $bookingDateTime,
            'party_size' => $partySize,
            'special_requests' => $specialRequests,
            'status' => 'pending'
        ];

        // Add user ID if logged in
        if (isLoggedIn()) {
            $bookingData['user_id'] = $_SESSION['user_id'];
        }        // Create booking
        $bookingId = $this->bookingModel->createBookingFromController($bookingData);

        if ($bookingId) {
            $_SESSION['success'] = 'Đặt bàn thành công! Chúng tôi sẽ liên hệ với bạn sớm nhất để xác nhận.';

            // Tạo subject và message cho email xác nhận đặt bàn
            $subject = "Đặt bàn thành công tại Buffet Booking";
            $message = "
                <h2>Xin chào $customerName,</h2>
                <p>Bạn đã đặt bàn thành công tại <b>Buffet Booking</b>!</p>
                <ul>
                    <li><b>Ngày:</b> $bookingDate</li>
                    <li><b>Giờ:</b> $bookingTime</li>
                    <li><b>Số lượng khách:</b> $partySize</li>
                    <li><b>Số điện thoại:</b> $customerPhone</li>
                </ul>
                <p>Chúng tôi sẽ liên hệ lại để xác nhận đặt bàn của bạn trong thời gian sớm nhất.</p>
                <p>Trạng thái: <b>Chờ xác nhận</b></p>
                <p>Cảm ơn bạn đã sử dụng dịch vụ!</p>
            ";

            // Gửi email xác nhận đã nhận phiếu đặt bàn
            sendResetMail($customerEmail, $subject, $message);

            // Lấy lại thông tin booking vừa tạo
            $booking = $this->bookingModel->getBookingDetails($bookingId);

            // Truyền biến $booking vào view PDF
            ob_start();
            include __DIR__ . '/../views/customer/booking/pdf_detail.php';
            $htmlContent = ob_get_clean();

            sendBookingPDFMail($customerEmail, $subject, $message, $htmlContent);

            // Redirect...
            if (isLoggedIn()) {
                redirect('/index.php?page=booking&action=detail&id=' . $bookingId);
            } else {
                redirect('/index.php');
            }
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi đặt bàn. Vui lòng thử lại.';
            $_SESSION['form_data'] = $_POST;
            $this->index();
        }
    }

    // AJAX endpoints
    public function getAvailableSlots()
    {
        $date = $_GET['date'] ?? '';
        $partySize = intval($_GET['party_size'] ?? 0);

        if (empty($date) || $partySize <= 0) {
            $this->jsonResponse(['error' => 'Invalid parameters'], 400);
        }

        $availableSlots = $this->bookingModel->getAvailableTimeSlots($date, $partySize);

        $this->jsonResponse(['slots' => $availableSlots]);
    }

    public function clearFormData()
    {
        if (isset($_SESSION['form_data'])) {
            unset($_SESSION['form_data']);
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    public function confirmBooking()
    {
        $this->requireAdmin();

        $bookingId = intval($_POST['booking_id'] ?? 0);
        $booking = $this->bookingModel->findById($bookingId);

        $this->bookingModel->updateBookingStatus($bookingId, 'confirmed');
    }

    public function modify()
    {
        $bookingId = $_GET['id'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;
        if (!$bookingId || !$userId) {
            header('Location: index.php?page=booking');
            exit;
        }

        $booking = $this->bookingModel->getBookingDetails($bookingId, $userId);
        if (!$booking) {
            header('Location: index.php?page=booking');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newDate = $_POST['booking_date'];
            $newTime = $_POST['booking_time'];
            $newDateTime = $newDate . ' ' . $newTime . ':00';
            $changedDateTime = $newDateTime !== $booking['reservation_time'];

            $updateData = [
                'customer_name'    => $_POST['customer_name'],
                'phone_number'     => $_POST['phone_number'],
                'number_of_guests' => $_POST['number_of_guests'],
                'reservation_time' => $newDateTime,
                'special_requests' => $_POST['special_requests'] ?? '',
                'status'           => $changedDateTime ? 'pending' : $booking['status'],
            ];

            print_r($updateData);
            echo '</pre>';

            $result = $this->bookingModel->updateBooking($bookingId, $updateData);

            // Gửi email nếu đổi ngày/giờ
            if ($result) {
                $_SESSION['success'] = 'Sửa bàn thành công! Chúng tôi sẽ liên hệ với bạn sớm nhất để xác nhận.';

                // Lấy lại thông tin booking vừa sửa
                $booking = $this->bookingModel->getBookingDetails($bookingId);

                // Lấy thông tin khách hàng từ dữ liệu booking
                $customerName = $booking['customer_name'] ?? '';
                $user = $this->userModel->findById($booking['user_id']);
                $customerEmail = $booking['email'] ?? '';
                if (empty($customerEmail) && !empty($booking['user_id'])) {
                    $user = $this->userModel->findById($booking['user_id']);
                    $customerEmail = $user['email'] ?? '';
                }
                $customerPhone = $booking['phone_number'] ?? '';
                $bookingDate = isset($booking['reservation_time']) ? date('Y-m-d', strtotime($booking['reservation_time'])) : '';
                $bookingTime = isset($booking['reservation_time']) ? date('H:i', strtotime($booking['reservation_time'])) : '';
                $partySize = $booking['number_of_guests'] ?? '';

                // Tạo subject và message cho email xác nhận đặt bàn
                $subject = "Sửa bàn thành công tại Buffet Booking";
                $message = "
                    <h2>Xin chào $customerName,</h2>
                    <p>Bạn đã sửa bàn thành công tại <b>Buffet Booking</b>!</p>
                    <ul>
                        <li><b>Ngày:</b> $bookingDate</li>
                        <li><b>Giờ:</b> $bookingTime</li>
                        <li><b>Số lượng khách:</b> $partySize</li>
                        <li><b>Số điện thoại:</b> $customerPhone</li>
                    </ul>
                    <p>Chúng tôi sẽ xác nhận đặt bàn của bạn trong thời gian sớm nhất.</p>
                    <p>Trạng thái: <b>Chờ xác nhận</b></p>
                    <p>Cảm ơn bạn đã sử dụng dịch vụ!</p>
                ";

                // Gửi email xác nhận đã nhận phiếu đặt bàn
                sendResetMail($customerEmail, $subject, $message);

                // Truyền biến $booking vào view PDF
                ob_start();
                include __DIR__ . '/../views/customer/booking/pdf_detail.php';
                $htmlContent = ob_get_clean();

                sendBookingPDFMail($customerEmail, $subject, $message, $htmlContent);
            }

            header('Location: index.php?page=booking&action=detail&id=' . $bookingId);
            exit;
        }

        $data = [
            'title' => 'Sửa Đặt Bàn - ' . SITE_NAME,
            'booking' => $booking
        ];
        $this->loadView('customer/booking/modify', $data);
    }
}
