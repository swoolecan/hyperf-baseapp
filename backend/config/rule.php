<?php
return [
    'adminindex' => [
		'noDomain' => true,
        'data' => ['suffix' => '/', 'pattern' => '/', 'route' => '/site-manager/index'],
    ],
	'entrance' => [
		'noDomain' => true,
		'data' => [
			'pattern' => '/<action:(signin|logout)>',
		    'route' => '/entrance/<action>',
		],
	],
	'upload-operaton' => [
		'only' => ['backend'],
		'data' => [
			'pattern' => '/upload/<attachment_code>/<action:(show|download|upeditor)>',
			'route' => '/upload/<action>',
		],
	],
	'upload-index' => [
		'only' => ['backend'],
		'data' => [
			'pattern' => '/upload/<attachment_code>/<table>/<field>',
			'route' => '/upload/index',
		],
	],
    'adminbase' => [
		'noDomain' => true,
        'data' => [
            'pattern' => '/<controller>/<action>',
            'route' => '/<controller>/<action>', 
        ],
    ],
    'admin' => [
		'noDomain' => true,
        'data' => [
            'pattern' => '/<sort>/<controller>/<action>',
            'route' => '/<sort>/<controller>/<action>', 
        ],
    ],
    'adminmodule' => [
		'noDomain' => true,
        'data' => [
            'pattern' => '/<sort>/<submodule>/<controller>/<action>',
            'route' => '/<sort>/<submodule>/<controller>/<action>', 
        ],
    ],
];
