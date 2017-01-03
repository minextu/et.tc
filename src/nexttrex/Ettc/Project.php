<?php namespace nexttrex\Ettc;

class Project
{
    private $name;
    private $description;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }
}
