<?php  namespace App\Services; 


use App\Exceptions\LimitExceededDownloadException;
use App\Exceptions\NoCapacityDownloadException;
use App\Exceptions\QualityViolationDownloadException;
use App\Exceptions\S3FileNotFoundException;
use App\Node;
use App\OtrkeyFile;
use App\User;
use Aws\Laravel\AwsFacade as AWS;

class DownloadService {

    const PREMIUM = 'premium';
    const REGISTERED = 'registered';
    const GUEST = 'guest';
    /**
     * @var StatService
     */
    private $statService;
    /**
     * @var NodeService
     */
    private $nodeService;


    function __construct(StatService $statService, NodeService $nodeService)
    {
        $this->statService = $statService;
        $this->nodeService = $nodeService;
    }


    public function createS3DownloadLink($filename)
    {
        /* @var $s3 \Aws\S3\S3Client */
        $s3 = AWS::get('s3');
        $object = '/otrkey/' . $filename;

        if (!$s3->doesObjectExist(config('aws.bucket'), $object)) {
            throw new S3FileNotFoundException;
        }

        $request = $s3->get(config('aws.bucket') . $object);
        $signedUrl = $s3->createPresignedUrl($request, '+2 hours');
        \Log::info("S3 link created for file $filename");

        return $signedUrl;
    }


    /**
     * @param OtrkeyFile $otrkeyFile
     * @param User $user
     *
     * @return string
     * @throws LimitExceededDownloadException
     * @throws NoCapacityDownloadException
     * @throws QualityViolationDownloadException
     * @throws \Exception
     */
    public function getDownloadLink(OtrkeyFile $otrkeyFile, $downloadType)
    {
        $node = $otrkeyFile->availableFiles->sortBy('busy_workers')->first();


        if ($downloadType == self::PREMIUM && $node->busy_workers > 150 && $otrkeyFile->awsOtrkeyFile) {
            try {
                $signedUrl = $this->createS3DownloadLink($otrkeyFile->name);
                $this->statService->trackAwsDownload($otrkeyFile->id);
                return $signedUrl;
            } catch (\Exception $e) {
                // ToDo: Mail senden
                Log::error($e);
            }
        }

        $this->assertUserAllowedToDownload($downloadType, $node);
        try {
            $url = $this->nodeService->generateDownloadLink($node, $otrkeyFile->name, $downloadType);
        } catch(\Exception $e) {
            if ($downloadType == self::PREMIUM && $otrkeyFile->awsOtrkeyFile) {
                try {
                    $signedUrl = $this->createS3DownloadLink($otrkeyFile->name);
                    $this->statService->trackAwsDownload($otrkeyFile->id);
                    return $signedUrl;
                } catch (\Exception $e) {
                    // ToDo: Mail senden
                    Log::error($e);
                    throw $e;
                }
            } else {
                throw $e;
            }
        }

        $this->statService->trackDownload($otrkeyFile->id);
        return $url;
    }


    public function validateDownloadToken($token, $filename, $downloadType)
    {
        return $this->nodeService->validateDownloadToken($token, $filename, $downloadType);
    }


    /**
     * @param User $user
     * @param Node $node
     * @param OtrkeyFile $otrkeyFile
     *
     * @throws LimitExceededDownloadException
     * @throws NoCapacityDownloadException
     * @throws QualityViolationDownloadException
     */
    protected function assertUserAllowedToDownload($downloadType, Node $node)
    {
        if($downloadType == self::PREMIUM) {
            return;
        }

        if ($downloadType == self::REGISTERED && $node->busy_workers > 120) {
            throw new NoCapacityDownloadException;
        }

        if ($downloadType == self::GUEST && $node->busy_workers > 90) {
            throw new NoCapacityDownloadException;
        }
    }
}