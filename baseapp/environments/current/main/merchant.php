<?php
return [
    'components' => [
		'urlManager' => [
            'rules' => \common\helpers\RuleFormat::pointRules('merchant', ['admin']),
		],
	],

    'as access' => [
        'allowActions' => [
        ]
	],
];
