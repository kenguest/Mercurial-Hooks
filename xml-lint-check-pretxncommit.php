#!/usr/bin/php
<?php
/**
 * This script is designed to be used as an XML Lint (syntax) check in a precommit hook for Mercurial.
 *
 * To get this script working, please put the following in your project's hgrc file, or in your user's hgrc
 * and place this script somewhere relative to your home directory and ensure the path below is updated to match.
 *
 * So, in your .hg/hgrc file for your project, or your user file at ~/.hgrc add something similar to this:
 *
 *     [hooks]
 *     pretxncommit.xml_lint_check = ~/bin/xml-lint-check-pretxncommit.php
 */

/**
 * Config options
 */
// If you don't want to see the output of this lint checker while you're committing, set verbose = FALSE
$verbose = true;
// Please specify the path to mercurial here
$hgpath  = '/usr/bin/hg';
$xmllintpath = '/usr/bin/xmllint';
// Check files with these extensions only
$xml_extensions = array('xml');

/**
 * Main Lint Check Logic Below
 */
$fail = FALSE;
verbose("Beginning XML Lint Check");

// Getting the revision we're committing (the tip)
exec($hgpath.' tip', $output);
$temp = explode(':', $output[0]);
$revision = intval(trim($temp[1]));
verbose("  on revision: $revision");

// Getting files changed
$output = array();
verbose("Getting files changed or added...");
exec("hg status --change $revision --added --modified", $output);
verbose("  found ".count($output)." files changed or added: \n----------\n".implode("\n", $output)."\n----------");

// Build our array of files we need to parse
$files = array();
foreach ($output as $file) {
    if (strlen($file) > 2) {
        $file = substr($file, 2);
        if (is_file($file)) {
            $parts = pathinfo($file);
            if (!isset($parts['extension']) || !in_array($parts['extension'], $xml_extensions)) {
                verbose("Not a known XML extension for file: '$file'");
                continue;
            }

            verbose("Checking XML Syntax for: '$file'...");
            $output = array();
            exec($xmllintpath . ' --noout '.escapeshellarg($file), $output, $retval);
            if ($retval === 0) {
                verbose("  Successfully passed XML Lint Check!");
            } else {
                echo "XML Parsing Error Detected in file '$file'\n----------";
                echo implode("\n", $output)."\n----------\n";
                $fail = TRUE;
            }
        }
    }
}

// If any file failed, return failure so it doesn't commit
if ($fail) {
    exit(255);
}
exit(0);

/**
 * Verbose output if enabled
 */
function verbose($message) {
    if ($GLOBALS['verbose']) {
        echo $message."\n";
    }
}
