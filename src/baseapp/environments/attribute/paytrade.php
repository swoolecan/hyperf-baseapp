<?php
return [
    'preferentialUse' => [
        'single' => '单独',
        'single_coupon' => '优惠券单独',
        'single_activity' => '活动单独',
        'share' => '共用',
        'share_coupon' => '优惠券共用',
        'share_activity' => '活动共用',
    ],
    'status' => [
        '' => '下单',
        'cancel' => '订单已取消',
        'paid' => '已支付',
        'receiving' => '备货中',
        'inservice' => '待收货',
        'refund' => '退货',
        'finish' => '完成',
        //'comment' => '已评论',
    ],
    'statusPay' => [
        '' => '未支付',
        'part' => '部分支付',
        'paid' => '支付完成',
    ],
    'statusPick' => [
        '' => '处理中',
        'sending' => '已发货',
        'received' => '已收',
    ],
    'payType' => [
        '' => '在线支付',
        'balance' => '余额支付',
        'face' => '到付',
        'other' => '他人代付',
        'other-mul' => '多人代付',
    ],
    'pickType' => [
        '' => '物流',
        'self' => '自提',
        'store' => '店销',
        'virtual' => '虚拟商品',
    ],
    'paymentCode' => [
        'wechat' => '微信支付',
        'alipay' => '支付宝',
        'underline' => '线下支付',
    ],
    'target' => [
        'account' => '充值到账号',
        'order' => '支付订单',
    ],
    'applyAfter' => [
        'refund' => '退款',
        'refund_part' => '部分退款',
    ],
    'statusAfter' => [
        '' => '等待处理',
        'inhand' => '处理中',
        'finish' => '已完成',
    ],
];
