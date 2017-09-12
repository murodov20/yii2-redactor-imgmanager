Imperavi Redactor with image manager functionality via db
=========================================================
Redactor with image manager, Image select widget and Multiple image placing widgets in one extension

Thanks to @vova07 for [yii2-imperavi-redactor](https://github.com/vova07/yii2-imperavi-redactor) and Thanks to @sam_dark and @yiiext for purchasing OEM license

Imperavi Redactor is the paid commercial extension, but some developers of Yii bought OEM license for 
for all Yii developers. That's why this version of Redactor has OEM license and you can use it for all your commercial projects. But you cann't update Redactor to new version.

Installation
------------

! Don't use this extension in production. Extension under development. if you want to go hard with this, you can use version 1.0.0.

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

+Development:

Either run

```
    php composer.phar require murodov20/yii2-redactor-imgmanager "*"
```

or add

```
    "murodov20/yii2-redactor-imgmanager": "*"
```

+Production:

```
    php composer.phar require murodov20/yii2-redactor-imgmanager "1.0.0"
```

or add

```
    "murodov20/yii2-redactor-imgmanager": "1.0.0"
```


to the require section of your `composer.json` file.

After this run migration:
```
php yii migrate --migrationPath="@vendor/murodov20/yii2-redactor-imgmanager/migrations"
```

Usage
-----

Once the extension is installed, simply use it in your code.

Simple configuration:

First of all, you need to add photo-manager module to your project:

```php
'modules' => [
		...
        'photo-manager' => [
            'class' => '\murodov20\redactor\PhotoManagerModule',
            'urlManager' => 'urlManager', //optional
        ]
        ...
    ],

```

In Redactor widget, Redactor will load image's src, In MultiplePhoto and PhotoInput widgets will be load image id.

Using widgets:

RedactorWidget:
```php
<?= $form
		->field($model, 'content')
		->textarea(['rows' => 6])
		->widget(MRedactorWidget::className(), [
            'settings' => [
                'lang' => 'ru',
                'minHeight' => 200,
                'imageUpload' => Yii::$app->urlManager->createUrl(['/photo-manager/image/ajax-upload']),
                'managerUrl' => Yii::$app->urlManager->createUrl(['/photo-manager/image/get']),
                'plugins' => [
                    'fontfamily',
                    'fontsize',
                    'fontcolor',
                    'table',
                    'clips',
                    'textexpander',
                    'video',
                    'fullscreen',
                    'manager'
                ]
            ]
        ]) ?>

```

PhotoInputWidget:
```php
<?= PhotoInputWidget::widget(
        [
            'model' => $model,
            'attribute' => 'image'
        ]
    ) ?>

```

MultiplePhoto:
```php
<?= MultiplePhoto::widget([
	    'model' => $model,
	    'attribute' => 'images'
	]) ?>

```

If you are using MultiplePhoto widget, 'attribute' param must be an array. 
You can access to values like this (eg. in Controller):
```php
$model = new Post();
if ($model->load(Yii::$app->request->post()) && $model->save){
	foreach ($model->images as $image) {
		$slider = new PostSlider();
		$slider->post_id = $model->id;
		$slider->image_id = $image;
		$slider->save();
	}
}

```
