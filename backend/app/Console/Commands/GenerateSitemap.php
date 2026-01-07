<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sitemap.xml by running the Node.js sitemap generator';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemap...');

        // Get the base path (backend directory)
        $basePath = base_path();
        
        // Navigate to the frontend directory (edicodes folder)
        $frontendPath = dirname($basePath) . DIRECTORY_SEPARATOR . 'edicodes';
        
        // Check if the frontend directory exists
        if (!is_dir($frontendPath)) {
            $this->error("Frontend directory not found at: {$frontendPath}");
            return 1;
        }

        // Check if generate-sitemap.js exists
        $sitemapScript = $frontendPath . DIRECTORY_SEPARATOR . 'generate-sitemap.js';
        if (!file_exists($sitemapScript)) {
            $this->error("Sitemap script not found at: {$sitemapScript}");
            return 1;
        }

        // Change to frontend directory and run the script
        try {
            $result = Process::path($frontendPath)
                ->run('node generate-sitemap.js');

            if ($result->successful()) {
                $this->info('Sitemap generated successfully! ✅');
                if ($result->output()) {
                    $this->line($result->output());
                }
                return 0;
            } else {
                $this->error('Failed to generate sitemap!');
                if ($result->errorOutput()) {
                    $this->error($result->errorOutput());
                }
                return 1;
            }
        } catch (\Exception $e) {
            // Fallback: try using exec directly
            $this->warn('Process facade failed, trying exec fallback...');
            
            $originalDir = getcwd();
            chdir($frontendPath);
            
            $output = [];
            $returnVar = 0;
            exec('node generate-sitemap.js 2>&1', $output, $returnVar);
            
            chdir($originalDir);
            
            if ($returnVar === 0) {
                $this->info('Sitemap generated successfully! ✅');
                if (!empty($output)) {
                    $this->line(implode("\n", $output));
                }
                return 0;
            } else {
                $this->error('Failed to generate sitemap!');
                if (!empty($output)) {
                    $this->error(implode("\n", $output));
                }
                return 1;
            }
        }
    }
}

