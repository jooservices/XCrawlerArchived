<?php

namespace Tests;

abstract class AbstractXCityTest extends AbstractCrawlingTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->fixtures = __DIR__ . '/../app/Jav/Tests/Fixtures/XCity';
    }
}
