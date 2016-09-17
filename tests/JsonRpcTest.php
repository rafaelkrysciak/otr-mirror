<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Services\NodeService;

class JsonRpcTest extends TestCase
{

    /**
     * Get Node info over JsonRPC
     *
     * @return void
     */
    public function testNodeInfo()
    {
        $nodeService = new NodeService(new \App\Services\OtrkeyFileService(new \App\Services\TvProgramService()));
        // ToDo: Remove dependency on an entry in DB
        $node = \App\Node::find(1);
        $nodeStatus = $nodeService->getNodeStatus($node);

        $this->assertArrayHasKey('BusyWorkers', $nodeStatus);
        $this->assertArrayHasKey('IdleWorkers', $nodeStatus);
        $this->assertArrayHasKey('Scoreboard', $nodeStatus);
        $this->assertArrayHasKey('totalDiskspace', $nodeStatus);
        $this->assertArrayHasKey('freeDiskspace', $nodeStatus);
        $this->assertArrayHasKey('filesCount', $nodeStatus);
        $this->assertArrayHasKey('loadAverage1', $nodeStatus);
        $this->assertArrayHasKey('loadAverage5', $nodeStatus);
        $this->assertArrayHasKey('loadAverage15', $nodeStatus);
    }

}
