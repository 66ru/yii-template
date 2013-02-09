yii-template
========

Yii template project with nice bundled extensions.

* Admin part build using [Yii-Bootstrap](http://www.cniska.net/yii-bootstrap/)
* [Twig](http://twig.sensiolabs.org/) used as render engine
* Written lightweight admin engine. Example: [User admin controller](https://github.com/mediasite/yii-template/blob/master/protected/controllers/admin/AdminUsersController.php)

### Screenshots
* [screenshot1](http://dl.dropbox.com/u/788488/Screenshots/yii-template screenshot1.png)
* [screenshot2](http://dl.dropbox.com/u/788488/Screenshots/yii-template screenshot2.png)

### Notice
Current version is built for cyrillic language support. Future versions will support english.

## Backwards incompatability changes
* Converted Boot* class names to Tb* class names
* Changed managing user passwords. Now there are separate magic password field and autofilled hashedPassword field
* Removed `save` scenario from AdminController
* Changed widgets configuration pattern (see AdminController::getEditFormElements)
* DependInputScriptWidget now DependedAjaxInputWidget
* Removed DependedInputWidget