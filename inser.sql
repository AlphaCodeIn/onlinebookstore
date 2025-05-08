-- Insert categories
INSERT INTO `categories` (`category_id`, `name`, `description`) VALUES
(1, 'Fiction', 'Novels, short stories, and other fictional works'),
(2, 'Non-Fiction', 'Factual books including biographies, history, and science'),
(3, 'Academic', 'Textbooks and educational materials'),
(4, 'Children', 'Books for young readers'),
(5, 'Spiritual', 'Books on religion, philosophy and spirituality');

-- Insert publishers
INSERT INTO `publishers` (`publisher_id`, `name`, `address`, `phone`, `email`, `website`) VALUES
(1, 'Penguin Random House India', '7th Floor, Infinity Tower C, DLF Cyber City, Gurugram 122002', '0124-4785600', 'customercare@penguinrandomhouse.in', 'www.penguin.co.in'),
(2, 'HarperCollins India', 'A-75, Sector 57, Noida 201301', '0120-4044800', 'contact@harpercollins.co.in', 'www.harpercollins.co.in'),
(3, 'Rupa Publications', 'D-78, Okhla Industrial Area Phase I, New Delhi 110020', '011-26816621', 'info@rupapublications.com', 'www.rupeepublications.com'),
(4, 'Westland Publications', 'No. 10, 5th Cross Street, Kasturba Nagar, Chennai 600020', '044-30809090', 'support@westlandbooks.com', 'www.westlandbooks.com'),
(5, 'Jaico Publishing House', '121 Mahatma Gandhi Road, Mumbai 400001', '022-22010941', 'jaico@jaicobooks.com', 'www.jaicobooks.com');

-- Insert users with Indian names and addresses
INSERT INTO `users` (`user_id`, `username`, `password_hash`, `email`, `first_name`, `last_name`, `phone`, `address_line1`, `address_line2`, `city`, `state`, `postal_code`, `country`, `created_at`, `last_login`, `is_active`, `password`) VALUES
(1, 'rajesh_kumar', '$2y$10$DLPJO.mfshA8n7tvM/K/UOGupkkC1fVlaHtwm8EqKaBxwXU9UeX06', 'rajesh.kumar@gmail.com', 'Rajesh', 'Kumar', '9876543210', '24 Green Park', 'Near Metro Station', 'New Delhi', 'Delhi', '110016', 'India', '2025-05-08 10:00:00', NULL, 1, ''),
(2, 'priya_sharma', '$2y$10$DLPJO.mfshA8n7tvM/K/UOGupkkC1fVlaHtwm8EqKaBxwXU9UeX06', 'priya.sharma@yahoo.com', 'Priya', 'Sharma', '8765432109', '45 MG Road', 'Opposite City Mall', 'Bangalore', 'Karnataka', '560001', 'India', '2025-05-08 10:00:00', NULL, 1, ''),
(3, 'amit_patel', '$2y$10$DLPJO.mfshA8n7tvM/K/UOGupkkC1fVlaHtwm8EqKaBxwXU9UeX06', 'amit.patel@gmail.com', 'Amit', 'Patel', '7654321098', '12 Nehru Nagar', 'Beside ICICI Bank', 'Mumbai', 'Maharashtra', '400025', 'India', '2025-05-08 10:00:00', NULL, 1, ''),
(4, 'ananya_gupta', '$2y$10$DLPJO.mfshA8n7tvM/K/UOGupkkC1fVlaHtwm8EqKaBxwXU9UeX06', 'ananya.gupta@hotmail.com', 'Ananya', 'Gupta', '6543210987', '78 Gandhi Road', 'Near Clock Tower', 'Kolkata', 'West Bengal', '700007', 'India', '2025-05-08 10:00:00', NULL, 1, ''),
(5, 'vikram_singh', '$2y$10$DLPJO.mfshA8n7tvM/K/UOGupkkC1fVlaHtwm8EqKaBxwXU9UeX06', 'vikram.singh@gmail.com', 'Vikram', 'Singh', '9432109876', '90 Patel Chowk', 'Behind Central Library', 'Chennai', 'Tamil Nadu', '600002', 'India', '2025-05-08 10:00:00', NULL, 1, '');

