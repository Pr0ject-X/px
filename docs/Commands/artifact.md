### build

Build the project artifact.

Run the following command:

    px core:artifact:build

**Options:**

- --build-dir[=BUILD-DIR]  
- --project-dir[=PROJECT-DIR]
- --build-copy=BUILD-COPY    
- --build-mirror=BUILD-MIRROR  
- --project-copy=PROJECT-COPY  
- --project-mirror=PROJECT-MIRROR   
- --remove-submodules=REMOVE-SUBMODULES 
- --search-submodules-depth[=SEARCH-SUBMODULES-DEPTH]

### deploy

Deploy the artifact for a given deploy type.

Run the following command:

    px core:artifact:deploy
    
**Options:**

 - --plugin-id[=PLUGIN-ID]  
 - --build-dir[=BUILD-DIR]  
 - --build-path          
 - -r, --repo=REPO         
 - -o, --origin=ORIGIN
 - -b, --branch=BRANCH
