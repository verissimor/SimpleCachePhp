SimpleCachePhp
==============

A simple mode to cache php pages. Support automatic on sessions and querystrings.

Using the cache
==============
To use this feature only add to the start of your code:

    <?php
    include "simple_cache_class.php";
    $cache = new SimpleCachePhp(__FILE__);
    ?>

And this snippet at the end:

    <?php $cache->CacheEnd(); ?>


Example
==============
For example, imagine that you have the following php page (uncached):

    <html>
        <head>
            <title></title>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        </head>
        <body>
            <h1>hello world</h1>
            <p>Now is <?php echo date('d/m/Y h:i:s'); ?></p>
        </body>
    </html>

Cached:

    <?php
    include "simple_cache_class.php";
    $cache = new SimpleCachePhp(__FILE__);
    ?>
    
    <html>
        <head>
            <title></title>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        </head>
        <body>
            <h1>hello world</h1>
            <p>Now is <?php echo date('d/m/Y h:i:s'); ?></p>
        </body>
    </html>
    
    <?php $cache->CacheEnd(); ?>
    
How this works?
==============

The first time the page is accessed it is generated normally. With ob_start, ob_get_contents and fopen a file is created with the cache.

The files are stored in the /cache. At each new access is checked if cache exists and has not expired.

The cache takes into account the filename, sessions and querystrings.

In the example above, the file would be generated: <b>/cache/index.php__</b>, and expire in 24 hours.

The parameters
==============

    $cache = new SimpleCachePhp($filename, $time, $ignoreKeysOnParametrize, $folderCache);
    
$filename
<blockquote>
    The name of the cache file will be saved. To use the current name of the file, use: __FILE__
</blockquote>

$time
<blockquote>
    The time the cache is stored before expiring.
    <i>Default: 86400 (24 hours)</i>
</blockquote>

$ignoreKeysOnParametrize
<blockquote>
    An array of keys that should be ignored. For example, if you use a login session but the pages are identical.
    <i>Default: null</i>
</blockquote>

$folderCache
<blockquote>
    The folder that the files are stored.
    <i>/cache/</i>
</blockquote>