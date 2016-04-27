<?php

namespace Netshark\Symfony\Distribution;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Util\Filesystem;

final class ProjectConfigurator
{
    /**
     * @var Composer
     */
    private $composer;

    /**
     * @var IOInterface
     */
    private $io;

    /**
     * @param Composer $composer
     * @param IOInterface $io
     */
    public function __construct(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    /**
     * Runs the project configurator.
     *
     * @return void
     */
    public function run()
    {
        $namespace = $this->ask('Namespace', function ($namespace) { return $this->validateNamespace($namespace); }, 'App');
        $packageName = $this->ask('Package name', function ($packageName) { return $this->validatePackageName($packageName); }, $this->suggestPackageName($namespace));
        $license = $this->ask('License', function ($license) { return trim($license); }, 'proprietary');
        $description = $this->ask('Description', function ($description) { return trim($description); }, '');

        $file = new JsonFile('./composer.json');
        $config = $file->read();
        $config['name'] = $packageName;
        $config['license'] = $license;
        $config['description'] = $description;
        $config['autoload']['psr-4'] = [$namespace . '\\' => 'src/'];
        $config['autoload-dev']['psr-4'] = [$namespace . '\\Tests\\' => 'tests/'];
        unset($config['scripts']['post-root-package-install']);
        $config['extra']['branch-alias']['dev-master'] = '1.0-dev';
        $file->write($config);

        $this->composer->setPackage(Factory::create($this->io, null, true)->getPackage()); // reload root package
        
        $filesystem = new Filesystem();
        $filesystem->removeDirectory('./app/Distribution');
    }

    /**
     * @param string $question
     * @param callable $validator
     * @param string|null $default
     * @return string
     */
    private function ask($question, callable $validator, $default = null)
    {
        return $this->io->askAndValidate($this->formatQuestion($question, $default), $validator, null, $default);
    }

    /**
     * @param string $question
     * @param string|null $default
     * @return string
     */
    private function formatQuestion($question, $default)
    {
        if ($default !== null) {
            return sprintf('<question>%s</question> (<comment>%s</comment>): ', $question, $default);
        }

        return sprintf('<question>%s</question>: ', $question);
    }

    /**
     * @param string $namespace
     * @throws \RuntimeException
     * @return string
     */
    private function validateNamespace($namespace)
    {
        $namespace = trim(trim(str_replace('/', '\\', $namespace)), '\\');

        if (!preg_match('#^[a-z_][a-z0-9_]*(\\\\[a-z_][a-z0-9_]*)*$#i', $namespace)) {
            throw new \RuntimeException('Invalid namespace.');
        }

        return $namespace;
    }

    /**
     * @param string $packageName
     * @throws \RuntimeException
     * @return string
     */
    private function validatePackageName($packageName)
    {
        $packageName = trim(strtolower($packageName));

        if (!preg_match('#^[a-z0-9]+(-[a-z0-9]+)*/[a-z0-9]+(-[a-z0-9]+)*$#', $packageName)) {
            throw new \RuntimeException('Invalid package name.');
        }

        return $packageName;
    }

    /**
     * Generates a Composer package name from a given PHP namespace.
     *
     * @param string $namespace
     * @return string
     */
    private function suggestPackageName($namespace)
    {
        $parts = explode('\\', $namespace);

        if (count($parts) === 1) {
            $parts[1] = $parts[0];
        }

        $vendor = strtolower($parts[0]);
        $project = strtolower(implode('-', array_slice($parts, 1)));

        return sprintf('%s/%s', $vendor, $project);
    }
}