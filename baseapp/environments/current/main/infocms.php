<?php
return [
    'components' => [         
        'urlManager' => [     
            'rules' => \common\helpers\RuleFormat::pointRules('infocms', ['fpromotion', 'fcommission']),      
        ],
    ],
    'modules' => [            
        'fpromotion' => ['class' => 'infocms\fpromotion\Module'],
        'fcommission' => ['class' => 'infocms\fcommission\Module'],
    ],
];
