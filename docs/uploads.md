### Uploads
----

Endpoint Class: `PrintifyImage`
```
use Garissman\Printify\PrintifyImage;
$printify_image = new PrintifyImage($printify_api, <Shop ID>);
```
**Requires Shop ID**

----

* Retrieve a list of uploaded images
  <br />
  `$all_images = $printify_images->all($query_options)`
  <br />
  **Parameters**
  <br />
  (optional) 
  ```
  $query_options = [
    'limit' => 100,
    'page' => 1
  ]
  ```
  Output:
  ```
    {
            "id": "5e16d66791287a0006e522b2",
            "file_name": "png-images-logo-1.jpg",
            "height": 5979,
            "width": 17045,
            "size": 1138575,
            "mime_type": "image/png",
            "preview_url": "https://example.com/image-storage/uuid1",
            "upload_time": "2020-01-09 07:29:43"
        },
        {
            "id": "5de50bf612c348000892b366",
            "file_name": "png-images-logo-2.jpg",
            "height": 360,
            "width": 360,
            "size": 19589,
            "mime_type": "image/jpeg",
            "preview_url": "https://example.com/image-storage/uuid2",
            "upload_time": "2019-12-02 13:04:54"
        }
  ```
* Retrieve an uploaded image by id
  <br />
  `$image = $printify_images->find($id)`
  <br />
  Output:
  ```
    {
        "id": "5e16d66791287a0006e522b2",
        "file_name": "png-images-logo-1.jpg",
        "height": 5979,
        "width": 17045,
        "size": 1138575,
        "mime_type": "image/png",
        "preview_url": "https://example.com/image-storage/uuid1",
        "upload_time": "2020-01-09 07:29:43"
    }
  ```
* Upload an image
  <br />
  `$image = $printify_images->create($file_name, $contents, $is_base64)`
  <br />
  `$contents` Can be either a image URL or a base64 encoded image
  <br />
  **Parameters**
  <br />
  (required)
  ```
  $file_name = 'test.png'
  $contents = '<URL>|<Base64 Image>'
  ```
  (requied if base64 contents)
  ```
  $is_base64 = true
  ```
* Archive an uploaded image
  <br />
  `$printify_images->archive($id)`
  <br />
  **Parameters**
  <br />
  (required)
  ```
  $id = '<Image ID>'
  ```
