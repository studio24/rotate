# Rotate

Simple file rotation utility which rotates and removes old files, useful where you cannot use logrotate (e.g. a Windows system) 
or you want to rotate or delete files based on a timestamp or date contained in the filename. 

## Installation

```sh
composer require studio24/rotate
```

## Usage

You can use Rotate in two modes: rotate (renames files and removes oldest files) or delete (deletes files according to a pattern).

Import at the top of your PHP script via:

### Rotate

Rotate log files in a similar manner to logrotate (e.g. renames debug.log to debug.log.1 - debug.log.10)

```sh
use studio24\Rotate\Rotate;

$rotate = new Rotate('path/to/debug.log');
$rotate->keep(10);
$rotate->run();
```

### Delete

#### Time-based
Delete all image files over 3 months old 

```sh
use studio24\Rotate\Delete;

$rotate = new Delete('path/to/images/*.jpg');
$deletedFiles = $rotate->deleteTimePeriod('3 months');
```

#### Time format in filename
Delete all order logfiles with a date in their filename over 3 months old.

```sh
use studio24\Rotate\Delete;

$rotate = new Delete('path/to/logs/orders.YYYYMMDD.log');
$deletedFiles = $rotate->deleteFilePattern('3 months');
```

#### Based on a custom callback 
Delete all image files called 1000.jpg and below.

```sh
use studio24\Rotate\Delete;

$rotate = new Delete('path/to/logs/*.jpg');
$deletedFiles = $rotate->deleteCallback(function($filename){
    if (basename($filename, 'jpg') <= 1000) {
        return true;
    } 
    return false;
});
```

## Filename patterns

The following patterns are supported when matching filenames:

* _*_ matches any string, for example _*.log_ matches all files ending .log
* _YYYYMMDD_ = matches time segment in a file, for example _order.YYYYMMDD.log_ matches a file in the format order.20160401.log

### Time segments

The following time segments are supported:

* YYYY = 4 digit year (e.g. 2016)
* MM = 2 digit month (e.g. 03)
* DD = 2 digit day (e.g. 01)
* hh = 2 digit hour (e.g. 12 or 15)
* mm = 2 digit minutes (e.g. 30)
* ss = 2 digit seconds (e.g. 25)
* W = 1-2 digit Week number (e.g. 5 or 12)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Credits

- [Simon R Jones](https://github.com/simonrjones)