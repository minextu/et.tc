<?php namespace Minextu\Ettc\Project;

use Tivie\GitLogParser\Format;
use Gioffreda\Component\Git\Git;

class Changelog
{
    /**
     * Generates logs out of all git commits
     *
     * @param  $git   Git Main git object
     * @param  int                       $count Amount of logs to return
     * @param  int                       $skip  Amount of logs to skip
     * @return array         Logs for the git object
     */
    public static function generateLogs(Git $git, $count, $skip)
    {
        $format = new Format();
        $logs = $git->log(
            [
                "-n $count",
                "--skip=$skip",
                "--decorate",
                "--pretty=format:" . $format->getFormatString()
            ]
        );

        $parsedLogs = self::parse($format, $logs);
        return self::removeExtraValues($parsedLogs);
    }

    /**
     * Parse git answer and convert it to an array
     *
     * @param  Format $format Git format
     * @param  string $log    Git log answer
     * @return array              All commits as array
     */
    private static function parse(Format $format, $log)
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

    /**
     * Strips all uneeded commit values out of the array
     *
     * @param  array $logs commit array
     * @return array           commit array without uneeded info
     */
    private static function removeExtraValues(array $logs)
    {
        $newLogs = [];

        foreach ($logs as $log) {
            $newLog =
            [
                "authorName" => $log["authorName"],
                "authorEmail" => $log["authorEmail"],
                "authorAvatar" => self::generateAvatarUrl($log["authorEmail"]),
                "authorDateTimestamp" => $log["authorDateTimestamp"],
                "subject" => $log['subject']
            ];
            $newLogs[] = $newLog;
        }

        return $newLogs;
    }


    /**
     * Generates the URL for the users avatar
     *
     * @param  string $email Email of the user
     * @return string          Avatar image url
     */
    private static function generateAvatarUrl($email)
    {
        $default = "http://img2.wikia.nocookie.net/__cb20110302033947/recipes/images/thumb/1/1c/Avatar.svg/480px-Avatar.svg.png";
        $size = 200;

        $avatar = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?d=" . urlencode($default) . "&s=" . $size . "&r=g";

        return $avatar;
    }
}
