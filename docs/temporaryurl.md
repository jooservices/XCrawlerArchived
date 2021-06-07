## TemporaryUrl
This table used for temporary Urls

## Onejav
- Used for `new` URL to know which page we are working on
- Update state `Completed` when have done

## R18
- Used for `released` URL to know which page we are working on and how many pages we have
- Update state `Completed` when have done

## XCity idols
> This one have bit more complicated
- `idols` will create `TemporaryUrl` for `pages` if no records
- `idols` will process on each `pages`
  - Get `links` for each `page` and save to `TemporaryUrl`
- `idol` will get `links` from `TemporaryUrl` for process and save detail to `Idol`
  - Then update `TemporaryUrl` as `completed` or `failed`
