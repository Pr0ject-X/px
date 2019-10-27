<?php

namespace Pr0jectX\Px\ProjectX\Plugin\DeployType;

use Pr0jectX\Px\Deploy\GitDeploy;
use Symfony\Component\Console\Input\InputOption;
use Pr0jectX\Px\Exception\DeployTypeOptionRequired;
use Robo\Task\Vcs\loadTasks as taskVcs;

/**
 * The GIT deployment implementation.
 */
class GitDeployType extends DeployTypeBase implements GitDeployTypeInterface
{
    use taskVcs;

    /**
     * {@inheritDoc}
     */
    public static function pluginId()
    {
        return 'git';
    }

    /**
     * {@inheritDoc}
     */
    public static function pluginLabel()
    {
        return 'Git';
    }

    /**
     * {@inheritDoc}
     */
    public static function deployOptions()
    {
        return [
            new InputOption(
                'repo',
                'r',
                InputOption::VALUE_REQUIRED,
                'Set the deployment git repository URL.'
            ),
            new InputOption(
                'origin',
                'o',
                InputOption::VALUE_REQUIRED,
                'Set the deployment git repository origin.',
                'origin'
            ),
            new InputOption(
                'branch',
                'b',
                InputOption::VALUE_REQUIRED,
                'Set the deployment git repository branch.',
                'master'
            )
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getRepo()
    {
        $options = $this->getOptions();

        if (!isset($options['repo'])) {
            throw new DeployTypeOptionRequired(
                static::pluginId(), 'repo'
            );
        }

        return $options['repo'];
    }

    /**
     * {@inheritDoc}
     */
    public function getOrigin()
    {
        $options = $this->getOptions();

        if (!isset($options['origin'])) {
            throw new DeployTypeOptionRequired(
                static::pluginId(), 'origin'
            );
        }

        return $options['origin'];
    }

    /**
     * {@inheritDoc}
     */
    public function getBranch()
    {
        return $this->getOptions()['branch'];
    }

    /**
     * Get the build versioning method.
     *
     * @return string
     *   The build version method (e.g. tag or file).
     */
    public function getBuildVersioningMethod()
    {
        return $this->getOptions()['versioning-method'] ?? 'tag';
    }

    /**
     * Determine if the build version should be applied.
     *
     * @return bool
     *   Return true if the build version shouldn't be applied; otherwise false.
     */
    public function noBuildVersion() {
        return $this->getOptions()['no-build-version'] ?? false;
    }

    /**
     * {@inheritDoc}
     */
    public function deploy()
    {
        $this->runGitInitProcess();

        if ($this->hasTrackedFilesChanged()) {
            $commitTask = $this->getGitBuildStack();

            if (!$this->noBuildVersion()) {
                $buildVersion = $this->buildSemanticVersion(
                    $this->latestBuildVersion()
                );

                if ($this->getBuildVersioningMethod() === 'file') {
                    if ($this->updateBuildVersionFile($buildVersion)) {
                        $commitTask->add($this->getBuildVersionFile());
                    }
                }
                $commitTask->commit("Build commit for {$buildVersion}.");

                if ($this->getBuildVersioningMethod() === 'tag') {
                    $commitTask->tag($buildVersion);
                }
            } else {
                $commitDate = date('m-d-Y \a\t g:ia');
                $commitTask->commit("Build commit on {$commitDate}");
            }
            $commitTask->run();

            $this->getGitBuildStack()
                ->exec("push -u --tags {$this->getOrigin()} {$this->getBranch()}")
                ->run();
        } else {
            $this->say("Deploy build hasn't changed.");
        }
    }

    /**
     * Run the GIT initialize process.
     */
    protected function runGitInitProcess()
    {
        $repo = $this->getRepo();
        $branch = $this->getBranch();
        $origin = $this->getOrigin();

        $stack = $this->getGitBuildStack();

        if (!$this->buildDirectoryHasGit()) {
            $stack
                ->exec('init')
                ->exec("remote add {$origin} {$repo}");

            if ($this->remoteBranchExist()) {
                $stack
                    ->exec('fetch --all')
                    ->exec("reset --soft {$origin}/{$branch}");
            }
            $stack->exec("checkout -b {$branch}");
        } else {
            $stack
                ->exec("checkout -b {$branch}")
                ->pull($origin, $branch);
        }
        $stack
            ->add('.')
            ->run();
    }

    /**
     * Build directory has GIT initialized.
     *
     * @return bool
     *   Return true if the build directory has GIT support; otherwise false.
     */
    protected function buildDirectoryHasGit()
    {
        return file_exists("{$this->getBuildDir()}/.git");
    }

    /**
     * Get the latest build version.
     *
     * @return string
     *   The latest build version based on the versioning method.
     */
    protected function latestBuildVersion()
    {
        switch ($this->getBuildVersioningMethod()) {
            case 'file':
                $version = $this->latestVersionFromFile();
                break;
            case 'tag':
            default:
                $version = $this->latestVersionFromTag();
                break;
        }

        if ($version === false
            || !preg_match('/(\d+.\d+.\d+)/', $version)) {
            $version = '0.0.0';
        }

        return $version;
    }

    /**
     * Get latest GIT build version tag.
     *
     * @return string|boolean
     *   Get the latest GIT build version tag; otherwise false if not found.
     */
    protected function latestVersionFromTag()
    {
        $task = $this->getGitBuildStack()
            ->exec('describe --abbrev=0 --match "*.*.*" --tags');

        /** @var \Robo\Result $result */
        $result = $this->runSilentCommand($task);
        $version = trim($result->getMessage());

        return !empty($version) ? $version : false;
    }

    /**
     * Get latest build version from file.
     *
     * If the build version doesn't exist then use the latest version from the
     * GIT tag as the fallback.
     *
     * @return string|boolean
     *   Get the latest build version from file; otherwise false if not found.
     */
    protected function latestVersionFromFile()
    {
        $versionFile = $this->getBuildVersionFile();

        if (!file_exists($versionFile)) {
            file_put_contents(
                $versionFile,
                $this->latestVersionFromTag() ?? '0.0.0'
            );
        }

        return file_get_contents($versionFile);
    }

    /**
     * Update build version file.
     *
     * @param $buildVersion
     *   The build version to set as the contents.
     *
     * @return bool
     *   Return true if contents has been updated; otherwise false.
     */
    protected function updateBuildVersionFile($buildVersion)
    {
        $status = file_put_contents(
            $this->getBuildVersionFile(), $buildVersion
        );

        return $status !== false ? true : false;
    }

    /**
     * Has tracked files changed.
     *
     * @return bool
     *   Return true if git tracked files changed; otherwise false.
     */
    protected function hasTrackedFilesChanged()
    {
        $task = $this->getGitBuildStack()
            ->exec("status --untracked-files=no --porcelain");

        /** @var \Robo\Result $result */
        $result = $this->runSilentCommand($task);

        $changes = array_filter(
            explode("\n", $result->getMessage())
        );

        return (bool) count($changes) != 0;
    }

    /**
     * Remote GIT branch exist.
     *
     * @return bool
     *   Return true if the remote branch exist; otherwise false.
     */
    protected function remoteBranchExist()
    {
        $task = $this->getGitBuildStack()
            ->exec("ls-remote --exit-code --heads {$this->getRepo()} {$this->getBranch()}");

        /** @var Result $result */
        $result = $this->runSilentCommand($task);

        return $result->getExitCode() === 0;
    }

    /**
     * Get the build version file name.
     *
     * @return string
     *   The path to the version file in the build directory.
     */
    protected function getBuildVersionFile()
    {
        return "{$this->getBuildDir()}/VERSION";
    }

    /**
     * Get the GIT build stack instance.
     *
     * @return \Robo\Task\Vcs\GitStack
     *   Get the GIT build stack instance.
     */
    protected function getGitBuildStack()
    {
        return $this->taskGitStack()->dir($this->getBuildDir());
    }
}
