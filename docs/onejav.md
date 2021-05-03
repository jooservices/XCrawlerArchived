
## Crawler
- Used `XCrawlerClient` with `onejav` logging
- Onejav don't get detail, because we already have enough data in list view.
- All commands will calling to `OnejavService`

## Command
- Daily to get items day by day `Carbon::now()->format('Y/m/d')`
  - Create `XCrawlerLog` with `source` = `onejav.new`
- New to get all items in `/new`
  - Called job for process
  - Page counted by use `TemporaryUrl`.`current_page`
  - Whenever we have done will update state_code to `Completed`
    
## Job
- We only have 1 job `app/Jobs/OnejavFetchJob.php` to get items by URL
  - Unique job for 3.600 second
  - Allow 60 jobs / minute and release back to pool after 60 minutes  
  - Create `XCrawlerLog` with `source` = `onejav.new`

## Database
- Create new item with unique `url`

## Works need to be done
- Automatic reset page count for `new`
