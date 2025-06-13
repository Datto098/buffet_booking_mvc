-- Import sample reviews for testing the review management system
-- This script adds realistic Vietnamese review data to the reviews table

-- Clear existing reviews (optional - remove if you want to keep existing data)
-- DELETE FROM reviews WHERE id BETWEEN 1 AND 20;

-- Insert sample review data
INSERT INTO `reviews` (`id`, `user_id`, `order_id`, `food_item_id`, `rating`, `title`, `comment`, `is_verified`, `is_approved`, `helpful_count`, `created_at`, `updated_at`) VALUES
(1, 5, NULL, 1, 5, 'Buffet tuyệt vời!', 'Deluxe Buffet thực sự xứng đáng với giá tiền. Đồ ăn đa dạng, tươi ngon, nhân viên phục vụ chu đáo. Nhất định sẽ quay lại!', 1, 1, 15, '2025-06-08 10:30:00', '2025-06-11 10:30:00'),
(2, 1, NULL, 5, 4, 'Thịt bò Mỹ ngon', 'Ba chỉ bò Mỹ nướng vừa phải, thịt mềm và thấm gia vị. Tuy nhiên hơi mặn một chút theo ý kiến cá nhân.', 1, 1, 8, '2025-06-09 14:20:00', '2025-06-11 14:20:00'),
(3, 7, NULL, 7, 5, 'Sushi tươi ngon', 'Sushi cá hồi tại đây rất tươi, cơm vừa phải, cá hồi béo ngậy. Giá cả hợp lý so với chất lượng.', 1, 1, 12, '2025-06-09 16:45:00', '2025-06-11 16:45:00'),
(4, 5, NULL, 13, 5, 'Bánh flan hoàn hảo', 'Bánh flan mềm mượt, vị caramel đậm đà nhưng không quá ngọt. Món tráng miệng lý tưởng!', 0, 1, 6, '2025-06-09 19:15:00', '2025-06-11 19:15:00'),
(5, 1, NULL, 2, 3, 'Buffet bình thường', 'Standard Buffet có đủ món nhưng không có gì đặc biệt. Phù hợp với ngân sách nhưng không có điểm nhấn.', 0, 1, 3, '2025-06-10 12:00:00', '2025-06-11 12:00:00'),
(6, 7, NULL, 8, 4, 'Gimbap Hàn Quốc authentic', 'Gimbap làm khá giống với bên Hàn Quốc, nhân đầy đủ và cơm nêm vừa miệng. Chỉ tiếc là hơi nhỏ.', 1, 1, 9, '2025-06-10 13:30:00', '2025-06-11 13:30:00'),
(7, 5, NULL, 14, 5, 'Trà đào cam sả tuyệt vời', 'Nước uống rất thơm và mát, vị chua ngọt hài hòa. Rất phù hợp với thời tiết nóng bức ở Sài Gòn.', 1, 1, 11, '2025-06-10 15:45:00', '2025-06-11 15:45:00'),
(8, 1, NULL, 6, 4, 'Sườn bò non tuyệt', 'Sườn non nướng vừa tái vừa chín, ướp gia vị đậm đà. Thịt mềm và rất thơm, giá hơi cao nhưng xứng đáng.', 0, 1, 7, '2025-06-10 18:20:00', '2025-06-11 18:20:00'),
(9, 7, NULL, 9, 3, 'Tempura tôm cần cải thiện', 'Vỏ bột hơi dày, tôm tươi nhưng chiên hơi kỹ. Cần cải thiện kỹ thuật chiên để giữ độ giòn.', 1, 0, 2, '2025-06-10 20:00:00', '2025-06-11 20:00:00'),
(10, 5, NULL, 11, 4, 'Cơm chiên Nhật ngon', 'Cơm chiên không bị khô, trứng và rau củ tươi ngon. Phần ăn vừa phải, phù hợp cho bữa trưa nhẹ.', 1, 1, 5, '2025-06-11 11:30:00', '2025-06-11 11:30:00'),
(11, 1, NULL, 12, 5, 'Mì udon tuyệt hảo', 'Mì dai ngon, thịt bò mềm và ngọt. Nước dùng đậm đà, một trong những món ngon nhất tại đây!', 1, 1, 13, '2025-06-11 12:45:00', '2025-06-11 12:45:00'),
(12, 7, NULL, 3, 4, 'Buffet chay đa dạng', 'Vegetarian Special có nhiều lựa chọn cho người ăn chay. Món ăn tươi ngon, giá cả hợp lý.', 0, 1, 4, '2025-06-11 14:00:00', '2025-06-11 14:00:00'),
(13, 5, NULL, 10, 3, 'Há cảo bình thường', 'Há cảo hấp ổn nhưng không có gì đặc biệt. Nhân tôm tươi nhưng vỏ hơi dày, cần cải thiện.', 1, 0, 1, '2025-06-11 16:30:00', '2025-06-11 16:30:00'),
(14, 1, NULL, 15, 4, 'Trà tắc thanh mát', 'Trà tắc có vị chua nhẹ và thơm mùi trà. Thức uống phù hợp để kết thúc bữa ăn buffet.', 0, 1, 6, '2025-06-11 17:15:00', '2025-06-11 17:15:00'),
(15, 7, NULL, 16, 2, 'Bia không đặc biệt', 'Bia lon thông thường, không có gì đặc sắc. Giá hơi cao so với chất lượng, nên cân nhắc.', 1, 1, 0, '2025-06-11 19:00:00', '2025-06-11 19:00:00'),
(16, 5, NULL, 1, 5, 'Lần thứ hai vẫn tuyệt!', 'Quay lại lần thứ hai vẫn rất hài lòng với Deluxe Buffet. Chất lượng đồ ăn ổn định, dịch vụ tốt.', 1, 1, 10, '2025-06-11 20:30:00', '2025-06-11 20:30:00'),
(17, 1, NULL, 7, 5, 'Sushi chuẩn Nhật', 'Sushi cá hồi ở đây làm rất chuẩn, cơm nêm vừa phải, cá tươi ngon. Một trong những món phải thử!', 1, 1, 14, '2025-06-11 21:00:00', '2025-06-11 21:00:00'),
(18, 7, NULL, 5, 5, 'Ba chỉ bò xuất sắc', 'Thịt bò Mỹ nướng hoàn hảo, ướp gia vị đậm đà, mềm và juicy. Đây là lý do tôi quay lại nhà hàng!', 1, 1, 16, '2025-06-11 21:45:00', '2025-06-11 21:45:00'),
(19, 5, NULL, 14, 4, 'Thức uống tốt', 'Trà đào cam sả rất ngon, hương vị tự nhiên không bị ngọt gắt. Rất thích hợp cho mùa hè.', 0, 0, 3, '2025-06-11 22:15:00', '2025-06-11 22:15:00'),
(20, 1, NULL, 13, 5, 'Tráng miệng hoàn hảo', 'Bánh flan ở đây ngon nhất từ trước đến nay. Mềm mượt, ngọt vừa phải, caramel thơm lừng.', 1, 1, 9, '2025-06-11 22:30:00', '2025-06-11 22:30:00')
ON DUPLICATE KEY UPDATE
    rating = VALUES(rating),
    title = VALUES(title),
    comment = VALUES(comment),
    is_verified = VALUES(is_verified),
    is_approved = VALUES(is_approved),
    helpful_count = VALUES(helpful_count),
    updated_at = VALUES(updated_at);

-- Update AUTO_INCREMENT for reviews table
ALTER TABLE reviews AUTO_INCREMENT = 21;
