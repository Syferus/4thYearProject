SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `phonegap_login` (
`reg_id` int(1) NOT NULL,
  `reg_date` varchar(20) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `height_of_container_cm` int(1)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


ALTER TABLE `phonegap_login`
 ADD PRIMARY KEY (`reg_id`);


ALTER TABLE `phonegap_login`
MODIFY `reg_id` int(1) NOT NULL AUTO_INCREMENT;