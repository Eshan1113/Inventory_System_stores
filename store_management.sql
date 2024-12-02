-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 06, 2025 at 03:14 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `store_management`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `check_user_permission` (IN `p_user_id` INT, IN `p_permission_name` VARCHAR(50), OUT `p_has_permission` BOOLEAN)   BEGIN
    SELECT EXISTS(
        SELECT 1
        FROM users u
        JOIN role_permissions rp ON u.role = rp.role
        JOIN user_permissions up ON rp.permission_id = up.permission_id
        WHERE u.user_id = p_user_id
        AND up.permission_name = p_permission_name
        AND u.status = 'active'
    ) INTO p_has_permission;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` int NOT NULL,
  `employee_code` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `group_id` int DEFAULT NULL,
  `location_id` int DEFAULT NULL,
  `contact_info` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `employee_code`, `full_name`, `group_id`, `location_id`, `contact_info`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'EMP001', 'Nimal Rathnayake', 1, 1, 'Nimal.Rathnayake@email.com', 'active', 5, 5, '2024-11-12 05:50:33', '2024-11-27 06:21:17'),
(2, 'EMP002', 'Uditha Kumarasinghhe', 2, 1, 'UdithaKumarasinghhe@email.com', 'active', 2, 2, '2024-11-12 05:50:33', '2024-11-19 07:42:42'),
(5, 'employee_code', 'full_name', 0, 0, 'contact_info', '', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(6, 'EMP004', 'Banda J.M.N.', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(7, 'EMP005', 'Gamage I.G.N.S.', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(8, 'EMP006', 'Sanath W.P.K.', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(9, 'EMP007', 'Ranasingha A.G.', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(10, 'EMP008', 'Wickramasingha A.G.C.S.', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(11, 'EMP009', 'Sanath D.M.D.', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(12, 'EMP010', 'Ravi V.', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(13, 'EMP011', 'Krishnamoorthi P.', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(14, 'EMP012', 'Prabakaran N.', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(15, 'EMP013', 'Nanayakkara N.S.', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(16, 'EMP014', 'Bandara K.M.P.', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(17, 'EMP015', 'Gobiraj S.', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(18, 'EMP016', 'Karthigesu S.', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(19, 'EMP017', 'Manamendra M.S.R.', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(20, 'EMP018', 'Kumara U.G.U.D.', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(21, 'EMP019', 'Kalapuge K.D.S.', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(22, 'EMP020', 'Senadeera R.W.K.G.P.', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(23, 'EMP021', 'Bandara M.M.W.', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(24, 'EMP022', 'Wickramasingha P.S.K.', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(25, 'EMP023', 'Wijesuriya B.G.A.', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(26, 'EMP024', 'Kotandeniya W.Y.M.S.B.', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(27, 'EMP025', 'Sujeewa M.M.S.', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(28, 'EMP026', 'Mahendran G.', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(29, 'EMP027', 'De Silva J.W.J.', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(30, 'EMP028', 'Suppaiya P.', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(31, 'EMP029', 'Rathnayaka R.M.I.K.K.', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(32, 'EMP030', 'Sudakaran W.', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(33, 'EMP031', 'Nissanka N.M.D.M.', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(34, 'EMP032', 'Rathnayaka R.M.M.D.', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(35, 'EMP033', 'Jayawardhana R.A.N.C.', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(36, 'EMP034', 'Wijekumar S.', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(37, 'EMP035', 'Jayatissa B.G.S.P.', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(38, 'EMP036', 'Vigneshwaran S.', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(39, 'EMP037', 'Siriwardhana K.G.T.H.', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(40, 'EMP038', 'Nagulan M.', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(41, 'EMP039', 'Senavirathna S.M.N.', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(42, 'EMP040', 'Thangaraja P.', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(43, 'EMP041', 'Sirisena M.M.', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(44, 'EMP042', 'Rajapaksha R.B.A.', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(45, 'EMP043', 'Senarathna R.L.W.K.', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(46, 'EMP044', 'Muththurajah P.', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(47, 'EMP045', 'Nissanka R.G.', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(48, 'EMP046', 'Jeganadan R.', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(49, 'EMP047', 'Senavirathna W.M.', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(50, 'EMP048', 'Kandaiya P.', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(51, 'EMP049', 'Jayathilaka J.M.A.N.', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(52, 'EMP050', 'Wimalarathna M.G.', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(53, 'EMP051', 'Premakeerthi W.W.K.N.', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(54, 'EMP052', 'Ariyawansha E.G.G.H.', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(55, 'EMP053', 'Alwis W.H.S.', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(56, 'EMP054', 'Wickramasinghe G.G.G.H.R.', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(57, 'EMP055', 'Wickramasinghe G.G.G.T.M.', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(58, 'EMP056', 'suranga', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(59, 'STAF001', 'R.M.K.D. Bandara', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(60, 'STAF002', 'R.M.A. Bandara', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(61, 'STAF003', 'M.M.Nadeer', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(62, 'STAF004', 'O.K.H.S. Kumara', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(63, 'STAF005', 'K.P.P. Ranasingha', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(64, 'STAF006', 'U.I.W. Adhikari ', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(65, 'STAF007', 'S.A.M.E.Samaraweera', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(66, 'STAF008', 'T.P.I.C. Perera', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(67, 'STAF009', 'W.D.P.A.Weerasingha', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(68, 'STAF010', 'W.G.H.Premathilaka ', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(69, 'STAF011', 'W.G.L.D.  Weerasinghe', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(70, 'STAF012', 'R.M.D.S.W.S.B. Rathnayake   ', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(71, 'STAF013', 'P.Gunarathna', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(72, 'STAF014', 'W.G.I.Wijayabandara ', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(73, 'STAF015', 'T.M.M.C.S.B. Gunarathna', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(74, 'STAF016', 'J.M.V.A.Ariyarathna ', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(75, 'STAF017', 'U. G. N. Thilakarathna ', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(76, 'STAF018', 'K. M..A.P. Krishnadasa ', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(77, 'STAF019', 'D.M. D.V.Rathnayake', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(78, 'STAF020', 'L C Thennakoon', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(79, 'STAF021', 'A.S.  Perera ', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(80, 'STAF022', 'U E R C Wijayaweera', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(81, 'WANI 01', 'Wanigasekara', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(82, 'WANI 02', 'Bandara Thennakoon T.M.Chaminda', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(83, 'WANI 03', 'Thilakaraj S.', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(84, 'WANI 04', 'Harichandra', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(85, 'WANI 05', 'Dananjaya', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(86, 'WANI 06', 'Weerasekara W.M.P.Piumal', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(87, 'WANI 07', 'Kasun Shanaka', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(88, 'WANI 08', 'Lahiru', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(89, 'WANI 09', 'Avishka', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(90, 'WANI 10', 'Dilshan J.M.Hasitha', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(91, 'WANI 11', 'Lakmal H.M.Tharidu', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(92, 'WANI 12', 'Jayasingha L.R.P.H.', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(93, 'WANI 13', 'Pramidu L.R.', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(94, 'WANI 14', 'Chandima', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(95, 'WANI 15', 'Siriwardhana', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(96, 'WANI 16', 'Gunawardhana C.U.', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(97, 'WANI 17', 'Rathnayaka S.', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(98, 'WANI 18', 'Gunasinhe Pradeep', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(99, 'WANI 19', 'Wimalarathna Sudesh', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(100, 'WANI 20', 'Kavishan E.M.Avishka', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(101, 'WANI 21', 'Perera K.Irosh', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(102, 'WANI 22', 'Gunasingha', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(103, 'WANI 23', 'Madushanka Suraj', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(104, 'WANI 24', 'Supun', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(105, 'WANI 25', 'Nirodha', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(106, 'WANI 26', 'Lakshan', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(107, 'WANI 27', 'Lakshitha', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(108, 'WANI 28', 'Hashan', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(109, 'WANI 29', 'Ranasingha D.B.', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(110, 'WANI 30', 'Bandara M.S.G.', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(111, 'RANJ 01', 'Ranjeewa W.A.', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(112, 'RANJ 02', 'Sanjaya W.A.Milan', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(113, 'RANJ 03', 'Gunarathna S.S.Vimala', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(114, 'RANJ 04', 'Rathnayaka Thiwanka', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(115, 'RANJ 05', 'Kumara I.P.G.Vijitha', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(116, 'RANJ 06', 'Madhuranga K.M.Dilan', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(117, 'RANJ 07', 'Kawshalya Supun', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(118, 'RANJ 08', 'Wijerathne Hashan Sandaruwan', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(119, 'RANJ 09', 'Kumara Ajith', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(120, 'RANJ 10', 'Kumara Udaya', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(121, 'RANJ 11', 'Rathnayaka Ravi', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(122, 'RANJ 12', 'Indrajith Saliya', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(123, 'RANJ 13', 'Ariyawansha Nipun', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(124, 'RANJ 14', 'Gunasinghe Pradeep', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(125, 'RANJ 15', 'Thennakoon Sampath', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(126, 'RANJ 16', 'Kulathunga Chamuditha Sankalpa', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(127, 'RANJ 17', 'Ekanayake Maneth Vidum', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(128, 'RANJ 18', 'Dimuthu', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(129, 'RANJ 19', 'Risadan', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(130, 'RANJ 20', 'Sulakshana Susith', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(131, 'RANJ 21', 'Madugalla', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(132, 'RANJ 22', 'Dilshan', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(133, 'RANJ 23', 'Naven', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(134, 'RANJ 24', 'Chathuranga Sidath', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(135, 'RANJ 25', 'Bandaranayaka', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(136, 'CHAM 01', 'Weerasingha A.G.C.Sanjeewa', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(137, 'CHAM 02', 'Dissanayaka Malidu', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(138, 'CHAM 03', 'Rathnayaka R.M.A.Saliya', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(139, 'CHAM 04', 'Dilshan K.W.G.Sasanka', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(140, 'CHAM 05', 'Krishanthan J.', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(141, 'CHAM 06', 'Chanaka Pradeep S.D.N.M.', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(142, 'CHAM 07', 'Ekanayaka E.M.', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(143, 'CHAM 08', 'Eranga', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(144, 'CHAM 09', 'Weerasinghe K.W.M.S.Prabath', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(145, 'CHAM 10', 'Maduwinda', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(146, 'CHAM 11', 'Sanjaya', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(147, 'CHAM 12', 'Chanaka', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(148, 'CHAM 13', 'Nihal', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(149, 'CHAM 14', 'Prasanna', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(150, 'CHAM 15', 'Darshana', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(151, 'CHAM 16', 'Loshan', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(152, 'CHAM 17', 'Dilshan N.', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(153, 'CHAM 18', 'Sampath', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(154, 'CHAM 19', 'Vikas', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(155, 'CHAM 20', 'Saman', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(156, 'CHAM 21', 'Alahakoon S.B.', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(157, 'CHAM 22', 'Kasun', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(158, 'CHAM 23', 'Ramesh', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(159, 'CHAM 24', 'Nimsara', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(160, 'CHAM 25', 'Rathnayaka', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(161, 'AMAR 01', 'Pushpakumara M.Amaranath', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(162, 'AMAR 02', 'Ananda W.S.', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(163, 'AMAR 03', 'Samarawickrama R.G.N.S.', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(164, 'AMAR 04', 'Sanjeewa', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(165, 'AMAR 05', 'Kumarathunga S.A.Randil', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(166, 'AMAR 06', 'Tharindu U.D.', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(167, 'AMAR 07', 'Niluka A.K.Dinidu', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(168, 'AMAR 08', 'Saththis S.', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(169, 'AMAR 09', 'Mohanaraj Punniyamurthi', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(170, 'AMAR 10', 'Khaleikumar Rukumar', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(171, 'AMAR 11', 'Dilshan Rajendran', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(172, 'AMAR 12', 'Prasana', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(173, 'RANG 01', 'Tawanation Rajendran', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(174, 'RANG 02', 'Sooriyabandara J.D.', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(175, 'RANG 03', 'Ravikumara Sivanadan', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(176, 'RANG 04', 'Thilak', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(177, 'RANG 05', 'Anandalal', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(178, 'RANG 06', 'Prasanna', 4, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(179, 'RANG 07', 'Rabukwella L.D.', 1, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(180, 'RANG 08', 'Manelge M.G.R.P.', 2, 1, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(181, 'RANG 09', 'Rathnayake R.M.Ruwan', 3, 2, '718032559', 'active', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `employee_groups`
--

CREATE TABLE `employee_groups` (
  `group_id` int NOT NULL,
  `group_name` varchar(50) NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `employee_groups`
--

INSERT INTO `employee_groups` (`group_id`, `group_name`, `description`) VALUES
(1, 'Maintenance', 'Maintenance and repair staff'),
(2, 'Operations', 'Operations team'),
(3, 'Engineering', 'Engineering team'),
(4, 'Supervisors', 'Supervisory staff'),
(5, 'Administration', 'Administrative staff'),
(6, 'Security', 'Security personnel'),
(7, 'Management', 'Management team'),
(8, 'Contr-1-Chamila', 'Chamila'),
(9, 'Contr-2-Ranjeewa', 'Ranjeewa'),
(10, 'Contr-3-Wanigasekara', 'Wanigasekara'),
(11, 'Contr-4-Paint-Ruwan', 'Paint-Ruwan'),
(12, 'Contr-5-Paint-Tikiri', 'Paint-Tikiri'),
(13, 'Contr-6-Sand Blast-Amare', 'Sand Blast-Amare');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int NOT NULL,
  `item_code` varchar(20) DEFAULT NULL,
  `local_item_code` varchar(10) DEFAULT NULL,
  `item_name` varchar(100) NOT NULL,
  `item_size` varchar(100) DEFAULT NULL,
  `specifications` text,
  `image_url` varchar(255) DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `location_id` int DEFAULT NULL,
  `quantity` int DEFAULT '0',
  `origin_country` varchar(50) DEFAULT NULL,
  `warranty_until` date DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_price` decimal(10,2) DEFAULT NULL,
  `status` enum('available','low_stock','out_of_stock') DEFAULT 'available',
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `low_stock_threshold` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `item_code`, `local_item_code`, `item_name`, `item_size`, `specifications`, `image_url`, `category_id`, `location_id`, `quantity`, `origin_country`, `warranty_until`, `purchase_date`, `purchase_price`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `low_stock_threshold`) VALUES
(1, 'ITEM000001', NULL, 'Power Drill', NULL, 'DeWalt 20V Max Cordless', '#', 1, 6, 2, 'USA', '2025-12-31', '2024-01-01', 299.99, 'available', 5, 2, '2024-11-12 05:50:33', '2025-09-06 07:06:51', 0),
(2, 'ITEM000002', NULL, 'Safety Helmet', NULL, 'Type 1 Class E', NULL, 2, 4, 15, 'Germany', '2026-12-31', '2024-01-01', 49.99, 'available', 3, 4, '2024-11-12 05:50:33', '2025-09-06 04:36:16', 0),
(3, 'ITEM000003', NULL, 'Measuring Tape', NULL, '25ft Stanley', '#', 1, 1, 15, 'China', '2025-06-30', '2024-01-01', 19.99, 'available', 3, 5, '2024-11-12 05:50:33', '2024-11-27 06:24:02', 0),
(9, 'ITEM000005', 'CD', 'CROWN DC inverter Welding Machine 250A', NULL, 'High Quality Brand New Inverter Welding Machine\r\nModel No: CT33100\r\nBrand: CROWN ( SWITZERLAND )\r\nType: Industrial/Domes tic\r\nSix Mon ths Company Warranty\r\nMade in China (CE,EMC)', 'upload/images.jpg', 1, 1, 2, 'sri lankan', '2025-03-29', '2024-11-21', 57750.00, 'available', 1, NULL, '2025-09-06 05:11:02', NULL, 1),
(25, 'ITEM000006', 'RW', 'RETOP Welding Machine 200A', NULL, 'Brand New High Quality Inverter Welding Machine\r\nModel No: RMMA200\r\nBrand: RETOP\r\nType: Domestic\r\nOne Year Company Warr anty\r\n\r\n', 'upload/61PIAB7d-JL.jpg', 1, 1, 2, 'china', '2025-04-26', '2024-11-21', 16850.00, 'available', 1, NULL, '2025-09-06 07:11:00', NULL, 1);

