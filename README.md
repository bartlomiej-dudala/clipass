CliPass
=======

CliPass is a little tool, written in PHP, to connect and gather data from KeePass 2.x
It uses KeePassHttp plugin, as PassIFox for Mozilla Firefox or chromeIPass for Google Chrome.

Features
--------
* simple searching via provided key
* "prefixing" search key - which is useful when using it as a part of bigger shell script
* git-credential-helper output adapter - for simple registering CliPass as git-credential-helper, but only for 'get' method ('store' method is not supported yet)

Requirements
------------
* PHP 5.3 or higher with mcrypt support enabled
* KeePass 2.x with KeePassHttp plugin

Installation
------------
Firstly you must install KeePassHttp plugin into your KeePass installation (https://github.com/pfn/keepasshttp).
Then download CliPass [url] and composer (into the same directory) http://getcomposer.org/download/ 

And then...

    php composer.phar install

Done. :)

Usage
-----

    php clipass.php --key=keyToSearchInKeePassDB
    php clipass.php --key=keyToSearchInKeePassDB --key-prefix=prefix- #it searches for prefix-keyToSearchInKeePassDB

When you run this tool first time, CliPass tries to associate with KeePass (in the same way as e.g. chromeIPass), and save the "identity" in the ~/clipass.identity file. If you want to remove this association - simply remove this file (or remove association field from "KeePassHttp Settings" entry in KeePass.

At this moment CliPass offers two output adapters:
* only-first-password - gather first entry from KeePass and show only password [default]
* git-credential-helper - for simple connect CliPass with git

Usefull tips
------------
* it is usefull to make an executable file somewhere in the $PATH e.g. /usr/local/bin/clipass

    #!/bin/bash
    php /pathToCliPass/clipass.php $1 $2 $3 $4

* running as git-credential-helper: e.g. /usr/local/bin/git-credential-clipass

    clipass --output-adapter=git-credential-helper --key-prefix=git-credential- --git-command=$1

* copy password to clipboard (with xclip):

    php /pathToCliPass/clipass.php --key=KEY | xclip -i -selection clipboard

Enjoy!
 
