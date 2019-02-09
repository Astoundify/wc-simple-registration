## Versioning

[Semantic Versioning](http://semver.org/) is used. Any release that makes a change that is not a regression from the previously release should be a minor release. 

## Creating a Release

1. Create a `release/x.x.x` branch off of master.
2. Add features or fix bugs. See sections below.
3. Assign at least one reviewer other than yourself to the Pull Request.
4. Once reviewed the reviewer can merge the release in to the `master` branch.

## Create a Release

### Update `readme.txt`

[Add a meaningful list of changes](https://github.com/Astoundify/wc-simple-registration/blob/master/readme.txt#L22) made in the new release.

### Bump Version Number

3 files need a version bump:

- [readme.txt](https://github.com/Astoundify/wc-simple-registration/blob/master/readme.txt#L7)
- [package.json](https://github.com/Astoundify/wc-simple-registration/blob/master/package.json#L2)
- [woocommerce-simple-registration.php](https://github.com/Astoundify/wc-simple-registration/blob/master/woocommerce-simple-registration.php.php#L6)

### Update Language Files

From a clean working directory:

```
$ npm install
$ grunt pot
```

### Tag Release

[Create a new release on Github](https://github.com/Astoundify/wc-simple-registration/releases/new). No binary needs generation, but it is a good idea to manually create a `.zip` file formatted with the version number, that extracts to `wc-simple-registration-3.0.0.zip` > `wc-simple-registration.zip` > `wc-simple-registration`
