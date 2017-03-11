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