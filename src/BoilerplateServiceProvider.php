<?php

declare(strict_types=1);

namespace Motomedialab\Boilerplate;

use Illuminate\Support\ServiceProvider;

final class BoilerplateServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        // Only run setup when running in the console (like during package:discover)
        if (!$this->app->runningInConsole()) {
            return;
        }

        // Avoid running setup multiple times
        $lockFile = base_path('.boilerplate-installed');
        if (file_exists($lockFile)) {
            return;
        }

        $this->ensureInstalledAsDevDependency();

        $this->runSetup($lockFile);
    }

    /**
     * Ensure the package is installed in the require-dev section.
     */
    protected function ensureInstalledAsDevDependency(): void
    {
        $composerJsonPath = base_path('composer.json');
        if (!file_exists($composerJsonPath)) {
            return;
        }

        $composerJson = json_decode(file_get_contents($composerJsonPath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return;
        }

        if (isset($composerJson['require']['motomedialab/boilerplate'])) {
            throw new \LogicException(
                "The 'motomedialab/boilerplate' package must only be installed as a development dependency. " .
                "Please run:\n" .
                "  composer remove motomedialab/boilerplate\n" .
                "  composer require --dev motomedialab/boilerplate"
            );
        }
    }

    /**
     * Run the boilerplate installation setup.
     */
    protected function runSetup(string $lockFile): void
    {
        echo "\n\e[34mMotoMediaLab Boilerplate: Starting post-install setup...\e[0m\n";

        $this->copyStubs();
        $this->updatePackageJson();
        $this->installNpmPackages();

        // 4. Create lock file to prevent re-running
        file_put_contents($lockFile, 'Installed at ' . date('Y-m-d H:i:s') . "\n");

        echo "\e[32mMotoMediaLab Boilerplate: Setup completed successfully!\e[0m\n\n";
    }

    /**
     * Copy stubs from the package to the host project root.
     */
    protected function copyStubs(): void
    {
        $stubs = [
            'phpstan.neon' => 'phpstan.neon',
            'rector.php' => 'rector.php',
            'pint.json' => 'pint.json',
            '.prettierignore' => '.prettierignore',
            '.prettierrc.json' => '.prettierrc.json',
        ];

        $stubsDir = __DIR__ . '/../stubs';

        foreach ($stubs as $stubName => $destName) {
            $source = $stubsDir . '/' . $stubName;
            $destination = base_path($destName);

            if (file_exists($source)) {
                if (!file_exists($destination)) {
                    copy($source, $destination);
                    echo "  - Copied stub: {$destName}\n";
                } else {
                    echo "  - Stub already exists (skipping): {$destName}\n";
                }
            } else {
                echo "  - \e[31mWarning:\e[0m Stub source not found: {$stubName}\n";
            }
        }
    }

    /**
     * Insert our custom commands into package.json.
     */
    protected function updatePackageJson(): void
    {
        $packageJsonPath = base_path('package.json');

        if (!file_exists($packageJsonPath)) {
            echo "  - \e[31mWarning:\e[0m package.json not found in project root. Skipping scripts injection.\n";
            return;
        }

        $content = file_get_contents($packageJsonPath);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "  - \e[31mWarning:\e[0m Failed to parse package.json. Skipping scripts injection.\n";
            return;
        }

        if (!isset($data['scripts'])) {
            $data['scripts'] = [];
        }

        $data['scripts']['format'] = "npx prettier --write resources";
        $data['scripts']['check'] = "npx prettier --check resources";
        $data['scripts']['preflight'] = "npm run format && ./vendor/bin/rector && ./vendor/bin/phpstan --memory-limit=2G && ./vendor/bin/pint --parallel && ./vendor/bin/pest --parallel";

        file_put_contents(
            $packageJsonPath,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL
        );

        echo "  - Updated package.json scripts.\n";
    }

    /**
     * Run npm install to pull in the required frontend tools.
     */
    protected function installNpmPackages(): void
    {
        echo "  - Installing node packages (prettier, prettier-plugin-blade, prettier-plugin-tailwindcss)...\n";
        
        $command = 'npm install --save-dev prettier prettier-plugin-blade prettier-plugin-tailwindcss';

        $output = [];
        $resultCode = 0;
        
        exec("cd " . escapeshellarg(base_path()) . " && {$command} 2>&1", $output, $resultCode);

        if ($resultCode === 0) {
            echo "  - Node packages installed successfully.\n";
        } else {
            echo "  - \e[31mWarning:\e[0m Failed to install node packages. Run this manually: `{$command}`\n";
            // Print error lines for debugging
            foreach (array_slice($output, -5) as $line) {
                echo "    > {$line}\n";
            }
        }
    }
}
