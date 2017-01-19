<?php namespace Minextu\EttcUi;

/**
 * A class extending this will contain the data for the model-view-presenter structure
 */
abstract class AbstractModel
{
    /**
     * Will be called after setView and setModel
     */
    public function init()
    {
    }
}
