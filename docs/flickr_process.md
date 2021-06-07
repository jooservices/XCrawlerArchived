## Flickr
- Init with `flickr:contacts`

- **FlickrContact** observers
    - `STATE_INIT` dispatch `PhotosJob`
        - Get all photos of contacts
        - Update contact.state_code `STATE_PHOTOS_COMPLETED`
    - `STATE_PHOTOS_COMPLETED` dispatch `ContactAlbumbsJob`
        - Get all albums
        - Update contact.state_code `STATE_ALBUM_COMPLETED`

Whenever FlickrContact.`STATE_ALBUM_COMPLETED` we'll reset loop back to `STATE_INIT`

- **FlickrAlbum** observers
    - `STATE_INIT` dispatch `AlbumPhotosJob`
        - Get all photos of album
        - Update album.state_code `STATE_PHOTOS_COMPLETED`


- `flickr:contact-info`
    - Actually this command will be removed soon
    - For moment this command used to process all pending contacts


- `flickr:photo-sizes`
    - This command used to get sizes of photos
    - We can't observer for FlickrPhoto to get sizes because will generate too much jobs
    
- `flickr:album-photos`
    - Actually this command will be removed soon
    - For moment this command used to process all pending albums
