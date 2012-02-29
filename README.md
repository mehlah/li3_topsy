# Topsy

li3\_topsy offers integration between [Lithium] [lithium] and [Topsy's Otter API] [topsy].

Otter API is a RESTful HTTP web service to Topsy. Topsy is a search engine that ranks links, photos and tweets by the number and quality of retweets they receive. The quality of retweets is determined by the influence of the Twitter users.

The Otter API provides access to topsy search results, url information and author information along with the intermediate data (like author influence) that is used in creation of search rankings. http://topsy.com/ has been created using the Otter API, and almost everything available on the site is accessible to developers.

## Installation

Theres a couple ways to get setup.

The best way is to add li3\_topsy as a Git submodule, in order
to keep up with the latest upgrades.

An alternative option is just to download li3\_topsy files under your main `libraries`
folder, or your `app/libraries` folder. You need to enable it by placing the
following at the end of your `app/config/bootstrap/libraries.php` file:

```php
Libraries::add('li3_topsy');
```

## Usage

Writing in progress...


[lithium]: http://lithify.me
[topsy]: http://topsy.com