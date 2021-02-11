<?php

namespace GislerCMS\TwigExtension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Custom TwigGoogleReviews twig-extension for getting google reviews
 * @package GislerCMS\TwigExtension
 */
class TwigGoogleReviews extends AbstractExtension
{
    const CACHE_FILE = __DIR__ . '/../../cache/google-reviews-%s.json';

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('getGoogleReviews', [$this, 'getGoogleReviews'])
        ];
    }

    /**
     * @param string $cid
     * @param int $minRating
     * @return array
     */
    public function getGoogleReviews($cid, $minRating = 4)
    {
        $cacheFile = sprintf(self::CACHE_FILE, $cid);
        if (file_exists($cacheFile)) {
            $cache = json_decode(file_get_contents($cacheFile), true);
            if ($cache['time'] > strtotime('-1 hour')) {
                return $cache['reviews'];
            }
        }

        $options = [
            'cid' => $cid,
            'min_rating' => $minRating
        ];

        $ch = curl_init('https://www.google.com/maps?cid=' . $options['cid']);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        $pattern = '/window\.APP_INITIALIZATION_STATE(.*);window\.APP_FLAGS=/ms';
        if (preg_match($pattern, $result, $match)) {
            $match[1] = trim($match[1], ' =;');
            $reviews = json_decode($match[1]);
            $reviews = ltrim($reviews[3][6], ")]}'");
            $reviews = json_decode($reviews);
            $reviews = $reviews[6][52][0];
        }
        $res = [];
        foreach ($reviews as $review) {
            if ($review[4] >= $options['min_rating']) {
                $res[] = [
                    'time' => $review[1],
                    'stars' => $review[4],
                    'photo' => $review[0][2],
                    'name' => $review[0][1],
                    'text' => $review[3]
                ];
            }
        }

        file_put_contents($cacheFile, json_encode(['time' => time(), 'reviews' => $res]));

        return $res;
    }
}
