Mercurial-Hooks
===============

For more details about any of these scripts, please view the file-level docblock within each script 

php-lint-check-pretxncommit.php
---------------
A PHP Lint Checker intended to be hooked up to mercurial via the pretxncommit hook that will block commits that have invalid PHP syntax

xml-lint-check-pretxncommit.php
---------------
An XML Lint Checker intended to be hooked up to mercurial via the pretxncommit hook that will block commits that have invalid XML. This utilised the xmllint 
command line tool which is required for it to work.
