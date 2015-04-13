<?php

namespace Codographic\Exception;

use Codographic\Exception;
use PhpParser\Node;


class UnknownNode extends Exception {
    public function __construct(Node $node = null) {
        if ($node) {
            $class = get_class($node);
            $message = "Node is instance of class '{$class}'. ";
            $actions = [
                'processNode'  => [],
                'processNodes' => [],
                'tally'        => [],
                'tallyArray'   => [],
            ];
            foreach ($node as $property => $value) {
                if (is_array($value)) {
                    if ($value && ($value[0] instanceof Node)) {
                        $actions['processNodes'][] = $property;
                    } else {
                        $actions['tallyArray'][] = $property;
                    }
                } else if ($value instanceof Node) {
                    $actions['processNode'][] = $property;
                } else {
                    $actions['tally'][] = $property;
                }
            }
            $message .=  var_export($actions, true);
        } else {
            $message = 'Unknown Node type.';
        }
        parent::__construct($message);
    }
}
