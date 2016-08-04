# Magento v1x New Extension Configuration

Drop this into each new extension repository to provide it with all of the files necessary for users. This repository shall contain licensing information, general installation and uninstallation notes necessary for the user.

## Version Control

This change log and release versions will be managed according to [keepachangelog.com](http://keepachangelog.com/) and [Semantic Versioning 2.0.0](http://semver.org/).  **Magento.Major.Minor.Fixes**

## Magento Compatible Versions

* *Magento Enterprise Edition* **1.13.x** ~ **1.14.x**
* *Magento Community Edition* **1.6.x** ~ **1.9.x**

## System Requirements

* PHP 5.4 >

## Installation

### Installation with [Modman](https://github.com/colinmollenhour/modman)

In the Magento root folder start a modman repository:

```bash
modman init
```

Clone the module directly from github repository:

```bash
modman clone git@github.com:merchantprotocol/M1.git
```

### Manual installation

Clone the project in any folder on your computer and copy the entire contents of the src folder in the Magento root directory:

```bash
cp -R path/module/src/* magento/path/
```

## Contributing

1. Create a fork!
2. Create a branch for the features: `git checkout -b my-new-feature`
3. Make commit yours changes: `git commit -am 'Add some feature'`
4. Give a push to branch: `git push origin my-new-feature`
5. Create a pull request

## Credits

Author||Version
--- | --- | ---
**Jonathon Byrd** | jonathon@merchantprotocol.com | `1.0.0.0`

## License

All code is protected and belongs to **Merchant Protocol**.
