<?php
/**
 * Marko Martinović
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
 * Please do not edit or add to this file if you wish to upgrade
 * Magento or this extension to newer versions in the future.
 * I give my best to conform to "non-obtrusive, best Magento practices"
 * style of coding. However, I do not guarantee functional accuracy of specific
 * extension behavior.Additionally Itake no responsibility for any possible
 * issue(s) resulting from extension usage. I reserve the full right not to
 * provide any kind of support for my free code. Thank you for your understanding.
 *
 * @category Mmartinovic
 * @package Productlist
 * @author Marko Martinović <marko.martinovic@inchoo.net>
 * @copyright Copyright (c) Marko Martinović (http://www.techytalk.info/)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class Atol_Productfeedgenerator_IndexController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

}