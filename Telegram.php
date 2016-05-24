<?php
/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2014, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCI\Plugin;

use PHPCI\Builder;
use PHPCI\Helper\Lang;
use PHPCI\Model\Build;
use b8\HttpClient;

/**
 * Telegram Plugin
 * @author       LEXASOFT <lexasoft83@gmail.com>
 * @package      PHPCI
 * @subpackage   Plugins
 */
class Telegram implements \PHPCI\Plugin
{
    protected $phpci;
    protected $build;
    protected $api_key;
    protected $message;
    protected $recipients;
    protected $send_log;

    /**
     * Standard Constructor
     *
     * @param Builder $phpci
     * @param Build   $build
     * @param array   $options
     */
    public function __construct(Builder $phpci, Build $build, array $options = array())
    {
        $this->phpci = $phpci;
        $this->build = $build;
        if (empty($options['api_key'])) {
            throw new \Exception("Not setting telegram api_key");
        }
        if (empty($options['recipients'])) {
            throw new \Exception("Not setting recipients");
        }
        $this->api_key = $options['api_key'];
        if (isset($options['message'])) {
            $this->message = $options['message'];
        } else {
            $this->message = '[%ICON_BUILD%] [%PROJECT_TITLE%](%PROJECT_URI%) - [Build #%BUILD%](%BUILD_URI%) has finished ' .
                'for commit [%SHORT_COMMIT% (%COMMIT_EMAIL%)](%COMMIT_URI%) ' .
                'on branch [%BRANCH%](%BRANCH_URI%)';
        }
        $this->recipients = array();
        if (is_string($options['recipients'])) {
            $this->recipients = array($options['recipients']);
        } elseif (is_array($options['recipients'])) {
            $this->recipients = $options['recipients'];
        }
        $this->send_log = isset($options['send_log']) && ((bool) $options['send_log'] !== false);
    }

    /**
     * Run Telegram plugin.
     * @return bool
     */
    public function execute()
    {
        $build_icon = $this->build->isSuccessful() ? '✅' : '❎';
        $buildMsg = $this->build->getLog();
        $buildMsg = str_replace(array('[0;32m', '[0;31m', '[0m', '/[0m'), array('', '', ''), $buildMsg);
        $buildmessages = explode('RUNNING PLUGIN: ', $buildMsg);
        $buildMsg = '';
        foreach ($buildmessages as $bm) {
            $pos = mb_strpos($bm, "\n");
            $firstRow = mb_substr($bm, 0, $pos);
            //skip long outputs
            if (($firstRow == 'slack_notify')||($firstRow == 'php_loc')||($firstRow == 'telegram')) {
                continue;
            }
            $buildMsg .= '*RUNNING PLUGIN: ' . $firstRow . "*\n";
            $buildMsg .= $firstRow == 'composer' ? '' : ('```' . mb_substr($bm, $pos) . '```');
        }
        $message = $this->phpci->interpolate(str_replace(array('%ICON_BUILD%'), array($build_icon), $this->message));

        $http = new HttpClient('https://api.telegram.org');
        $http->setHeaders(array('Content-Type: application/json'));
        $uri = '/bot'. $this->api_key . '/sendMessage';

        foreach ($this->recipients as $chat_id) {
            $params = array(
                'chat_id' => $chat_id,
                'text' => $message,
                'parse_mode' => 'Markdown',
            );

            $http->post($uri, json_encode($params));

            if ($this->send_log) {
                $params = array(
                    'chat_id' => $chat_id,
                    'text' => $buildMsg,
                    'parse_mode' => 'Markdown',
                );

                $http->post($uri, json_encode($params));
            }
        }

        return true;
    }
}
