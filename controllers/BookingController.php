<?php

/**
 * Booking Controller
 */

require_once 'BaseController.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Notification.php';
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
    // Lấy thông tin nhà hàng cho footer
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM restaurant_info WHERE id = 1");
        $stmt->execute();
        $restaurantInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        // Lấy danh sách địa chỉ đang mở (status = 1)
        $stmt2 = $db->prepare("SELECT address FROM addresses WHERE status = 1");
        $stmt2->execute();
        $addresses = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $restaurantInfo = [
            'restaurant_name' => SITE_NAME,
            'address' => 'Địa chỉ nhà hàng',
            'phone' => '0123-456-789',
            'email' => ADMIN_EMAIL,
            'description' => 'Nội dung giới thiệu về nhà hàng...'
        ];
        $addresses = [];
    }

    $data = [
        'title' => 'Đặt Bàn - ' . SITE_NAME,
        'info' => $restaurantInfo,
        'addresses' => $addresses
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

    public function payment()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePaymentSubmission();
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
        $location = $_POST['booking_location'] ?? '';
        error_log("BookingController - Extracted: date=$date, time=$time, partySize=$partySize");

        // Validate dữ liệu
        if (!$date || !$time || !$partySize || !$location) {
            $response = [
                'available' => false,
                'message' => 'Vui lòng chọn đầy đủ ngày, địa chỉ chi nhánh, giờ và số lượng khách.'
            ];
            error_log("BookingController - Validation failed: " . json_encode($response));
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        // Validate thời gian đặt bàn phải trước ít nhất 2 tiếng
        $bookingDateTime = $date . ' ' . $time;
        $bookingTimestamp = strtotime($bookingDateTime);
        $currentTimestamp = time();
        $minimumAdvanceTime = $currentTimestamp + (2 * 60 * 60); // 2 tiếng = 2 * 60 * 60 giây

        if ($bookingTimestamp < $currentTimestamp) {
            $response = [
                'available' => false,
                'message' => 'Không thể đặt bàn cho thời gian trong quá khứ.'
            ];
            error_log("BookingController - Past time validation failed: " . json_encode($response));
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        } elseif ($bookingTimestamp <= $minimumAdvanceTime) {
            $currentTime = date('H:i', $currentTimestamp);
            $requiredTime = date('H:i', $minimumAdvanceTime + 60); // Thêm 1 phút để đảm bảo
            $response = [
                'available' => false,
                'message' => "Bạn phải đặt bàn trước ít nhất 2 tiếng. Hiện tại là {$currentTime}, bạn chỉ có thể đặt bàn từ {$requiredTime} trở đi."
            ];
            error_log("BookingController - 2-hour advance validation failed: " . json_encode($response));
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        // Gọi model để kiểm tra bàn trống
        try {
            $result = $this->bookingModel->checkAvailability($date, $time, $partySize, $location);
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

        // Lấy thông tin nhà hàng cho footer
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM restaurant_info WHERE id = 1");
            $stmt->execute();
            $restaurantInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $restaurantInfo = [
                'restaurant_name' => SITE_NAME,
                'address' => 'Địa chỉ nhà hàng',
                'phone' => '0123-456-789',
                'email' => ADMIN_EMAIL,
                'description' => 'Nội dung giới thiệu về nhà hàng...'
            ];
        }

        $data = [
            'title' => 'Lịch Sử Đặt Bàn - ' . SITE_NAME,
            'bookings' => $bookings,
            'total_pages' => (int)$totalPages,
            'current_page' => (int)$currentPage,
            'filter_params' => '',
            'info' => $restaurantInfo // Thêm dòng này
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

        // Lấy thông tin nhà hàng cho footer
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM restaurant_info WHERE id = 1");
            $stmt->execute();
            $restaurantInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $restaurantInfo = [
                'restaurant_name' => SITE_NAME,
                'address' => 'Địa chỉ nhà hàng',
                'phone' => '0123-456-789',
                'email' => ADMIN_EMAIL,
                'description' => 'Nội dung giới thiệu về nhà hàng...'
            ];
        }

        $data = [
            'title' => 'Chi Tiết Đặt Bàn - ' . SITE_NAME,
            'booking' => $booking,
            'info' => $restaurantInfo // Thêm dòng này
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
        $bookingLocation = sanitizeInput($_POST['booking_location'] ?? '');
        $specialRequests = sanitizeInput($_POST['special_requests'] ?? '');

        // Get age group data
        $adultCount = intval($_POST['adult_count'] ?? 0);
        $children11_17Count = intval($_POST['children_11_17_count'] ?? 0);
        $children6_10Count = intval($_POST['children_6_10_count'] ?? 0);
        $children0_5Count = intval($_POST['children_0_5_count'] ?? 0);

        // Calculate total party size from age groups
        $totalGuests = $adultCount + $children11_17Count + $children6_10Count + $children0_5Count;
        if ($totalGuests > 0) {
            $partySize = $totalGuests; // Override party_size with calculated total
        }

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

        // Validate age group counts
        if ($adultCount < 0 || $adultCount > 20) {
            $errors[] = 'Số lượng người lớn không hợp lệ';
        }
        if ($children11_17Count < 0 || $children11_17Count > 10) {
            $errors[] = 'Số lượng trẻ em 11-17 tuổi không hợp lệ';
        }
        if ($children6_10Count < 0 || $children6_10Count > 10) {
            $errors[] = 'Số lượng trẻ em 6-10 tuổi không hợp lệ';
        }
        if ($children0_5Count < 0 || $children0_5Count > 10) {
            $errors[] = 'Số lượng trẻ em 0-5 tuổi không hợp lệ';
        }

        // Must have at least one adult for booking
        if ($adultCount == 0) {
            $errors[] = 'Phải có ít nhất 1 người lớn để đặt bàn';
        }

        if (empty($bookingLocation)) {
            $errors[] = 'Vui lòng chọn địa chỉ chi nhánh';
        }

        // Validate date and time
        $bookingDateTime = $bookingDate . ' ' . $bookingTime;
        $bookingTimestamp = strtotime($bookingDateTime);
        $currentTimestamp = time();
        $minimumAdvanceTime = $currentTimestamp + (2 * 60 * 60); // 2 tiếng = 2 * 60 * 60 giây

        if ($bookingTimestamp < $currentTimestamp) {
            $errors[] = 'Không thể đặt bàn cho thời gian trong quá khứ';
        } elseif ($bookingTimestamp <= $minimumAdvanceTime) {
            $currentTime = date('H:i', $currentTimestamp);
            $requiredTime = date('H:i', $minimumAdvanceTime + 60); // Thêm 1 phút để đảm bảo
            $errors[] = "Bạn phải đặt bàn trước ít nhất 2 tiếng. Hiện tại là {$currentTime}, bạn chỉ có thể đặt bàn từ {$requiredTime} trở đi.";
        }

        $address = $bookingLocation;
        if (!$this->bookingModel->hasTablesForAddress($address)) {
            $errors[] = 'Địa chỉ này chưa có bàn nào!';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $_SESSION['form_data'] = $_POST;
            $this->index();
            return;
        }

        // Check availability again
        $availability = $this->bookingModel->checkAvailability($bookingDate, $bookingTime, $partySize, $bookingLocation);

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
            'booking_location' => $bookingLocation,
            'special_requests' => $specialRequests,
            'status' => 'pending',
            'adult_count' => $adultCount,
            'children_11_17_count' => $children11_17Count,
            'children_6_10_count' => $children6_10Count,
            'children_0_5_count' => $children0_5Count,
            'guest_breakdown' => json_encode([
                'adults' => $adultCount,
                'children_11_17' => $children11_17Count,
                'children_6_10' => $children6_10Count,
                'children_0_5' => $children0_5Count
            ])
        ];

        // Calculate payment amounts
        $paymentData = $this->calculatePaymentAmounts($bookingDate, $bookingTime, $bookingData);
        $bookingData = array_merge($bookingData, $paymentData);

        // Add user ID if logged in
        if (isLoggedIn()) {
            $bookingData['user_id'] = $_SESSION['user_id'];
        }        // Create booking
        $bookingId = $this->bookingModel->createBookingFromController($bookingData);

        if ($bookingId) {
            // Notification will be created automatically by database trigger
            $_SESSION['success'] = 'Đặt bàn thành công! Chúng tôi sẽ liên hệ với bạn sớm nhất để xác nhận.';

            // Tạo subject và message cho email xác nhận đặt bàn
            $subject = "Đặt bàn thành công tại Buffet Booking";
            $message = "
                <h2>Xin chào $customerName,</h2>
                <p>Bạn đã đặt bàn thành công tại <b>Buffet Booking</b>!</p>
                <ul>
                    <li><b>Ngày:</b> $bookingDate</li>
                    <li><b>Giờ:</b> $bookingTime</li>
                    <li><b>Số lượng khách:</b> $partySize người</li>
                    <li><b>Địa chỉ chi nhánh:</b> $bookingLocation</li>
                    <li><b>Số điện thoại:</b> $customerPhone</li>
                </ul>
                <h3>Thông tin thanh toán:</h3>
                <ul>
                    <li><b>Tổng tiền:</b> " . number_format($paymentData['total_amount'], 0, ',', '.') . " VNĐ</li>
                    <li><b>Cần trả trước (15%):</b> " . number_format($paymentData['prepaid_amount'], 0, ',', '.') . " VNĐ</li>
                    <li><b>Còn lại khi tới ăn:</b> " . number_format($paymentData['remaining_amount'], 0, ',', '.') . " VNĐ</li>
                </ul>
                <div style='background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                    <strong>Lưu ý quan trọng:</strong><br>
                    - Quý khách cần thanh toán trước 15% để xác nhận đặt bàn<br>
                    - Phần còn lại (85%) sẽ thanh toán khi tới nhà hàng<br>
                    - Có thể phát sinh thêm phụ phí tùy theo yêu cầu thực tế
                </div>
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

    private function handlePaymentSubmission()
    {
        $this->validateCSRF();

        // Get form data (same validation as handleBookingSubmission)
        $customerName = sanitizeInput($_POST['customer_name'] ?? '');
        $customerEmail = sanitizeInput($_POST['customer_email'] ?? '');
        $customerPhone = sanitizeInput($_POST['customer_phone'] ?? '');
        $bookingDate = $_POST['booking_date'] ?? '';
        $bookingTime = $_POST['booking_time'] ?? '';
        $partySize = intval($_POST['party_size'] ?? 0);
        $bookingLocation = sanitizeInput($_POST['booking_location'] ?? '');
        $specialRequests = sanitizeInput($_POST['special_requests'] ?? '');

        // Get age group data
        $adultCount = intval($_POST['adult_count'] ?? 0);
        $children11_17Count = intval($_POST['children_11_17_count'] ?? 0);
        $children6_10Count = intval($_POST['children_6_10_count'] ?? 0);
        $children0_5Count = intval($_POST['children_0_5_count'] ?? 0);

        // Calculate total party size from age groups
        $totalGuests = $adultCount + $children11_17Count + $children6_10Count + $children0_5Count;
        if ($totalGuests > 0) {
            $partySize = $totalGuests;
        }

        // Validation (same as handleBookingSubmission)
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

        if ($adultCount == 0) {
            $errors[] = 'Phải có ít nhất 1 người lớn để đặt bàn';
        }

        if (empty($bookingLocation)) {
            $errors[] = 'Vui lòng chọn địa chỉ chi nhánh';
        }

        // Validate date and time
        $bookingDateTime = $bookingDate . ' ' . $bookingTime;
        $bookingTimestamp = strtotime($bookingDateTime);
        $currentTimestamp = time();
        $minimumAdvanceTime = $currentTimestamp + (2 * 60 * 60);

        if ($bookingTimestamp < $currentTimestamp) {
            $errors[] = 'Không thể đặt bàn cho thời gian trong quá khứ';
        } elseif ($bookingTimestamp <= $minimumAdvanceTime) {
            $currentTime = date('H:i', $currentTimestamp);
            $requiredTime = date('H:i', $minimumAdvanceTime + 60);
            $errors[] = "Bạn phải đặt bàn trước ít nhất 2 tiếng. Hiện tại là {$currentTime}, bạn chỉ có thể đặt bàn từ {$requiredTime} trở đi.";
        }

        if (!$this->bookingModel->hasTablesForAddress($bookingLocation)) {
            $errors[] = 'Địa chỉ này chưa có bàn nào!';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $_SESSION['form_data'] = $_POST;
            $this->index();
            return;
        }

        // Check availability
        $availability = $this->bookingModel->checkAvailability($bookingDate, $bookingTime, $partySize, $bookingLocation);

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
            'booking_location' => $bookingLocation,
            'special_requests' => $specialRequests,
            'status' => 'pending_payment', // Different status for payment pending
            'adult_count' => $adultCount,
            'children_11_17_count' => $children11_17Count,
            'children_6_10_count' => $children6_10Count,
            'children_0_5_count' => $children0_5Count,
            'guest_breakdown' => json_encode([
                'adults' => $adultCount,
                'children_11_17' => $children11_17Count,
                'children_6_10' => $children6_10Count,
                'children_0_5' => $children0_5Count
            ])
        ];

        // Calculate payment amounts
        $paymentData = $this->calculatePaymentAmounts($bookingDate, $bookingTime, $bookingData);
        $bookingData = array_merge($bookingData, $paymentData);

        // Add user ID if logged in
        if (isLoggedIn()) {
            $bookingData['user_id'] = $_SESSION['user_id'];
        }

        // Store booking data in session to create after payment success
        $_SESSION['pending_booking'] = $bookingData;

        // Create VNPay payment
        $this->createVNPayPayment($paymentData['prepaid_amount'], $bookingData);
    }

    private function createVNPayPayment($amount, $bookingData)
    {
        // Include VNPay config
        require_once __DIR__ . '/../config/vnpay.php';

        date_default_timezone_set('Asia/Ho_Chi_Minh');

        // Generate unique transaction reference
        $vnp_TxnRef = time() . "_BOOKING_" . rand(1000, 9999);

        // Order info for VNPay
        $vnp_OrderInfo = 'Thanh toán đặt bàn - ' . $bookingData['customer_name'] . ' - ' . $bookingData['booking_datetime'];
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $amount * 100; // VNPay requires amount * 100
        $vnp_Locale = 'vn';
        $vnp_BankCode = 'NCB';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        // Payment expiration time (15 minutes)
        $startTime = date("YmdHis");
        $expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));

        // Return URL for booking payment
        $vnp_Returnurl = SITE_URL . "/booking/vnpay_return";

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => VNP_TMNCODE,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => $expire
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        // Sort data
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = VNP_URL . "?" . $query;
        if (defined('VNP_HASHSECRET')) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, VNP_HASHSECRET);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        // Store payment info in session for return processing
        $_SESSION['vnpay_booking_payment'] = [
            'vnp_txn_ref' => $vnp_TxnRef,
            'amount' => $amount,
            'booking_data' => $bookingData,
            'payment_data' => $inputData
        ];

        // Redirect to VNPay
        redirect($vnp_Url);
    }

    public function vnpay_return()
    {
        require_once __DIR__ . '/../config/vnpay.php';

        // Check if we have payment info in session
        if (!isset($_SESSION['vnpay_booking_payment'])) {
            $_SESSION['error'] = 'Không tìm thấy thông tin thanh toán';
            redirect('/booking');
            return;
        }

        $paymentInfo = $_SESSION['vnpay_booking_payment'];

        // Verify VNPay response
        $vnp_SecureHash = $_GET['vnp_SecureHash'] ?? '';
        $inputData = $_GET;
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);

        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, VNP_HASHSECRET);

        if ($secureHash === $vnp_SecureHash) {
            $vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? '';
            $vnp_TxnRef = $_GET['vnp_TxnRef'] ?? '';

            // Check if transaction reference matches
            if ($vnp_TxnRef === $paymentInfo['vnp_txn_ref']) {
                if ($vnp_ResponseCode === '00') {
                    // Payment successful - create booking
                    $bookingData = $paymentInfo['booking_data'];
                    $bookingData['payment_status'] = 'partial'; // Paid 15%
                    $bookingData['status'] = 'confirmed'; // Change status to confirmed after payment

                    try {
                        $bookingId = $this->bookingModel->createBookingFromController($bookingData);

                        if ($bookingId) {
                            // Clear session data
                            unset($_SESSION['vnpay_booking_payment']);
                            unset($_SESSION['pending_booking']);
                            unset($_SESSION['form_data']);

                            // Create notification
                            $_SESSION['success'] = 'Thanh toán thành công! Đặt bàn của bạn đã được xác nhận.';

                            // Send confirmation email
                            $this->sendBookingConfirmationEmail($bookingId, $bookingData);

                            if (isLoggedIn()) {
                                redirect('/booking/detail/' . $bookingId);
                            } else {
                                redirect('/');
                            }
                        } else {
                            $_SESSION['error'] = 'Thanh toán thành công nhưng có lỗi khi tạo đặt bàn. Vui lòng liên hệ hỗ trợ.';
                            redirect('/booking');
                        }
                    } catch (Exception $e) {
                        error_log("Booking creation error after payment: " . $e->getMessage());
                        $_SESSION['error'] = 'Có lỗi xảy ra khi xử lý đặt bàn. Vui lòng liên hệ hỗ trợ.';
                        redirect('/booking');
                    }
                } else {
                    // Payment failed
                    unset($_SESSION['vnpay_booking_payment']);
                    $_SESSION['error'] = 'Thanh toán không thành công. Vui lòng thử lại.';
                    $_SESSION['form_data'] = $paymentInfo['booking_data'];
                    redirect('/booking');
                }
            } else {
                $_SESSION['error'] = 'Thông tin giao dịch không khớp.';
                redirect('/booking');
            }
        } else {
            $_SESSION['error'] = 'Chữ ký không hợp lệ.';
            redirect('/booking');
        }
    }

    private function sendBookingConfirmationEmail($bookingId, $bookingData)
    {
        // Send confirmation email similar to handleBookingSubmission
        $customerEmail = $bookingData['customer_email'];
        $customerName = $bookingData['customer_name'];

        $subject = "Đặt bàn thành công tại Buffet Booking";
        $message = "
            <h2>Xin chào $customerName,</h2>
            <p>Bạn đã đặt bàn và thanh toán thành công tại <b>Buffet Booking</b>!</p>
            <ul>
                <li><b>Mã đặt bàn:</b> #$bookingId</li>
                <li><b>Ngày giờ:</b> {$bookingData['booking_datetime']}</li>
                <li><b>Số lượng khách:</b> {$bookingData['party_size']} người</li>
                <li><b>Địa chỉ:</b> {$bookingData['booking_location']}</li>
            </ul>
            <h3>Thông tin thanh toán:</h3>
            <ul>
                <li><b>Tổng tiền:</b> " . number_format($bookingData['total_amount'], 0, ',', '.') . " VNĐ</li>
                <li><b>Đã thanh toán:</b> " . number_format($bookingData['prepaid_amount'], 0, ',', '.') . " VNĐ</li>
                <li><b>Còn lại:</b> " . number_format($bookingData['remaining_amount'], 0, ',', '.') . " VNĐ</li>
            </ul>
            <p><strong>Trạng thái:</strong> Đã xác nhận và thanh toán</p>
            <p>Cảm ơn bạn đã sử dụng dịch vụ!</p>
        ";

        // Include mail helper and send email
        require_once __DIR__ . '/../helpers/mail_helper.php';
        sendResetMail($customerEmail, $subject, $message);
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
            // Debug logging
            error_log("BookingController modify POST - Data received: " . json_encode($_POST));
            error_log("BookingController modify - Booking ID: " . $bookingId);
            error_log("BookingController modify - User ID: " . $userId);

            try {
                // Validate required fields
                if (
                    empty($_POST['customer_name']) || empty($_POST['phone_number']) ||
                    empty($_POST['booking_date']) || empty($_POST['booking_time']) ||
                    empty($_POST['number_of_guests'])
                ) {
                    $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc.';
                    header('Location: index.php?page=booking&action=modify&id=' . $bookingId);
                    exit;
                }

                $newDate = $_POST['booking_date'];
                $newTime = $_POST['booking_time'];
                $newDateTime = $newDate . ' ' . $newTime . ':00';
                $changedDateTime = $newDateTime !== $booking['reservation_time'];                // Lấy email từ user (vì table reservations không có field email)
                $email = '';
                if (!empty($booking['customer_email'])) {
                    $email = $booking['customer_email'];
                } elseif (!empty($booking['user_id'])) {
                    $user = $this->userModel->findById($booking['user_id']);
                    if ($user && !empty($user['email'])) {
                        $email = $user['email'];
                    }
                }
                $updateData = [
                    'customer_name'    => $_POST['customer_name'],
                    'phone_number'     => $_POST['phone_number'],
                    'number_of_guests' => (int)$_POST['number_of_guests'],
                    'reservation_time' => $newDateTime,
                    'notes'            => $_POST['notes'] ?? '',
                    'status'           => $changedDateTime ? 'pending' : $booking['status'],
                ];


                $result = $this->bookingModel->updateBooking($bookingId, $updateData);

                // Debug logging
                error_log("BookingController modify - Update data: " . json_encode($updateData));
                error_log("BookingController modify - Update result: " . ($result ? 'SUCCESS' : 'FAILED'));

                // Gửi email nếu đổi ngày/giờ
                if ($result) {
                    $_SESSION['success'] = 'Sửa bàn thành công! Chúng tôi sẽ liên hệ với bạn sớm nhất để xác nhận.';

                    // Lấy lại thông tin booking vừa sửa
                    $booking = $this->bookingModel->getBookingDetails($bookingId);                    // Lấy thông tin khách hàng từ dữ liệu booking
                    $customerName = $booking['customer_name'] ?? '';
                    $customerEmail = '';
                    if (!empty($booking['customer_email'])) {
                        $customerEmail = $booking['customer_email'];
                    } elseif (!empty($booking['user_id'])) {
                        $user = $this->userModel->findById($booking['user_id']);
                        if ($user && !empty($user['email'])) {
                            $customerEmail = $user['email'];
                        }
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
                ";                // Gửi email xác nhận đã nhận phiếu đặt bàn
                    if (!empty($customerEmail)) {
                        sendResetMail($customerEmail, $subject, $message);

                        // Tạo nội dung HTML cho PDF
                        $htmlContent = "
                        <h2>PHIẾU ĐẶT BÀN</h2>
                        <p><strong>Khách hàng:</strong> $customerName</p>
                        <p><strong>Số điện thoại:</strong> $customerPhone</p>
                        <p><strong>Ngày:</strong> $bookingDate</p>
                        <p><strong>Giờ:</strong> $bookingTime</p>
                        <p><strong>Số lượng khách:</strong> $partySize</p>
                        <p><strong>Trạng thái:</strong> Chờ xác nhận</p>
                    ";
                        sendBookingPDFMail($customerEmail, $subject, $message, $htmlContent);
                    }
                } else {
                    $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật booking. Vui lòng thử lại.';
                }

                header('Location: index.php?page=booking&action=detail&id=' . $bookingId);
                exit;
            } catch (Exception $e) {
                error_log("Booking update error: " . $e->getMessage());
                $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật booking. Vui lòng thử lại.';
                header('Location: index.php?page=booking&action=modify&id=' . $bookingId);
                exit;
            }
        }

        // Lấy thông tin nhà hàng cho footer
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM restaurant_info WHERE id = 1");
            $stmt->execute();
            $restaurantInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $restaurantInfo = [
                'restaurant_name' => SITE_NAME,
                'address' => 'Địa chỉ nhà hàng',
                'phone' => '0123-456-789',
                'email' => ADMIN_EMAIL,
                'description' => 'Nội dung giới thiệu về nhà hàng...'
            ];
        }

        $data = [
            'title' => 'Sửa Đặt Bàn - ' . SITE_NAME,
            'booking' => $booking,
            'info' => $restaurantInfo // Thêm dòng này
        ];
        $this->loadView('customer/booking/modify', $data);
    }

    /**
     * Calculate payment amounts based on date, time and booking data
     */
    private function calculatePaymentAmounts($bookingDate, $bookingTime, $bookingData)
    {
        // Get pricing from database
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM age_based_pricing ORDER BY min_age");
            $stmt->execute();
            $agePricing = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($agePricing)) {
                // Default pricing if no database record
                $agePricing = [
                    ['age_group' => 'adults', 'min_age' => 18, 'max_age' => 100, 'lunch_price' => 299000, 'dinner_price' => 399000, 'weekend_price' => 449000],
                    ['age_group' => 'children_11_17', 'min_age' => 11, 'max_age' => 17, 'lunch_price' => 199000, 'dinner_price' => 199000, 'weekend_price' => 199000],
                    ['age_group' => 'children_6_10', 'min_age' => 6, 'max_age' => 10, 'lunch_price' => 99000, 'dinner_price' => 99000, 'weekend_price' => 99000],
                    ['age_group' => 'children_0_5', 'min_age' => 0, 'max_age' => 5, 'lunch_price' => 0, 'dinner_price' => 0, 'weekend_price' => 0]
                ];
            }
        } catch (Exception $e) {
            // Default pricing on error
            $agePricing = [
                ['age_group' => 'adults', 'min_age' => 18, 'max_age' => 100, 'lunch_price' => 299000, 'dinner_price' => 399000, 'weekend_price' => 449000],
                ['age_group' => 'children_11_17', 'min_age' => 11, 'max_age' => 17, 'lunch_price' => 199000, 'dinner_price' => 199000, 'weekend_price' => 199000],
                ['age_group' => 'children_6_10', 'min_age' => 6, 'max_age' => 10, 'lunch_price' => 99000, 'dinner_price' => 99000, 'weekend_price' => 99000],
                ['age_group' => 'children_0_5', 'min_age' => 0, 'max_age' => 5, 'lunch_price' => 0, 'dinner_price' => 0, 'weekend_price' => 0]
            ];
        }

        // Determine pricing based on date and time
        $bookingDateTime = new DateTime($bookingDate);
        $dayOfWeek = $bookingDateTime->format('w'); // 0 = Sunday, 6 = Saturday
        $timeSlot = intval(explode(':', $bookingTime)[0]);

        // Weekend pricing (Saturday = 6, Sunday = 0)
        $isWeekend = $dayOfWeek == 0 || $dayOfWeek == 6;

        $totalAmount = 0;

        // Calculate cost for each age group
        foreach ($agePricing as $pricing) {
            $count = 0;
            $pricePerPerson = 0;

            // Get count for this age group
            switch ($pricing['age_group']) {
                case 'adults':
                    $count = $bookingData['adult_count'] ?? 0;
                    break;
                case 'children_11_17':
                    $count = $bookingData['children_11_17_count'] ?? 0;
                    break;
                case 'children_6_10':
                    $count = $bookingData['children_6_10_count'] ?? 0;
                    break;
                case 'children_0_5':
                    $count = $bookingData['children_0_5_count'] ?? 0;
                    break;
            }

            if ($count > 0) {
                // Determine price based on time slot
                if ($isWeekend) {
                    $pricePerPerson = $pricing['weekend_price'];
                } else if ($timeSlot >= 11 && $timeSlot < 15) {
                    // Lunch time (11:00-15:00)
                    $pricePerPerson = $pricing['lunch_price'];
                } else if ($timeSlot >= 17 && $timeSlot < 21) {
                    // Dinner time (17:00-21:00)
                    $pricePerPerson = $pricing['dinner_price'];
                } else {
                    // Default to dinner price
                    $pricePerPerson = $pricing['dinner_price'];
                }

                $totalAmount += $count * $pricePerPerson;
            }
        }

        $prepaidAmount = round($totalAmount * 0.15); // 15% prepayment
        $remainingAmount = $totalAmount - $prepaidAmount;

        return [
            'total_amount' => $totalAmount,
            'prepaid_amount' => $prepaidAmount,
            'remaining_amount' => $remainingAmount,
            'payment_status' => 'pending' // Default status
        ];
    }
}
