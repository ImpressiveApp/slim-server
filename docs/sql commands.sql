ALTER TABLE `customer_details` DROP `Customer_AddressDetails`;

ALTER TABLE `order_details` DROP `Address`;

======

ALTER TABLE `customer_details` 
ADD `Message` VARCHAR(100) NOT NULL AFTER `Created_Timestamp`, 
ADD `Active_Timestamp` TIMESTAMP NOT NULL AFTER `Message`;

ALTER TABLE `customer_details` DROP `Message`;
ALTER TABLE `customer_details` DROP `Active_Timestamp`;

ALTER TABLE `customer_details` 
ADD `Message` VARCHAR(100) NULL DEFAULT NULL AFTER `Created_Timestamp`, 
ADD `Active_Timestamp` TIMESTAMP NULL DEFAULT NULL AFTER `Message`;

=========================================================
3-17-2017

CREATE TABLE `retail_order_details` (
  `Order_Id` int(100) NOT NULL AUTO_INCREMENT,
  `Customer_Name` varchar(100) NULL,
  `Customer_Mobno` bigint(10) NOT NULL,  
  `Created_Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Rec_Timestamp`timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,  
  `Order_Status` varchar(100) NOT NULL,
  `No_Of_Items` int(100) NOT NULL,
  `Type_Of_Clothes` varchar(100) NOT NULL,
  `Cost` decimal(6,2) DEFAULT 0,
  PRIMARY KEY (`Order_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;