-- ============================================================
-- MEGASTORE - MySQL schema + seed data
-- Converted from Django (premium_shop) project
-- ============================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `cart_items`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `profiles`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(254) DEFAULT '',
  `first_name` VARCHAR(150) DEFAULT '',
  `last_name` VARCHAR(150) DEFAULT '',
  `is_superuser` TINYINT(1) NOT NULL DEFAULT 0,
  `date_joined` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `profiles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL UNIQUE,
  `address` TEXT,
  `city` VARCHAR(100),
  CONSTRAINT `fk_profile_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL UNIQUE,
  `image` VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(220) NOT NULL UNIQUE,
  `image` VARCHAR(255),
  `description` TEXT,
  `price` DECIMAL(10,2) NOT NULL,
  `stock` INT NOT NULL DEFAULT 10,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `cart_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
  CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `address` TEXT NOT NULL,
  `delivery_option` VARCHAR(20) NOT NULL DEFAULT 'standard',
  `payment_method` VARCHAR(20) NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
  `total_price` DECIMAL(10,2) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
  CONSTRAINT `fk_oi_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_oi_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `rating` INT NOT NULL,
  `comment` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_review_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_review_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Users (Django PBKDF2 hashes can't be verified by PHP). All seeded users use password: password123

-- (Hashes below are PHP bcrypt of 'password123'.)

INSERT INTO `users` (`id`,`username`,`password`,`email`,`first_name`,`last_name`,`is_superuser`,`date_joined`) VALUES
(1,'Shikha','$2y$10$SVPcv/7M6or/rF08wTXKUOuv3ktCGeHvET8J.HRRNJa8g0dU1u6yu','shikhakr455@gmail.com','Shikha','Kumari',1,'2026-06-16 11:26:45.088458'),
(2,'Sana','$2y$10$SVPcv/7M6or/rF08wTXKUOuv3ktCGeHvET8J.HRRNJa8g0dU1u6yu','','','',0,'2026-06-17 07:50:23.123509'),
(3,'Priya','$2y$10$SVPcv/7M6or/rF08wTXKUOuv3ktCGeHvET8J.HRRNJa8g0dU1u6yu','','','',0,'2026-06-17 07:50:43.653275'),
(4,'Raghu','$2y$10$SVPcv/7M6or/rF08wTXKUOuv3ktCGeHvET8J.HRRNJa8g0dU1u6yu','','','',0,'2026-06-17 07:53:22.666678');


INSERT INTO `profiles` (`id`,`user_id`,`address`,`city`) VALUES
(1,1,NULL,NULL);


INSERT INTO `categories` (`id`,`name`,`slug`,`image`) VALUES
(1,'Mobile','mobile','Mobile.webp'),
(2,'Laptop','laptop','Laptop.jpg'),
(3,'Headphones','headphones','Headphones.jpg'),
(4,'Baby Products','baby-products','Baby_Products.webp'),
(6,'Tablet','tablet','Tablet.jfif'),
(7,'Power Banks','power-banks','Power_Banks.jfif'),
(8,'Smart TVs','smart-tvs','Smart_TVs.jfif'),
(9,'Computer & Accessories','computer-accessories','Computer__Accessories.jfif'),
(10,'Cameras & Photography','cameras-photography','Cameras__Photography.jpg'),
(11,'Fashion','fashion','Fashion.webp'),
(12,'Home & Living','home-living','Home__Living.jfif'),
(13,'Books & Education','books-education','Books__Education.jpg'),
(14,'Beauty & Personal Care','beauty-personal-care','Beauty__Personal_Care.jpg'),
(15,'Speakers','speakers','woofer-1885354_960_720.png');


INSERT INTO `products` (`id`,`category_id`,`name`,`slug`,`image`,`description`,`price`,`stock`,`created_at`) VALUES
(1,1,'iPhone 15 Pro','iphone-15-pro','iphone_15_pro.jpg','Apple’s flagship smartphone featuring the A17 Pro chip built on 3nm architecture, delivering industry-leading performance and efficiency. It includes a titanium frame for durability, a Pro camera system with improved low-light photography, and advanced computational photography features for cinematic-quality video recording.',129900,10,'2026-06-16 11:50:29.058022'),
(2,1,'Samsung Galaxy S24 Ultra','samsung-galaxy-s24-ultra','Samsung_Galaxy_S24_Ultra.jpg','Premium Android flagship powered by AI-enhanced features, offering real-time translation, intelligent photo editing, and a 200MP pro-grade camera system. Built for productivity with S-Pen support and a dynamic AMOLED display for ultra-smooth visuals.',109999,34,'2026-06-16 11:51:18.672443'),
(3,1,'OnePlus 12','oneplus-12','OnePlus_12.avif','A performance-centric smartphone featuring Snapdragon 8 Gen 3, ultra-fast charging technology, and a Hasselblad-tuned camera system for natural color accuracy and sharp imaging.
64,999',64998.99,24,'2026-06-16 11:52:03.493466'),
(4,1,'Redmi Note 13 Pro','redmi-note-13-pro','Redmi_Note_13_Pro.png','A value-for-money smartphone featuring a 200MP high-resolution camera, AMOLED display, and long-lasting battery optimized for daily multitasking and entertainment.',24998.99,43,'2026-06-16 11:52:39.781583'),
(5,1,'Realme GT Neo 5','realme-gt-neo-5','Realme_GT_Neo_5.jfif','Gaming-focused smartphone designed for high-performance users with Snapdragon chipset, advanced cooling system, and ultra-fast 150W charging for minimal downtime.',29998.95,51,'2026-06-16 11:53:17.592195'),
(6,1,'iQOO Neo 9','iqoo-neo-9','iQOO_Neo_9.jpg','Performance smartphone optimized for gaming with high refresh rate display, flagship processor, and liquid cooling technology for sustained performance.',31999,29,'2026-06-16 11:53:53.034267'),
(7,1,'Vivo V30','vivo-v30','Vivo_V30.jpg','Stylish portrait-focused smartphone featuring professional-grade camera enhancements, sleek lightweight design, and optimized night photography mode.',33998.99,24,'2026-06-16 11:54:36.108427'),
(8,1,'Nothing Phone 2','nothing-phone-2','Nothing_Phone_2.webp','Unique transparent design smartphone featuring Glyph LED interface for notifications, clean UI experience, and balanced flagship performance.',44999,34,'2026-06-16 11:55:09.549005'),
(9,1,'Motorola Edge 50','motorola-edge-50','Motorola_Edge_50.webp','Clean Android smartphone offering near-stock Android experience, curved OLED display, and reliable all-day performance with optimized battery life.',39998.99,17,'2026-06-16 11:55:42.731936'),
(10,1,'OPPO Reno 11','oppo-reno-11','OPPO_Reno_11.webp','Camera-centric smartphone designed for portrait photography with AI enhancements, sleek curved design, and fast charging support.',36999,10,'2026-06-16 11:56:21.169770'),
(11,2,'MacBook Air M3','macbook-air-m3','MacBook_Air_M3.webp','Ultra-lightweight Apple laptop powered by the M3 chip, delivering exceptional performance and battery life. Perfect for students, developers, and professionals requiring silent, fanless computing with premium build quality.',114900,32,'2026-06-16 12:13:02.794493'),
(12,2,'Dell XPS 13','dell-xps-13','Dell_Inspiron_15.webp','Premium Windows ultrabook featuring edge-to-edge display, compact aluminum body, and powerful Intel processors designed for productivity and portability.',129000,31,'2026-06-16 12:13:49.963460'),
(13,2,'HP Pavilion 15','hp-pavilion-15','HP_Pavilion_15.webp','Balanced laptop offering solid performance for students and office users, equipped with modern CPU, SSD storage, and immersive audio experience.',65000,41,'2026-06-16 12:14:22.859247'),
(14,2,'Lenovo IdeaPad Slim 5','lenovo-ideapad-slim-5','Lenovo_IdeaPad_Slim_5.jfif','Thin and lightweight laptop designed for everyday productivity, online learning, and multitasking with energy-efficient performance.',57999.99,37,'2026-06-16 12:15:02.223959'),
(15,2,'ASUS ROG Strix G16','asus-rog-strix-g16','ASUS_ROG_Strix_G16.jpg','High-performance gaming laptop featuring advanced GPU, RGB keyboard, and superior cooling system for competitive gaming and heavy workloads.',149998.99,41,'2026-06-16 12:15:33.120853'),
(16,2,'Acer Aspire 7','acer-aspire-7','Acer_Aspire_7.jpg','Budget gaming laptop offering dedicated graphics performance suitable for casual gaming, programming, and multimedia editing.',55000,32,'2026-06-16 12:16:20.681714'),
(17,2,'MSI GF63 Thin','msi-gf63-thin','MSI_GF63_Thin.jpg','Lightweight gaming laptop combining portability with decent GPU performance for gamers on a budget.',189900,43,'2026-06-16 12:16:57.621119'),
(18,2,'Dell Inspiron 15','dell-inspiron-15','Dell_Inspiron_15_40qG8QG.webp','Reliable everyday laptop suitable for office tasks, browsing, and academic use with stable performance and durability.',62000,27,'2026-06-16 12:17:32.512493'),
(19,2,'HP Victus Gaming','hp-victus-gaming','HP_Victus_Gaming.avif','Affordable gaming laptop designed for smooth gameplay, featuring modern GPU support and enhanced thermal design.',85000,59,'2026-06-16 12:18:07.933393'),
(20,2,'MacBook Pro M3','macbook-pro-m3','MacBook_Pro_M3.jpg','Professional-grade Apple laptop built for creators, developers, and designers requiring high performance for video editing and software development.',189900,10,'2026-06-16 12:20:31.216552'),
(21,15,'Sony HT-S20R','sony-ht-s20r','Sony_HT-S20R.jfif','5.1 channel home theatre system delivering cinematic surround sound experience ideal for movies and gaming.',19990,24,'2026-06-17 03:34:52.352917'),
(22,15,'JBL Flip 6','jbl-flip-6','JBL_Flip_6.jpg','Portable waterproof Bluetooth speaker offering deep bass, clear audio output, and durable outdoor-ready design.',11999,29,'2026-06-17 03:35:30.377365'),
(23,15,'boAt Stone 1200','boat-stone-1200','boAt_Stone_1200.jpg','Powerful portable speaker with RGB lighting, loud bass output, and long battery backup for parties and gatherings.',4999,34,'2026-06-17 03:36:09.105501'),
(24,15,'Marshall Emberton II','marshall-emberton-ii','Marshall_Emberton_II.avif','Premium portable speaker delivering signature Marshall sound with compact rugged design and long battery life.',14999,47,'2026-06-17 03:36:52.795570'),
(25,15,'Sony SRS-XE200','sony-srs-xe200','Sony_SRS-XE200.jpg','Durable speaker with 360-degree sound dispersion and water-resistant build for outdoor usage.',12999,32,'2026-06-17 03:37:25.358033'),
(26,15,'JBL PartyBox 110','jbl-partybox-110','JBL_PartyBox_110.webp','High-power party speaker with deep bass, LED light effects, and karaoke-ready audio output.',29999,41,'2026-06-17 03:37:57.490692'),
(27,15,'Philips Soundbar 2.1','philips-soundbar-21','Philips_Soundbar_2.1.png','Budget-friendly soundbar with strong bass output and Bluetooth connectivity for home entertainment setups.',7999,29,'2026-06-17 03:38:31.784862'),
(28,15,'Blaupunkt SBA10','blaupunkt-sba10','Blaupunkt_SBA10.jfif','Compact soundbar designed for small rooms offering clear audio enhancement for televisions.',3498.99,47,'2026-06-17 03:39:07.202534'),
(29,15,'boAt Aavante Bar','boat-aavante-bar','boAt_Aavante_Bar.jpg','Affordable soundbar system delivering balanced audio output suitable for daily entertainment.',8999,24,'2026-06-17 03:39:44.333786'),
(30,3,'Sony WH-1000XM5','sony-wh-1000xm5','Sony_WH-1000XM5.jpg','Industry-leading noise cancelling headphones with dual processor technology, delivering crystal-clear audio, deep bass, and exceptional comfort for long listening sessions. Ideal for travel and professional use.',29990,23,'2026-06-17 03:40:36.953318'),
(31,3,'Bose QuietComfort 45','bose-quietcomfort-45','Bose_QuietComfort_45.webp','Premium headphones designed for all-day comfort with world-class active noise cancellation and balanced sound profile suitable for music and calls.',27999,32,'2026-06-17 03:41:18.571842'),
(32,3,'AirPods Pro 2','airpods-pro-2','AirPods_Pro_2.webp','Apple’s advanced earbuds featuring adaptive transparency mode, spatial audio, and powerful H2 chip for seamless connectivity within Apple ecosystem.',24900,53,'2026-06-17 03:41:58.332152'),
(33,3,'boAt Rockerz 450','boat-rockerz-450','boAt_Rockerz_450.webp','Budget-friendly wireless headphones with deep bass, long battery life, and lightweight design suitable for daily use.',1999,31,'2026-06-17 03:42:46.389218'),
(34,3,'JBL Tune 760NC','jbl-tune-760nc','JBL_Tune_760NC.webp','Affordable noise-cancelling headphones with JBL signature sound and comfortable over-ear design.',6999,29,'2026-06-17 03:43:22.927512'),
(35,3,'Sennheiser HD 450BT','sennheiser-hd-450bt','Sennheiser_HD_450BT.webp','High-quality wireless headphones offering balanced audio output, deep bass, and strong build quality for audiophiles.',10999,44,'2026-06-17 03:44:10.298526'),
(36,3,'Skullcandy Crusher Evo','skullcandy-crusher-evo','Skullcandy_Crusher_Evo.webp','Bass-heavy headphones featuring adjustable sensory bass technology for immersive music experience.',12999,10,'2026-06-17 03:55:54.753147'),
(37,3,'Sony WH-XB910N','sony-wh-xb910n','Sony_WH-XB910N.webp','Extra bass headphones with active noise cancellation and long battery life for entertainment lovers.',13990,15,'2026-06-17 03:56:32.701602'),
(38,3,'Noise Two Wireless','noise-two-wireless','Noise_Two_Wireless.jpg','Affordable lightweight headphones designed for daily music and calls with decent sound clarity.',2498.98,17,'2026-06-17 03:57:36.316925'),
(39,3,'Apple AirPods Max','apple-airpods-max','Apple_AirPods_Max.jpg','Premium over-ear headphones with high-fidelity audio, spatial sound, and luxury build quality for professional users.',59899.99,35,'2026-06-17 03:58:11.643573');


INSERT INTO `reviews` (`id`,`product_id`,`user_id`,`rating`,`comment`,`created_at`) VALUES
(1,1,2,4,'Veery good camera quality','2026-06-17 07:50:27.689997'),
(2,1,3,3,'Easy to use','2026-06-17 07:51:02.749447'),
(3,2,4,5,'Very Nice model','2026-06-17 07:53:39.245023'),
(4,2,3,4,'very nice product','2026-06-17 14:18:38.867644');
