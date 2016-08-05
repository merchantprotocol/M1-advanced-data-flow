# M1 Advanced Data flow

 - [Buy It](https://merchantprotocol.com/store/magento-extensions/magento-v1-0/advanced-dataflow.html)
 - [Demo It](http://demo.merchantprotocol.com/M1-advanced-data-flow)
 - [Forum: Talk About It](https://merchantprotocol.com/forums/forum/magento-plugin-forum/advanced-dataflow/)

The “Advanced Dataflow” extension is a set of additional adapters and parsers to increase the Magento Dataflow flexibility. As you may know, Magento migration processes are introduced with profiles. Each profile is a combination of actions (adapters / parsers / mappers) to transfer data from one format into another. The adapters and parsers variety makes the migration process flexible. Magento enables customers and products (with or without an inventory) to be exported / imported using CSV or Excel XML formats. The “Advanced Dataflow” extends default bounds to cover more needs. The most essential “Advanced Dataflow” feature is the order import and export. It appends orders to list of available entities to migrate.

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
