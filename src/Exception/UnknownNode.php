<?php

namespace Codographic\Exception;

use Codographic\Exception;
use PhpParser\Node;


class UnknownNode extends Exception {
    public function __construct(Node $node = null) {
        $class = get_class($node);
        $message = "Node is instance of class '{$class}'";
        parent::__construct($message);
    }
}
