<?php

/*
|--------------------------------------------------------------------------
| Voten Helpers
|--------------------------------------------------------------------------
|
| This file contains our general helper functions that can be accessed from
| everywhere in the application code. If you find this as "bad practice",
| don't read this file then!
|
*/

if (!function_exists('getRequestIpAddress')) {
    /**
     * Returns the real IP address of the request even if the website is using Cloudflare.
     *
     * @return string
     */
    function getRequestIpAddress()
    {
        return $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}

if (!function_exists('firstRate')) {
    /**
     * Calculates the rate for votable model (currently used for submissions and comments).
     *
     * @return float
     */
    function firstRate()
    {
        $startTime = 1473696439;
        $created = time();
        $timeDiff = $created - $startTime;

        return $timeDiff / 45000;
    }
}

if (!function_exists('rate')) {
    /**
     * Calculates the rate for sorting by hot.
     *
     * @param int       $upvotes
     * @param int       $downvotes
     * @param timestamp $created
     *
     * @return float
     */
    function rate($upvotes, $downvotes, $created)
    {
        $startTime = 1473696439; // strtotime('2016-09-12 16:07:19')
        $created = strtotime($created);
        $timeDiff = $created - $startTime;

        $x = $upvotes - $downvotes;

        if ($x > 0) {
            $y = 1;
        } elseif ($x == 0) {
            $y = 0;
        } else {
            $y = -1;
        }

        if (abs($x) >= 1) {
            $z = abs($x);
        } else {
            $z = 1;
        }

        return (log10($z) * $y) + ($timeDiff / 45000);
    }
}

if (!function_exists('settings')) {
    /**
     * makes it easy for interacting with user's settings (that is implemented as json).
     *
     * @param string $key
     *
     * @return mixed
     */
    function settings($key = null)
    {
        $settings = app('App\Settings');

        return $key ? $settings->get($key) : $settings;
    }
}

if (!function_exists('domain')) {
    /**
     * Squeezes the domain address from a valid URL.
     *
     * @param string $url
     *
     * @return string
     */
    function domain($url)
    {
        return str_ireplace('www.', '', parse_url($url, PHP_URL_HOST));
    }
}

if (!function_exists('isValidUrl')) {
    /**
     * Validates $url.
     *
     * @param string $key
     *
     * @return bool
     */
    function isValidUrl($url)
    {
        return !filter_var($url, FILTER_VALIDATE_URL) === false;
    }
}

if (!function_exists('isMobileDevice')) {
    /**
     * Is visitor viewing site on a mobile device?
     *
     * @return bool
     */
    function isMobileDevice()
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36';

        return preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4));
    }
}

if (!function_exists('confirmPassword')) {
    /**
     * Used for "Enter your password to confirm cases".
     *
     * @param string $password
     *
     * @return bool
     */
    function confirmPassword($password)
    {
        return Illuminate\Support\Facades\Hash::check($password, auth()->user()->password);
    }
}

if (!function_exists('externalJson')) {
    /**
     * Convert external JSON into an object.
     *
     * @param string $url
     *
     * @return obj
     */
    function externalJson($url)
    {
        try {
            return json_decode(file_get_contents($url));
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('rssForHumans')) {
    /**
     * Converts a RSS formatted number into a humen-friendly string. (used for backend dashboard's statistics).
     *
     * @param int $bytes
     *
     * @return string
     */
    function rssForHumans($bytes)
    {
        $Ki = 2 ** 10;
        $Mi = 2 ** 20;
        $Gi = 2 ** 30;

        if ($bytes < $Ki) {
            return $bytes.' B';
        } elseif ($bytes < $Mi) {
            return round($bytes / $Ki, 3).' KiB';
        } elseif ($bytes < $Gi) {
            return round($bytes / $Mi, 3).' MiB';
        } else {
            return round($bytes / $Gi, 3).' GiB';
        }
    }
}

if (!function_exists('getUrlTitle')) {
    function getUrlTitle($url) {
        $f = file_get_contents($url);
        preg_match('/<title>(?<title>.*?)<\/title>/si', $f, $title);
        $encode = mb_detect_encoding($title['title'], array('GB2312','GBK','UTF-8', 'CP936')); //得到字符串编码
        $file_charset = iconv_get_encoding()['internal_encoding']; //当前文件编码
        if ( $encode != 'CP936' && $encode != $file_charset) {
            return iconv($encode, $file_charset, $title['title']);
        }
        return $title['title'];
    }
}

if (!function_exists('getUrlImg')) {
    function getUrlImg($url) {
        $f = file_get_contents($url);
        if (str_contains($url,'mp.weixin.qq.com')) {
            //微信的文章
            $pattern = '/var msg_cdn_url = "(.*?)";/s';
            preg_match_all($pattern,$f,$matches);
            if(array_key_exists(1, $matches) && !empty($matches[1][0])) {
                $temp = $matches[1][0];
            } else {
                $temp='';
            }
        } else {
            $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";
            preg_match_all($pattern,$f,$matchContent);
            if(isset($matchContent[1][0])){
                $temp=$matchContent[1][0];
            }else{
                $temp='';
            }
            $temp='';
        }
        return $temp;
    }
}

if (!function_exists('slackNotification')) {
    function slackNotification($author_name, $subject, $title, array $fields = null, $url ='', $color = 'good'){
        $url = $url?:config('app.url');
        return \Slack::to(config('slack.activity_channel'))
            ->disableMarkdown()
            ->attach(
                [
                    'text' => $title,
                    'pretext' => '[链接]('.$url.')',
                    'author_name' => $author_name,
                    'author_link' => $url,
                    'mrkdwn_in' => ['pretext'],
                    'color'     => $color,
                    'fields' => $fields
                ]
            )->send($subject);
    }
}


if (!function_exists('iso8601')) {
    /**
     * Converts timestamp to ISO8601 format.
     *
     * @param string $time
     *
     * @return string
     */
    function iso8601($time)
    {
        return gmdate('c', strtotime($time));
    }
}


if (!function_exists('activeClass')) {
    /**
     * returns active-class if the current URI is the same as sent URI.
     *
     * @param $uri
     * @param string $active_class
     *
     * @return string
     */
    function activeClass($uri, $active_class = 'is-active')
    {
        if (!starts_with($uri, '/')) {
            $uri = '/'.$uri;
        }

        $current_uri = str_after(url()->current(), config('app.url'));

        if (starts_with($current_uri, $uri)) {
            return ' '.$active_class;
        }
    }
}
