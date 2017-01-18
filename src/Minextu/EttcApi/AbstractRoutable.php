<?php namespace Minextu\EttcApi;

use Minextu\Ettc\Ettc;
use Respect\Rest\Routable;

abstract class AbstractRoutable implements Routable
{
    protected $ettc;
    final public function __construct($ettc)
    {
        $this->ettc = $ettc;
    }
}
