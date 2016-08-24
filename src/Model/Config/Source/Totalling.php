<?php
/* ExtName
 *
 * User        karen
 * Date        8/20/16
 * Time        12:44 PM
 * @category   Webshopapps
 * @package    Webshopapps_ExtnName
 * @copyright   Copyright (c) 2016 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2016, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */

namespace WebShopApps\ProductRate\Model\Config\Source;


class Totalling implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \WebShopApps\ProductRate\Model\Carrier\ProductRate
     */
    protected $_carrierProductRate;

    /**
     * @param \WebShopApps\ProductRate\Model\Carrier\ProductRate $carrierProductRate
     */
    public function __construct(\WebShopApps\ProductRate\Model\Carrier\ProductRate $carrierProductRate)
    {
        $this->_carrierProductRate = $carrierProductRate;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $arr = [];
        foreach ($this->_carrierProductRate->getCode('totalling') as $k => $v) {
            $arr[] = ['value' => $k, 'label' => $v];
        }
        return $arr;
    }
}
