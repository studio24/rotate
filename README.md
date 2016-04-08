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

```sh
use studio24\Rotate\Rotate;
```

## A note on filename patterns

Both Rotate and Delete pass in the filename pattern you want to match for in the constructor or via the `setFilenameFormat()` method.
This can be used to match a single file, files matching a pattern, or files with a datetime within the filename pattern. 

The following patterns are supported when matching filenames:

* `debug.log` - exact match for a file called debug.log
* `*` - matches any string, for example `*.log` matches all files ending .log
* `{Ymd}` - matches time segment in a file, for example `order.{Ymd}.log` matches a file in the format order.20160401.log

#### Search within the current folder
When matching patterns all files in the specified folder are scanned, we do not recursively search for files. For example 
passing `path/to/*.log` will search for all files ending .log in the folder path/to.

#### Datetime formats
For datetime formats, any date format supported by [DateTime::createFromFormat](http://php.net/datetime.createfromformat) is allowed 
excluding the Timezone identifier `e` and whitespace and separator characters. 

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

Rotate keeps 10 copies of files by default, you can change this via:

```
$rotate->keep(20);
```

#### Rotated based on filesize
You can only rotate files when they reach a certain filesize, rather than automatically rotate each time the `$rotate->run()` method is run.
 
```
$rotate->size("12MB");
```

### Delete

Deletes files based on modification time, datetime in the filename, or based on a custom callback function.

#### Time-based

Deletes files based on the modification time. For example, to delete all JPG files in a folder over 3 months old: 

```sh
use studio24\Rotate\Delete;

$rotate = new Delete('path/to/images/*.jpg');
$deletedFiles = $rotate->deleteByFileModifiedDate('3 months');
```

The `deleteByFileModifiedDate()` method accepts either a valid DateInterval object or a relative date format as specified on 
[Relative Formats](http://php.net/manual/en/datetime.formats.relative.php).

#### Time format in filename
Deletes files based on the datetime in the filename. For example, to delete all  order logfiles with a date in their 
filename over 3 months old:

```sh
use studio24\Rotate\Delete;

$rotate = new Delete('path/to/logs/orders.YYYYMMDD.log');
$deletedFiles = $rotate->deleteByFilenameTime('3 months');
```

#### Based on a custom callback 
Deletes files based on a custom callback function, this is useful if you need to perform more complex code to assess whether a file
should be deleted. For example, to delete all image files called 1000.jpg and below:

```sh
use studio24\Rotate\Delete;
use studio24\Rotate\DirectoryIterator;

$rotate = new Delete('path/to/logs/*.jpg');
$deletedFiles = $rotate->deleteByCallback(function(DirectoryIterator $file){
    if ($file->getBasename() <= 1000) {
        return true;
    } 
    return false;
});
```

##### DirectoryIterator
The callback function must accept one parameter `$file`, which is of type `\studio24\Rotate\DirectoryIterator`.

This iterator has the following methods in addition to the normal [SPL DirectoryIterator](http://php.net/DirectoryIterator).

* `isMatch()` - whether the current file matches the filename patter we are looking for
* `hasDate()` - whether the current file has a datetime within the filename
* `getFilenameDate()` - return the datetime from the current file (as a DateTime object)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Credits

- [Simon R Jones](https://github.com/simonrjones)