<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmailTemplatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('email_templates')->delete();

        \DB::statement("insert into `email_templates` (`id`, `type`, `subject`, `content`, `info`, `status`, `created_at`, `updated_at`) values('1','reset_password','Reset Password','<div style=\" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';\">\n                <h3 style=\" font-size: 22px; text-transform: capitalize; font-family: Segoe, \'Segoe UI\', \'sans-serif\'; margin-top: 20px;color: #f55d04;\">Reset Your Password</h3>\n                <h3 style=\"font-size:18px;line-height: 25px;font-weight: normal;\">\n                Hi {{name}},\n                </h3>\n                <h3 style=\"font-size:18px;line-height: 25px;font-weight: normal;\">\n                Tap the button below to reset your account password. If you didn\'t request for reset password, you can safely delete this email.\n                </h3>\n                <div style=\"margin: 40px 0; text-align: center;\">\n                <a href=\"{{link}}\" target=\"_blank\" style=\"display: inline-block;padding: 12px 15px;font-family: \'Source Sans Pro\', Helvetica, Arial, sans-serif;font-size: 16px;color: #ffffff;text-decoration: none;border-radius: 6px;width: 130px;background-color:#1c639e;text-align: center;\">Reset Password</a>\n                </div>\n                <p style=\"font-size:17px;line-height: 25px;font-weight: normal;margin-top: 40px;margin-bottom: 40px;color: #555;\">If that doesn\'t work, copy and paste the following link in your browser:{{link}}</p>\n                </div>\n                \n                <div style=\" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';\">\n                <div style=\"font-size: 15px; color: #555;\">\n                <p style=\"font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;\">Cheers,</p>\n                {{app_name}}\n                </div>\n                </div>','{\"name\":\"User full name\",\"link\":\"Link for reset password\",\"app_name\":\"Website name\"}','1','2019-11-13 17:38:27','2020-02-27 12:34:09'),
        ('2','sign_up_confirmation','Sign up Confirmation','<div style=\" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';\">\n                <h3 style=\" font-size: 22px; text-transform: capitalize; font-family: Segoe, \'Segoe UI\', \'sans-serif\'; margin-top: 20px;color: #f55d04;\">Verify your email to start using {{app_name}}</h3>\n                <h3 style=\"font-size:18px;line-height: 25px;font-weight: normal;\">\n                Hi {{name}},\n                </h3>\n                <h3 style=\"font-size:18px;line-height: 25px;font-weight: normal;\">\n                Thank you for signing up. Click the button below to verify your {{app_name}} account.\n                </h3>\n                <div style=\"margin: 40px 0; text-align:center;\">\n                <a href=\"{{link}}\" target=\"_blank\" style=\"display: inline-block;padding: 12px 15px;font-family: \'Source Sans Pro\', Helvetica, Arial, sans-serif;font-size: 16px;color: #ffffff;text-decoration: none;border-radius: 6px;width: 150px;background-color:#1c639e;text-align: center;\">Verify Email Address</a>\n                </div>\n                <p style=\"font-size:17px;line-height: 25px;font-weight: normal;margin-top: 40px;margin-bottom: 40px;color: #555;\">If that doesn\'t work, copy and paste the following link in your browser: <a href=\"{{link}}\" target=\"_blank\">{{link}}</a></p>\n                <p style=\"font-size:17px;line-height: 25px;font-weight: normal;color: #555;\">If you did not create an account, no further action is required.</p>\n                </div>\n                \n                <div style=\" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';\">\n                <div style=\"margin-top: 30px;  font-size: 15px; color: #555;\">\n                <p style=\"font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;\">Cheers,</p>\n                {{app_name}}\n                </div>\n                </div>','{\"name\":\"User full name\",\"link\":\"Link for Verify Email Address\",\"app_name\":\"Website name\"}','1','2019-12-03 18:28:21','2020-02-27 12:33:29'),
        ('3','send_password','Account Password','<div style=\" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';\">\n                <h3 style=\" font-size: 22px; text-transform: capitalize; font-family: Segoe, \'Segoe UI\', \'sans-serif\'; margin-top: 20px;color: #f55d04;\">Account Password</h3>\n                <h3 style=\"font-size:18px;line-height: 25px;font-weight: normal;\">\n                Hi {{name}},\n                </h3>\n                <p style=\"font-size: 17px; line-height: 25px; margin-top: 40px; margin-bottom: 40px; color: rgb(85, 85, 85);\"><span style=\"font-weight: normal;\">To login your account, please use the following password: </span><b>{{password}}</b></p>\n                <p style=\"font-size: 17px; line-height: 25px; margin-top: 40px; margin-bottom: 40px; color: rgb(85, 85, 85);\"><span style=\"font-weight: normal;\">Do not share this password with anyone. {{app_name}} takes your account security very seriously. {{app_name}} will never ask you to disclose your password.</span></p>\n                </div>\n                \n                <div style=\" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';\">\n                <div style=\"font-size: 15px; color: #555;\">\n                <p style=\"font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;\">Cheers,</p>\n                {{app_name}}\n                </div>\n                </div>','{\"name\":\"User full name\",\"app_name\":\"Website name\",\"password\":\"Account Password\"}','1','2020-02-28 07:34:55','2020-02-28 07:58:18'),
        ('4','paid_package_upgrade_downgrade_by_admin','unPaid Package Upgrade / Downgrade By Admin','<div style=\" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';\">\n                <h3 style=\" font-size: 22px; text-transform: capitalize; font-family: Segoe, \'Segoe UI\', \'sans-serif\'; margin-top: 20px;color: #f55d04;\">Package Downgraded By admin</h3>\n                <h3 style=\"font-size:18px;line-height: 25px;font-weight: normal;\">\n                Hi {{name}},\n                </h3>\n                <h3 style=\"font-size:18px;line-height: 25px;font-weight: normal;\">\n                Your Account Package was downgraded by the admin. If that was a mistake, please contact</h3>\n                </div>\n                \n                <div style=\" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';\">\n                <div style=\"margin-top: 30px;  font-size: 15px; color: #555;\">\n                <p style=\"font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;\">Cheers,</p>\n                {{app_name}}\n                </div>\n                </div>','{\"app_name\":\"Website name\",\"name\":\"User full name\",\"from\":\"Previous package\",\"to\":\"New package\", \"previous_type\": \"Type of previous package\", \"new_type\":\"Type of new package\"}','1','2019-12-03 18:28:21','2020-02-27 12:33:29'),
        ('5','payment_success','Payment Successful','<div style=\" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';\">\r\n                <h3 style=\" font-size: 22px; text-transform: capitalize; font-family: Segoe, \'Segoe UI\', \'sans-serif\'; margin-top: 20px;color: #f55d04;\">Payment Successful</h3>\r\n                <h3 style=\"font-size:18px;line-height: 25px;font-weight: normal;\">\r\n                Hi {{name}},\r\n                </h3>\r\n                <h3 style=\"font-size:18px;line-height: 25px;font-weight: normal;\">\r\n                Your package payment has been received successfully!</h3>\r\n                </div>\r\n                \r\n                <div style=\" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';\">\r\n                <div style=\"margin-top: 30px;  font-size: 15px; color: #555;\">\r\n                <p style=\"font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;\">Cheers,</p>\r\n                {{app_name}}\r\n                </div>\r\n                </div>','{\"name\":\"User full name\",\"app_name\":\"Website name\"}','1','2019-12-03 18:28:21','2022-01-18 12:39:16'),
        ('6','package_switch_notification','Package Switched','<div style=\" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';\">\n                <h3 style=\" font-size: 22px; text-transform: capitalize; font-family: Segoe, \'Segoe UI\', \'sans-serif\'; margin-top: 20px;color: #f55d04;\">Package Switched</h3>\n                <h3 style=\"font-size:18px;line-height: 25px;font-weight: normal;\">\n                Hi {{name}},\n                </h3>\n                <h3 style=\"font-size:18px;line-height: 25px;font-weight: normal;\">\n                Your marketing package has been changed.</h3><p style=\"font-size: 18px; line-height: 25px; font-weight: normal;\">If that was a mistake, please contact support.</p>\n                </div>\n                \n                <div style=\" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';\">\n                <div style=\"margin-top: 30px;  font-size: 15px; color: #555;\">\n                <p style=\"font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;\">Cheers,</p>\n                {{app_name}}\n                </div>\n                </div>','{\"name\":\"User full name\",\"old_package\":\"Old package name\",\"new_package\":\"New package name\",\"link\":\"Login link\",\"app_name\":\"Website name\",\"platform\":\"Platform name\"}','1','2019-12-03 18:28:21','2020-02-27 12:33:29'),
        ('7','account_inactive','Account Info','<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hello,&nbsp;{{name}}</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;We are going to&nbsp;{{action}}&nbsp;your account within&nbsp;{{days}}&nbsp;days!</p><p><br></p>','{\"name\":\"User full name\",\"days\":\"in number of days accout will be deactivated.\",\"action\":\"Deleted or Inactive\"}','1','2019-12-03 18:28:21','2022-01-25 09:37:30'),
        ('8','lite_account_created_on_other_platforms','Lite account created on other platforms','<div style=\" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';\">\r\n                <h3 style=\" font-size: 22px; text-transform: capitalize; font-family: Segoe, \'Segoe UI\', \'sans-serif\'; margin-top: 20px;color: #f55d04;\">Lite account created on other platforms</h3>\r\n                <h3 style=\"font-size:18px;line-height: 25px;font-weight: normal;\">\r\n                Hi {{name}},\r\n                </h3>\r\n                <h3 style=\"font-size: 18px; line-height: 25px;\"><p style=\"margin-top: 40px; margin-bottom: 40px; font-size: 17px; line-height: 25px;\">You have got a lite account of&nbsp;<span style=\"font-size: 18px;\"><b>{{platforms}}</b></span><span style=\"font-weight: normal; color: inherit; font-family: inherit;\">.</span><span style=\"font-weight: normal; color: inherit; font-family: inherit;\">&nbsp;You can just go to the platforms and sign in with the same credentials.</span></p></h3><h3 style=\"font-size:18px;line-height: 25px;font-weight: normal;\"><p style=\"margin-top: 40px; margin-bottom: 40px; font-size: 17px; line-height: 25px;\"><span style=\"font-weight: bolder;\">Note:</span>&nbsp;If you already have an account on a specific platform then you can use your existing credentials.</p></h3>\r\n                </div>\r\n                \r\n                <div style=\" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';\">\r\n                <div style=\"margin-top: 30px;  font-size: 15px; color: #555;\">\r\n                <p style=\"font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;\">Cheers,</p>\r\n                {{app_name}}\r\n                </div>\r\n                </div>','{\"name\":\"User full name\",\"app_name\":\"Website name\"}','1','2019-12-03 18:28:21','2022-01-18 12:44:50'),
        ('9','package_downgrade_after_subscription_expired','Package Downgraded','<div style=\" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';\">\n                <h3 style=\" font-size: 22px; text-transform: capitalize; font-family: Segoe, \'Segoe UI\', \'sans-serif\'; margin-top: 20px;color: #f55d04;\">Package Downgraded By admin</h3>\n                <h3 style=\"font-size:18px;line-height: 25px;font-weight: normal;\">\n                Hi {{name}},\n                </h3>\n                <h3 style=\"font-size:18px;line-height: 25px;font-weight: normal;\">\n                Your Account Package was downgraded by the admin. If that was a mistake, please contact</h3>\n                </div>\n                \n                <div style=\" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';\">\n                <div style=\"margin-top: 30px;  font-size: 15px; color: #555;\">\n                <p style=\"font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;\">Cheers,</p>\n                {{app_name}}\n                </div>\n                </div>','{\"name\":\"User full name\",\"from\":\"From package name\",\"to\":\"To package name\",\"upgrade_link\":\"Package upgrade link\",\"contact_link\":\"Contact us page link\",\"app_name\":\"Website name\"}','1','2019-12-03 18:28:21','2020-02-27 12:33:29'),
        ('10','account_info','Account Info','<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hello,&nbsp;{{name}}</p><p>&nbsp;&nbsp;&nbsp;&nbsp;Your email marketing account status has been changed to&nbsp;{{action}}.</p><p><br></p>','{\"name\":\"User full name\",\"action\":\"Deleted or Inactive\"}','1','2019-12-03 18:28:21','2022-01-25 09:41:23'),
        ('11','packages_expiry_follow_up_email','Account Expiry Info','<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hello,&nbsp;{{name}}</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;We are going to&nbsp;{{action}}&nbsp;your account within&nbsp;{{days}}&nbsp;days!</p><p><br></p>','{ \"name\":\"User full name\", \"remaining_days\":\"Subscription days remaining\", \"expiry_date\":\"Subscription expiry date\", \"upgrade_link\":\"upgrade link\", \"contact_link\":\"Contact Link\", \"app_name\":\"App name\"  }','1','2019-12-03 18:28:21','2022-01-25 09:37:30'),
        ('12','campaign_sent_notification','Campaign Sent Notification','<div style=\" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';\">\r\n                <h3 style=\" font-size: 22px; text-transform: capitalize; font-family: Segoe, \'Segoe UI\', \'sans-serif\'; margin-top: 20px;color: #f55d04;\"><span style=\"font-size: 14px;\">﻿</span>Campaign Sent</h3>\r\n                <h3 style=\"font-size:18px;line-height: 25px;font-weight: normal;\">\r\n                Hi {{name}},\r\n                </h3>\r\n                <h3 style=\"font-size: 18px; line-height: 25px;\"><span style=\"font-weight: normal;\">\r\n                One of your {{campaign_type}}s: </span><b>\"{{campaign_name}}\"</b><span style=\"font-weight: normal;\">&nbsp;is sent.</span></h3><h3 style=\"font-size: 18px; line-height: 25px;\"><span style=\"font-weight: normal; font-size: 14px;\">Campaign created at: {{created_at}}</span></h3><h3 style=\"font-size: 18px; line-height: 25px;\"><span style=\"font-weight: normal; font-size: 14px;\">Campaign sent at: {{updated_at}}</span></h3>\r\n                </div>\r\n                \r\n                <div style=\" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';\">\r\n                <div style=\"font-size: 15px; color: #555;\">\r\n                <p style=\"font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;\">Cheers,</p>\r\n                {{app_name}}\r\n                </div>\r\n                </div>','{\"name\":\"User full name\",\"campaign_type\":\"Campaign type\",\"campaign_name\":\"Campaign name\",\"created_at\":\"Campaign created\",\"sent_at\":\"Campaign sent at\",\"app_name\":\"Website name\"}','1','2019-11-13 17:38:27','2022-02-20 14:55:30'),
        ('14','unpaid_package_upgrade_downgrade_by_admin','Paid Package Upgrade / Downgrade By Admin','<div style=\" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';\">\r\n<h3 style=\"font-size:18px;line-height: 25px;font-weight: normal;\"><span style=\"color: inherit; font-family: inherit;\">Dear {{name}},</span><br></h3></div><div style=\" padding:10px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';\">                \r\n<h3 style=\"font-size:18px;line-height: 25px;font-weight: normal;\"><span style=\"color: rgb(85, 85, 85); font-family: Segoe, &quot;Segoe UI&quot;, sans-serif; font-size: 17px;\">Your package change request has been initiated by the administrator. Tab the button below if you accept to proceed.</span><br></h3>\r\n<div style=\"margin: 40px 0; text-align: center;\">\r\n<a href=\"{{link}}\" target=\"_blank\" style=\"display: inline-block;padding: 12px 15px;font-family: \'Source Sans Pro\', Helvetica, Arial, sans-serif;font-size: 16px;color: #ffffff;text-decoration: none;border-radius: 6px;width: 130px;background-color:#009a71;text-align: center;\">Payment Checkout</a></div>\r\n</div>\r\n\r\n<div style=\" padding:30px 30px 10px;  font-family: Segoe, \'Segoe UI\', \'sans-serif\';\">\r\n<div style=\"font-size: 15px; color: #555;\">\r\n<p style=\"font-size: 15px; font-style: italic; font-weight: 600; margin-bottom: 0;\">Regards,</p>\r\n{{app_name}}\r\n</div>\r\n</div>','{\"app_name\":\"Website name\",\"name\":\"User full name\",\"link\":\"Checkout Url\"}','1','2019-12-03 18:28:21','2020-02-27 12:33:29');");
    }
}