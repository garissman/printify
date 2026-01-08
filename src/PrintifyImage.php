<?php

namespace Garissman\Printify;

use Garissman\Printify\Structures\Image;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PrintifyImage extends PrintifyBaseEndpoint
{
    protected string $structure = Image::class;

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function all(array $query_options = []): LengthAwarePaginator|Collection
    {
        if (empty($query_options) || !array_key_exists('limit', $query_options)) {
            $query_options['limit'] = 50;
        }
        $response = $this->client->doRequest('uploads.json', 'GET', $query_options);
        return $this->collectStructure($response->json(), $query_options);
    }

    /**
     * Retrieve an uploaded image by id
     *
     * @param string $id
     * @return Image
     * @throws ConnectionException
     * @throws RequestException
     */
    public function find($id): Image
    {
        $response = $this->client->doRequest('uploads/' . $id . '.json');
        return Image::from($response->json());
    }

    /**
     * Upload an image
     * Upload image files either via image URL or image file base64-encoded contents.
     * The file will be stored in the Merchant's account Media Library.
     *
     * @param string $file_name
     * @param string $contents - The file URL or base64 image
     * @param boolean $is_base64
     * @return Image
     * @throws ConnectionException
     * @throws RequestException
     */
    public function create(string $file_name, string $contents, bool $is_base64 = false): Image
    {
        $data = [
            'file_name' => $file_name
        ];
        if ($is_base64) {
            $data['contents'] = $contents;
        } else {
            $data['url'] = $contents;
        }
        $response = $this->client->doRequest('uploads/images.json', 'POST', $data);
        return Image::from($response->json());
    }

    /**
     * Archive an uploaded image
     *
     * @param string $id
     * @return boolean
     * @throws ConnectionException
     * @throws RequestException
     */
    public function archive($id): bool
    {
        $response = $this->client->doRequest('uploads/' . $id . '/archive.json', 'POST');
        return $response->ok();
    }
}
