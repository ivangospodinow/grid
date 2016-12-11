<?php

namespace Grid\Plugin;

interface PaginationLinkInterface
{
    public function createPaginationLink(int $page) : string;
    public function getActivePaginationPage() : int;
}
