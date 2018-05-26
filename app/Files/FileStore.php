<?php

namespace App\Files;

use App\Models\Image;
use Exception;
use Slim\Http\UploadedFile;

class FileStore
{
	protected $model = null;

	public function getStored(){
		return $this->model;
	}

	public function store(UploadedFile $file)
	{
		try{
			$model = $this->createModel($file);
			$file->moveTo(uploads_path($model->uuid));
		} catch (Exception $e) {

		}

		return $this;
	}

	protected function createModel(UploadedFile $file)
	{
		return $this->model = Image::create([
			'uuid' => '550e8400-e29b-41d4-a716-446655440000'
		]);
	}
}