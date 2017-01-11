<?php namespace nexttrex\Ettc;

class Project
{
    private $title;
    private $description;

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }
}
