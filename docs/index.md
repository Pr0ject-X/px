
## Core Architecture

Project-X was created as a plugable CLI application that can be used to streamline redundant tasks for a given project. Due to this type of architecture there are only a few commands that are provided by the main application; so it can remain as lean as possible. Most commands will be loaded from a third-party plugins which will need to be required per project.

## Install CLI Shortcut

It can be quite redundant invoking the `vendor/droath/px` executable from the vendor directory. The recommended method would be to run the `vendor/droath/px core:cli-shortcut` command. Which will create an `px` function in the users default shell (bash, zsh) RC file.

Then you can run the `px` command within the project root directory, and will no longer need to supply the full vendor path. This only needs to be executed once and all projects will be able to benefit from it.
