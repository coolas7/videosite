<?php

namespace App\Utils;


use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use App\Utils\Interfaces\CacheInterface;



class RedisCache implements CacheInterface 
{

	public $cache;


	public function __construct()
	{

		$this->cache = new TagAwareAdapter(
			new RedisAdapter(
				RedisAdapter::createConnection('redis://localhost/1')
			)
		);

	}

}