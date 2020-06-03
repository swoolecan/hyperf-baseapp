<?php
return [
    'status' => [
        '' => '下单',
        'cancel' => '订单已取消',
        'submit' => '审核订单',
        'check' => '财务审核',
        'reading' => '备货中',
        'express' => '发货中',
        'refund' => '退货',
        'finish' => '完成',
        'halt' => '终止',
        'exception' => '异常',
        //'comment' => '已评论',
    ],
    'statusPay' => [
        'pay_no' => '未支付',
        'pay_doing' => '已付部分费用',
        'pay_finish' => '支付完成',
        'pay_refunded_apply' => '申请退款',
        'pay_refunded' => '已退款',
        'pay_exception' => '支付异常',
    ],
    'statusPick' => [
        'p_reading' => '备货中',
        'p_sending' => '已发货中',
        'p_receiving' => '收货中',
        'p_finish' => '完成',
    ],
	'target' => [
		'account' => '充值到账号',
		'order' => '充值到单',
		'express' => '充值到发货单',
	],
	'statusAccount' => [
		'' => '发起支付',
		'exception' => '异常',
		'cancel' => '取消',
		'double' => '重复-异常',
		'success' => '支付成功',
	],
    'ordersort' => [
        'stock' => '普通订单',
        'scene' => '展销会订单',
        'presell' => '预售',
        'erporder' => 'ERP订单',
    ],
	'isOrder' => [
		0 => '为提交下单',
		1 => '已下单',
	],
];
