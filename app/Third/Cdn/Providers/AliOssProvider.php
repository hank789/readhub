<?php

namespace App\Third\Cdn\Providers;

use Aws\S3\BatchDelete;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Illuminate\Support\Collection;
use Publiux\laravelcdn\Contracts\CdnHelperInterface;
use Publiux\laravelcdn\Providers\Contracts\ProviderInterface;
use Publiux\laravelcdn\Providers\Provider;
use Publiux\laravelcdn\Validators\Contracts\ProviderValidatorInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

/**
 * Class AwsS3Provider
 * Amazon (AWS) S3.
 *
 *
 * @category Driver
 *
 * @property string  $provider_url
 * @property string  $threshold
 * @property string  $version
 * @property string  $region
 * @property string  $credential_key
 * @property string  $credential_secret
 * @property string  $buckets
 * @property string  $acl
 * @property string  $cloudfront
 * @property string  $cloudfront_url
 * @property string $http
 *
 * @author   Mahmoud Zalt <mahmoud@vinelab.com>
 */
class AliOssProvider extends Provider implements ProviderInterface
{
    /**
     * All the configurations needed by this class with the
     * optional configurations default values.
     *
     * @var array
     */
    protected $default = [
        'url' => null,
        'threshold' => 10,
        'providers' => [
            'oss' => [
                'root_dir' => 'readhub',
            ],
        ],
    ];

    /**
     * Required configurations (must exist in the config file).
     *
     * @var array
     */
    protected $rules = ['url'];

    /**
     * this array holds the parsed configuration to be used across the class.
     *
     * @var Array
     */
    protected $supplier;

    /**
     * @var Instance of Aws\S3\S3Client
     */
    protected $s3_client;

    /**
     * @var Instance of Guzzle\Batch\BatchBuilder
     */
    protected $batch;

    /**
     * @var \Publiux\laravelcdn\Contracts\CdnHelperInterface
     */
    protected $cdn_helper;

    /**
     * @var \Publiux\laravelcdn\Validators\Contracts\ConfigurationsInterface
     */
    protected $configurations;

    /**
     * @var \Publiux\laravelcdn\Validators\Contracts\ProviderValidatorInterface
     */
    protected $provider_validator;

    /**
     * @param \Symfony\Component\Console\Output\ConsoleOutput $console
     * @param \Publiux\laravelcdn\Validators\Contracts\ProviderValidatorInterface $provider_validator
     * @param \Publiux\laravelcdn\Contracts\CdnHelperInterface                    $cdn_helper
     */
    public function __construct(
        ConsoleOutput $console,
        ProviderValidatorInterface $provider_validator,
        CdnHelperInterface $cdn_helper
    ) {
        $this->console = $console;
        $this->provider_validator = $provider_validator;
        $this->cdn_helper = $cdn_helper;
    }

    /**
     * Read the configuration and prepare an array with the relevant configurations
     * for the (AWS S3) provider. and return itself.
     *
     * @param $configurations
     *
     * @return $this
     */
    public function init($configurations)
    {
        // merge the received config array with the default configurations array to
        // fill missed keys with null or default values.
        $this->default = array_replace_recursive($this->default, $configurations);

        $supplier = [
            'provider_url' => $this->default['url'],
            'threshold' => $this->default['threshold'],
            'root_dir' => $this->default['providers']['oss']['root_dir'],
        ];

        // check if any required configuration is missed
        $this->provider_validator->validate($supplier, $this->rules);

        $this->supplier = $supplier;

        return $this;
    }

    /**
     * Upload assets.
     *
     * @param $assets
     *
     * @return bool
     */
    public function upload($assets)
    {

        // user terminal message
        $this->console->writeln('<fg=yellow>Comparing local files and bucket...</fg=yellow>');

        // upload each asset file to the CDN
        if (count($assets) > 0) {
            $this->console->writeln('<fg=yellow>Upload in progress......</fg=yellow>');
            foreach ($assets as $file) {
                try {
                    //$extension = $file->getExtension();
                    $this->console->writeln('<fg=cyan>'.'Uploading file path: '.$file->getRealpath().'</fg=cyan>');
                    $file_path = $this->supplier['root_dir'].'/'.str_replace('\\', '/', $file->getPathName());
                    Storage::put($file_path,File::get($file));

                } catch (S3Exception $e) {
                    $this->console->writeln('<fg=red>'.$e->getMessage().'</fg=red>');

                    return false;
                }
            }

            // user terminal message
            $this->console->writeln('<fg=green>Upload completed successfully.</fg=green>');
        } else {
            // user terminal message
            $this->console->writeln('<fg=yellow>No new files to upload.</fg=yellow>');
        }

        return true;
    }

    /**
     * Create an S3 client instance
     * (Note: it will read the credentials form the .env file).
     *
     * @return bool
     */
    public function connect()
    {
        return true;
    }

    /**
     * @param $s3_client
     */
    public function setS3Client($s3_client)
    {
        $this->s3_client = $s3_client;
    }

    /**
     * @param $assets
     * @return mixed
     */
    private function getFilesAlreadyOnBucket($assets)
    {
        return $assets;
    }

    /**
     * @return array
     */
    public function getBucket()
    {
        // this step is very important, "always assign returned array from
        // magical function to a local variable if you need to modify it's
        // state or apply any php function on it." because the returned is
        // a copy of the original variable. this prevent this error:
        // Indirect modification of overloaded property
        // Vinelab\Cdn\Providers\AwsS3Provider::$buckets has no effect

        return rtrim(key($bucket), '/');
    }

    /**
     * Empty bucket.
     *
     * @return bool
     */
    public function emptyBucket()
    {


        // user terminal message

        try {
            $this->console->writeln('<fg=yellow>Emptying in progress...</fg=yellow>');

        } catch (S3Exception $e) {
            $this->console->writeln('<fg=red>'.$e->getMessage().'</fg=red>');

            return false;
        }

        $this->console->writeln('<fg=green>The bucket '.$this->getBucket().' is now empty.</fg=green>');

        return true;
    }

    /**
     * This function will be called from the CdnFacade class when
     * someone use this {{ Cdn::asset('') }} facade helper.
     *
     * @param $path
     *
     * @return string
     */
    public function urlGenerator($path)
    {
        $url = $this->getUrl();

        return $url . '/' . $path;
    }

    /**
     * @return string
     */
    public function getCloudFront()
    {
        if (!is_bool($cloudfront = $this->cloudfront)) {
            return false;
        }

        return $cloudfront;
    }

    /**
     * @return string
     */
    public function getCloudFrontUrl()
    {
        return rtrim($this->cloudfront_url, '/').'/';
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return rtrim($this->provider_url, '/') . '/'.$this->supplier['root_dir'];
    }

    /**
     * @param $attr
     *
     * @return Mix | null
     */
    public function __get($attr)
    {
        return isset($this->supplier[$attr]) ? $this->supplier[$attr] : null;
    }
}