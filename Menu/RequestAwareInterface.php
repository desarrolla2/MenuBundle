<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 19/10/17
 * Time: 14:46
 */

namespace Desarrolla2\MenuBundle\Menu;

use Symfony\Component\HttpFoundation\Request;

interface RequestAwareInterface
{
    public function setRequest(Request $request);
}