<?php
/**
 * Input.php
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

//namespace Overtrue\Wechat;

//use Overtrue\Wechat\Utils\Bag;
require_once(dirname(__FILE__) . '/Utils/Bag.php');

class Input extends Bag
{

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct(array_merge($_GET, $_POST));
    }
}
