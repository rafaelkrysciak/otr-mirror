<?php  namespace App\Services;

use App\AwsOtrkeyFile;
use App\OtrkeyFile;
use Aws\Laravel\AwsFacade as AWS;
use Carbon\Carbon;
use \Log;

class AwsS3Service {


    /**
     * Delete files on AWS::S3 which are older then 10 days
     *
     * @return int
     */
    public function deleteOldFiles()
    {
        $s3 = AWS::get('s3');

        $awsFiles = AwsOtrkeyFile::with('otrkeyFile')->get();

        $count = 0;

        foreach ($awsFiles as $awsFile) {
            if ($awsFile->otrkeyFile->start > Carbon::now()->subDays(10)) {
                continue;
            }
            try {
                $s3->deleteObject([
                    "Bucket" => config('aws.bucket'),
                    "Key"    => 'otrkey/' . $awsFile->otrkeyFile->name,
                ]);
                $awsFile->delete();
                $count++;
            } catch (\Exception $e) {
                Log::error($e);
            }
        }

        return $count;
    }


    /**
     * Sync files with database
     *
     * @return int count read files
     */
    public function syncFiles()
    {
        $s3 = AWS::get('s3');
        $iterator = $s3->getIterator('ListObjects', [
            "Bucket" => config('aws.bucket'),
            "Prefix" => "otrkey/"
        ]);

        $count = 0;
        foreach ($iterator as $object) {
            try {
                $filename = basename($object['Key']);

                $otrkeyFile = OtrkeyFile::filename($filename)->first();
                if (is_null($otrkeyFile)) {
                    Log::error("Aws file not found : {$filename}");
                    continue;
                }

                $count++;

                $awsOtrkeyFile = AwsOtrkeyFile::where('otrkeyfile_id', '=', $otrkeyFile->id)->first();
                if (is_null($awsOtrkeyFile)) {
                    AwsOtrkeyFile::create([
                        'otrkeyfile_id' => $otrkeyFile->id,
                        'last_modified' => Carbon::parse($object['LastModified']),
                        'size'          => $object['Size'],
                        'checksum'      => trim($object['ETag'], ''),
                    ]);
                } else {
                    $awsOtrkeyFile->last_modified = Carbon::parse($object['LastModified']);
                    $awsOtrkeyFile->size = $object['Size'];
                    $awsOtrkeyFile->checksum = trim($object['ETag'], '"');
                    $awsOtrkeyFile->save();

                    if ($awsOtrkeyFile->checksum != $otrkeyFile->checksum) {
                        Log::error("Aws checksum mismatch for file {$otrkeyFile->id}:{$otrkeyFile->name}");
                    }
                }
            } catch (\Exception $e) {
                Log::error($e);
            }
        }

        return $count;
    }
}