-- Insert books with authentic Indian titles and cover images
INSERT INTO `books` (`book_id`, `isbn`, `title`, `author_name`, `description`, `publisher_id`, `publication_date`, `language`, `pages`, `price`, `stock_quantity`, `cover_image_url`, `is_featured`, `is_active`, `created_at`, `updated_at`, `category_id`) VALUES
(1, '9780143103398', 'The God of Small Things', 'Arundhati Roy', 'Booker Prize winning novel set in Kerala', 1, '1997-01-01', 'English', 336, 399.00, 50, 'https://m.media-amazon.com/images/I/81V9XpSITJL._SL1500_.jpg', 1, 1, '2025-05-08 10:00:00', NULL, 1),
(2, '9788122204120', 'Train to Pakistan', 'Khushwant Singh', 'Historical novel about the Partition of India', 1, '1956-01-01', 'English', 181, 299.00, 35, 'https://m.media-amazon.com/images/I/71YHjVXyR0L._SL1500_.jpg', 1, 1, '2025-05-08 10:00:00', NULL, 1),
(3, '9780143068285', 'The Palace of Illusions', 'Chitra Banerjee Divakaruni', 'Mahabharata retold from Draupadi\'s perspective', 2, '2008-01-01', 'English', 360, 349.00, 40, 'https://m.media-amazon.com/images/I/71JSMX5WGQL._SL1500_.jpg', 0, 1, '2025-05-08 10:00:00', NULL, 1),
(4, '9788172234980', 'The Discovery of India', 'Jawaharlal Nehru', 'Classic work on Indian history', 3, '1946-01-01', 'English', 600, 450.00, 25, 'https://m.media-amazon.com/images/I/81WUB4sD2hL._SL1500_.jpg', 1, 1, '2025-05-08 10:00:00', NULL, 2),
(5, '9789387693820', 'My Experiments with Truth', 'Mahatma Gandhi', 'Autobiography of the Father of the Nation', 4, '1927-01-01', 'English', 480, 275.00, 30, 'https://m.media-amazon.com/images/I/71tGZL-lNIL._SL1500_.jpg', 0, 1, '2025-05-08 10:00:00', NULL, 2),
(6, '9780143103399', 'The Guide', 'R.K. Narayan', 'Classic novel set in the fictional town of Malgudi', 2, '1958-01-01', 'English', 220, 249.00, 45, 'https://m.media-amazon.com/images/I/81WUB4sD2hL._SL1500_.jpg', 1, 1, '2025-05-08 10:00:00', NULL, 1),
(7, '9788171673400', 'Gitanjali', 'Rabindranath Tagore', 'Nobel Prize winning collection of poems', 1, '1910-01-01', 'English', 104, 199.00, 60, 'https://m.media-amazon.com/images/I/71tGZL-lNIL._SL1500_.jpg', 0, 1, '2025-05-08 10:00:00', NULL, 5),
(8, '9788185986004', 'The Room on the Roof', 'Ruskin Bond', 'Coming-of-age novel set in Dehradun', 5, '1956-01-01', 'English', 192, 225.00, 55, 'https://m.media-amazon.com/images/I/71YHjVXyR0L._SL1500_.jpg', 1, 1, '2025-05-08 10:00:00', NULL, 1),
(9, '9780143426449', 'The Immortals of Meluha', 'Amish Tripathi', 'First book of Shiva Trilogy', 2, '2010-01-01', 'English', 390, 299.00, 65, 'https://m.media-amazon.com/images/I/71JSMX5WGQL._SL1500_.jpg', 1, 1, '2025-05-08 10:00:00', NULL, 1),
(10, '9789386224563', 'Half Girlfriend', 'Chetan Bhagat', 'Contemporary love story set in Delhi and Bihar', 4, '2014-01-01', 'English', 280, 199.00, 70, 'https://m.media-amazon.com/images/I/81WUB4sD2hL._SL1500_.jpg', 0, 1, '2025-05-08 10:00:00', NULL, 1),
(11, '9780143333623', 'Wings of Fire', 'A.P.J. Abdul Kalam', 'Autobiography of India\'s Missile Man', 1, '1999-01-01', 'English', 180, 250.00, 40, 'https://m.media-amazon.com/images/I/71tGZL-lNIL._SL1500_.jpg', 1, 1, '2025-05-08 10:00:00', NULL, 2),
(12, '9789388754438', 'The Art of Thinking Clearly', 'Rolf Dobelli', 'A guide to better decision making', 3, '2013-01-01', 'English', 384, 299.00, 30, 'https://m.media-amazon.com/images/I/71JSMX5WGQL._SL1500_.jpg', 0, 1, '2025-05-08 10:00:00', NULL, 3),
(13, '9780143444870', 'Panchatantra', 'Vishnu Sharma', 'Ancient Indian collection of interrelated animal fables', 1, '2000-01-01', 'English', 240, 175.00, 50, 'https://m.media-amazon.com/images/I/71YHjVXyR0L._SL1500_.jpg', 1, 1, '2025-05-08 10:00:00', NULL, 4),
(14, '9780143104548', 'The Bhagavad Gita', 'Eknath Easwaran', 'Classic Indian spiritual text', 2, '2007-01-01', 'English', 208, 225.00, 60, 'https://m.media-amazon.com/images/I/71tGZL-lNIL._SL1500_.jpg', 1, 1, '2025-05-08 10:00:00', NULL, 5),
(15, '9780143333624', 'The Argumentative Indian', 'Amartya Sen', 'Essays on Indian history, culture and identity', 1, '2005-01-01', 'English', 432, 399.00, 25, 'https://m.media-amazon.com/images/I/81WUB4sD2hL._SL1500_.jpg', 0, 1, '2025-05-08 10:00:00', NULL, 2);

