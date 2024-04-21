<?php

namespace App\Utils;


use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use App\Utils\Interfaces\UploaderInterface;



class LocalUploader implements UploaderInterface 
{


	private $targetDirectory;

	public $file;


	public function __construct($targetDirectory)
	{

		$this->targetDirectory = $targetDirectory;


	}


	public function upload($file)
	{

		$video_number = random_int(1,10000000);
		$fileName = $video_number.'.'.$file->guessExtension();

		try {

			$file->move($this->getTargetDirectory(), $fileName);

		} catch (FileException $e) {

		}


		$org_file_name = $this->clear(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));


		return [$fileName, $org_file_name];


	}


	private function getTargetDirectory()
	{

		return $this->targetDirectory;

	}


	private function clear($string)
	{

		$string = preg_replace('/[^A-Za-z0-9- ]+/', '', $string);

		return $string;

	}

	public function delete($path)
	{

		$fileSystem = new Filesystem();

		try {

			$fileSystem->remove('.'.$path);

		} catch (IOExceptionInterface $e) {

			echo "Error deleting file at ".$e->getPath();

		}

		return true;

	}


}