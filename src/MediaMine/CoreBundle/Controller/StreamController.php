<?php
namespace MediaMine\CoreBundle\Controller;

use Alchemy\BinaryDriver\Configuration;
use Doctrine\ORM\Query;
use JMS\DiExtraBundle\Annotation\Inject;
use MediaMine\CoreBundle\Shared\EntitityManagerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StreamController extends AbstractController
{
    use EntitityManagerAware;

    const FORMAT_FLV = 'flv';
    const FORMAT_MP4 = 'mp4';
    const FORMAT_WEBM = 'webm';
    const FORMAT_M3U8 = 'm3u8';
    const AVCONV = 'avconv';
    const FFMPEG = 'ffmpeg';

    const DEFAULT_FORMAT = self::FORMAT_FLV;
    const DEFAULT_SAVE_PATH = 'web/stream/';

    protected $lib = self::FFMPEG;

    protected $mimeTypes = array(
        self::FORMAT_FLV  => 'video/x-flv',
        self::FORMAT_MP4  => 'video/mp4',
        self::FORMAT_WEBM => 'video/webm',
        self::FORMAT_M3U8 => 'application/x-mpegURL',
    );

    /**
     * @Inject("%kernel.root_dir%")
     */
    public $rootDir;

    /**
     * @Inject("%config%")
     */
    public $config;

    /**
     * @Inject("snc_redis.default")
     * @var \Redis
     */
    public $redis;

    public function indexAction(Request $request, $slug, $bitrate, $width, $height, $pathKey, $format)
    {
        $response = new Response();
        $response->setStatusCode(200);
        ini_set('memory_limit', '2000M');
        set_time_limit(0);

        if (!$pathKey) {
            throw new NotFoundHttpException();
        }
        $files = $this->getRepository('File\File')->findFullBy(array('pathKey' => $pathKey));
        if (!count($files)) {
            throw new NotFoundHttpException();
        } else {
            $file = $files[0];
            $filePath = $file->getPath();

            if (!in_array($format, array_keys($this->mimeTypes))) {
                $format = self::DEFAULT_FORMAT;
            }
            $this->redis->hset('stream', $pathKey, 1);
            $cacheName = $pathKey . '.' . $format;

            $params = array(
                '%PATH_KEY%' => $pathKey,
                '%INPUT_FILE%' => $filePath
            );

            if ($height) {
                $params['%HEIGHT%'] = $height;
                $cacheName = $height . '-' . $cacheName;
            }
            if ($width) {
                $params['%WIDTH%'] = $width;
                $cacheName = $width . '-' . $cacheName;
            }
            if ($bitrate) {
                $params['%BITRATE%'] = $bitrate;
                $cacheName = $bitrate . '-' . $cacheName;
            }

            $params['%CACHE_NAME%'] = $cacheName;
            $path = $this->rootDir . '/../' . self::DEFAULT_SAVE_PATH . $pathKey . '/';
            if (!file_exists($path)) {
                mkdir($path);
            }
            $params['%OUTPUT_PATH%'] = $path;

            $params['%TMP_NAME%'] = 'tmp-' . $params['%CACHE_NAME%'];
            $params['%LOG_NAME%'] = $params['%TMP_NAME%'] . '.log';

            $params['%OUTPUT_TMP_FILE%'] = $params['%OUTPUT_PATH%'] . $params['%TMP_NAME%'];
            $params['%OUTPUT_FILE%'] = $params['%OUTPUT_PATH%'] . $params['%CACHE_NAME%'];
            $params['%LOG_FILE%'] = $params['%OUTPUT_PATH%'] . $params['%LOG_NAME%'];

            if (array_key_exists('%HEIGHT%', $params) && array_key_exists('%WIDTH%', $params)) {
                $params['%SIZE%'] = '-s ' . $params['%OUTPUT_PATH%'] . 'x' . $params['%LOG_NAME%'];
            } else {
                $params['%SIZE%'] = '';
            }

            $params['%AUDIO_BITRATE%'] = (($params['%BITRATE%'] < 768) ? ((int)($params['%BITRATE%'] / 3)) : 256);
            $params['%VIDEO_BITRATE%'] = $params['%BITRATE%'] - $params['%AUDIO_BITRATE%'];

            if ($request->getMethod() == 'HEAD') {
                $response->headers->set('Accept-Ranges', 'bytes');
                $response->headers->set('Content-Type', $this->mimeTypes[$format]);
                return $response;
            } else {
                switch ($format) {
                    case self::FORMAT_FLV :
                        return $this->encodeFlv($params);
                        break;
                    case self::FORMAT_MP4 :
                        return $this->encodeMp4($params);
                        break;
                    case self::FORMAT_WEBM :
                        return $this->encodeWebm($params);
                        break;
                    case self::FORMAT_M3U8 :
                        return $this->encodeM3u8($params);
                        break;
                }
            }
        }
        return $response;
    }

    protected function encodeFlv($params)
    {
        $response = new Response();
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', $this->mimeTypes[self::FORMAT_FLV]);
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $commands = array();
        $commands[] = $this->getBinary() . ' -ss 0 -y -i "%INPUT_FILE%" -async 1 -b:v %VIDEO_BITRATE%k %SIZE% -ar 44100 -ac 2 -b:a %AUDIO_BITRATE%k %FORMAT_OPT% -c:v %VIDEO_CODEC% -c:a %AUDIO_CODEC% -preset superfast -threads 0 %EXTRA% "%OUTPUT_TMP_FILE%" </dev/null >>"%LOG_FILE%" 2>&1';
        $commands[] = 'ln -s "%OUTPUT_TMP_FILE%" "%OUTPUT_FILE%"';
        $commands[] = 'php ' . $this->rootDir . '/console stream:end %PATH_KEY%';
        $params['%FORMAT_OPT%'] = '-f flv';
        $params['%VIDEO_CODEC%'] = 'libx264';
        $params['%AUDIO_CODEC%'] = 'libmp3lame';
        $params['%EXTRA%'] = '';
        $commands = str_replace(array_keys($params), array_values($params), $commands);
        if (!file_exists($params['%OUTPUT_TMP_FILE%']) && !file_exists($params['%OUTPUT_FILE%'])) {
            $exec = '(' . implode(' && ', $commands) . ') >>"' . $params['%LOG_FILE%'] . '" 2>&1 &';
            $this->getLogger()->debug($exec);
            shell_exec($exec);
        }
        flush();
        sleep(5);
        $this->readOnTheFly($params['%OUTPUT_TMP_FILE%']);
        return $response;
    }

    public function getBinary()
    {
        if (file_exists(self::FFMPEG) && is_executable(self::FFMPEG)) {
            return self::FFMPEG;
        } else {
            return self::AVCONV;
        }
    }

    protected function readOnTheFly($file, $start = 0, $end = -1)
    {
        $lastpos = $start;
        while (true) {
            usleep(1000000); //3 s
            clearstatcache(false, $file);
            $len = filesize($file);
            if ($len <= $lastpos) {
                break;
            } elseif ($len > $lastpos) {
                $f = fopen($file, "rb");
                if ($f === false)
                    die();
                fseek($f, $lastpos);
                while (!feof($f)) {
                    $buffer = fread($f, 4096);
                    echo $buffer;
                    flush();
                }
                $lastpos = ftell($f);
                fclose($f);
            }
        }
    }

    protected function encodeMp4($params)
    {
        $response = new Response();
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', $this->mimeTypes[self::FORMAT_MP4]);
        $response->headers->set('Content-Transfer-Encoding', 'binary');

        $commands = array();
        $commands[] = $this->getBinary() . ' -y -i "%INPUT_FILE%" -b:v %VIDEO_BITRATE%k %SIZE% -ar 44100 -ac 2 -b:a %AUDIO_BITRATE%k %FORMAT_OPT% -c:v %VIDEO_CODEC% -c:a %AUDIO_CODEC% -preset superfast -threads 0 %EXTRA% "%OUTPUT_TMP_FILE%" </dev/null >>"%LOG_FILE%" 2>&1';
        $commands[] = 'cp "%OUTPUT_TMP_FILE%" "%OUTPUT_FILE%"';
        $commands[] = 'php ' . $this->rootDir . '/console stream:end %PATH_KEY%';
        $params['%FORMAT_OPT%'] = '-f mp4';
        $params['%VIDEO_CODEC%'] = 'libx264';
        $params['%AUDIO_CODEC%'] = 'aac';
        $params['%EXTRA%'] = '-strict experimental'; //-movflags faststart
        $commands = str_replace(array_keys($params), array_values($params), $commands);
        if (!file_exists($params['%OUTPUT_TMP_FILE%']) && !file_exists($params['%OUTPUT_FILE%'])) {
            $exec = '(' . implode(' && ', $commands) . ') >>"' . $params['%LOG_FILE%'] . '" 2>&1 &';
            $this->getLogger()->debug($exec);
            shell_exec($exec);
        }
        flush();
        sleep(5);
        $this->readOnTheFly($params['%OUTPUT_TMP_FILE%']);
        return $response;
    }

    protected function encodeWebm($params)
    {
        $response = new Response();
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', $this->mimeTypes[self::FORMAT_WEBM]);
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $commands = array();
        $commands[] = $this->getBinary() . ' -y -i "%INPUT_FILE%" -b:v %VIDEO_BITRATE%k %SIZE% %FORMAT_OPT% -c:v %VIDEO_CODEC% -c:a %AUDIO_CODEC% -b:a %AUDIO_BITRATE%k -ar 44100 -ac 2 -threads 0 %EXTRA% "%OUTPUT_TMP_FILE%" </dev/null >>"%LOG_FILE%" 2>&1';
        $commands[] = 'cp "%OUTPUT_TMP_FILE%" "%OUTPUT_FILE%"';
        $commands[] = 'php ' . $this->rootDir . '/console stream:end %PATH_KEY%';
        $params['%FORMAT_OPT%'] = '-f webm';
        $params['%VIDEO_CODEC%'] = 'libvpx';
        $params['%AUDIO_CODEC%'] = 'libvorbis';
        $params['%EXTRA%'] = '-minrate ' . $params['%BITRATE%'] . 'k -maxrate ' . $params['%BITRATE%'] . 'k -crf 20'; // Lower crf is better quality
        $commands = str_replace(array_keys($params), array_values($params), $commands);
        if (!file_exists($params['%OUTPUT_TMP_FILE%']) && !file_exists($params['%OUTPUT_FILE%'])) {
            $exec = '(' . implode(' && ', $commands) . ') >>"' . $params['%LOG_FILE%'] . '" 2>&1 &';
            $this->getLogger()->debug($exec);
            shell_exec($exec);
        }
        flush();
        sleep(5);
        $this->readOnTheFly($params['%OUTPUT_TMP_FILE%']);
        die;
        return $response;
    }

    protected function encodeM3u8($params)
    {
        $response = new Response();
        $response->setStatusCode(200);
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->headers->set('Content-Type', $this->mimeTypes[self::FORMAT_M3U8]);
        setlocale(LC_CTYPE, "fr_FR.UTF-8");
        $conf = new Configuration();
        $conf->set('ffprobe.binaries', $this->config['system']['paths']['ffprobe']);
        $conf->set('ffmpeg.binaries', $this->config['system']['paths']['ffmpeg']);
        $ffprobe = \FFMpeg\FFProbe::create($conf);
        $duration = $ffprobe
            ->format($params['%INPUT_FILE%'])// extracts file informations
            ->get('duration');             // returns the duration property
        $params['%OUTPUT_PATH_M3U8%'] = $params['%OUTPUT_PATH%'] . 'm3u8_' . $params['%BITRATE%'];
        if (!file_exists($params['%OUTPUT_PATH_M3U8%'])) {
            mkdir($params['%OUTPUT_PATH_M3U8%']);

            $commands = array();
            $commands[] = 'cd %OUTPUT_PATH%';
            $commands[] = $this->getBinary() . ' -y -i "%INPUT_FILE%" -b:v %VIDEO_BITRATE%k -maxrate %VIDEO_BITRATE%k %SIZE% -ar 44100 -ac 2 -b:a %AUDIO_BITRATE%k -c:v %VIDEO_CODEC% -pix_fmt yuv420p -c:a %AUDIO_CODEC% -preset superfast -threads 0 %EXTRA% < /dev/null >> "%LOG_FILE%" 2>&1';
            $commands[] = 'mv "%OUTPUT_PATH_M3U8%/part.m3u8" "%OUTPUT_FILE%"';
            $commands[] = 'sed -i "s/part/m3u8_%BITRATE%\/part/g" "%OUTPUT_FILE%"';
            $commands[] = 'php ' . $this->rootDir . '/console stream:end %PATH_KEY%';

            $params['%VIDEO_CODEC%'] = 'libx264';
            $params['%AUDIO_CODEC%'] = 'aac';
            $params['%EXTRA%'] = //'-hls_base_url ' . 'm3u8_' . $params['%BITRATE%'] . '' .
                ' -strict experimental -hls_time 10 -hls_list_size 9999999 "' . $params['%OUTPUT_PATH_M3U8%'] . '/part.m3u8"';
            $commands = str_replace(array_keys($params), array_values($params), $commands);
            if (!file_exists($params['%OUTPUT_TMP_FILE%']) && !file_exists($params['%OUTPUT_FILE%'])) {
                $exec = '( ' . implode(' && ', $commands) . ' ) >> "' . $params['%LOG_FILE%'] . '" 2>&1 &';
                file_put_contents($params['%LOG_FILE%'], $exec);
                $this->getLogger()->debug($exec);
                shell_exec($exec);
            }
        }
        flush();
        sleep(5);
        $m3u8 = $this->generateM3u8Array($duration, 10, 'm3u8_' . $params['%BITRATE%'] . '/');
        $content = '';
        foreach ($m3u8 as $line) {
            $content = $content . $line . PHP_EOL;
        }
        file_put_contents($params['%OUTPUT_FILE%'], $content);
        $response->setContent($content);
        return $response;
    }

    public function generateM3u8Array($totalDuration, $segmentDuration, $path = '')
    {
        $targetDuration = $segmentDuration;
        $m3u8 = array();
        $m3u8[] = '#EXTM3U';
        $m3u8[] = '#EXT-X-VERSION:3';
        $m3u8[] = '#EXT-X-TARGETDURATION:' . $targetDuration;
        $m3u8[] = '#EXT-X-MEDIA-SEQUENCE:0';
        $duration = 0;
        $i = 0;
        while ($duration < $totalDuration) {
            $m3u8[] = '#EXTINF:' . $targetDuration . ',';
            $m3u8[] = $path . 'part' . $i . '.ts';
            $duration = $duration + $targetDuration;
            $i++;
        }
        $m3u8[] = '#EXT-X-ENDLIST';
        return $m3u8;
    }
}