-- Insert some sample orders
INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `total_amount`, `status`, `shipping_address`, `billing_address`, `payment_method`, `notes`, `tracking_number`) VALUES
(1, 1, '2025-05-08 10:30:00', 648.00, 'Delivered', '24 Green Park, Near Metro Station, New Delhi 110016', '24 Green Park, Near Metro Station, New Delhi 110016', 'COD', 'Please deliver after 6pm', 'DEL123456789'),
(2, 2, '2025-05-08 11:15:00', 399.00, 'Shipped', '45 MG Road, Opposite City Mall, Bangalore 560001', '45 MG Road, Opposite City Mall, Bangalore 560001', 'COD', 'Gift wrapping required', 'DEL987654321'),
(3, 3, '2025-05-08 12:00:00', 898.00, 'Processing', '12 Nehru Nagar, Beside ICICI Bank, Mumbai 400025', '12 Nehru Nagar, Beside ICIC Bank, Mumbai 400025', 'COD', NULL, 'DEL456789123');

-- Insert order items
INSERT INTO `orderitems` (`order_item_id`, `order_id`, `book_id`, `quantity`, `unit_price`) VALUES
(1, 1, 1, 1, 399.00),
(2, 1, 8, 1, 225.00),
(3, 1, 13, 1, 175.00),
(4, 2, 1, 1, 399.00),
(5, 3, 4, 1, 450.00),
(6, 3, 11, 1, 250.00),
(7, 3, 14, 1, 225.00);

-- Insert payments
INSERT INTO `payments` (`payment_id`, `order_id`, `amount`, `payment_date`, `payment_method`, `transaction_id`, `status`) VALUES
(1, 1, 648.00, '2025-05-08 10:30:00', 'COD', 'COD123456', 'Completed'),
(2, 2, 399.00, '2025-05-08 11:15:00', 'COD', 'COD789012', 'Completed'),
(3, 3, 898.00, '2025-05-08 12:00:00', 'COD', 'COD345678', 'Pending');

-- Insert wishlist items
INSERT INTO `wishlists` (`wishlist_id`, `user_id`, `book_id`, `added_date`) VALUES
(1, 1, 3, '2025-05-08 10:05:00'),
(2, 1, 9, '2025-05-08 10:05:00'),
(3, 2, 5, '2025-05-08 10:10:00'),
(4, 3, 14, '2025-05-08 10:15:00');