<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class HomeContentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('home_contents')->delete();

        \DB::statement(
            "INSERT INTO `home_contents` (`id`, `name`, `description`, `image`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
            (11, 'Banner Text', '<h3>WE MAKE</h3>\r\n                <h1 class=\"all-h1\">\r\n                  <span class=\"green\"> Email Marketing</span>\r\n                </h1>\r\n                <h1 class=\"all-h1\">Software Simple.</h1>\r\n                <p>\r\n                  Aenean sed nibh a magna posuere tempor. Nunc faucibus\r\n                  pellentesque nunc in aliquet. Donec congue, nunc vel tempor\r\n                  congue, enim sapien lobortis ipsum, in volutpat sem ex in\r\n                  ligula.\r\n                </p>', NULL, 1, NULL, '2021-10-31 05:53:02', '2021-10-31 05:53:02'),
            (1, 'Banner Video Link', 'https://www.youtube.com/', NULL, 1, NULL, '2021-10-29 05:58:41', '2021-10-29 05:58:41'),
            (2, 'Video Banner Text', '<p>\r\n              Interested how our software works for you? \r\n              <a href=\"https://www.youtube.com/watch?v=R1mb-eDEfm0\" class=\"video-hover\">\r\n                Watch out 1 minute video\r\n              </a>\r\n            </p>', NULL, 1, NULL, '2021-10-29 06:01:19', '2021-10-29 09:00:45'),
            (3, 'Grow Section Text', '<div class=\"growing-content\">\r\n                <h2 class=\"all-h2\">Start Grow With Email Marketing</h2>\r\n                <p>\r\n                  Aenean sed nibh a magna posuere tempor. Nunc faucibus\r\n                  pellentesque nunc in aliquet.\r\n                </p>\r\n                <a href=\"/signup\">\r\n                <button type=\"button\" class=\"growing-btn\">\r\n                  <span>Get Started Now</span>\r\n                </button>\r\n                </a>\r\n              </div>', NULL, 1, NULL, '2021-10-29 06:02:54', '2021-12-20 10:58:45'),
            (4, 'Grow section Data', '<div class=\"growing-content growing-content-padding ms-md-auto\">\r\n                <h2 class=\"all-h2\">1M+</h2>\r\n                <p class=\"gr-p\">\r\n                  Customers visit Albino every month to get their service done.\r\n                </p>\r\n              </div>\r\n              <div class=\"growing-content growing-content-padding ms-md-auto\">\r\n                <h2 class=\"all-h2\">92%</h2>\r\n                <p class=\"gr-p\">\r\n                  Satisfaction rate comes from our awesome customers.\r\n                </p>\r\n              </div>\r\n              <div class=\"growing-content growing-content-padding ms-md-auto\">\r\n                <h2 class=\"all-h2\">4.9/5.0</h2>\r\n                <p class=\"gr-p gr-p-margin\">\r\n                  Average customer ratings we have got all over internet.\r\n                </p>\r\n              </div>', NULL, 1, NULL, '2021-10-29 06:03:46', '2021-10-29 06:03:46'),
            (5, 'How it works Text', '<h2 classname=\"all-h2\">How It Works</h2>\r\n            <p>\r\n              That’s why we’ve build an email marketing software that’s easy to\r\n              use, save your time and get results.\r\n            </p>', NULL, 1, NULL, '2021-10-29 06:04:51', '2021-10-29 06:05:05'),
            (6, 'How it works explaination', '<div class=\"col-md-3 col-12\"><div class=\"works-content works-content-border\"><div class=\"no-effect works-content-icon\"><img class=\"img-fluid  works-icons\" src=\"/images/rocket.svg?35e2f8a50204e10cdab96f3a714576da\" alt=\" Site Logo\"></div><h3>Easy Integration</h3><p>Sed a magna semper, porta purus eu, ullamcorper ligula. Nam sit amet consectetur sapien.</p></div></div><div class=\"col-md-3 col-12\"><div class=\"works-content works-content-border-two\"><div class=\"works-content-icon\"><img class=\"img-fluid works-icons\" src=\"/images/megaphone.svg?f427cbc02f75cfc8b954e8fc8bf1b8d8\" alt=\" Site Logo\"></div><h3>Campaigns</h3><p>Sed a magna semper, porta purus eu, ullamcorper ligula. Nam sit amet consectetur sapien.</p></div></div><div class=\"col-md-3 col-12\"><div class=\"works-content works-content-border\"><div class=\"works-content-icon\"><img class=\"img-fluid works-icons\" src=\"/images/mind.svg?0c7ceaad0b6ddb95b8003f9936a2e6f2\" alt=\" Site Logo\"></div><h3>Peace Of Mind</h3><p>Sed a magna semper, porta purus eu, ullamcorper ligula. Nam sit amet consectetur sapien.</p></div></div><div class=\"col-md-3 col-12\"><div class=\"works-content\"><div class=\"no-effect works-content-icon\"><img class=\"img-fluid works-icons\" src=\"/images/clock.svg?0162773e46243161f12ecd6bb365f7e9\" alt=\" Site Logo\"></div><h3>Less Time</h3><p>Sed a magna semper, porta purus eu, ullamcorper ligula. Nam sit amet consectetur sapien.</p></div></div>', NULL, 1, NULL, '2021-10-29 06:06:54', '2021-10-29 06:11:20'),
            (7, 'Offering section Text', '<h2 class=\"all-h2\">We’re Offering You Best Services For Your Marketing</h2><p>That’s why we’ve build an email marketing software that’s easy to use, save your time and get results.</p>', NULL, 1, NULL, '2021-10-29 06:08:24', '2021-10-29 10:56:00'),
            (8, 'Analytics Section Flow', '<div class=\"col-md-3\"><div class=\"analytics-content analytics-content-border text-center\"><h3 class=\"text-center\">1</h3><h4>Setup A Campaign In Minutes</h4></div></div><div class=\"col-md-3\"><div class=\"analytics-content analytics-content-border text-center\"><h3 class=\"text-center\">2</h3><h4>Target Your Specific Audience</h4></div></div><div class=\"col-md-3\"><div class=\"analytics-content analytics-content-border text-center\"><h3 class=\"text-center\">3</h3><h4>Interactively Change Copy And Creative</h4></div></div><div class=\"col-md-3\"><div class=\"analytics-content text-center\"><h3 class=\"text-center\">4</h3><h4>Launch Ad Campaign And Watch Reports</h4></div></div>', NULL, 1, NULL, '2021-10-29 06:14:25', '2021-10-29 06:14:25'),
            (9, 'Footer Text', '<p>\r\n                  Lorem Ipsum is simply dummy text of the printing and\r\n                  typesetting industry. Lorem Ipsum has been the industry’s\r\n                  standard dummy text.\r\n                </p>', NULL, 1, NULL, '2021-10-29 10:09:50', '2021-10-29 10:10:01'),
            (10, 'FAQ Text', 'Got question? We’ve got answers! All teams can use our awesome\r\n                  add recipients to campaigns, create leads and even react in\r\n                  real.', NULL, 1, NULL, '2021-10-29 12:40:58', '2021-12-17 17:38:39');
            "
        );
    }
}
