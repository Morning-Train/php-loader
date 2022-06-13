<?php

    namespace Morningtrain\PHPLoader;

    use http\Exception;
    use Symfony\Component\Finder\Finder;

    /**
     * For loading, and initializing, all classes or files in a directory
     * Directory and classes must follow PSR-4
     */
    class Loader
    {

        private bool $useCacheFile = false; // TODO: Implement a method for caching the file->class map for faster subsequent load times
        private ?string $className = null;
        private ?string $hasMethod = null;
        private string|array $fileName = '*.php';
        private null|string|array $notFileName = null;
        private ?string $callMethod = null;
        private ?string $callStaticMethod = null;
        private bool $invoke = false;
        private bool $construct = false;
        private bool $isExpectingClasses = false;

        private array $files = [];
        private array $classes = [];

        /**
         * Create a ClassLoader for use
         *
         * @param string|array $path
         *
         * @return static
         */
        public static function create(string|array $path): static
        {
            return new static($path);
        }

        public function __construct(private string|array $path)
        {
        }

        public function __destruct()
        {
            $this->findFiles();
            $this->loadClasses();
            $this->handleClasses();
        }

        /**
         * Whether to read from or create a file containing all known classes
         * This is for faster loading on later runs
         *
         * @return $this
         */
        public function useCacheFile(): static
        {
            $this->useCacheFile = true;

            return $this;
        }

        /**
         * Loaded classes must be of this class or a subclass thereof
         *
         * @param string $class
         *
         * @return $this
         */
        public function isA(string $class): static
        {
            $this->className = $class;
            $this->isExpectingClasses = true;

            return $this;
        }

        /**
         * Class must have the following method
         *
         * @param string $hasMethod
         *
         * @return $this
         */
        public function hasMethod(string $hasMethod): static
        {
            $this->hasMethod = $hasMethod;
            $this->isExpectingClasses = true;

            return $this;
        }

        /**
         * Filename must match before being loaded
         * Defaults to .php
         *
         * @param string|array $fileNameMatch
         *
         * @return $this
         */
        public function fileName(string|array $fileNameMatch): static
        {
            $this->fileName = $fileNameMatch;

            return $this;
        }

        /**
         * Filename that MUST NOT match before being loaded
         *
         * @param string|array $fileNameMatch
         *
         * @return $this
         */
        public function notFileName(string|array $fileNameMatch): static
        {
            $this->notFileName = $fileNameMatch;

            return $this;
        }

        /**
         * Call this method on an instance of the loaded classes.
         * If this is set then construct() is not necessary to specify
         *
         * @param string $callMethod
         *
         * @return $this
         */
        public function call(string $callMethod): static
        {
            $this->callMethod = $callMethod;
            $this->isExpectingClasses = true;

            return $this;
        }

        /**
         * Call this static method on all loaded classes if it exists
         *
         * @param string $staticCallMethod
         *
         * @return $this
         */
        public function callStatic(string $staticCallMethod): static
        {
            $this->callStaticMethod = $staticCallMethod;
            $this->isExpectingClasses = true;

            return $this;
        }

        /**
         * Construct and invoke class
         * If this is set then construct() is not necessary to specify
         *
         * @return $this
         */
        public function invoke(): static
        {
            $this->invoke = true;
            $this->isExpectingClasses = true;

            return $this;
        }

        /**
         * Construct all loaded classes
         *
         * @return $this
         */
        public function construct(): static
        {
            $this->construct = true;
            $this->isExpectingClasses = true;

            return $this;
        }

        /**
         * Find all files matching file params and store them in $files
         */
        private function findFiles(): void
        {
            $finder = new Finder();
            $finder->files()->name($this->fileName)->in($this->path);

            if ($this->notFileName !== null) {
                $finder->notName($this->notFileName);
            }

            if (!$finder->hasResults()) {
                return;
            }

            foreach ($finder as $file) {
                $this->files[] = $file->getPathname();
            }
        }

        /**
         * Load all found classes by files and store them in $classes
         */
        private function loadClasses(): void
        {
            $classes = [];
            foreach ($this->files as $file) {
                require $file;
                $classes[] = \pathinfo($file, PATHINFO_FILENAME);
            }

            if (!$this->isExpectingClasses) {
                return;
            }

            $all_classes = get_declared_classes();
            $l = count($all_classes); // Length of class array

            // Loop backwards through all known classes to find ours
            foreach ($classes as $class) {
                for ($i = $l - 1; $i >= 0; $i--) {
                    $current_class_parts = explode('\\', $all_classes[$i]);
                    $current_class_name = $current_class_parts[array_key_last($current_class_parts)];

                    if ($current_class_name === $class) {
                        $this->classes[] = $all_classes[$i];
                    }
                }
            }
        }

        /**
         * Validate, call, construct, invoke the classes as defined
         */
        private function handleClasses(): void
        {
            foreach ($this->classes as $class) {
                $this->handleClass($class);
            }
        }

        /**
         * Validate, call, construct, invoke a class as defined
         *
         * @param string $class
         */
        private function handleClass(string $class): void
        {
            // If a classname has been set. Make sure the loaded class matches before continuing
            if ($this->className && !is_a($class, $this->className, true)) {
                return;
            }
            // If class must have a given method then return if method does not exist
            if ($this->hasMethod && !method_exists($class, $this->hasMethod)) {
                return;
            }

            // Call the static method if set and exists
            if ($this->callStaticMethod && method_exists($class, $this->callStaticMethod)) {
                $method = $this->callStaticMethod;
                $class::$method();
            }

            if ($this->construct || $this->invoke || $this->callMethod) {
                $instance = new $class();
                if ($this->invoke) {
                    $instance();
                }
                if ($this->callMethod) {
                    $method = $this->callMethod;
                    $instance->$method();
                }
            }
        }
    }