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
     * {@inheritDoc}
     */
    public function deploy()
    {
        $this->runGitInitProcess();

        if ($this->hasTrackedFilesChanged()) {
            $buildVersion = $this->buildSemanticVersion(
                $this->latestVersionTag()
            );

            $this->getGitBuildStack()
                ->commit("Build commit for {$buildVersion}.")
                ->tag($buildVersion)
                ->run();

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
        } else {
            $stack
                ->checkout($branch)
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
     * Get latest GIT version tag.
     *
     * @return string
     *   Get the latest GIT version tag.
     */
    protected function latestVersionTag()
    {
        $task = $this->getGitBuildStack()
            ->exec('describe --abbrev=0 --tags');

        /** @var \Robo\Result $result */
        $result = $this->runSilentCommand($task);
        $version = trim($result->getMessage());

        return !empty($version) && preg_match('/(\d+.\d+.\d+)/', $version)
            ? $version
            : '0.0.0';
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
