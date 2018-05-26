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
			$this->c->image->make(uploads_path($image->uuid))->encode('png')
		);

		return $this->respondWithHeaders($response);
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
}
