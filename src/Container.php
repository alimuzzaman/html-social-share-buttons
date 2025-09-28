<?php
namespace HtmlSocialShare;

use HtmlSocialShare\Utils\ArrayUtils;

/**
 * Service container for dependency injection
 * 
 * Provides a simple service container with support for singletons,
 * factories, parameters, and circular dependency detection.
 * Separates pure resolution logic from side effects.
 * 
 * @package HtmlSocialShare
 * @since 3.0.0
 */
class Container
{
    private array $services = [];
    private array $instances = [];
    private array $parameters = [];
    private array $resolving = []; // Track services being resolved to detect circular dependencies

    /**
     * Register a service definition
     *
     * @param string $id Service identifier
     * @param mixed $service Service definition (callable, instance, or class name)
     * @param bool $singleton Whether to cache instance
     * @return void
     */
    public function set(string $id, $service, bool $singleton = true): void
    {
        if (!self::isValidServiceId($id)) {
            throw new \InvalidArgumentException("Invalid service ID: {$id}");
        }

        $this->services[$id] = [
            'definition' => $service,
            'singleton' => $singleton,
        ];

        // Clear cached instance if it exists
        unset($this->instances[$id]);
    }

    /**
     * Get a service instance
     *
     * @param string $id Service identifier
     * @return mixed Service instance
     * @throws \RuntimeException If service not found or circular dependency detected
     */
    public function get(string $id)
    {
        // Check for circular dependency
        if (isset($this->resolving[$id])) {
            $chain = implode(' -> ', array_keys($this->resolving)) . ' -> ' . $id;
            throw new \RuntimeException("Circular dependency detected: {$chain}");
        }

        // Return cached singleton instance
        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        }

        if (!array_key_exists($id, $this->services)) {
            throw new \RuntimeException("Service '{$id}' not found");
        }

        $serviceDefinition = $this->services[$id];
        
        // Mark as resolving
        $this->resolving[$id] = true;
        
