<?php

namespace Printify;
use AriMoralesJordan\Printify\Structures\Image;

class PrintifyImage extends PrintifyBaseEndpoint
{
    protected $_structure = Image::class;

    public function all(array $query_options = []): Collection
    {
        if (empty($query_options) || !array_key_exists('limit', $query_options)) {
            $query_options['limit'] = 100;
        }
        if (isset($query_options['paginate']) && $query_options['paginate']) {
            $this->_api_client->paginate = true;
        }
        $query = PrintifyApiClient::formatQuery($query_options);
        $items = $this->_api_client->doRequest('uploads.json'.$query);
        return $this->collectStructure($items['data']);
    }

    /**
     * Retrieve an uploaded image by id
     *
     * @param string $id
     * @return Image
     */
    public function find($id): Image
    {
        $item = $this->_api_client->doRequest('uploads/'.$id.'.json');
        return new Image($item);
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
        $item = $this->_api_client->doRequest('uploads/images.json', 'POST', $data);
        return new Image($item);
    }

    /**
     * Archive an uploaded image
     *
     * @param string $id
     * @return boolean
     */
    public function archive($id): bool
    {
        $this->_api_client->doRequest('uploads/'.$id.'/archive.json');
        return $this->_api_client->status_code === 200;
    }
}
