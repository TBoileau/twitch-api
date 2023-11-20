# CONTRIBUTING

Please take a moment to read this document so that you can easily follow the contribution process.
## Issues
[Issues](https://github.com/TBoileau/twitch-api/issues) is the ideal channel for bug reports, new features or `pull requests', but please note the following restrictions:
* Do not use this channel for personal requests for help (use [Stack Overflow](http://stackoverflow.com/)).
* It is forbidden to insult or offend in any way when commenting on a `issue`. Respect the opinions of others and stay focused on the main discussion.

## Report a bug
A bug is a concrete error, caused by the code present in this `repository`.

Guide :
1. Make sure you don't create a report that already exists. Think about doing some research before publishing an issue.
2. Check that the bug has been fixed, by trying out the latest version of the code on the `production` or `develop` branch.
3. Isolate the problem to create a simple, identifiable test case.

## New feature
New features are always welcome. However, take the time to think about it, and make sure it fits in with the project's objectives.

It's up to you to present solid arguments to convince the project's developers of the benefits of this feature.

## Pull request
Good `pull requests` are a great help. They should remain within the scope of the project and should not contain `commits` unrelated to the project.

Please ask before posting your `pull request`, otherwise you risk wasting work time because the project team doesn't want to integrate your work.

Follow this process to submit a `pull request` that respects good practice:
1. Fork the repository on your account
2. Create a new branch that will contain your feature, modification or correction:
    * For a new feature or modification :
        ```
        git checkout develop
        git checkout -b feature/<feature-name>
        ```
    * For a new fix :
        ```
        git checkout production
        git checkout -b hotfix/<feature-name>
        ```
   *You can also use [git-flow](https://danielkummer.github.io/git-flow-cheatsheet/index.fr_FR.html) to simplify the management of your branches :*
    * For a new feature or modification:
        ```
        git flow feature start <feature-name>
        ```
    * For a new fix :
        ```
        git flow hotfix start <hotfix-name>
        ```
3. `Commit` your changes, be sure to respect the naming convention of your `commits` as follows:
    ```
    <type>: <subject>
    <BLANK LINE>
    <body>
    <BLANK LINE>
    <footer>
    ```
    The header is mandatory.
    
    Types:
   * **build**: Changes that have an effect on the system (installation of new dependencies, compose, npm, environments, ...).
   * **ci**: Continuous integration configuration
   * **cd**: Configuration of continuous deployment
   * **docs**: Documentation modifications
   * **feat**: New functionality
   * **fix**: Fix (`hotfix`)
   * **perf**: Code modification to optimize performance
   * **refactor**: Any code modification as part of refactoring
   * **style**: Corrections specific to coding style (PSR-12)
   * **test**: Addition of a new test or correction of an existing test

4. Code testing and analysis :
   Before **push** to Github, remember to run a static analysis of the files and all tests.

5. Push your branch to your repository:
    ```
    git push origin <branch-name> 
    ```

6. Open a new `pull request` with a precise title and description.

## Versioning
Respect `Semantic Versioning 2` :
> Given a version number MAJOR.MINOR.PATCH, increment the:
>
> MAJOR version when you make incompatible API changes,
>
> MINOR version when you add functionality in a backwards-compatible manner, and
>
> PATCH version when you make backwards-compatible bug fixes.