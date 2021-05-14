# Flickr states

## Contacts
`public const STATE_INIT = 'FCIN';` // Contact have just created

`public const STATE_INFO_COMPLETED = 'FCIC';` // Contact filled  with detail

`public const STATE_PHOTOS_PROCESSING = 'FCPP';` // Fetching photos

`public const STATE_PHOTOS_COMPLETED = 'FCPC';` // Photos fetch completed

_If we have no more `STATE_INIT` then reset this state back to `STATE_INIT`_

`public const STATE_PHOTOS_FAILED = 'FCPF';` // Photos fetch failed

_We need to do something_
