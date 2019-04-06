# httpEcho.php

A small collection of PHP scripts for rendering HTTP client, request, and server details as JSON, pretty-printed JSON, plain text ([Markdown](https://en.wikipedia.org/wiki/Markdown) really), or HTML.

## Installation

1. Expand code into folder of your choice.
2. Use [Composer](https://getcomposer.org/) to install the prerequisites (just [Mustache](https://github.com/bobthecow/mustache.php/wiki)):
   `composer install`
3. There is no step 3 🙂

## Usage

There are currently four supported output formats:
1. HTML (default):
   * `./index.php`
2. JSON with MIME-Type `application/json`:
   * `./index.php?want=json`
   * `./json.php`
3. Pretty-printed JSON with MIME-Type `text/plain`
   * `./index.php?want=jsonText`
   * `./jsonText.php`
4. Plain text ([Markdown](https://en.wikipedia.org/wiki/Markdown) formatted)
   * `./index.php?want=text`
   * `./text.php`
   
## Suggested NGINX Config for Nicer URLs

The following example NGINX config illustrates a technique for removing the `.php` from the URLs, turning URLs of the form `http://server.tld/httpEcho/text.php` into URLs of the form `http://server.tld/httpEcho/text`.
Note that this sample config supports query strings, so URLs of the following form `http://server.tld/httpEcho/text?p1=val1&p2=val2` will also work.

```
# set up httpEcho
location /httpEcho/ {
  try_files $uri $uri/ $uri.php?$query_string;
}
```