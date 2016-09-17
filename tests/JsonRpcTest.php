<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Services\NodeService;

class JsonRpcTest extends TestCase
{
    /**
     * Ensure that the JsonRPC is working
     *
     * @return void
     */
    public function testNodeCall()
    {
        $nodeService = new NodeService(new \App\Services\OtrkeyFileService(new \App\Services\TvProgramService()));
        $diskSpace = $nodeService->getTotalFreeDiskSpace();
        $this->assertNotEmpty($diskSpace);
    }
}
