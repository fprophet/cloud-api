<?php

    class DIContainer
    {
        private $container = array();

        public function resolve($class)
        {
            $reflection = new ReflectionClass($class);

            $constructor = $reflection->getConstructor();

            if ($constructor === null) {
                return new $class();
            }

            $dependencies = [];

            foreach ($constructor->getParameters() as $parameter) {
             
                $type = $parameter->getType();

                // var_dump($type->getName());

                // die("here");
                if ($type->isBuiltin()) {
                    if ($parameter->isDefaultValueAvailable()) {

                        $dependencies[] = $parameter->getDefaultValue();
                        continue;
                    }

                    // throw new Exception("Unresolvable parameter: {$parameter->getName()}");
                }
                else {
                    $constructedParameter = $this->resolve($parameter->getType()->getName());
                    $dependencies[] = $constructedParameter;
                }

            }

            return $reflection->newInstanceArgs(
                $dependencies
            );
        }
    }

?>