        try {
            $instance = $this->resolveService($id, $serviceDefinition);
            
            // Cache singleton instances
            if ($serviceDefinition['singleton']) {
                $this->instances[$id] = $instance;
            }
            
            return $instance;
        } finally {
            // Always clean up resolving state
            unset($this->resolving[$id]);
        }
    }

    /**
     * Check if service exists
     *
     * @param string $id Service identifier
     * @return bool True if service is registered
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services);
    }

    /**
     * Remove service and its instance
     *
     * @param string $id Service identifier
     * @return void
     */
    public function remove(string $id): void
    {
        unset($this->services[$id], $this->instances[$id]);
    }

    /**
     * Set parameter value
     *
     * @param string $key Parameter key (supports dot notation)
     * @param mixed $value Parameter value
     * @return void
     */
    public function setParameter(string $key, $value): void
    {
        $this->parameters = ArrayUtils::set($this->parameters, $key, $value);
    }

    /**
     * Get parameter value
     *
     * @param string $key Parameter key (supports dot notation)
     * @param mixed $default Default value if not found
     * @return mixed Parameter value or default
     */
    public function getParameter(string $key, $default = null)
    {
        return ArrayUtils::get($this->parameters, $key, $default);
    }

    /**
     * Check if parameter exists
     *
     * @param string $key Parameter key
     * @return bool True if parameter exists
     */
    public function hasParameter(string $key): bool
    {
        return ArrayUtils::has($this->parameters, $key);
    }

    /**
     * Get all parameters
     *
     * @return array All parameters
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Register factory service (always creates new instance)
     *
     * @param string $id Service identifier
     * @param callable $factory Factory function
     * @return void
     */
    public function factory(string $id, callable $factory): void
    {
        $this->set($id, $factory, false);
    }

    /**
     * Register singleton service
     *
     * @param string $id Service identifier
     * @param mixed $service Service definition
     * @return void
     */
    public function singleton(string $id, $service): void
    {
        $this->set($id, $service, true);
    }

    /**
     * Create child container with inherited services
     *
     * @return self New child container
     */
    public function createChild(): self
    {
        $child = new self();
        $child->services = $this->services; // Inherit service definitions
        $child->parameters = $this->parameters; // Inherit parameters
        // Don't inherit instances - each container manages its own
        return $child;
    }

    /**
     * Get list of registered service IDs
     *
     * @return array Service identifiers
     */
    public function getServiceIds(): array
    {
        return array_keys($this->services);
    }

    /**
     * Get container statistics
     *
     * @return array Container statistics
     */
    public function getStats(): array
    {
        return [
            'services_registered' => count($this->services),
            'instances_cached' => count($this->instances),
            'parameters_set' => count($this->parameters),
            'memory_usage' => self::getMemoryUsage($this->instances),
        ];
    }

    /**
     * Clear all cached instances (keeps service definitions)
     *
     * @return void
     */
    public function clearInstances(): void
    {
        $this->instances = [];
    }

    /**
     * Clear everything (services, instances, parameters)
     *
     * @return void
     */
    public function clear(): void
    {
        $this->services = [];
        $this->instances = [];
        $this->parameters = [];
        $this->resolving = [];
    }

    /**
     * Resolve service definition to instance
     *
     * @param string $id Service identifier
     * @param array $serviceDefinition Service configuration
     * @return mixed Service instance
     * @throws \RuntimeException If service cannot be resolved
     */
    private function resolveService(string $id, array $serviceDefinition)
    {
        $definition = $serviceDefinition['definition'];

        if (is_callable($definition)) {
            return $this->resolveCallable($definition);
        }

        if (is_string($definition) && class_exists($definition)) {
            return $this->resolveClass($definition);
        }

        if (is_object($definition)) {
            return $definition; // Return instance as-is
        }

        throw new \RuntimeException("Cannot resolve service '{$id}': invalid definition");
    }

    /**
     * Resolve callable service definition
     *
     * @param callable $callable Callable definition
     * @return mixed Service instance
     */
    private function resolveCallable(callable $callable)
    {
        try {
            return $callable($this);
        } catch (\Throwable $e) {
            throw new \RuntimeException("Error resolving callable service: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Resolve class name service definition with automatic constructor injection
     *
     * @param string $className Class name
     * @return object Service instance
     * @throws \RuntimeException If class cannot be instantiated
     */
    private function resolveClass(string $className): object
    {
        try {
            $reflection = new \ReflectionClass($className);
            
            if (!$reflection->isInstantiable()) {
                throw new \RuntimeException("Class '{$className}' is not instantiable");
            }

            $constructor = $reflection->getConstructor();
            
            if ($constructor === null) {
                // No constructor - simple instantiation
                return new $className();
            }

            // Resolve constructor dependencies
            $parameters = $constructor->getParameters();
            $dependencies = [];
            
            foreach ($parameters as $parameter) {
                $dependencies[] = $this->resolveParameter($parameter);
            }

            return $reflection->newInstanceArgs($dependencies);
        } catch (\ReflectionException $e) {
            throw new \RuntimeException("Cannot resolve class '{$className}': " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Resolve constructor parameter
     *
     * @param \ReflectionParameter $parameter Parameter reflection
     * @return mixed Resolved parameter value
     * @throws \RuntimeException If parameter cannot be resolved
     */
    private function resolveParameter(\ReflectionParameter $parameter)
    {
        $type = $parameter->getType();
        
        if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
            $className = $type->getName();
            
            // Try to get service by class name (interface or class)
            if ($this->has($className)) {
                return $this->get($className);
            }
            
            // Try to auto-resolve if it's a concrete class
            if (class_exists($className)) {
                return $this->resolveClass($className);
            }
        }
        
        // Try parameter by name
        if ($this->hasParameter($parameter->getName())) {
            return $this->getParameter($parameter->getName());
        }
        
        // Use default value if available
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }
        
        // Allow null if nullable
        if ($parameter->allowsNull()) {
            return null;
        }
        
        throw new \RuntimeException("Cannot resolve parameter '{$parameter->getName()}' for auto-wiring");
    }

    // ===== PURE FUNCTIONS (NO SIDE EFFECTS) =====

    /**
     * Pure function: Validate service identifier format
     *
     * @param string $id Service identifier
     * @return bool True if valid
     */
    public static function isValidServiceId(string $id): bool
    {
        if (empty($id)) {
            return false;
        }
        
        // Allow alphanumeric, underscore, dot, and backslash (for namespaced classes)
        return preg_match('/^[a-zA-Z_\\\\][a-zA-Z0-9_\\\\.]*$/', $id) === 1;
    }

    /**
     * Pure function: Calculate memory usage of cached instances
     *
     * @param array $instances Cached instances array
     * @return int Memory usage in bytes
     */
    public static function getMemoryUsage(array $instances): int
    {
        try {
            return strlen(serialize($instances));
        } catch (\Throwable $e) {
            // Fallback if serialization fails
            return count($instances) * 1024; // Rough estimate
        }
    }

    /**
     * Pure function: Analyze service dependencies
     *
     * @param array $services Service definitions array
     * @return array Dependency analysis
     */
    public static function analyzeDependencies(array $services): array
    {
        $analysis = [
            'total_services' => count($services),
            'singletons' => 0,
            'factories' => 0,
            'callable_services' => 0,
            'class_services' => 0,
            'instance_services' => 0,
        ];
        
        foreach ($services as $definition) {
            if ($definition['singleton']) {
                $analysis['singletons']++;
            } else {
                $analysis['factories']++;
            }
            
            $service = $definition['definition'];
            if (is_callable($service)) {
                $analysis['callable_services']++;
            } elseif (is_string($service) && class_exists($service)) {
                $analysis['class_services']++;
            } elseif (is_object($service)) {
                $analysis['instance_services']++;
            }
        }
        
        return $analysis;
    }

    /**
     * Pure function: Validate container configuration
     *
     * @param array $config Container configuration
     * @return array Validation result with 'valid' boolean and 'errors' array
     */
    public static function validateConfiguration(array $config): array
    {
        $errors = [];
        
        if (isset($config['services']) && !is_array($config['services'])) {
            $errors[] = "Services configuration must be an array";
        }
        
        if (isset($config['parameters']) && !is_array($config['parameters'])) {
            $errors[] = "Parameters configuration must be an array";
        }
        
        // Validate service definitions
        if (isset($config['services'])) {
            foreach ($config['services'] as $id => $service) {
                if (!self::isValidServiceId($id)) {
                    $errors[] = "Invalid service ID: {$id}";
                }
                
                if (!is_callable($service) && !is_string($service) && !is_object($service)) {
                    $errors[] = "Invalid service definition for: {$id}";
                }
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
