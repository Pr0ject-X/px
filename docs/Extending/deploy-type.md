
###  Plugin Implementation

Currently project-x core only supports the `git` artifact deploy type. If you would like to develop your own deploy type plugin then you'll need to create a custom composer package and implement the same plugin namespace `[CUSTOM NAMESPACE]\ProjectX\Plugin\DeployType`. 

Look at the `/src/ProjectX/Plugin/DeployType/GitDeployType.php` for an example.