--
-- Triggers `items`
--
DELIMITER $$
CREATE TRIGGER `after_item_insert` AFTER INSERT ON `items` FOR EACH ROW BEGIN
    INSERT INTO user_activity_logs (user_id, activity_type, activity_description)
    VALUES (NEW.created_by, 'INSERT', CONCAT('Added new item: ', NEW.item_name));
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_item_update` AFTER UPDATE ON `items` FOR EACH ROW BEGIN
    INSERT INTO user_activity_logs (user_id, activity_type, activity_description)
    VALUES (
        NEW.updated_by, 
        'UPDATE', 
        CONCAT('Updated item: ', NEW.item_name, 
               ' Category: ', (SELECT category_name FROM item_categories WHERE category_id = NEW.category_id),
               ' Location: ', (SELECT location_name FROM locations WHERE location_id = NEW.location_id))
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_item_insert` BEFORE INSERT ON `items` FOR EACH ROW BEGIN
    SET NEW.item_code = CONCAT('ITEM', LPAD(COALESCE((
        SELECT MAX(CONVERT(SUBSTRING(item_code, 5), SIGNED)) + 1
        FROM items), 1), 6, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `item_categories`
--

CREATE TABLE `item_categories` (
  `category_id` int NOT NULL,
  `category_name` varchar(50) NOT NULL,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `item_categories`
--

INSERT INTO `item_categories` (`category_id`, `category_name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Tools', 'Hand and power tools', '2024-11-13 06:04:42', NULL),
(2, 'Safety Equipment', 'Personal protective equipment', '2024-11-13 06:04:42', NULL),
(3, 'Consumables', 'Items that are used up over time', '2024-11-13 06:04:42', NULL),
(4, 'Spare Parts', 'Replacement parts for equipment', '2024-11-13 06:04:42', NULL),
(5, 'Office Supplies', 'General office materials', '2024-11-13 06:04:42', NULL),
(6, 'Damaged', 'products that are broken, cracked, scratched are reentered into the system. make sure to discard item first from the system and to indicate  previous item code if re entered ,', '2025-09-05 17:58:32', '2025-09-05 18:53:17'),
(7, 'Defective', 'Items that have functional issues, such as not turning on, not charging, or not connecting properly.quote item code if reentered. make-sure to remove from system.', '2025-09-05 18:50:04', '2025-09-13 18:47:37'),
(8, 'Missing Parts', 'Items that are missing essential components or accessories.', '2025-09-05 18:52:12', '2025-09-07 18:50:14');

-- --------------------------------------------------------

--
-- Table structure for table `item_transactions`
--

CREATE TABLE `item_transactions` (
  `transaction_id` int NOT NULL,
  `item_id` int DEFAULT NULL,
  `employee_id` int DEFAULT NULL,
  `transaction_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `transaction_type` enum('issue','return','damage','lost','discard') NOT NULL,
  `quantity` int NOT NULL,
  `status` enum('completed','pending') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'pending',
  `remarks` text,
  `created_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `item_transactions`
--

INSERT INTO `item_transactions` (`transaction_id`, `item_id`, `employee_id`, `transaction_date`, `transaction_type`, `quantity`, `status`, `remarks`, `created_by`) VALUES
(3, 1, 1, '2024-11-12 07:42:16', 'return', 1, 'completed', 'Returned in good condition', 1),
(4, 2, 142, '2024-11-13 13:22:41', 'damage', 2, 'completed', 'TEST', 1),
(5, 2, 1, '2024-11-19 12:11:00', 'issue', 1, 'completed', 'e', 1),
(7, 1, 2, '2024-11-19 12:15:00', 'issue', 1, 'completed', 'f', 1),
(8, 1, 8, '2024-11-19 14:34:00', 'issue', 1, 'pending', '2', 1),
(9, 1, 13, '2024-11-20 14:53:00', 'issue', 1, 'pending', '', 1),
(10, 2, 8, '2024-11-21 08:54:00', 'issue', 1, 'pending', 'd', NULL),
(11, 1, 13, '2024-11-21 08:55:00', 'issue', 1, 'pending', 'gg', NULL),
(12, 1, 13, '2024-11-21 08:55:00', 'issue', 1, 'pending', 'gg', NULL),
(13, 1, 1, '2024-11-21 11:23:00', 'issue', 1, 'pending', 'f', 1),
(14, 1, 1, '2024-11-21 11:23:00', 'issue', 1, 'pending', 'f', 1),
(15, 1, 1, '2024-11-21 11:25:00', 'issue', 1, 'pending', '2', 1);

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `location_id` int NOT NULL,
  `location_name` varchar(100) NOT NULL,
  `address` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`location_id`, `location_name`, `address`) VALUES
(1, 'Main Warehouse Store', 'Pallekale IDB'),
(2, 'KTI', 'Pallekale'),
(3, 'Training School', 'Pallekale'),
(4, 'Personal', 'personal location address'),
(5, 'Workshop-01', 'Pallekale'),
(6, 'Workshop-02', 'Pallekale'),
(7, 'Workshop-03', 'pallekale'),
(8, 'Mini Sores', 'Mini Sores ');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role` enum('admin','user') NOT NULL,
  `permission_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role`, `permission_id`) VALUES
('admin', 1),
('admin', 2),
('admin', 3),
('admin', 4),
('user', 4),
('admin', 5),
('admin', 6),
('admin', 7),
('admin', 8),
('user', 8),
('admin', 9),
('admin', 10),
('user', 10),
('admin', 11),
('admin', 12),
('user', 12);

-- --------------------------------------------------------

--
-- Table structure for table `sub_locations`
--

CREATE TABLE `sub_locations` (
  `sublocation_id` int NOT NULL,
  `location_id` int DEFAULT NULL,
  `sublocation_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `sub_locations`
--

INSERT INTO `sub_locations` (`sublocation_id`, `location_id`, `sublocation_name`) VALUES
(1, 1, 'Wearhouse Office Tool Room '),
(2, 1, 'Main Tool issuing Room '),
(3, 2, 'KTI-Container'),
(4, 2, 'KTI-Workshop tool store'),
(5, 3, 'Supervisor Storage Area 1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `full_name`, `role`, `status`, `created_at`, `last_login`) VALUES
(1, 'admin', '$2y$10$a37//9hVBO8fin3f5QQAUuqG1H2UKy1WaDD40SFj2mthnE4WMI0se', 'admin@store.com', 'System Administrator', 'admin', 'active', '2024-11-12 05:56:13', '2025-09-06 03:14:17'),
(2, 'user', '$2y$10$a37//9hVBO8fin3f5QQAUuqG1H2UKy1WaDD40SFj2mthnE4WMI0se', 'user@store.com', 'Regular User', 'user', 'active', '2024-11-12 05:56:13', '2025-09-06 09:55:45'),
(3, 'Nadeer-Super_User', '079390a74c25c8ff86f87044ea8f98b800c107fa', 'nadeer@gmail.com', 'nadeer store', 'user', 'active', '2024-11-13 06:17:50', NULL),
(4, 'Jayantha-Super_User', 'b8393f5d9c94cb83c371338bb569417e0cc3307d', 'jayantha@gmail.com', 'jayantha qs', 'user', 'active', '2024-11-13 06:20:06', NULL),
(5, 'eshan-admin', '2fc7b7b293cc283c108bea69f64a63d8bc6d1ab2', 'eshan@gmail.com', 'Eshan-Super-Admin', 'admin', 'active', '2024-11-13 06:21:07', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_activity_logs`
--

CREATE TABLE `user_activity_logs` (
  `log_id` int NOT NULL,
  `user_id` int NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `activity_description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `activity_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_activity_logs`
--

INSERT INTO `user_activity_logs` (`log_id`, `user_id`, `activity_type`, `activity_description`, `ip_address`, `activity_timestamp`) VALUES
(1, 2, 'UPDATE', 'Updated item: Power Drill Category: Tools Location: Workshop-02', NULL, '2024-11-13 06:22:30'),
(2, 4, 'UPDATE', 'Updated item: Safety Helmet Category: Safety Equipment Location: Personal', NULL, '2024-11-13 06:23:42'),
(3, 5, 'UPDATE', 'Updated item: Measuring Tape Category: Tools Location: Main Warehouse Store', NULL, '2024-11-13 06:25:10'),
(4, 4, 'UPDATE', 'Updated item: Safety Helmet Category: Safety Equipment Location: Personal', NULL, '2024-11-13 07:52:41'),
(5, 1, 'LOGIN', 'User logged in', '192.168.1.132', '2024-11-19 06:00:32'),
(6, 1, 'LOGIN', 'User logged in', '192.168.1.132', '2024-11-19 06:07:08'),
(7, 4, 'UPDATE', 'Updated item: Safety Helmet Category: Safety Equipment Location: Personal', NULL, '2024-11-19 06:42:14'),
(8, 4, 'UPDATE', 'Updated item: Safety Helmet Category: Safety Equipment Location: Personal', NULL, '2024-11-19 06:42:14'),
(9, 4, 'UPDATE', 'Updated item: Safety Helmet Category: Safety Equipment Location: Personal', NULL, '2024-11-19 06:42:14'),
(10, 2, 'UPDATE', 'Updated item: Power Drill Category: Tools Location: Workshop-02', NULL, '2024-11-19 06:45:28'),
(11, 1, 'LOGIN', 'User logged in', '192.168.1.210', '2024-11-19 06:47:02'),
(12, 2, 'UPDATE', 'Updated item: Power Drill Category: Tools Location: Workshop-02', NULL, '2024-11-19 06:48:37'),
(13, 2, 'UPDATE', 'Updated item: Power Drill Category: Tools Location: Workshop-02', NULL, '2024-11-19 07:08:56'),
(14, 2, 'UPDATE', 'Updated item: Power Drill Category: Tools Location: Workshop-02', NULL, '2024-11-19 07:51:51'),
(15, 2, 'UPDATE', 'Updated item: Power Drill Category: Tools Location: Workshop-02', NULL, '2024-11-19 07:52:37'),
(16, 2, 'UPDATE', 'Updated item: Power Drill Category: Tools Location: Workshop-02', NULL, '2024-11-19 07:52:45'),
(17, 2, 'UPDATE', 'Updated item: Power Drill Category: Tools Location: Workshop-02', NULL, '2024-11-19 09:04:35'),
(18, 1, 'LOGIN', 'User logged in', '192.168.1.210', '2024-11-19 09:05:22'),
(19, 1, 'LOGIN', 'User logged in', '192.168.1.210', '2024-11-19 09:40:36'),
(20, 1, 'LOGIN', 'User logged in', '192.168.1.210', '2024-11-19 09:40:40'),
(21, 1, 'LOGIN', 'User logged in', '192.168.1.210', '2025-09-05 17:44:48'),
(22, 1, 'LOGIN', 'User logged in', '192.168.1.210', '2025-09-05 17:45:21'),
(23, 1, 'LOGIN', 'User logged in', '192.168.1.132', '2025-09-05 18:08:45'),
(24, 1, 'LOGIN', 'User logged in', '192.168.1.132', '2025-09-05 20:53:37'),
(25, 1, 'LOGIN', 'User logged in', '192.168.1.210', '2025-09-05 22:26:15'),
(26, 1, 'LOGIN', 'User logged in', '192.168.1.210', '2025-09-06 00:13:58'),
(27, 2, 'LOGIN', 'User logged in', '192.168.1.210', '2025-09-06 00:15:12'),
(28, 1, 'LOGIN', 'User logged in', '192.168.1.210', '2025-09-06 00:28:45'),
(29, 2, 'LOGIN', 'User logged in', '192.168.1.210', '2025-09-06 00:28:59'),
(30, 1, 'LOGIN', 'User logged in', '192.168.1.210', '2025-09-06 00:29:23'),
(31, 2, 'LOGIN', 'User logged in', '192.168.1.210', '2025-09-06 00:29:36'),
(32, 1, 'LOGIN', 'User logged in', '192.168.1.210', '2025-09-06 00:30:03'),
(33, 1, 'LOGIN', 'User logged in', '192.168.1.100', '2025-09-06 01:06:57'),
(34, 2, 'UPDATE', 'Updated item: Power Drill Category: Tools Location: Workshop-02', NULL, '2025-09-06 01:08:29'),
(35, 1, 'INSERT', 'Added new item: girnncsbsbs', NULL, '2025-09-06 04:05:02'),
(36, 1, 'INSERT', 'Added new item: girnncsbsbs', NULL, '2025-09-06 04:05:15'),
(37, 4, 'UPDATE', 'Updated item: Safety Helmet Category: Safety Equipment Location: Personal', NULL, '2025-09-06 04:36:16'),
(38, 2, 'UPDATE', 'Updated item: Power Drill Category: Tools Location: Workshop-02', NULL, '2025-09-06 04:36:52'),
(39, 2, 'UPDATE', 'Updated item: Power Drill Category: Tools Location: Workshop-02', NULL, '2025-09-06 04:37:39'),
(40, 1, 'INSERT', 'Added new item: girnncsbsbs', NULL, '2025-09-06 04:43:53'),
(41, 1, 'INSERT', 'Added new item: ghjnmnbvc', NULL, '2025-09-06 04:57:58'),
(42, 1, 'INSERT', 'Added new item: RETOP Welding Machine 200A', NULL, '2025-09-06 05:06:19'),
(43, 1, 'INSERT', 'Added new item: CROWN DC inverter Welding Machine 250A', NULL, '2025-09-06 05:11:02'),
(44, 1, 'LOGIN', 'User logged in', '192.168.1.132', '2025-09-06 05:19:58'),
(45, 1, 'INSERT', 'Added new item: CROWN DC inverter Welding Machine 250A Model No: CT33100', NULL, '2025-09-06 05:24:08'),
(46, 1, 'INSERT', 'Added new item: CROWN DC inverter Welding Machine 250A Model No: CT33100', NULL, '2025-09-06 05:24:51'),
(47, 1, 'INSERT', 'Added new item: kuijyhtbgvfc', NULL, '2025-09-06 05:26:18'),
(48, 1, 'INSERT', 'Added new item: CROWN Cut Off Machine 2200W', NULL, '2025-09-06 05:31:11'),
(49, 1, 'INSERT', 'Added new item: gffgfg', NULL, '2025-09-06 05:46:59'),
(50, 1, 'INSERT', 'Added new item: gffgfg', NULL, '2025-09-06 05:47:03'),
(51, 1, 'INSERT', 'Added new item: 34567', NULL, '2025-09-06 05:48:26'),
(52, 1, 'INSERT', 'Added new item: uynhbgvfc', NULL, '2025-09-06 06:23:53'),
(53, 1, 'INSERT', 'Added new item: hyhjhjjh', NULL, '2025-09-06 06:33:40'),
(54, 1, 'INSERT', 'Added new item: fjghdgkjfdkg', NULL, '2025-09-06 06:35:34'),
(55, 1, 'INSERT', 'Added new item: girnncsbsbs', NULL, '2025-09-06 06:37:46'),
(56, 1, 'INSERT', 'Added new item: jynhbgvfcdx', NULL, '2025-09-06 06:45:23'),
(57, 1, 'LOGIN', 'User logged in', '192.168.1.132', '2025-09-06 06:47:14'),
(58, 1, 'INSERT', 'Added new item: new', NULL, '2025-09-06 06:48:56'),
(59, 1, 'INSERT', 'Added new item: mnhbgvfdc', NULL, '2025-09-06 06:51:19'),
(60, 1, 'INSERT', 'Added new item: new', NULL, '2025-09-06 06:58:51'),
(61, 2, 'UPDATE', 'Updated item: Power Drill Category: Tools Location: Workshop-02', NULL, '2025-09-06 07:05:16'),
(62, 2, 'UPDATE', 'Updated item: Power Drill Category: Tools Location: Workshop-02', NULL, '2025-09-06 07:05:22'),
(63, 2, 'UPDATE', 'Updated item: Power Drill Category: Tools Location: Workshop-02', NULL, '2025-09-06 07:06:31'),
(64, 2, 'UPDATE', 'Updated item: Power Drill Category: Tools Location: Workshop-02', NULL, '2025-09-06 07:06:51'),
(65, 1, 'INSERT', 'Added new item: RETOP Welding Machine 200A', NULL, '2025-09-06 07:11:00'),
(66, 1, 'INSERT', 'Added new item: 5', NULL, '2025-09-06 07:33:15'),
(67, 2, 'LOGIN', 'User logged in', '192.168.1.132', '2025-09-06 09:55:45'),
(68, 1, 'LOGIN', 'User logged in', '192.168.1.132', '2025-09-06 09:55:59'),
(69, 1, 'LOGIN', 'User logged in', '192.168.1.132', '2025-09-06 03:14:17');

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `permission_id` int NOT NULL,
  `permission_name` varchar(50) NOT NULL,
  `permission_description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` (`permission_id`, `permission_name`, `permission_description`) VALUES
(1, 'create_item', 'Can create new items'),
(2, 'edit_item', 'Can edit existing items'),
(3, 'delete_item', 'Can delete items'),
(4, 'view_item', 'Can view items'),
(5, 'create_employee', 'Can create new employees'),
(6, 'edit_employee', 'Can edit existing employees'),
(7, 'delete_employee', 'Can delete employees'),
(8, 'view_employee', 'Can view employees'),
(9, 'manage_transactions', 'Can manage transactions'),
(10, 'view_transactions', 'Can view transactions'),
(11, 'manage_locations', 'Can manage locations'),
(12, 'view_locations', 'Can view locations');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `employee_code` (`employee_code`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `location_id` (`location_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `employee_groups`
--
ALTER TABLE `employee_groups`
  ADD PRIMARY KEY (`group_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`),
  ADD UNIQUE KEY `item_code` (`item_code`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_location` (`location_id`);

--
-- Indexes for table `item_categories`
--
ALTER TABLE `item_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `item_transactions`
--
ALTER TABLE `item_transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_transaction_date` (`transaction_date`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role`,`permission_id`),
  ADD KEY `fk_role_permissions_permission_id` (`permission_id`);

--
-- Indexes for table `sub_locations`
--
ALTER TABLE `sub_locations`
  ADD PRIMARY KEY (`sublocation_id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `fk_user_activity_logs_user_id` (`user_id`),
  ADD KEY `idx_activity_timestamp` (`activity_timestamp`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `permission_name` (`permission_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=182;

--
-- AUTO_INCREMENT for table `employee_groups`
--
ALTER TABLE `employee_groups`
  MODIFY `group_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `item_categories`
--
ALTER TABLE `item_categories`
  MODIFY `category_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `item_transactions`
--
ALTER TABLE `item_transactions`
  MODIFY `transaction_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `location_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sub_locations`
--
ALTER TABLE `sub_locations`
  MODIFY `sublocation_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  MODIFY `log_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `permission_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `employee_groups` (`group_id`),
  ADD CONSTRAINT `employees_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`),
  ADD CONSTRAINT `employees_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `employees_ibfk_4` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `fk_items_category` FOREIGN KEY (`category_id`) REFERENCES `item_categories` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_items_location` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `items_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `item_transactions`
--
ALTER TABLE `item_transactions`
  ADD CONSTRAINT `item_transactions_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`),
  ADD CONSTRAINT `item_transactions_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`),
  ADD CONSTRAINT `item_transactions_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_role_permissions_permission_id` FOREIGN KEY (`permission_id`) REFERENCES `user_permissions` (`permission_id`) ON DELETE CASCADE;

--
-- Constraints for table `sub_locations`
--
ALTER TABLE `sub_locations`
  ADD CONSTRAINT `sub_locations_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`);

--
-- Constraints for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  ADD CONSTRAINT `fk_user_activity_logs_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
