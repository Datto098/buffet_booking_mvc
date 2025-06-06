<?php
/**
 * Booking Controller
 */

require_once 'BaseController.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/User.php';

class BookingController extends BaseController {
    private $bookingModel;
    private $userModel;

    public function __construct() {
        $this->bookingModel = new Booking();
        $this->userModel = new User();
    }

    public function index() {
        $data = [
            'title' => 'Đặt Bàn - ' . SITE_NAME
        ];

        $this->loadView('customer/booking/index', $data);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleBookingSubmission();
        } else {
            $this->index();
        }
    }

    public function checkAvailability() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }

        $date = $_POST['booking_date'] ?? '';
        $time = $_POST['booking_time'] ?? '';
        $partySize = intval($_POST['party_size'] ?? 0);

        if (empty($date) || empty($time) || $partySize <= 0) {
            $this->jsonResponse(['error' => 'Invalid parameters'], 400);
        }

        // Validate date (must be today or future)
        $bookingDateTime = $date . ' ' . $time;
        if (strtotime($bookingDateTime) < time()) {
            $this->jsonResponse(['error' => 'Không thể đặt bàn cho thời gian trong quá khứ'], 400);
        }

        // Check availability
        $availability = $this->bookingModel->checkAvailability($date, $time, $partySize);

        $this->jsonResponse([
            'available' => $availability['available'],
            'message' => $availability['message'],
            'suggestedTimes' => $availability['suggestedTimes'] ?? []
        ]);
    }

    public function myBookings() {
        $this->requireLogin();

        $userId = $_SESSION['user_id'];
        $bookings = $this->bookingModel->getUserBookings($userId);

        $data = [
            'title' => 'Lịch Sử Đặt Bàn - ' . SITE_NAME,
            'bookings' => $bookings
        ];

        $this->loadView('customer/booking/my_bookings', $data);
    }

    public function detail() {
        $this->requireLogin();

        $bookingId = intval($_GET['id'] ?? 0);

        if ($bookingId <= 0) {
            redirect('/index.php?page=booking&action=myBookings');
        }

        $booking = $this->bookingModel->findById($bookingId);

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

    public function cancel() {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=booking&action=myBookings');
        }

        $this->validateCSRF();

        $bookingId = intval($_POST['booking_id'] ?? 0);

        if ($bookingId <= 0) {
            $_SESSION['error'] = 'ID đặt bàn không hợp lệ';
            redirect('/index.php?page=booking&action=myBookings');
        }

        $booking = $this->bookingModel->findById($bookingId);

        // Check if booking belongs to current user
        if (!$booking || $booking['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Không tìm thấy thông tin đặt bàn';
            redirect('/index.php?page=booking&action=myBookings');
        }

        // Check if booking can be cancelled (e.g., at least 2 hours before booking time)
        if (strtotime($booking['booking_datetime']) - time() < 2 * 3600) {
            $_SESSION['error'] = 'Không thể hủy đặt bàn trong vòng 2 giờ trước giờ đặt';
            redirect('/index.php?page=booking&action=detail&id=' . $bookingId);
        }

        if ($booking['status'] !== 'confirmed' && $booking['status'] !== 'pending') {
            $_SESSION['error'] = 'Không thể hủy đặt bàn này';
            redirect('/index.php?page=booking&action=detail&id=' . $bookingId);
        }

        // Update booking status
        if ($this->bookingModel->updateBookingStatus($bookingId, 'cancelled')) {
            $_SESSION['success'] = 'Đã hủy đặt bàn thành công';

            // TODO: Send cancellation email

        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi hủy đặt bàn';
        }

        redirect('/index.php?page=booking&action=myBookings');
    }

    private function handleBookingSubmission() {
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

            // TODO: Send confirmation email

            // Redirect to booking detail if logged in, otherwise to home
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
    public function getAvailableSlots() {
        $date = $_GET['date'] ?? '';
        $partySize = intval($_GET['party_size'] ?? 0);

        if (empty($date) || $partySize <= 0) {
            $this->jsonResponse(['error' => 'Invalid parameters'], 400);
        }

        $availableSlots = $this->bookingModel->getAvailableTimeSlots($date, $partySize);

        $this->jsonResponse(['slots' => $availableSlots]);
    }
}
?>
