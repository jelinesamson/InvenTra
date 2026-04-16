-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2026 at 10:24 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `edutrackdb`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_product` (IN `p_code` VARCHAR(50), IN `p_product_type` VARCHAR(50), IN `p_size` VARCHAR(20), IN `p_department` VARCHAR(50), IN `p_quantity` INT, IN `p_incoming_qty` INT, IN `p_price` DOUBLE, IN `p_status` VARCHAR(20), IN `p_account_id` INT)   BEGIN
    DECLARE v_product_id INT;

    -- Insert product
    INSERT INTO products 
    (product_code, product_type, size, department, quantity, incoming_qty, price, status) 
    VALUES 
    (p_code, p_product_type, p_size, p_department, p_quantity, p_incoming_qty, p_price, p_status);

    -- Get last inserted ID
    SET v_product_id = LAST_INSERT_ID();

    -- Call logJournal procedure (dapat meron ka nito)
    CALL logJournal(
        v_product_id,
        p_incoming_qty,
        0,
        'Add',
        p_quantity,
        p_account_id
    );

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_product` (IN `p_code` VARCHAR(50), IN `p_account_id` INT)   BEGIN
    DECLARE v_product_id INT;
    DECLARE v_quantity INT;

    -- get product
    SELECT product_id, quantity 
    INTO v_product_id, v_quantity
    FROM products
    WHERE product_code = p_code;

    IF v_product_id IS NOT NULL THEN

        -- soft delete
        UPDATE products 
        SET is_deleted = 1
        WHERE product_id = v_product_id;

        -- log
        CALL logJournal(v_product_id, 0, 0, 'Delete', v_quantity, p_account_id);

    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `logJournal` (IN `p_product_id` INT, IN `p_incoming` INT, IN `p_sales` INT, IN `p_notes` VARCHAR(255), IN `p_qty` INT, IN `p_account_id` INT)   BEGIN

    INSERT INTO product_journal
    (product_id, incoming_quantity, sales, notes, journal_qty, account_id, date_time)
    VALUES
    (p_product_id, p_incoming, p_sales, p_notes, p_qty, p_account_id, NOW());

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `receive_product` (IN `p_code` VARCHAR(50), IN `p_account_id` INT)   BEGIN
    DECLARE v_product_id INT;
    DECLARE v_qty INT;
    DECLARE v_incoming INT;
    DECLARE v_new_qty INT;

    -- get product
    SELECT product_id, quantity, incoming_qty
    INTO v_product_id, v_qty, v_incoming
    FROM products
    WHERE product_code = p_code;

    IF v_product_id IS NOT NULL THEN

        IF v_incoming > 0 THEN

            SET v_new_qty = v_qty + v_incoming;

            UPDATE products
            SET quantity = v_new_qty,
                incoming_qty = 0,
                status = 'Successfully'
            WHERE product_id = v_product_id;

            CALL logJournal(v_product_id, 0, 0, 'Receive', v_new_qty, p_account_id);

        END IF;

    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_product` (IN `p_code` VARCHAR(50), IN `p_product_type` VARCHAR(50), IN `p_size` VARCHAR(20), IN `p_department` VARCHAR(50), IN `p_incoming_qty` INT, IN `p_price` DOUBLE, IN `p_account_id` INT)   BEGIN
    DECLARE v_product_id INT;
    DECLARE v_quantity INT;
    DECLARE v_status VARCHAR(20);

    SELECT product_id, quantity 
    INTO v_product_id, v_quantity
    FROM products
    WHERE product_code = p_code;

    IF v_product_id IS NOT NULL THEN

        SET v_status = IF(p_incoming_qty > 0, 'On the Way', 'Successfully');

        UPDATE products
        SET product_type = p_product_type,
            size = p_size,
            department = p_department,
            incoming_qty = p_incoming_qty,
            price = p_price,
            status = v_status
        WHERE product_id = v_product_id;

    END IF;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `account_id` int(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(250) NOT NULL,
  `role` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`account_id`, `firstName`, `lastName`, `email`, `password`, `role`, `status`) VALUES
(3, 'Jeline', 'Buensuceso', 'admin@gmail.com', '$2y$10$k3sp/k0f.X5KZAPaM/59wuAT.ildMHwNTYDm93/TANngOmPeLWixG', 'admin', 'approved'),
(4, 'Eugene', 'Agulto', 'eugene@gmail.com', '$2y$10$RNvHGFVIG3IIkahQiwUvJuDte4APhmiicbTFw93aXb7IJ9n9QPLdK', 'cashier', 'approved'),
(12, 'althea', 'cruz', 'althea@gmail.com', '$2y$10$Yvw/MakXYIuUUR0xZCCyuew.Uy7FzLHLcMlQ7ivORscO/K1bYmFhO', 'cashier', 'approved'),
(13, 'Calista', 'Ferrer', 'cali@gmail.com', '$2y$10$YGKjY4BPshW14BX5feSJw.H/LLY9zlK3LoSegqxsVvqYJXV74KXCu', '', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_code` varchar(50) NOT NULL,
  `product_type` varchar(50) NOT NULL,
  `size` varchar(50) NOT NULL,
  `department` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `incoming_qty` int(11) NOT NULL,
  `price` double NOT NULL,
  `status` varchar(50) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_code`, `product_type`, `size`, `department`, `quantity`, `incoming_qty`, `price`, `status`, `is_deleted`) VALUES
(20, 'UNI001', 'Uniform', 'Small', 'CICT', 12, 0, 450, 'active', 0),
(21, 'B001', 'Book', 'None', 'CBEA', 16, 0, 350, 'active', 0),
(31, 'L001', 'ID Lace', 'None', 'COE', 0, 10, 250, 'active', 0),
(39, 'UNI002', 'Uniform', 'Medium', 'COED', 9, 2, 400, 'active', 0),
(41, 'M001', 'Merchandise', 'Large', 'CICT', 0, 15, 500, 'active', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_journal`
--

