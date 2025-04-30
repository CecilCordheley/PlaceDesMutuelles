<?php
namespace Async;

abstract class asyncFunction{
    private $args;
    public function __construct(){
        $this->args=[];
    }
    public function handle($args){
        $this->args=$args;
    }
}