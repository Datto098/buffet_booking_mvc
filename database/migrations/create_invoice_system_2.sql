-- 2. Insert giá vé mặc định
INSERT INTO `buffet_pricing` (`type`, `age_min`, `age_max`, `price`, `description`) VALUES
('adult', 18, NULL, 299000.00, 'Giá buffet người lớn'),
('child', 11, 17, 199000.00, 'Giá buffet trẻ em 11-17 tuổi'),
('child', 6, 10, 99000.00, 'Giá buffet trẻ em 6-10 tuổi'),
('child', 0, 5, 0.00, 'Miễn phí cho trẻ em dưới 6 tuổi');
