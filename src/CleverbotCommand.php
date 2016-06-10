<?php

namespace PhpCleverSlackBot;

class CleverbotCommand extends \PhpSlackBot\Command\BaseCommand
{
    const API_URL  = 'https://cleverbot.io/1.0/';

    protected $user = null;
    protected $key  = null;
    protected $nick = null;

    public function __construct($user, $key)
    {
        $this->user = $user;
        $this->key  = $key;
    }

    public function setNick($nick)
    {
        $this->nick = $nick;
    }

    protected function configure()
    {
    }

    protected function execute($data, $context)
    {
        if (!isset($data['type']) || !isset($data['user']) || !isset($data['text'])) {
            return;
        }

        if ($data['type'] == 'message') {
            if ($data['user'] == $context['self']['id']) {
                return;
            }

            $mention_self = '<@' . $context['self']['id'] . '>';
            $mention_self_text_position = strpos($data['text'], $mention_self);
            $channel = $this->getChannelNameFromChannelId($data['channel']);

            if ($mention_self_text_position === false && $channel) {
                return;
            }

            $text = str_replace($mention_self, '', $data['text']);
            $text = preg_replace('/(^|\s)[^A-Za-z0-9]*($|\s)/', ' ', $text);
            $text = trim($text);

            if (strtolower($text) == 'ping') {
                $message = str_replace(array('i', 'I'), array('o', 'O'), $text);
            } else {
                $options = array(
                    'user' => $this->user,
                    'key'  => $this->key,
                );
                if ($this->nick) {
                    $options['nick'] = $this->nick;
                }
                $response = $this->request(self::API_URL . 'create', $options);
                if ($response->status != 'success' && $response->status != 'Error: reference name already exists') {
                    $message = $response->status;
                } else {
                    if (isset($response->nick)) {
                        $options = $options + array('nick' => $response->nick);
                    }
                    $options['text'] = $text;
                    $response = $this->request(self::API_URL . 'ask', $options);
                    if ($response->status != 'success') {
                        $message = $response->status;
                    } else {
                        $message = $response->response;
                    }
                }
            }

            $this->send($data['channel'], $data['user'], $message);
        }
    }

    protected function request($url, $data)
    {
        $data = http_build_query($data);
        $options = array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
        );
        $curl = curl_init($url);
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            throw new \ErrorException(curl_error($curl));
        }
        curl_close($curl);

        return json_decode($response);
    }
}
