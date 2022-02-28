
The following commands are provided by the Project-X core component. These commands are available on every project.

## cli-shortcut

The `cli-shortcut` command adds CLI integration into the local shell, currently supports bash or zsh.

    vendor/bin/px core:cli-shortcut

After adding the CLI integration you can type `px` inside the terminal without including the composer vendor bin directory. If you have problems, verify that you resourced the shell RC file using `source .bashrc` or `source .zshrc`.

##  install

The `install` command provides a way to install Project-X plugins.

  	vendor/bin/px core:install

**Arguments**

	searchTerm (optional): The search term to filter out plugins; otherwise all plugins are shown.

**Options**

	--working-directory[=WORKING-DIRECTORY]: The composer working directory.

##  save

The `save` command saves the project path into the user configuration so that you can switch between projects using the `switch` command.

  	vendor/bin/px core:save

**Arguments**

	name: The name of the project.

**Options**

	--edit: Set if you want to manually edit this file.

##  remove

The `remove` command removes the project path from the user configuration.

  	vendor/bin/px core:remove

##  switch

The `switch` command allows you to change projects seamlessly. This command requires that you are using Project-X on at least two separate projects.

  	vendor/bin/px core:switch

**Arguments**

	project: The project name.

**Options**

	--raw: If set the raw output will be returned.

##  status

The `status` command displays project information such as what configuration files are been used, etc.

  	vendor/bin/px core:status


