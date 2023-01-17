<?php

return [
	'mode'                  => 'utf-8',
	'format'                => 'A4',
	'author'                => '',
	'subject'               => '',
	'keywords'              => '',
	'creator'               => 'Laravel Pdf',
	'display_mode'          => 'fullpage',
	'tempDir'               => public_path('storage/temp/'),
	'font_path' 			=> public_path('fonts'),
	'font_data' => [
		'chinesefont' => [
			'R'  => 'SimplifiedChineseFonts.ttf',    // regular font
		],
	]
];