CREATE TABLE `product_journal` (
  `journal_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `incoming_quantity` int(11) DEFAULT 0,
  `sales` int(11) DEFAULT 0,
  `notes` varchar(50) NOT NULL,
  `journal_qty` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `date_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_journal`
--

INSERT INTO `product_journal` (`journal_id`, `product_id`, `incoming_quantity`, `sales`, `notes`, `journal_qty`, `account_id`, `date_time`) VALUES
(53, 20, 10, 0, 'Add', 0, 3, '2026-04-02 06:31:35'),
(54, 20, 0, 0, 'Receive', 10, 3, '2026-04-02 06:31:55'),
(55, 20, 5, 0, 'Edit', 10, 3, '2026-04-02 06:32:13'),
(56, 21, 20, 0, 'Add', 0, 3, '2026-04-02 06:33:00'),
(59, 21, 0, 0, 'Receive', 20, 3, '2026-04-02 06:35:07'),
(60, 21, 5, 0, 'Edit', 20, 3, '2026-04-02 06:35:16'),
(61, 20, 5, 1, 'Sale TXN-3936A5D', 9, 3, '2026-04-02 06:35:37'),
(62, 21, 5, 2, 'Sale TXN-4E9FC15', 18, 3, '2026-04-02 06:35:58'),
(63, 20, 5, 1, 'Sale TXN-073D971', 8, 3, '2026-04-02 07:00:23'),
(64, 21, 5, 1, 'Sale TXN-1AC8895', 17, 3, '2026-04-02 07:00:42'),
(65, 20, 5, 1, 'Sale TXN-1AC8895', 7, 3, '2026-04-02 07:00:42'),
(66, 21, 5, 1, 'Sale TXN-8A64232', 16, 3, '2026-04-02 07:02:34'),
(67, 20, 5, 1, 'Sale TXN-92756E0', 6, 3, '2026-04-02 07:02:42'),
(68, 21, 5, 1, 'Sale TXN-92756E0', 15, 3, '2026-04-02 07:02:42'),
(69, 20, 5, 1, 'Sale TXN-F528D7D', 5, 3, '2026-04-05 05:45:25'),
(70, 21, 5, 1, 'Sale TXN-F528D7D', 14, 3, '2026-04-05 05:45:25'),
(71, 20, 0, 0, 'Receive', 10, 3, '2026-04-05 05:47:15'),
(72, 21, 0, 0, 'Receive', 19, 3, '2026-04-05 05:47:20'),
(73, 20, 0, 1, 'Sale TXN-90072B6', 9, 3, '2026-04-05 05:48:00'),
(74, 20, 0, 1, 'Sale TXN-3026E5D', 8, 4, '2026-04-05 07:58:40'),
(75, 20, 10, 0, 'Edit', 8, 3, '2026-04-05 14:06:09'),
(76, 20, 2, 0, 'Edit', 8, 3, '2026-04-05 16:56:24'),
(77, 20, 2, 0, 'Edit', 8, 3, '2026-04-05 16:56:26'),
(78, 21, 1, 0, 'Edit', 19, 3, '2026-04-05 17:02:58'),
(79, 21, 0, 0, 'Receive', 20, 3, '2026-04-06 01:14:13'),
(80, 20, 0, 0, 'Receive', 10, 3, '2026-04-06 01:46:53'),
(81, 20, 5, 0, 'Edit', 10, 3, '2026-04-06 01:47:07'),
(82, 20, 0, 0, 'Edit', 10, 3, '2026-04-06 03:08:18'),
(83, 20, 5, 0, 'Edit', 10, 3, '2026-04-06 03:08:56'),
(84, 20, 5, 1, 'Sale TXN-70595B3', 9, 12, '2026-04-06 07:40:32'),
(103, 31, 10, 0, 'Add', 0, 3, '2026-04-10 02:23:57'),
(104, 31, 0, 0, 'Receive', 10, 3, '2026-04-10 02:24:59'),
(105, 31, 10, 0, 'Edit', 10, 3, '2026-04-10 02:25:22'),
(106, 31, 10, 1, 'Sale TXN-28B49B8', 9, 3, '2026-04-10 02:27:52'),
(107, 31, 10, 1, 'Sale TXN-5C00315', 8, 12, '2026-04-10 02:37:16'),
(108, 21, 0, 1, 'Sale TXN-5C00315', 19, 12, '2026-04-10 02:37:16'),
(109, 20, 5, 1, 'Sale TXN-5C00315', 8, 12, '2026-04-10 02:37:16'),
(110, 21, 0, 2, 'Sale TXN-AE29FB1', 17, 12, '2026-04-10 06:03:26'),
(111, 20, 0, 0, 'Receive', 13, 3, '2026-04-10 07:53:26'),
(114, 20, 0, 1, 'Sale TXN-44E92FC', 12, 12, '2026-04-10 08:01:08'),
(115, 31, 10, 8, 'Sale TXN-5358D59', 0, 12, '2026-04-10 08:01:23'),
(127, 21, 0, 1, 'Sale TXN-93EF6F5', 16, 3, '2026-04-10 23:24:04'),
(128, 20, 0, 0, 'Edit', 12, 3, '2026-04-11 15:00:19'),
(129, 21, 0, 0, 'Edit', 16, 3, '2026-04-11 15:00:27'),
(130, 31, 0, 0, 'Edit', 0, 3, '2026-04-11 15:00:33'),
(131, 39, 0, 0, 'Add', 10, 3, '2026-04-11 15:02:57'),
(132, 39, 0, 0, 'Edit', 10, 3, '2026-04-11 23:40:28'),
(133, 39, 0, 0, 'Edit', 7, 3, '2026-04-12 00:12:05'),
(134, 39, 5, 0, 'Incoming Edit', 7, 3, '2026-04-12 00:13:00'),
(135, 39, 0, 0, 'Receive', 12, 3, '2026-04-12 00:13:21'),
(136, 39, 0, 0, 'Edit', 11, 3, '2026-04-12 00:15:18'),
(137, 39, 2, 0, 'Incoming Edit', 11, 3, '2026-04-12 00:15:41'),
(138, 39, 0, 0, 'Receive', 13, 3, '2026-04-12 00:15:53'),
(139, 39, 0, 0, 'Edit', 13, 3, '2026-04-12 00:18:04'),
(140, 39, 5, 0, 'Incoming Add', 13, 3, '2026-04-12 00:25:23'),
(141, 39, 2, 0, 'Incoming Add', 13, 3, '2026-04-12 00:27:04'),
(142, 39, 0, 0, 'Delete', 13, 3, '2026-04-12 00:32:27'),
(143, 39, 0, 0, 'Restore', 13, 3, '2026-04-12 00:32:50'),
(144, 39, 0, 0, 'Delete', 13, 3, '2026-04-12 00:33:15'),
(145, 39, 0, 0, 'Restore', 13, 3, '2026-04-12 00:34:59'),
(146, 39, 0, 0, 'Edit', 13, 3, '2026-04-12 00:36:57'),
(147, 39, 0, 0, 'Delete', 13, 3, '2026-04-13 01:29:18'),
(148, 39, 0, 0, 'Restore', 13, 3, '2026-04-13 01:46:35'),
(149, 39, 0, 0, 'Edit', 13, 3, '2026-04-13 01:59:48'),
(154, 39, 0, 0, 'Edit', 0, 3, '2026-04-15 12:09:43'),
(155, 39, 0, 0, 'Delete', 0, 3, '2026-04-15 12:09:50'),
(156, 39, 0, 0, 'Restore', 0, 3, '2026-04-15 12:09:56'),
(157, 39, 10, 0, 'Incoming Edit', 0, 3, '2026-04-15 12:10:15'),
(158, 39, 0, 0, 'Receive', 10, 3, '2026-04-15 12:10:22'),
(159, 39, 2, 0, 'Incoming Add', 10, 3, '2026-04-15 12:10:35'),
(160, 39, 2, 1, 'Sale TXN-488A240', 9, 3, '2026-04-15 12:10:48'),
(161, 41, 0, 0, 'Add', 0, 3, '2026-04-16 06:52:36'),
(162, 41, 0, 0, 'Edit', 0, 3, '2026-04-16 06:52:47'),
(163, 41, 0, 0, 'Edit', 0, 3, '2026-04-16 06:53:38'),
(164, 41, 10, 0, 'Incoming Edit', 0, 3, '2026-04-16 06:54:00'),
(165, 41, 5, 0, 'Incoming Add', 0, 3, '2026-04-16 06:54:14'),
(166, 41, 0, 0, 'Delete', 0, 3, '2026-04-16 06:56:11'),
(167, 41, 0, 0, 'Restore', 0, 3, '2026-04-16 06:56:18'),
(168, 41, 0, 0, 'Delete', 0, 3, '2026-04-16 06:56:36'),
(169, 41, 0, 0, 'Restore', 0, 3, '2026-04-16 06:56:42');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` varchar(20) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `paid` decimal(10,2) NOT NULL,
  `change_amount` decimal(10,2) NOT NULL,
  `vat` decimal(10,2) NOT NULL,
  `status` varchar(20) DEFAULT 'Completed',
  `account_id` int(11) DEFAULT NULL,
  `date_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `total`, `paid`, `change_amount`, `vat`, `status`, `account_id`, `date_time`) VALUES
('TXN-073D971', 450.00, 450.00, 0.00, 54.00, 'completed', 3, '2026-04-02 15:00:23'),
('TXN-1AC8895', 800.00, 800.00, 0.00, 96.00, 'completed', 3, '2026-04-02 15:00:42'),
('TXN-28B49B8', 250.00, 250.00, 0.00, 30.00, 'completed', 3, '2026-04-10 10:27:52'),
('TXN-3026E5D', 450.00, 450.00, 0.00, 54.00, 'completed', 4, '2026-04-05 15:58:40'),
('TXN-3936A5D', 450.00, 500.00, 50.00, 54.00, 'completed', 3, '2026-04-02 14:35:37'),
('TXN-44E92FC', 450.00, 450.00, 0.00, 54.00, 'completed', 12, '2026-04-10 16:01:08'),
('TXN-488A240', 400.00, 400.00, 0.00, 48.00, 'completed', 3, '2026-04-15 20:10:48'),
('TXN-4E9FC15', 700.00, 1000.00, 300.00, 84.00, 'completed', 3, '2026-04-02 14:35:58'),
('TXN-5358D59', 2000.00, 2000.00, 0.00, 240.00, 'completed', 12, '2026-04-10 16:01:23'),
('TXN-5C00315', 1050.00, 1500.00, 450.00, 126.00, 'completed', 12, '2026-04-10 10:37:16'),
('TXN-70595B3', 450.00, 500.00, 50.00, 54.00, 'completed', 12, '2026-04-06 15:40:32'),
('TXN-8A64232', 350.00, 350.00, 0.00, 42.00, 'completed', 3, '2026-04-02 15:02:34'),
('TXN-90072B6', 450.00, 450.00, 0.00, 54.00, 'completed', 3, '2026-04-05 13:48:00'),
('TXN-92756E0', 800.00, 900.00, 100.00, 96.00, 'completed', 3, '2026-04-02 15:02:42'),
('TXN-93EF6F5', 350.00, 350.00, 0.00, 42.00, 'completed', 3, '2026-04-11 07:24:03'),
('TXN-AE29FB1', 700.00, 700.11, 0.11, 84.00, 'completed', 12, '2026-04-10 14:03:26'),
('TXN-D852E32', 120.00, 120.00, 0.00, 14.40, 'completed', 3, '2026-04-10 22:10:32'),
('TXN-F528D7D', 800.00, 1000.00, 200.00, 96.00, 'completed', 3, '2026-04-05 13:45:25');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_items`
--

CREATE TABLE `transaction_items` (
  `item_id` int(11) NOT NULL,
  `transaction_id` varchar(20) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_items`
--

INSERT INTO `transaction_items` (`item_id`, `transaction_id`, `product_id`, `product_name`, `category`, `qty`, `unit_price`, `total_price`) VALUES
(11, 'TXN-3936A5D', 20, 'UNI001', 'Uniform', 1, 450.00, 450.00),
(12, 'TXN-4E9FC15', 21, 'B001', 'Book', 2, 350.00, 700.00),
(13, 'TXN-073D971', 20, 'UNI001', 'Uniform', 1, 450.00, 450.00),
(14, 'TXN-1AC8895', 21, 'B001', 'Book', 1, 350.00, 350.00),
(15, 'TXN-1AC8895', 20, 'UNI001', 'Uniform', 1, 450.00, 450.00),
(16, 'TXN-8A64232', 21, 'B001', 'Book', 1, 350.00, 350.00),
(17, 'TXN-92756E0', 20, 'UNI001', 'Uniform', 1, 450.00, 450.00),
(18, 'TXN-92756E0', 21, 'B001', 'Book', 1, 350.00, 350.00),
(19, 'TXN-F528D7D', 20, 'UNI001', 'Uniform', 1, 450.00, 450.00),
(20, 'TXN-F528D7D', 21, 'B001', 'Book', 1, 350.00, 350.00),
(21, 'TXN-90072B6', 20, 'UNI001', 'Uniform', 1, 450.00, 450.00),
(22, 'TXN-3026E5D', 20, 'UNI001', 'Uniform', 1, 450.00, 450.00),
(23, 'TXN-70595B3', 20, 'UNI001', 'Uniform', 1, 450.00, 450.00),
(24, 'TXN-28B49B8', 31, 'L001', 'ID Lace', 1, 250.00, 250.00),
(25, 'TXN-5C00315', 31, 'L001', 'ID Lace', 1, 250.00, 250.00),
(26, 'TXN-5C00315', 21, 'B001', 'Book', 1, 350.00, 350.00),
(27, 'TXN-5C00315', 20, 'UNI001', 'Uniform', 1, 450.00, 450.00),
(28, 'TXN-AE29FB1', 21, 'B001', 'Book', 2, 350.00, 700.00),
(29, 'TXN-44E92FC', 20, 'UNI001', 'Uniform', 1, 450.00, 450.00),
(30, 'TXN-5358D59', 31, 'L001', 'ID Lace', 8, 250.00, 2000.00),
(32, 'TXN-93EF6F5', 21, 'B001', 'Book', 1, 350.00, 350.00),
(33, 'TXN-488A240', 39, 'UNI002', 'Uniform', 1, 400.00, 400.00);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_products`
-- (See below for the actual view)
--
CREATE TABLE `v_products` (
`product_id` int(11)
,`product_code` varchar(50)
,`product_type` varchar(50)
,`size` varchar(50)
,`department` varchar(50)
,`quantity` int(11)
,`incoming_qty` int(11)
,`price` double
,`status` varchar(50)
);

-- --------------------------------------------------------

--
-- Structure for view `v_products`
--
DROP TABLE IF EXISTS `v_products`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_products`  AS SELECT `products`.`product_id` AS `product_id`, `products`.`product_code` AS `product_code`, `products`.`product_type` AS `product_type`, `products`.`size` AS `size`, `products`.`department` AS `department`, `products`.`quantity` AS `quantity`, `products`.`incoming_qty` AS `incoming_qty`, `products`.`price` AS `price`, `products`.`status` AS `status` FROM `products` WHERE `products`.`is_deleted` = 0 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD UNIQUE KEY `id` (`account_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_journal`
--
ALTER TABLE `product_journal`
  ADD PRIMARY KEY (`journal_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_product_journal_account` (`account_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`);

--
-- Indexes for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `product_journal`
--
ALTER TABLE `product_journal`
  MODIFY `journal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_journal`
--
ALTER TABLE `product_journal`
  ADD CONSTRAINT `fk_product_journal_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_journal_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`),
  ADD CONSTRAINT `transaction_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
