<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Files\FileStore;
use App\Models\Image;
use Exception;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

class ImageController extends Controller
{
	private $imageExtension = 'png';
	private $maxSize = 800;
	private $minSize = 10;
	private $defaultSize = 100;

	public function store(Request $request, Response $response, $args)
	{
		if (!$upload = $request->getUploadedFiles()['file'] ?? null) {
			return $response->withStatus(422);
		}

		try{
			$this->c->image->make($upload->file);
		} catch (Exception $e) {
			return $response->withStatus(422);
		}

		$fileStore = (new FileStore())->store($upload);

		return $response->withJson([
			'data' => [
				'uuid' => $fileStore->getStored()->uuid
			]
		]);
	}

	public function show(Request $request, Response $response, $args)
	{
		extract($args);
		try {
			$image = Image::where('uuid', $uuid)->firstOrFail();
		} catch (Exception $e) {
			return $response->withStatus(422);
		}

		$response->getBody()->write(
			$this->getProcessedImage($request, $image)
		);

		return $this->respondWithHeaders($response);
	}

	protected function getProcessedImage(Request $request, Image $image)
	{
		return $this->c->image->cache(function ($builder) use ($request, $image) {
			$this->processImage(
				$request,
				$builder->make(uploads_path($image->uuid))
			);
		});
	}

	protected function processImage(Request $request, $builder)
	{
		return $builder->resize(null, $this->getRequestedSize($request), function ($constraint) {
			$constraint->aspectRatio();
		})->encode('png');
	}

	protected function respondWithHeaders(Response $response)
	{
		foreach ($this->getResponseHeaders() as $name => $value) {
			$response = $response->withHeader($name, $value);
		}

		return $response;
	}

	protected function getResponseHeaders()
	{
		return [
			'Content-Type' => $this->imageExtension
		];
	}

	protected function getRequestedSize(Request $request)
	{
		return max(min($request->getParam('s'), $this->maxSize)??$this->defaultSize, $this->minSize);
	}

}
