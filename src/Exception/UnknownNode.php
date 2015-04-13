<?php

namespace Codographic\Exception;

use Codographic\Analyser;
use Codographic\Exception;
use PhpParser\Node;


class UnknownNode extends Exception {
    public function __construct(Node $node = null) {
        if ($node) {
            $class = get_class($node);
            $message = "Node is instance of class '{$class}'. ";
            $actions = [];
            foreach ($node as $property => $value) {
                if (!in_array($property, Analyser::NODE_PROPERTIES)) {
                    $actions[] = $property;
                }
            }
            $message .=  var_export($actions, true);
        } else {
            $message = 'Unknown Node type.';
        }
        parent::__construct($message);
    }
}
