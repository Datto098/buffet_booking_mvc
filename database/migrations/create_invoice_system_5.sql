-- 5. Insert các loại phí phát sinh phổ biến
INSERT INTO `additional_charge_types` (`name`, `description`, `default_price`, `sort_order`) VALUES
('Khăn ướt', 'Phí khăn ướt cho khách', 5000.00, 1),
('Nước ngọt', 'Nước ngọt các loại', 15000.00, 2),
('Bia', 'Bia các loại', 25000.00, 3),
('Nước suối', 'Nước suối đóng chai', 10000.00, 4),
('Phí vệ sinh', 'Phí vệ sinh bàn ghế đặc biệt', 20000.00, 5),
('Phí đậu xe', 'Phí đậu xe ô tô/xe máy', 10000.00, 6);
