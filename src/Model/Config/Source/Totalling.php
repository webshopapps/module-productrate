<?php
/**
 * WebShopApps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * WebShopApps ProductRate
 *
 * @category WebShopApps
 * @package WebShopApps_ProductRate
 * @copyright Copyright (c) 2016 Zowta LLC (http://www.WebShopApps.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author WebShopApps Team sales@webshopapps.com
 *
 */
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace WebShopApps\ProductRate\Model\Config\Source;

class Totalling implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \WebShopApps\ProductRate\Model\Carrier\ProductRate
     */
    private $carrierProductRate;

    /**
     * @param \WebShopApps\ProductRate\Model\Carrier\ProductRate $carrierProductRate
     */
    public function __construct(\WebShopApps\ProductRate\Model\Carrier\ProductRate $carrierProductRate)
    {
        $this->carrierProductRate = $carrierProductRate;
    }

	/**
	 * @return array
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
    public function toOptionArray()
    {
        $arr = [];
        foreach ($this->carrierProductRate->getCode('totalling') as $k => $v) {
            $arr[] = ['value' => $k, 'label' => $v];
        }
        return $arr;
    }
}
