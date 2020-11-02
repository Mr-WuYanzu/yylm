<?php
namespace app\index\controller;
use think\Controller;
class Index extends Controller
{
	
	public function _empty($name)
    {
        return $this->fetch('/Public/404');
    }
    public function index()
    {
        echo "Hello world";
    }
	
}
