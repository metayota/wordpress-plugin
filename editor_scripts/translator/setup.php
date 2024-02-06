<?php
include_once('../metayota/library.php');


$db->query("
CREATE TABLE `translation` (
  `id` int NOT NULL,
  `translation_key` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `language` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `translation` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `depends_on` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `available_variables` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `generated_key` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'The key was generated by the system because of depends_on rule.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `translation`
--

INSERT INTO `translation` (`id`, `translation_key`, `language`, `translation`, `depends_on`, `available_variables`, `generated_key`) VALUES
(1, 'forgot_password_title', 'de', 'Passwort vergessen?', NULL, NULL, 0),
(2, 'forgot_password_text', 'de', NULL, NULL, NULL, 0),
(3, 'forgot_password_button', 'de', NULL, NULL, NULL, 0),
(4, 'troubles_logging_in', 'de', NULL, NULL, NULL, 0),
(5, 'resend_registration_mail', 'de', NULL, NULL, NULL, 0),
(6, 'reset_password', 'de', NULL, NULL, NULL, 0),
(7, 'please_select', 'en', 'Please select...', NULL, NULL, 0),
(8, 'choose_file', 'en', 'Choose a File', NULL, NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `translation`
--
ALTER TABLE `translation`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `translation_key_language` (`language`,`translation_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `translation`
--
ALTER TABLE `translation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;");