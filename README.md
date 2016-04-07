# Rotate

_Note: Not ready for production use: code is in development_

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

Rotate log files in a similar manner to logrotate.

The following example rotates the file debug.log, this renames debug.log to debug.log.1, debug.log.1 to debug.log.2, debug.log.2 to 
debug.log.3 and so on. It keeps 10 copies, so it deletes debug.log.10 and renames debug.log.9 to debug.log.10.

```sh
use studio24\Rotate\Rotate;

$rotate = new Rotate('path/to/debug.log');
$rotate->run();
```

#### How many copies to keep

You can change the default number of copies to keep (10) via:

```
$rotate->keep(20);
```

#### Rotated based on filesize
You can only rotate files when they reach a certain filesize, rather than automatically rotate each time the $rotate->run() method is run.
 
```
$rotate->size("12MB");
```

### Delete

#### Time-based
Delete all image files over 3 months old 

```sh
use studio24\Rotate\Delete;

$rotate = new Delete('path/to/images/*.jpg');
$deletedFiles = $rotate->deleteByFileModifiedDate('3 months');
```

This method accepts either a valid DateInterval object or a relative date format as specified on 
[Relative Formats](http://php.net/manual/en/datetime.formats.relative.php).

#### Time format in filename
Delete all order logfiles with a date in their filename over 3 months old.

```sh
use studio24\Rotate\Delete;

$rotate = new Delete('path/to/logs/orders.YYYYMMDD.log');
$deletedFiles = $rotate->deleteByFilenameTime('3 months');
```

#### Based on a custom callback 
Delete all image files called 1000.jpg and below.

```sh
use studio24\Rotate\Delete;

$rotate = new Delete('path/to/logs/*.jpg');
$deletedFiles = $rotate->deleteByCallback(function(DirectoryIterator $file){
    if ($file->getBasename() <= 1000) {
        return true;
    } 
    return false;
});
```

## Filename patterns

The following patterns are supported when matching filenames:

* _*_ matches any string, for example _*.log_ matches all files ending .log
* {Ymd} = matches time segment in a file, for example order.{Ymd}.log matches a file in the format order.20160401.log

Any date format supported by [DateTime::createFromFormat](http://php.net/datetime.createfromformat) is allowed 
(excluding the Timezone identifier 'e' and whitespace and separator characters)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Credits

- [Simon R Jones](https://github.com/simonrjones)