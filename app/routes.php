<?php
// Routes
$app->get('/',function(){

  echo "Home Url. Give /rest/(api name)/";
});

$pattern="/rest";

// CustomerDetails Table
$app->post($pattern.'/createNewCustomer/','CustomerDetails:createNewCustomer');
$app->post($pattern.'/getVerificationAccounts/','CustomerDetails:getVerificationAccounts');
$app->post($pattern.'/authentication/','CustomerDetails:authentication');
$app->post($pattern.'/getUser/','CustomerDetails:getUser');
$app->post($pattern.'/getCustomerHistory/','CustomerDetails:getCustomerHistory');
$app->post($pattern.'/updateCustomer/','CustomerDetails:updateCustomer');
$app->post($pattern.'/setAccountStatus/','CustomerDetails:setAccountStatus');
$app->post($pattern.'/setCustomerStatus/','CustomerDetails:setCustomerStatus');
$app->post($pattern.'/setPassword/','CustomerDetails:setPassword');
$app->post($pattern.'/getUnpaidCustomers/','CustomerDetails:getUnpaidCustomers');

// OrderDetails Table
$app->post($pattern.'/createOrder/','OrderDetails:createOrder');
$app->post($pattern.'/updateOrder/','OrderDetails:updateOrder');
$app->post($pattern.'/getOrderHistory/','OrderDetails:getOrderHistory');
$app->post($pattern.'/getUserOrderHistory/','OrderDetails:getUserOrderHistory');
$app->post($pattern.'/getAllOpenOrders/','OrderDetails:getAllOpenOrders');
$app->post($pattern.'/getOpenOrders/','OrderDetails:getOpenOrders');

// TimeSlots Table 
$app->post($pattern.'/availableSlots/','TimeSlots:availableSlots');
$app->post($pattern.'/updateTimeSlot/','TimeSlots:updateTimeSlot');
$app->post($pattern.'/getSlots/','TimeSlots:getSlots');


// Transactions Table 
$app->post($pattern.'/getTransactionHistory/','Transactions:getTransactionHistory');

// Promocodes Table 
$app->post($pattern.'/createPromocode/','Promocodes:createPromocode');
$app->post($pattern.'/getPromoCodes/','Promocodes:getPromoCodes');



// Ratecard Table
$app->post($pattern.'/getRateCard/','RateCard:getRateCard');
$app->post($pattern.'/updateRateCard/','RateCard:updateRateCard');
$app->get($pattern.'/sms/','RateCard:sms');


// Cron Jobs
$app->get($pattern.'/referralcodeProcessor/','CronJobs:referralcodeProcessor');
$app->get($pattern.'/deleteExpiredPromocodes/','CronJobs:deleteExpiredPromocodes');
$app->get($pattern.'/allCronJobs/','CronJobs:allCronJobs');
$app->get($pattern.'/deleteExpiredFestivalPromocodes/','CronJobs:deleteExpiredFestivalPromocodes');
$app->get($pattern.'/deleteExpiredOtherPromocodes/','CronJobs:deleteExpiredOtherPromocodes');
$app->get($pattern.'/createScheduledPromocodes/','CronJobs:createScheduledPromocodes');


////////////////////////////////////////
// correct
/*
// CustomerDetails Table
$app->post($pattern.'/createNewCustomer/','CustomerDetails:createNewCustomer');
$app->get($pattern.'/getVerificationAccounts/','CustomerDetails:getVerificationAccounts');
$app->post($pattern.'/authentication/','CustomerDetails:authentication');
$app->get($pattern.'/getUser/','CustomerDetails:getUser');
$app->get($pattern.'/getCustomerHistory/','CustomerDetails:getCustomerHistory');
$app->post($pattern.'/updateCustomer/','CustomerDetails:updateCustomer');
$app->post($pattern.'/setAccountStatus/','CustomerDetails:setAccountStatus');
$app->post($pattern.'/setPassword/','CustomerDetails:setPassword');
$app->get($pattern.'/getUnpaidCustomers/','CustomerDetails:getUnpaidCustomers');

// OrderDetails Table
$app->post($pattern.'/createOrder/','OrderDetails:createOrder');
$app->post($pattern.'/updateOrder/','OrderDetails:updateOrder');
$app->get($pattern.'/getOrderHistory/','OrderDetails:getOrderHistory');
$app->get($pattern.'/getUserOrderHistory/','OrderDetails:getUserOrderHistory');
$app->get($pattern.'/getAllOpenOrders/','OrderDetails:getAllOpenOrders');
$app->get($pattern.'/getOpenOrders/','OrderDetails:getOpenOrders');

// TimeSlots Table 
$app->get($pattern.'/availableSlots/','TimeSlots:availableSlots');
$app->post($pattern.'/updateTimeslot/','TimeSlots:updateTimeslot');

// Transactions Table 
$app->get($pattern.'/getTransactionHistory/','Transactions:getTransactionHistory');

// Promocodes Table 
$app->post($pattern.'/createPromocode/','Promocodes:createPromocode');
$app->get($pattern.'/findPromocode/','Promocodes:findPromocode');

// Ratecard Table
$app->get($pattern.'/getRateCard/','RateCard:getRateCard');
$app->post($pattern.'/updateRateCard/','RateCard:updateRateCard');
$app->get($pattern.'/sms/','RateCard:sms');


// Cron Jobs
$app->get($pattern.'/referralcodeProcessor/','CronJobs:referralcodeProcessor');
$app->get($pattern.'/deleteExpiredPromocodes/','CronJobs:deleteExpiredPromocodes');
$app->get($pattern.'/allCronJobs/','CronJobs:allCronJobs');
$app->get($pattern.'/deleteExpiredFestivalPromocodes/','CronJobs:deleteExpiredFestivalPromocodes');
$app->get($pattern.'/deleteExpiredOtherPromocodes/','CronJobs:deleteExpiredOtherPromocodes');
*/

?>