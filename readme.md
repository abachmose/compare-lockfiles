**Compare lockfiles from package managers like Yarn and NPM**

This tool makes it easy for you to migrate from NPM to Yarn or vice versa. It gives you a nice table to clarify the modules versions.

To compare lockfiles you run the command from you'r project eg.:

`./lockfiles compare package-lock.json:npm yarn.lock:yarn`, where the `:npm` and `:yarn` specifies the parser to parse the lockfile

Optional parameters: 

`[-p|--prioritize]`: Specifies which lockfile to prioritize (the package manager you wan't to upgrade to). Specify which lockfile to prioritize eg: `-p npm`, where `npm` specifies the parser to prioritize

This tool is also available on packagist: https://packagist.org/packages/slowmose/compare-lock-files