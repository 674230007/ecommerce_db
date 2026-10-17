-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 14, 2026 at 04:30 PM
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
-- Database: `ecommerce_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `customer_name` varchar(100) DEFAULT '',
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT '',
  `slip_image` varchar(255) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `total_price`, `status`, `created_at`, `customer_name`, `address`, `phone`, `slip_image`) VALUES
(3, 6, 1340.00, 'Completed (Received)', '2026-09-30 08:25:01', '', NULL, '', ''),
(5, 6, 840.00, 'Completed (Received)', '2026-09-30 08:37:36', 'Nattiwut', '28/400 สมุทรสาคร', '0648420000', 'slip_1790757456_6abcca509194f.png');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(3, 3, 6, 3, 250.00),
(4, 3, 8, 1, 590.00),
(6, 5, 6, 1, 250.00),
(7, 5, 8, 1, 590.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image_url` varchar(255) DEFAULT '',
  `category` varchar(50) DEFAULT 'อื่นๆ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`, `created_at`, `image_url`, `category`) VALUES
(5, 'ลิปสติกเนื้อแมทท์', 'สีสวยชัด ติดทนนาน ไม่ตกร่อง ริมฝีปากเนียนนุ่ม', 290.00, 28, '2026-09-30 07:46:41', 'https://images.unsplash.com/photo-1562986005-19082a4a9bbf?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OTB8fGxpcHN0aWNrfGVufDB8fDB8fHww', 'ลิป'),
(6, 'บลัชออนแก้วใส Gelly Blush', 'แก้มระเรื่อดูสุขภาพดี เป็นธรรมชาติ เกลี่ยง่าย', 250.00, 21, '2026-09-30 07:46:41', 'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?w=400', 'บรัชออน'),
(7, 'มาสคาร่ากันน้ำ Volumizing Mascara', 'ขนตายาวหนางอนเด้งตลอดวัน ไม่แพนด้า', 320.00, 19, '2026-09-30 07:46:41', 'https://images.unsplash.com/photo-1650793450119-3d608d0a9532?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NDR8fG1hc2NhcmF8ZW58MHx8MHx8fDA%3D', 'มาสคาร่า'),
(8, 'รองพื้นเนื้อบางเบา Liquid Foundation', 'ปกปิดเรียบเนียน คุมมัน ผิวสวยธรรมชาติ', 590.00, 12, '2026-09-30 07:46:41', 'https://plus.unsplash.com/premium_photo-1676914336000-f8b2f9edd56a?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8Y29tcGFjdCUyMHBvd2RlcnxlbnwwfHwwfHx8MA%3D%3D', 'รองพื้น'),
(9, 'แป้งพัฟคุมมัน Air Cushion SPF50+', 'แป้งคุชชั่นเนื้อเนียนละเอียด ปกปิดดีเยี่ยม คุมมันตลอดวัน หน้าผ่องไม่ดรอป', 450.00, 25, '2026-09-30 08:44:59', 'https://images.unsplash.com/photo-1737014892220-4123c7e020a1?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8Y29tcGFjdCUyMHBvd2RlcnxlbnwwfHwwfHx8MA%3D%3D', 'รองพื้น'),
(10, 'เซรั่มบำรุงผิวหน้า Glow Hyaluron', 'เซรั่มบำรุงผิวหน้าสูตรเข้มข้น เติมความชุ่มชื้น ผิวฉ่ำโกลว์ ดูสุขภาพดี', 690.00, 15, '2026-09-30 08:44:59', 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?w=500&auto=format&fit=crop&q=60', 'เซรั่ม'),
(11, 'อายไลเนอร์กันน้ำ Super Sharp', 'หัวพู่กันเรียวเล็ก เขียนง่าย กันน้ำ กันเหงื่อ ไม่เยิ้มระหว่างวัน', 190.00, 30, '2026-09-30 08:44:59', 'https://images.unsplash.com/photo-1733590258239-a1606722908f?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTh8fFdhdGVycHJvb2YlMjBCcm93fGVufDB8fDB8fHww', 'อื่นๆ'),
(12, 'ดินสอเขียนคิ้ว Waterproof Brow', 'หัวออโต้เส้นคมชัด เม็ดสีแน่น ติดทนนานพร้อมแปรงปัดในตัว', 150.00, 40, '2026-09-30 08:44:59', 'https://plus.unsplash.com/premium_photo-1683120989931-87c51d2f4e4f?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8V2F0ZXJwcm9vZiUyMEJyb3d8ZW58MHx8MHx8fDA%3D', 'อื่นๆ'),
(13, 'พาเลทอายแชโดว์ 9 สี Warm Tone', 'โทนสีน้ำตาลพีชสุดฮิต แต่งได้ทุกวัน เนื้อแมทและชิมเมอร์วิบวับ', 390.00, 20, '2026-09-30 08:44:59', 'https://images.unsplash.com/photo-1516975080664-ed2fc6a32937?w=500&auto=format&fit=crop&q=60', 'อื่นๆ'),
(14, 'ลิปทินท์เนื้อเจลฉ่ำวาว Juicy Tint', 'สีสวยติดทนนาน ปากอิ่มน้ำ ดูน่าจุ๊บ ไม่ตกร่อง', 220.00, 35, '2026-09-30 08:44:59', 'https://images.unsplash.com/photo-1586495777744-4413f21062fa?w=500&auto=format&fit=crop&q=60', 'เซรั่ม'),
(15, 'ไฮไลท์หน้าพุ่ง Glow Highlighter', 'เนื้อชิมเมอร์เนียนนุ่ม ปัดแล้วหน้าฉ่ำวาว เล่นแสงสวยมีมิติ', 320.00, 18, '2026-09-30 08:44:59', 'https://images.unsplash.com/photo-1596704017254-9b121068fb31?w=500&auto=format&fit=crop&q=60', 'อื่นๆ'),
(16, 'สเปรย์น้ำแร่ล็อกเมคอัพ Make Up Fixing', 'ช่วยล็อกเครื่องสำอางให้ติดทนนานตลอดวัน พร้อมบำรุงผิวให้สดชื่น', 290.00, 22, '2026-09-30 08:44:59', 'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?w=500&auto=format&fit=crop&q=60', 'เซรั่ม'),
(17, 'คอนซีลเลอร์ปกปิดรอยสิว Full Cover', 'ปกปิดรอยคล้ำใต้ตาและรอยสิวได้อย่างเนียนสนิท ไม่เป็นคราบ', 260.00, 28, '2026-09-30 08:44:59', 'https://images.unsplash.com/photo-1631729371254-42c2892f0e6e?w=500&auto=format&fit=crop&q=60', 'รองพื้น'),
(18, 'มาส์กหน้าสูตรเข้มข้น Sleeping Mask', 'ฟื้นฟูผิวเร่งด่วนข้ามคืน เติมเต็มความชุ่มชื้น นุ่มเด้งทันทีที่ตื่นนอน', 490.00, 12, '2026-09-30 08:44:59', 'https://images.unsplash.com/photo-1630333978163-fe8c95db0f91?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8Vml0LUMlMjBNYXNrJTIwU2hlZXR8ZW58MHx8MHx8fDA%3D', 'เซรั่ม'),
(19, 'ลิปสติกเนื้อแมท Velvet Matte Lip', 'ลิปเนื้อแมทเนียนนุ่ม เม็ดสีแน่นชัด ติดทนนาน ไม่ตกร่อง', 290.00, 30, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1618510069246-21f564373621?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Njh8fGxpcHN0aWNrfGVufDB8fDB8fHww', 'ลิป'),
(20, 'ลิปทินท์เนื้อเจลฉ่ำวาว Juicy Tint', 'ลิปทินท์ปากอิ่มน้ำ สีสวยธรรมชาติ ดูสดใสตลอดวัน', 220.00, 25, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1537277033580-8792f1e42c47?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NTR8fGxpcHN0aWNrfGVufDB8fDB8fHww', 'เซรั่ม'),
(21, 'ลิปกลอสปากอวบอิ่ม Crystal Gloss', 'เพิ่มความเงางามให้ริมฝีปากดูอวบอิ่มฉ่ำวาวแบบสาวเกาหลี', 250.00, 20, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1631214524115-9942bf927d4a?q=80&w=1374&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'ลิป'),
(22, 'ลิปบาล์มเปลี่ยนสี Rose Balm', 'บำรุงริมฝีปากชุ่มชื้น พร้อมเปลี่ยนเป็นสีชมพูระเรื่อเป็นธรรมชาติ', 180.00, 40, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1695634503987-f6f18706708e?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MzB8fGxpcHN0aWNrfGVufDB8fDB8fHww', 'ลิป'),
(23, 'ลิปสติกเนื้อซาติน Satin Luxe Lip', 'สัมผัสเนียนนุ่มลื่น ทาง่าย สีชัด ให้ริมฝีปากดูหรูหรา', 350.00, 15, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1555050455-f96634b5cba6?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTR8fGxpcHN0aWNrfGVufDB8fDB8fHww', 'ลิป'),
(24, 'ลิควิดลิปสติกติดทน Water Proof Lip', 'ลิควิดเนื้อบางเบา แห้งไว ติดทนนานตลอดวัน ไม่ติดแมสก์', 310.00, 25, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1571646034647-52e6ea84b28c?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTZ8fGxpcHN0aWNrfGVufDB8fDB8fHww', 'ลิป'),
(25, 'ลิปสติกออร์แกนิก Natural Organic Lip', 'สารสกัดจากธรรมชาติ ปลอดภัย บำรุงริมฝีปากไม่ให้คล้ำเสีย', 390.00, 18, '2026-09-30 12:52:53', 'https://plus.unsplash.com/premium_photo-1676380030496-1b1fd3a33a1b?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NDF8fGxpcHN0aWNrfGVufDB8fDB8fHww', 'ลิป'),
(26, 'ลิปแพ็คคู่สุดคุ้ม Duo Lip Set', 'เซ็ตคู่สีฮิต ทาเบลนด์ขอบปากสวยละมุนในเซ็ตเดียว', 450.00, 12, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1600852306771-c963331af110?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mzl8fGxpcHN0aWNrfGVufDB8fDB8fHww', 'ลิป'),
(27, 'ลิปสติกเนื้อเวลเวท Velvet Stain', 'เนื้อสัมผัสกึ่งแมท เบาสบายปาก เกลี่ยง่ายไม่ตกร่อง', 270.00, 30, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1542452255191-c85a98f2c5d1?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTF8fGxpcHN0aWNrfGVufDB8fDB8fHww', 'ลิป'),
(28, 'ลิปสลีปปิ้งมาส์ก Overnight Lip Mask', 'มาส์กบำรุงริมฝีปากข้ามคืน ลดความคล้ำ ปากนุ่มฟูใน 1 คืน', 290.00, 22, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1586495777744-4413f21062fa?w=500&auto=format&fit=crop&q=60', 'เซรั่ม'),
(29, 'บรัชออนแก้วใส Gelly Blush', 'บรัชออนเนื้อเจลลี่ แก้มระเรื่อสุขภาพดี เป็นธรรมชาติ เกลี่ยง่าย', 250.00, 21, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1516975080664-ed2fc6a32937?w=500&auto=format&fit=crop&q=60', 'บรัชออน'),
(30, 'บลัชออนฝุ่นชิมเมอร์ Glow Powder Blush', 'บลัชออนเนื้อฝุ่นผสมชิมเมอร์ละเอียด แก้มเงาเล่นแสงสวย', 280.00, 20, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1701271482230-5ecaec3cd3e1?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTR8fGJsdXNoJTIwbWFrZXVwfGVufDB8fDB8fHww', 'บรัชออน'),
(31, 'บลัชออนเนื้อครีม Creamy Cheek', 'เกลี่ยง่าย กลืนไปกับผิว ให้ลุคผิวฉ่ำวาวดูเป็นสาวสุขภาพดี', 320.00, 15, '2026-09-30 12:52:53', 'https://plus.unsplash.com/premium_photo-1689245691735-46a8f0c86054?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTE2fHxibHVzaCUyMG1ha2V1cHxlbnwwfHwwfHx8MA%3D%3D', 'เซรั่ม'),
(32, 'พาเลทบลัชออน 4 ช่อง Multi Blush', 'รวม 4 เฉดสีในตลับเดียว แต่งได้ทุกโทนสีผิว', 450.00, 10, '2026-09-30 12:52:53', 'https://plus.unsplash.com/premium_photo-1677526496597-aa0f49053ce2?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NDV8fGJsdXNoJTIwbWFrZXVwfGVufDB8fDB8fHww', 'บรัชออน'),
(33, 'บลัชออนคุชชั่น Cushion Blush', 'คุชชั่นบลัชพัฟเกลี่ยง่าย สีชัด ติดทนตลอดวัน', 300.00, 18, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1575686717697-f43bd36e74c2?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MjZ8fGJsdXNoJTIwbWFrZXVwfGVufDB8fDB8fHww', 'รองพื้น'),
(34, 'บลัชออนเนื้อแมทท์ Matte Peach Blush', 'สีพีชละมุน เนื้อแมทท์เนียนละเอียด คุมมัน ไม่เป็นคราบ', 260.00, 25, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1590156424570-698d124ec7dd?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MjN8fGJsdXNoJTIwbWFrZXVwfGVufDB8fDB8fHww', 'บรัชออน'),
(35, 'บลัชออนไล่สี Ombre Blush', 'บลัชออนไล่เฉดสี สวยละมุน ปัดแล้วหน้าดูมีมิติ', 340.00, 14, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1716287384717-972e03bf4179?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTh8fGJsdXNoJTIwbWFrZXVwfGVufDB8fDB8fHww', 'บรัชออน'),
(36, 'บลัชออนโมจิ Soft Mochi Blush', 'เนื้อนุ่มเด้งดึ๋ง ไม่เป็นฝุ่น ติดทนยาวนาน', 290.00, 22, '2026-09-30 12:52:53', 'https://plus.unsplash.com/premium_photo-1676818430931-b822bcc41dd7?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTd8fGJsdXNoJTIwbWFrZXVwfGVufDB8fDB8fHww', 'บรัชออน'),
(37, 'บลัชออนกันน้ำ Sweat Proof Blush', 'สูตรกันเหงื่อและกันน้ำ สีไม่ดรอประหว่างวัน', 310.00, 19, '2026-09-30 12:52:53', 'https://plus.unsplash.com/premium_photo-1726840825289-8de3e79e42d5?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OXx8Ymx1c2glMjBtYWtldXB8ZW58MHx8MHx8fDA%3D', 'บรัชออน'),
(38, 'บลัชออนดอกไม้ Natural Flower Blush', 'ดีไซน์ลวดลายดอกไม้สวยงาม เม็ดสีละมุน ปัดแล้วหน้าเด็ก', 380.00, 12, '2026-09-30 12:52:53', 'https://plus.unsplash.com/premium_photo-1678449464890-56f5bb2a0192?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8Ymx1c2glMjBtYWtldXB8ZW58MHx8MHx8fDA%3D', 'บรัชออน'),
(39, 'มาสคาร่ากันน้ำ Volumizing Mascara', 'ขนตายาวหนาเด้งตลอดวัน ไม่แพนด้า กันน้ำดีเยี่ยม', 320.00, 19, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1725792825192-cbfe46ed9236?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MzR8fG1hc2NhcmF8ZW58MHx8MHx8fDA%3D', 'มาสคาร่า'),
(40, 'มาสคาร่าสูตรไฟเบอร์ Fiber Lash Mascara', 'ต่อขนตาให้ยาวขึ้นทันทีเสมือนต่อขนตาปลอม', 350.00, 15, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1631214499943-07647378b067?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MjZ8fG1hc2NhcmF8ZW58MHx8MHx8fDA%3D', 'มาสคาร่า'),
(41, 'มาสคาร่าใสปัดคิ้วและขนตา Clear Mascara', 'ล็อกขนตาและคิ้วให้เรียงเส้นสวยเป็นระเบียบดูเป็นธรรมชาติ', 190.00, 30, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1706170498499-235824fd317c?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTZ8fG1hc2NhcmF8ZW58MHx8MHx8fDA%3D', 'มาสคาร่า'),
(42, 'มาสคาร่างอนเด้ง Curled Lash Mascara', 'ล็อกขนตาให้งอนเด้งไม่มีตกตลอด 24 ชั่วโมง', 290.00, 24, '2026-09-30 12:52:53', 'https://plus.unsplash.com/premium_photo-1678377960130-bb70ffeb1cd8?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTN8fG1hc2NhcmF8ZW58MHx8MHx8fDA%3D', 'มาสคาร่า'),
(43, 'มาสคาร่าหัวแปรงจิ๋ว Skinny Mascara', 'ซอกซอนเข้าถึงขนตาเส้นเล็กและขนตาล่างได้อย่างง่ายดาย', 220.00, 28, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1619168213439-8af6b0fd5956?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTV8fG1hc2NhcmF8ZW58MHx8MHx8fDA%3D', 'มาสคาร่า'),
(44, 'มาสคาร่าล้างออกง่าย Easy Wash Mascara', 'สูตรล้างออกง่ายด้วยน้ำอุ่น ไม่ต้องถูรอบดวงตาแรงๆ', 260.00, 20, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1512207159096-c2c91b1dfadd?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTF8fG1hc2NhcmF8ZW58MHx8MHx8fDA%3D', 'มาสคาร่า'),
(45, 'มาสคาร่าสีดำสนิท Ultra Black Mascara', 'เม็ดสีดำเข้มข้น เพิ่มความคมชัดให้ดวงตาดูกลมโต', 310.00, 18, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1670832209136-fad04d9920f9?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTJ8fG1hc2NhcmF8ZW58MHx8MHx8fDA%3D', 'มาสคาร่า'),
(46, 'มาสคาร่าบำรุงขนตา Lash Serum Mascara', 'ปัดพร้อมบำรุงด้วยวิตามิน ขนตาแข็งแรงไม่หลุดร่วงง่าย', 380.00, 12, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1619406266880-7e130b6c65c0?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTB8fG1hc2NhcmF8ZW58MHx8MHx8fDA%3D', 'มาสคาร่า'),
(47, 'มาสคาร่ากันเหงื่อ Sports Waterproof', 'เหมาะสำหรับสายลุย กันน้ำกันเหงื่อขั้นสุด', 330.00, 16, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1650664370914-f026578ec2a4?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8N3x8bWFzY2FyYXxlbnwwfHwwfHx8MA%3D%3D', 'มาสคาร่า'),
(48, 'มาสคาร่าเพิ่มความหนา Mega Volume', 'เพิ่มวอลุ่มให้ขนตาดูหนาฟูสะพรึงแบบสาวสายฝอ', 340.00, 14, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1631214540553-ff044a3ff1d4?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8bWFzY2FyYXxlbnwwfHwwfHx8MA%3D%3D', 'มาสคาร่า'),
(49, 'รองพื้นเนื้อบางเบา Liquid Foundation', 'ปกปิดเรียบเนียน คุมมัน ผิวสวยธรรมชาติ', 590.00, 12, '2026-09-30 12:52:53', 'https://plus.unsplash.com/premium_photo-1673892639871-bc5d325fcd64?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NXx8Y29tcGFjdCUyMHBvd2RlcnxlbnwwfHwwfHx8MA%3D%3D', 'รองพื้น'),
(50, 'แป้งพัฟคุมมัน Air Cushion SPF50+', 'คุชชั่นเนื้อเนียนละเอียด ปกปิดดีเยี่ยม หน้าผ่องไม่ดรอป', 450.00, 25, '2026-09-30 12:52:53', 'https://media.istockphoto.com/id/504897928/photo/pressed-face-powder-and-puff.webp?a=1&b=1&s=612x612&w=0&k=20&c=fqixMNv7voKTT83ESdfSqCZCtgCilDRbf4L7uTcdFyY=', 'รองพื้น'),
(51, 'แป้งฝุ่นโปร่งแสง Translucent Powder', 'คุมมัน เซ็ตผิวหน้าให้เนียนนุ่ม ไม่เปลี่ยนสีรองพื้น', 390.00, 22, '2026-09-30 12:52:53', 'https://media.istockphoto.com/id/872852738/photo/make-up-face-powder-isolated-on-white-background.webp?a=1&b=1&s=612x612&w=0&k=20&c=ULLgErSWcHLKwaA5Rgl5ao6Yx0CyXLkf9J1gMEEa4zk=\r\n', 'รองพื้น'),
(52, 'รองพื้นคุมมันขั้นเทพ Matte Foundation', 'ปกปิดรอยสิวและจุดด่างดำสนิท คุมมันยาวนาน 12 ชม.', 650.00, 10, '2026-09-30 12:52:53', 'https://plus.unsplash.com/premium_photo-1676818430931-b822bcc41dd7?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NTl8fGNvbXBhY3QlMjBwb3dkZXJ8ZW58MHx8MHx8fDA%3D', 'รองพื้น'),
(53, 'คอนซีลเลอร์ปกปิดริ้วรอย Full Cover Concealer', 'ปกปิดรอยคล้ำใต้ตาและรอยสิวได้อย่างเนียนกริบ', 260.00, 28, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1631729371254-42c2892f0e6e?w=500&auto=format&fit=crop&q=60', 'รองพื้น'),
(54, 'แป้งผสมรองพื้น Two Way Cake', 'ปกปิดเนียนเด้ง กันน้ำ กันเหงื่อในตลับเดียว', 420.00, 18, '2026-09-30 12:52:53', 'https://plus.unsplash.com/premium_photo-1678932075282-556a5e386a04?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NjZ8fGNvbXBhY3QlMjBwb3dkZXJ8ZW58MHx8MHx8fDA%3D', 'รองพื้น'),
(55, 'บีบีครีมผสมกันแดด BB Cream Glow', 'ปรับสีผิวให้สว่างใสเรียบเนียนทันทีที่ทา', 320.00, 20, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1661347243240-5ce7c9992369?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8QkIlMjBDcmVhbSUyMEdsb3d8ZW58MHx8MHx8fDA%3D', 'เซรั่ม'),
(56, 'ซีซีครีมคุมมัน CC Cream Matte', 'คุมความมัน ปรับผิวหน้าให้เนียนใสเป็นธรรมชาติ', 340.00, 16, '2026-09-30 12:52:53', 'https://media.istockphoto.com/id/1312358174/photo/smudged-make-up-gradient-texture-palette-creamy-matte-beige-concealer-foundation-cc-or-bb.webp?a=1&b=1&s=612x612&w=0&k=20&c=i9NAdTB2PxR3YGVYGkRLJLSQOfi3hBcIAEAjbR1zEsg=', 'เซรั่ม'),
(57, 'รองพื้นงานผิวโกลว์ Glow Foundation', 'ให้ผิวหน้าฉ่ำวาวดูสุขภาพดีสไตล์สาวเกาหลี', 620.00, 14, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1737014892220-4123c7e020a1?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8Y29tcGFjdCUyMHBvd2RlcnxlbnwwfHwwfHx8MA%3D%3D', 'รองพื้น'),
(58, 'แป้งอัดแข็งไม่ผสมรองพื้น Pressed Powder', 'ทัชอัปหน้าระหว่างวัน ควบคุมความมัน ไม่เป็นคราบ', 290.00, 25, '2026-09-30 12:52:53', 'https://plus.unsplash.com/premium_photo-1673892639871-bc5d325fcd64?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NXx8Y29tcGFjdCUyMHBvd2RlcnxlbnwwfHwwfHx8MA%3D%3D\r\n', 'รองพื้น'),
(59, 'เซรั่มบำรุงผิวหน้า Glow Hyaluron', 'เติมความชุ่มชื้น ผิวฉ่ำโกลว์ ดูสุขภาพดีอิ่มน้ำ', 690.00, 15, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?w=500&auto=format&fit=crop&q=60', 'เซรั่ม'),
(60, 'สเปรย์น้ำแร่ล็อกเมคอัพ Make Up Fixing', 'ล็อกเครื่องสำอางติดทน พร้อมบำรุงผิวให้สดชื่น', 290.00, 22, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?w=500&auto=format&fit=crop&q=60', 'เซรั่ม'),
(61, 'มาส์กหน้าสูตรเข้มข้น Sleeping Mask', 'ฟื้นฟูผิวเร่งด่วนข้ามคืน นุ่มเด้งทันทีที่ตื่นนอน', 490.00, 12, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1728977627486-a4371790266b?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8Vml0LUMlMjBNYXNrJTIwU2hlZXR8ZW58MHx8MHx8fDA%3D', 'เซรั่ม'),
(62, 'โทนเนอร์เช็ดทำความสะอาดผิวหน้า Clear Toner', 'ทำความสะอาดล้ำลึก กระชับรูขุมขน ปรับสมดุลผิว', 350.00, 20, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1629198726018-604230bdb091?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8Q2xlYXIlMjBUb25lcnxlbnwwfHwwfHx8MA%3D%3D', 'เซรั่ม'),
(63, 'ครีมบำรุงผิวหน้า Whitening Cream', 'บำรุงผิวหน้ากระจ่างใส ลดเลือนจุดด่างดำ', 550.00, 18, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1763503839418-2b45c3d7a3c3?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8N3x8V2hpdGVuaW5nJTIwQ3JlYW18ZW58MHx8MHx8fDA%3D', 'เซรั่ม'),
(64, 'เจลว่านหางจระเข้บำรุงผิว Aloe Vera Gel', 'ปลอบประโลมผิวไหม้แดด เติมน้ำให้ผิวชุ่มชื้น', 190.00, 40, '2026-09-30 12:52:53', 'https://plus.unsplash.com/premium_photo-1757073464528-670144d3e1d5?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OXx8Q2xlYW5zaW5nJTIwT2lsfGVufDB8fDB8fHww', 'เซรั่ม'),
(65, 'กันแดดหน้าใส Sun Protection SPF50+', 'กันแดดเนื้อบางเบา ไม่เหนียวเหนอะหนะ ปกป้องยูวีขั้นสุด', 450.00, 25, '2026-09-30 12:52:53', 'https://media.istockphoto.com/id/1500014387/photo/orange-tube-of-sunscreen-on-sandy-beach-top-view.webp?a=1&b=1&s=612x612&w=0&k=20&c=jVqXTcjDV0kdKZU29KsoazTjB_UrfmO6tV5vKVPX-SI=', 'เซรั่ม'),
(66, 'คลีนซิ่งออยล์ล้างเครื่องสำอาง Cleansing Oil', 'ล้างเมคอัพกันน้ำได้อย่างสะอาดหมดจด ไม่ระคายเคืองผิว', 390.00, 22, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1699373383910-6f9cf75ee50a?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8Q2xlYW5zaW5nJTIwT2lsfGVufDB8fDB8fHww', 'เซรั่ม'),
(67, 'แผ่นมาส์กหน้าสูตรวิตามิน Vit-C Mask Sheet', 'บำรุงผิวเร่งด่วน ผิวหน้าดูกระจ่างใสอมชมพู', 69.00, 60, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1596202353109-44fa2a2d8a16?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8Vml0LUMlMjBNYXNrJTIwU2hlZXR8ZW58MHx8MHx8fDA%3D', 'เซรั่ม'),
(68, 'อายครีมบำรุงรอบดวงตา Eye Care Cream', 'ลดรอยคล้ำและถุงใต้ตา ให้รอบดวงตาสดใส', 420.00, 16, '2026-09-30 12:52:53', 'https://images.unsplash.com/photo-1622910076411-b126ff7e469b?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8RXllJTIwQ2FyZSUyMENyZWFtfGVufDB8fDB8fHww', 'เซรั่ม');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `membership_level` varchar(20) DEFAULT 'General',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `membership_level`, `created_at`) VALUES
(5, 'admin', '123456', 'admin@store.com', 'admin', 'Admin', '2026-09-30 07:56:41'),
(6, 'customer', '123456', 'customer@store.com', 'customer', 'VIP', '2026-09-30 07:56:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
