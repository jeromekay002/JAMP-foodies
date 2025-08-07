-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 07, 2025 at 11:13 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jampfoodies`
--

-- --------------------------------------------------------

--
-- Table structure for table `cartitems`
--

CREATE TABLE `cartitems` (
  `cart_item_id` int(11) NOT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `food_id` int(11) DEFAULT NULL,
  `food_name` varchar(100) DEFAULT NULL,
  `food_image` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) DEFAULT NULL,
  `category_image` varchar(100) DEFAULT NULL,
  `description` text NOT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`, `category_image`, `description`, `status`) VALUES
(1, 'Burgers', '1754590626_Dynamic Double Burger Close-up.png', ' Juicy, flavorful, and freshly made.', 'Active'),
(2, 'Fries', '1754590492_Golden Crispy Fries.png', 'Crispy, golden, and perfectly salted', 'Active'),
(3, 'Drinks', '1754590541_Spectrum of Refreshing Cocktails.png', 'Refreshing sips for every taste.', 'Active'),
(4, 'Pizza', '1754590584_Gourmet Rainbow Veggie Pizza Delight.png', 'Hot, cheesy, and irresistibly delicious.', 'Active'),
(5, 'Desserts', '1754594623_Lavish Purple Frosted Pastries Display.png', 'Sweet treats to end your meal perfectly.', 'Active'),
(6, 'Chicken ', '1754590793_Close-up of Fried Chicken with Sauce.png', 'Crispy, golden, and perfectly salted', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone_number` varchar(100) DEFAULT NULL,
  `delivery_address` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `user_id`, `full_name`, `email`, `phone_number`, `delivery_address`) VALUES
(1, 1, 'Jerome Kaloki', 'jeromekaloki@gmail.com', '0790656804', 'Nairobi, Kenya');

-- --------------------------------------------------------

--
-- Table structure for table `food`
--

CREATE TABLE `food` (
  `food_id` int(11) NOT NULL,
  `food_name` varchar(100) DEFAULT NULL,
  `product_category` varchar(100) DEFAULT NULL,
  `food_image` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food`
--

INSERT INTO `food` (`food_id`, `food_name`, `product_category`, `food_image`, `price`, `quantity`, `description`, `status`) VALUES
(3, 'Cocacola', 'Drinks', '1754585807_Food_18.jpeg', 100.00, 30, 'Pure Niceness', 'In Stock'),
(6, 'Pizza Special', 'Pizza', '1754589780_Grilled Chicken and Pineapple Gourmet Pizza.png', 120.00, 2, 'Hot, cheesy, and irresistibly delicious.', 'In Stock'),
(7, 'Burger King', 'Burgers', '1754589867_Gourmet Hamburger on Red Background.png', 500.00, 10, 'Hot, cheesy, and irresistibly delicious.', 'In Stock'),
(8, 'Refreshing drink ', 'Drinks', '1754589970_Refreshing Strawberry Drink at an Upscale Bar.png', 600.00, 3, ' Juicy, flavorful, and freshly made.', 'In Stock'),
(9, 'Cake Pisces', 'Desserts', '1754590094_Lavish Purple Frosted Pastries Display.png', 1000.00, 8, 'delicious ', 'In Stock'),
(10, 'Pizza Slice Special', 'Pizza', '1754590230_Pizza Slice on Plate.png', 200.00, 8, 'Hot, cheesy, and irresistibly delicious.', 'In Stock'),
(11, 'Fries + 1 Burger', 'Fries', '1754590410_Classic American Diner Scene with Burgers.png', 650.00, 10, 'Crispy, golden, and perfectly salted ', 'In Stock'),
(12, 'chicken stick rolls', 'Chicken ', '1754590880_Glossy Skewered Fried Foods.png', 900.00, 12, ' Juicy, flavorful, and freshly made.', 'In Stock'),
(13, 'Bucket of chicken wings ', 'Chicken ', '1754591439_Delicious Fried Chicken Basket.png', 350.00, 7, 'chicken wings + fries  ', 'In Stock');

-- --------------------------------------------------------

--
-- Table structure for table `orderitems`
--

CREATE TABLE `orderitems` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `food_id` int(11) DEFAULT NULL,
  `food_name` varchar(100) DEFAULT NULL,
  `food_image` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderitems`
--

INSERT INTO `orderitems` (`order_item_id`, `order_id`, `food_id`, `food_name`, `food_image`, `quantity`, `price`, `total_price`) VALUES
(1, 3, 3, 'Cocacola', '1754585807_Food_18.jpeg', 2, 100.00, 200.00),
(2, 3, 6, 'Pizza Special', '1754589780_Grilled Chicken and Pineapple Gourmet Pizza.png', 1, 120.00, 120.00),
(3, 4, 3, 'Cocacola', '1754585807_Food_18.jpeg', 2, 100.00, 200.00),
(4, 4, 6, 'Pizza Special', '1754589780_Grilled Chicken and Pineapple Gourmet Pizza.png', 1, 120.00, 120.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `phone_number` varchar(12) DEFAULT NULL,
  `delivery_address` varchar(255) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `payment_status` varchar(100) DEFAULT NULL,
  `payment_method` enum('Mpesa','Cash') DEFAULT NULL,
  `order_status` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `phone_number`, `delivery_address`, `total_amount`, `payment_status`, `payment_method`, `order_status`, `created_at`, `updated_at`) VALUES
(3, 1, '0790656804', 'Nairobi, Kenya', 320.00, 'Pending', 'Mpesa', 'Received', '2025-08-07 22:46:40', '2025-08-08 05:46:40');

-- --------------------------------------------------------

--
-- Table structure for table `shoppingcart`
--

CREATE TABLE `shoppingcart` (
  `cart_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shoppingcart`
--

INSERT INTO `shoppingcart` (`cart_id`, `customer_id`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `role`, `password`, `created_at`) VALUES
(1, 'Jerome Kaloki', 'jeromekaloki@gmail.com', 'Customer', '$2y$10$yYBFDHtbvEHi/7ZzSva63eK8B5YxsPb7S1SYUoPxqlSHCF5JD9b9y', '2025-08-07 18:58:56'),
(2, 'Mary ', 'mary@gmail.com', 'admin', '$2y$10$/YbYQCLzvDDue/L0E/VsK./1ZNzajfHQk36X3Yqy8rzexcObD4TeC', '2025-08-07 19:14:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cartitems`
--
ALTER TABLE `cartitems`
  ADD PRIMARY KEY (`cart_item_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `food`
--
ALTER TABLE `food`
  ADD PRIMARY KEY (`food_id`);

--
-- Indexes for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD PRIMARY KEY (`order_item_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `shoppingcart`
--
ALTER TABLE `shoppingcart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cartitems`
--
ALTER TABLE `cartitems`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `food`
--
ALTER TABLE `food`
  MODIFY `food_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `orderitems`
--
ALTER TABLE `orderitems`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `shoppingcart`
--
ALTER TABLE `shoppingcart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
