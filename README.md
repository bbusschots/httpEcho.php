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