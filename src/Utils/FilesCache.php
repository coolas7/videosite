<?php

namespace App\Utils;


use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use App\Utils\Interfaces\CacheInterface;



class FilesCache implements CacheInterface 
{

	public $cache;


	public function __construct()
	{

		$this->cache = new TagAwareAdapter(
			new FilesystemAdapter()
		);

	}

}
