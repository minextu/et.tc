<?php namespace Minextu\Ettc\Project;

use Tivie\GitLogParser\Format;

class Changelog
{
    /**
     * [generateLogs description]
     * @param    \Gioffreda\Component\Git\Git   $git     Main git object
     * @param    int   $count  Amount of logs to return
     * @param    int   $skip   Amount of logs to skip
     * @return   array         Logs for the git object
     */
    public static function generateLogs($git, $count, $skip)
    {
        $format = new Format();
        $logs = $git->log(
            [
                "-n $count",
                "--skip=$skip",
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
