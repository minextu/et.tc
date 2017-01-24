<?php namespace Minextu\Ettc\Project;

use Tivie\GitLogParser\Format;

class Changelog
{
    public static function generateLogs($git)
    {
        $format = new Format();
        $logs = $git->log(
            [
                "-n -1",
                "--decorate",
                "--pretty=format:" . $format->getFormatString()
            ]);

        $parsedLogs = self::parse($format, $logs);
        return self::removeExtraValues($parsedLogs);
    }

    private static function parse($format, $log)
    {
        $buffer = array();
        $commits = explode($format->getCommitDelimiter(), $log);

        foreach ($commits as $commit) {
            $fields = explode($format->getFieldDelimiter(), $commit);
            $entry = array();
            foreach ($fields as $field) {
                if (!preg_match('/^\[(\S*)\](.*)/', $field, $matches)) {
                    continue;
                }
                $entry[trim($matches[1])] = trim($matches[2]);
            }
            if (!empty($entry)) {
                $buffer[] = $entry;
            }
        }

        return $buffer;
    }

    private static function removeExtraValues($logs)
    {
        $newLogs = [];

        foreach ($logs as $log) {
            $newLog =
            [
                "authorName" => $log["authorName"],
                "authorEmail" => $log["authorEmail"],
                "authorDateTimestamp" => $log["authorDateTimestamp"],
                "subject" => $log['subject']
            ];
            $newLogs[] = $newLog;
        }

        return $newLogs;
    }
}
