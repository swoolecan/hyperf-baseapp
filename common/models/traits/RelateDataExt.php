<?php

namespace common\models\traits;

trait RelateDataExt
{
	// league
	public function getProductLeagueData() { return $this->_getPointModelData('product-league', $this->product_id); }
	public function getMerchantLeagueData() { return $this->_getPointModelData('merchant-league', $this->merchant_id); }
	public function getCategoryLeagueData() { return $this->_getPointModelData('category-league', $this->category_code, 'code'); }
	public function getMerchantDecorationData() { return $this->_getPointModelData('merchant-decoration', $this->merchant_id); }
    public function getUserDecorationData() { return $this->_getPointModelData('user-decoration', $this->user_id); }

    // groupon
	public function getHouseData() { return $this->_getPointModelData('house-decoration', $this->house_id); }
	public function getGrouponData() { return $this->_getPointModelData('groupon-groupon', $this->groupon_id); }
	public function getProductGrouponData() { return $this->_getPointModelData('product-groupon', $this->product_id); }
	public function getWebsiteGrouponData() { return $this->_getPointModelData('website-groupon', $this->website_id); }
	public function getCouponGrouponData() { return $this->_getPointModelData('coupon-groupon', $this->coupon_id); }

    // commission
	public function getMallData() { return $this->_getPointModelData('mall-commission', $this->mall_id); }
	public function getSubjectCommissionData() { return $this->_getPointModelData('subject-commission', $this->subject_id); }
	public function getBuyerData() { return $this->_getPointModelData('buyer-commission', $this->buyer_id); }
	public function getSortcmsCommissionData() { return $this->_getPointModelData('sortcms-commission', $this->sort); }
	public function getBrandCommissionData() { return $this->_getPointModelData('brand-commission', $this->brand_code, 'code'); }

    // other
    public function getBookData() { return $this->_getPointModelData('book', $this->book_code, 'code'); }

	public function getAdviserDatas($type = '')
	{
		$infos = (array) $this->getPointInfos('merchant', ['where' => ['is_advertiser' => 1, 'status' => '']]);
		if ($type == 'id') {
			return array_keys($infos);
		}
		return $infos;
	}
}
